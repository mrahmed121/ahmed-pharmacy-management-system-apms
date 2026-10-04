<?php
namespace App\Http\Controllers\Api\V1;
use App\Domains\Inventory\Models\Medicine;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class InventoryController extends Controller
{
    public function medicines(Request $request): JsonResponse
    {
        $q = Medicine::with(['batches' => fn($q) => $q->orderBy('expiry_date')])->orderBy('name');
        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('barcode', 'like', "%$s%")->orWhere('generic_name', 'like', "%$s%"));
        }
        $p = $q->paginate($request->get('per_page', 20));
        $items = collect($p->items())->map(fn($m) => [...$m->toArray(), 'total_stock' => $m->batches->sum('quantity')]);
        return response()->json(['data' => $items, 'meta' => ['current_page' => $p->currentPage(), 'per_page' => $p->perPage(), 'total' => $p->total()]]);
    }
    public function lowStock(Request $request): JsonResponse
    {
        $meds = Medicine::with('batches')->get()->filter(fn($m) => $m->batches->sum('quantity') <= $m->reorder_level)->values();
        return response()->json(['data' => $meds]);
    }
    public function nearExpiry(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 90);
        $batches = \App\Domains\Inventory\Models\Batch::with('medicine')
            ->where('quantity', '>', 0)
            ->where('expiry_date', '<=', now()->addDays($days)->toDateString())
            ->orderBy('expiry_date')->get();
        return response()->json(['data' => $batches]);
    }
}
