<?php
namespace App\Http\Controllers\Api\V1;
use App\Domains\Inventory\Models\Batch;
use App\Domains\Inventory\Models\Medicine;
use App\Domains\Sales\Models\Sale;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cid = $request->user()->company_id;
        $lowStock = Medicine::where('company_id', $cid)->get()->filter(fn($m) =>
            Batch::where('company_id', $cid)->where('medicine_id', $m->id)->sum('quantity') <= $m->reorder_level)->count();
        return response()->json(['data' => [
            'today_revenue' => Sale::where('company_id', $cid)->whereDate('created_at', today())->sum('total'),
            'today_invoices' => Sale::where('company_id', $cid)->whereDate('created_at', today())->count(),
            'total_medicines' => Medicine::where('company_id', $cid)->count(),
            'low_stock_count' => $lowStock,
            'near_expiry_count' => Batch::where('company_id', $cid)->where('quantity', '>', 0)->where('expiry_date', '<=', now()->addDays(90)->toDateString())->count(),
        ]]);
    }
}
