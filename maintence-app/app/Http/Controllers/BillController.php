<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Bill\StoreBillRequest;
use App\Http\Requests\Bill\UpdateBillRequest;
use App\Models\Bill;
use App\Models\Order;
use App\Models\Part;
use App\Models\RecordPart;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BillController extends Controller
{

    /**
     * @OA\Get(
     * path="/api/orders/{order_id}/bills",
     * summary="Get All Bills for an Order",
     * description="Retrieve the list of parts/bills associated with a specific order.",
     * tags={"Bills"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(name="order_id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(
     * response=200,
     * description="List of bills",
     * @OA\JsonContent(type="object", @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/BillResource")))
     * )
     * )
     */
    public function index(string $orderId): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        // We need a BillResource first. 
        // I will define it quickly below or we use a simple structure if one doesn't exist.
        // Assuming we need to create it strictly:
        $bills = Bill::with('part')->where('order_id', $orderId)->get();
        
        // Since we didn't make a BillResource file yet, I will create it in the next step.
        return \App\Http\Resources\BillResource::collection($bills);
    }

    /**
     * @OA\Post(
     * path="/api/orders/{order_id}/bills",
     * summary="Add Part to Order",
     * description="Creates a Bill and a 'Consumed' RecordPart (Type 2). Deducts stock.",
     * tags={"Bills"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(name="order_id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(required=true, @OA\JsonContent(required={"part_id", "part_amount"}, @OA\Property(property="part_id", type="integer"), @OA\Property(property="part_amount", type="integer"))),
     * @OA\Response(response=201, description="Part Added")
     * )
     */
    public function store(StoreBillRequest $request, string $orderId): JsonResponse
    {
        return DB::transaction(function () use ($request, $orderId) {
            $part = Part::findOrFail($request->part_id);
            $order = Order::findOrFail($orderId);

            // 1. Check Stock
            if ($part->amount < $request->part_amount) {
                throw ValidationException::withMessages(['part_amount' => "Insufficient stock. Only {$part->amount} available."]);
            }

            // 2. Create Bill (The link between Order and Part)
            $bill = Bill::create([
                'order_id' => $order->id,
                'part_id' => $part->id,
                'part_amount' => $request->part_amount,
                // Default price, but user can edit it later via Update API
                'part_price' => $part->price_default * $request->part_amount, 
            ]);

            // 3. Create RecordPart (Type 2 = Consumed)
            RecordPart::create([
                'part_id' => $part->id,
                'modify_type_id' => 2, // Consumed
                'modified_amount' => $request->part_amount,
                'modification_date' => now(),
            ]);

            // 4. Update Actual Part Table (To keep current stock fast to read)
            $part->decrement('amount', $request->part_amount);

            return response()->json(['message' => 'Part added successfully', 'bill' => $bill], 201);
        });
    }

    /**
     * @OA\Put(
     * path="/api/bills/{id}",
     * summary="Edit Bill Item",
     * description="Updates price/amount. Automatically creates RecordPart to adjust stock difference.",
     * tags={"Bills"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(required=true, @OA\JsonContent(required={"part_price", "part_amount"}, @OA\Property(property="part_price", type="number"), @OA\Property(property="part_amount", type="integer"))),
     * @OA\Response(response=200, description="Bill updated")
     * )
     */
    public function update(UpdateBillRequest $request, string $id): JsonResponse
    {
        return DB::transaction(function () use ($request, $id) {
            $bill = Bill::findOrFail($id);
            $part = $bill->part;

            $oldAmount = $bill->part_amount;
            $newAmount = $request->part_amount;
            $diff = $newAmount - $oldAmount;

            if ($diff > 0) {
                // We need MORE parts (Type 2: Consumed)
                if ($part->amount < $diff) {
                    throw ValidationException::withMessages(['part_amount' => "Insufficient stock to increase quantity."]);
                }
                RecordPart::create([
                    'part_id' => $part->id,
                    'modify_type_id' => 2, // Consumed
                    'modified_amount' => $diff,
                    'modification_date' => now(),
                ]);
                $part->decrement('amount', $diff);

            } elseif ($diff < 0) {

                $returnAmount = abs($diff);
                RecordPart::create([
                    'part_id' => $part->id,
                    'modify_type_id' => 1, // Added/Returned
                    'modified_amount' => $returnAmount,
                    'modification_date' => now(),
                ]);
                $part->increment('amount', $returnAmount);
            }

            // Update the Bill itself
            $bill->update([
                'part_amount' => $newAmount,
                'part_price' => $request->part_price, 
            ]);

            return response()->json(['message' => 'Bill updated successfully', 'bill' => $bill]);
        });
    }

    /**
     * @OA\Delete(
     * path="/api/bills/{id}",
     * summary="Remove Part from Order",
     * description="Deletes Bill and creates 'Restock' RecordPart (Type 1). Returns items to inventory.",
     * tags={"Bills"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Bill deleted and stock returned")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $bill = Bill::findOrFail($id);
            $part = $bill->part;

            
            RecordPart::create([
                'part_id' => $part->id,
                'modify_type_id' => 1, // Added/Returned
                'modified_amount' => $bill->part_amount,
                'modification_date' => now(),
            ]);

            $part->increment('amount', $bill->part_amount);

            $bill->delete();

            return response()->json(['message' => 'Part removed from order and returned to stock']);
        });
    }
}

