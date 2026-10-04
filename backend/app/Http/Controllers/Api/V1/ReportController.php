<?php
namespace App\Http\Controllers\Api\V1;
use App\Domains\Sales\Models\Sale;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ReportController extends Controller
{
    public function dailySales(Request $request): JsonResponse
    {
        $days = min((int) $request->get('days', 30), 365);
        $data = Sale::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as invoices'), DB::raw('SUM(total) as revenue'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy(DB::raw('DATE(created_at)'))->orderBy('date')->get();
        return response()->json(['data' => $data]);
    }
}
