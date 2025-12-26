<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Resources\PartResource;
use App\Models\Order;
use App\Models\Part;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DashboardController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/dashboard/low-stock-parts",
     * summary="Get Low Stock Parts",
     * description="Returns a list of parts where the current amount is less than or equal to the warning amount.",
     * tags={"Dashboard"},
     * security={{"apiAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(
     * property="data",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="Compressor Valve"),
     * @OA\Property(property="amount", type="integer", example=2),
     * @OA\Property(property="warning_amount", type="integer", example=5),
     * @OA\Property(property="price_default", type="number", format="float", example=150.00)
     * )
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function lowStockParts(): AnonymousResourceCollection
    {
        $parts = Part::whereColumn('amount', '<=', 'warning_amount')->get();

        return PartResource::collection($parts);
    }

    /**
     * @OA\Get(
     * path="/api/dashboard/pending-orders",
     * summary="Get Pending Orders",
     * description="Returns orders that have been added but not yet started. Supports manual chunking via page parameter.",
     * tags={"Dashboard"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(
     * name="page",
     * in="query",
     * description="The chunk number (1 for first 10, 2 for next 10, etc.)",
     * required=false,
     * @OA\Schema(
     * type="integer",
     * default=1
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(
     * property="data",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="id", type="integer", example=15),
     * @OA\Property(property="customer_name", type="string", example="Some name"),
     * @OA\Property(property="device_model", type="string", example="Samsung S24"),
     * @OA\Property(property="assigned_member", type="string", example="another name"),
     * @OA\Property(property="add_date", type="string", format="date-time", example="2025-12-06T10:00:00Z"),
     * @OA\Property(property="status", type="string", example="pending"),
     * @OA\Property(property="notes", type="string", example="Screen crack")
     * )
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function pendingOrders(): AnonymousResourceCollection
    {
        $page = (int) $request->input('page', 1);
        $limit = 10;
 
        $offset = ($page - 1) * $limit;

        $orders = Order::with(['deviceModel', 'member'])
            ->whereNotNull('add_date')
            ->whereNull('start_date')
            ->whereNull('finish_date')
            ->latest('add_date')
            ->skip($offset)
            ->take($limit)
            ->get();

        return OrderResource::collection($orders);
    }
}