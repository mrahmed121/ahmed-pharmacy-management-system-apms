<?php
namespace App\Http\Controllers\Api\V1;
use App\Domains\Purchases\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class PurchaseController extends Controller
{
    public function suppliers(Request $request): JsonResponse
    {
        return response()->json(['data' => Supplier::orderBy('name')->get()]);
    }
}
