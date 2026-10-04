<?php
namespace App\Http\Controllers\Api\V1;
use App\Domains\Inventory\Models\Batch;
use App\Domains\Inventory\Models\Medicine;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class ImportController extends Controller
{
    /**
     * CSV import for medicines + opening stock.
     * Expected columns: name, generic_name, barcode, unit, rack_location, reorder_level, sale_price, batch_number, expiry_date, quantity, purchase_price
     */
    public function medicines(Request $request): JsonResponse
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:2048']);
        $companyId = $request->user()->company_id;
        $path = $request->file('file')->getRealPath();

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $expected = ['name','generic_name','barcode','unit','rack_location','reorder_level','sale_price','batch_number','expiry_date','quantity','purchase_price'];

        $imported = 0; $errors = [];
        $row = 1;
        DB::transaction(function () use ($handle, $header, $expected, $companyId, &$imported, &$errors, &$row) {
            while (($data = fgetcsv($handle)) !== false) {
                $row++;
                $record = array_combine($header, $data);
                $validator = Validator::make($record, [
                    'name' => 'required|string|max:255',
                    'sale_price' => 'nullable|numeric|min:0',
                    'quantity' => 'nullable|numeric|min:0',
                    'expiry_date' => 'nullable|date',
                    'reorder_level' => 'nullable|numeric|min:0',
                ]);
                if ($validator->fails()) {
                    $errors[] = "Row $row: " . implode(', ', $validator->errors()->all());
                    continue;
                }
                $medicine = Medicine::firstOrCreate(
                    ['company_id' => $companyId, 'name' => $record['name']],
                    [
                        'generic_name' => $record['generic_name'] ?? null,
                        'barcode' => $record['barcode'] ?? null,
                        'unit' => $record['unit'] ?? 'strip',
                        'rack_location' => $record['rack_location'] ?? null,
                        'reorder_level' => $record['reorder_level'] ?? 0,
                        'sale_price' => $record['sale_price'] ?? 0,
                    ]
                );
                if (!empty($record['batch_number']) && !empty($record['quantity'])) {
                    Batch::firstOrCreate(
                        ['company_id' => $companyId, 'medicine_id' => $medicine->id, 'batch_number' => $record['batch_number']],
                        [
                            'expiry_date' => $record['expiry_date'] ?? now()->addYear()->toDateString(),
                            'quantity' => $record['quantity'],
                            'purchase_price' => $record['purchase_price'] ?? 0,
                        ]
                    );
                }
                $imported++;
            }
        });
        fclose($handle);

        return response()->json(['data' => ['imported' => $imported, 'errors' => $errors]]);
    }

    /**
     * CSV export of current inventory.
     */
    public function exportInventory(Request $request)
    {
        $companyId = $request->user()->company_id;
        $medicines = Medicine::with('batches')->where('company_id', $companyId)->get();

        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['name','generic_name','barcode','unit','rack_location','total_stock','sale_price','is_controlled']);
        foreach ($medicines as $m) {
            fputcsv($output, [$m->name, $m->generic_name, $m->barcode, $m->unit, $m->rack_location, $m->batches->sum('quantity'), $m->sale_price, $m->is_controlled ? 'yes' : 'no']);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory-' . date('Y-m-d') . '.csv"',
        ]);
    }
}
