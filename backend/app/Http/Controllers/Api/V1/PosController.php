<?php
namespace App\Http\Controllers\Api\V1;
use App\Domains\Sales\Models\Sale;
use App\Domains\Sales\Services\SaleService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class PosController extends Controller
{
    public function __construct(private SaleService $sales) {}
    public function index(Request $request): JsonResponse
    {
        $p = Sale::with('items.medicine')->orderByDesc('created_at')->paginate($request->get('per_page', 20));
        return response()->json(['data' => $p->items(), 'meta' => ['current_page' => $p->currentPage(), 'per_page' => $p->perPage(), 'total' => $p->total()]]);
    }
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|integer|exists:medicines,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'discount' => 'nullable|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'payment_method' => 'nullable|in:cash,card,mobile',
            'paid' => 'nullable|numeric|min:0',
            'idempotency_key' => 'nullable|string|max:100',
        ]);
        $sale = $this->sales->createSale($request->user()->company_id, $request->user()->id, $data['items'], $data);
        return response()->json(['data' => $sale], 201);
    }
}
