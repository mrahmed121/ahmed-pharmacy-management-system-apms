<?php
namespace App\Domains\Sales\Services;
use App\Domains\Inventory\Models\Batch;
use App\Domains\Inventory\Models\Medicine;
use App\Domains\Sales\Models\Sale;
use App\Domains\Sales\Models\SaleItem;
use App\Domains\Shared\Services\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
/**
 * FEFO (First-Expired-First-Out) sale processing.
 * Deducts stock from earliest-expiring batches first, transactionally.
 * Concurrency-safe via row-level locking (SELECT ... FOR UPDATE).
 */
class SaleService
{
    public function __construct(private AuditService $audit) {}

    /**
     * @param array $items [['medicine_id' => int, 'quantity' => float], ...]
     */
    public function createSale(int $companyId, int $userId, array $items, array $meta = []): Sale
    {
        // Idempotency: return existing sale if key was already used
        if (!empty($meta['idempotency_key'])) {
            $existing = Sale::where('idempotency_key', $meta['idempotency_key'])->first();
            if ($existing) return $existing->load('items');
        }

        return DB::transaction(function () use ($companyId, $userId, $items, $meta) {
            $saleItems = [];
            $subtotal = 0;

            foreach ($items as $item) {
                $medicine = Medicine::where('company_id', $companyId)->findOrFail($item['medicine_id']);
                $qtyNeeded = (float) $item['quantity'];
                if ($qtyNeeded <= 0) throw ValidationException::withMessages(['quantity' => 'Quantity must be positive.']);

                // Lock batches in FEFO order for concurrency safety
                $batches = Batch::where('company_id', $companyId)
                    ->where('medicine_id', $medicine->id)
                    ->where('quantity', '>', 0)
                    ->orderBy('expiry_date')
                    ->lockForUpdate()
                    ->get();

                $available = $batches->sum('quantity');
                if ($available < $qtyNeeded) {
                    throw ValidationException::withMessages([
                        'stock' => "Insufficient stock for {$medicine->name}. Available: {$available}, needed: {$qtyNeeded}."
                    ]);
                }

                // FEFO deduction
                $remaining = $qtyNeeded;
                foreach ($batches as $batch) {
                    if ($remaining <= 0) break;
                    $deduct = min((float) $batch->quantity, $remaining);
                    $batch->decrement('quantity', $deduct);
                    $remaining -= $deduct;

                    $lineTotal = $deduct * (float) $medicine->sale_price;
                    $saleItems[] = [
                        'medicine_id' => $medicine->id,
                        'batch_id' => $batch->id,
                        'quantity' => $deduct,
                        'unit_price' => $medicine->sale_price,
                        'line_total' => $lineTotal,
                    ];
                    $subtotal += $lineTotal;
                }
            }

            $discount = (float) ($meta['discount'] ?? 0);
            $total = max(0, $subtotal - $discount);

            $sale = Sale::create([
                'company_id' => $companyId,
                'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad((string)(Sale::whereDate('created_at', today())->count() + 1), 5, '0', STR_PAD_LEFT),
                'customer_name' => $meta['customer_name'] ?? null,
                'customer_phone' => $meta['customer_phone'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'paid' => $meta['paid'] ?? $total,
                'payment_method' => $meta['payment_method'] ?? 'cash',
                'idempotency_key' => $meta['idempotency_key'] ?? null,
                'created_by' => $userId,
            ]);

            foreach ($saleItems as $si) {
                $si['sale_id'] = $sale->id;
                SaleItem::create($si);
            }

            $this->audit->log('sales.create', $sale, ['invoice' => $sale->invoice_number, 'total' => $total]);

            return $sale->load('items');
        });
    }
}
