<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

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
}