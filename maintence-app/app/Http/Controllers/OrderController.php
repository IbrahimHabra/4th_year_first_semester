<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;


/**
 * @OA\Post(
 * path="/api/orders",
 * summary="Create New Order",
 * description="Creates a new maintenance order for a customer.",
 * tags={"Orders"},
 * security={{"apiAuth":{}}},
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * required={"model_id", "member_id", "customer_name"},
 * @OA\Property(property="model_id", type="integer", example=10, description="ID of the device model"),
 * @OA\Property(property="member_id", type="integer", example=2, description="ID of the technician assigned"),
 * @OA\Property(property="customer_name", type="string", example="Adel Kharma"),
 * @OA\Property(property="notes", type="string", example="Screen is cracked, touch not working", nullable=true)
 * )
 * ),
 * @OA\Response(
 * response=201,
 * description="Order created successfully",
 * @OA\JsonContent(
 * @OA\Property(property="message", type="string", example="Order created successfully"),
 * @OA\Property(property="data", ref="#/components/schemas/OrderResource")
 * )
 * ),
 * @OA\Response(response=422, description="Validation Error")
 * )
 */
class OrderController extends Controller
{
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = Order::create([
            'model_id' => $request->model_id,
            'member_id' => $request->member_id,
            'customer_name' => $request->customer_name,
            'notes' => $request->notes,
            // 'add_date' is handled by database default (CURRENT_TIMESTAMP)
            // 'on_hand_price' defaults to 0 via migration
        ]);

        return response()->json([
            'message' => 'Order created successfully',
            'data' => new OrderResource($order->load(['deviceModel', 'member'])),
        ], 201);
    }

    /**
     * @OA\Get(
     * path="/api/orders",
     * summary="Get All Orders (Chunked)",
     * description="Retrieve orders in chunks of 10. Use 'page' parameter to navigate.",
     * tags={"Orders"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(name="page", in="query", description="Chunk number (default 1)", required=false, @OA\Schema(type="integer", default=1)),
     * @OA\Response(
     * response=200,
     * description="List of orders",
     * @OA\JsonContent(type="object", @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/OrderResource")))
     * )
     * )
     */
    public function index(\Illuminate\Http\Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $page = (int) $request->input('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $orders = Order::with(['deviceModel', 'member'])
            ->latest('add_date')
            ->skip($offset)
            ->take($limit)
            ->get();

        return OrderResource::collection($orders);
    }

    /**
     * @OA\Get(
     * path="/api/orders/{id}",
     * summary="Get Order Details",
     * description="Retrieve full details of a specific order, including its current status and bills.",
     * tags={"Orders"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(
     * response=200,
     * description="Order details",
     * @OA\JsonContent(
     * @OA\Property(property="data", ref="#/components/schemas/OrderResource")
     * )
     * ),
     * @OA\Response(response=404, description="Order not found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        // Load bills too so we can see what parts were used
        $order = Order::with(['deviceModel', 'member', 'bills.part'])->findOrFail($id);

        return response()->json([
            'data' => new OrderResource($order)
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/orders/{id}",
     * summary="Delete Order",
     * description="Deletes an order. WARNING: This will automatically delete all associated bills due to cascade constraints.",
     * tags={"Orders"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Order deleted successfully")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $order = Order::findOrFail($id);
        
        // Because of ->cascadeOnDelete() in your migration, 
        // this will also delete the rows in the 'bills' table for this order.
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }

    /**
     * @OA\Put(
     * path="/api/orders/{id}",
     * summary="Edit Order Details",
     * description="Updates manual fields like notes and on_hand_price. Does NOT affect bills/parts.",
     * tags={"Orders"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true, 
     * @OA\JsonContent(
     * @OA\Property(property="customer_name", type="string"),
     * @OA\Property(property="notes", type="string"),
     * @OA\Property(property="on_hand_price", type="number", description="Manual Price Override"),
     * @OA\Property(property="member_id", type="integer")
     * )
     * ),
     * @OA\Response(response=200, description="Order updated")
     * )
     */
    public function update(\App\Http\Requests\Order\UpdateOrderRequest $request, string $id): JsonResponse
    {
        $order = Order::findOrFail($id);
        
        $order->update($request->validated());

        return response()->json([
            'message' => 'Order details updated',
            'data' => new OrderResource($order->load(['deviceModel', 'member']))
        ]);
    }
}