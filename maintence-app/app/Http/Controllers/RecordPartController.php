<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RecordPart\StoreRecordPartRequest;
use App\Http\Requests\RecordPart\UpdateRecordPartRequest;
use App\Http\Resources\RecordPartResource;
use App\Models\Part;
use App\Models\RecordPart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class RecordPartController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/record-parts",
     * summary="Get Records (Chunked 10)",
     * description="Returns records in chunks of 10. Default is Page 1 (Records 1-10). Send ?page=2 for 11-20.",
     * tags={"Inventory Records"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(
     * name="page",
     * in="query",
     * description="Chunk number (Default: 1)",
     * required=false,
     * @OA\Schema(type="integer", default=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="List of records",
     * @OA\JsonContent(type="object", @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/RecordPartResource")))
     * )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // 1. Determine Chunk (Default to Page 1 if missing)
        $page = (int) $request->input('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // 2. Fetch specific chunk
        $records = RecordPart::with(['part', 'modifyType'])
            ->latest('modification_date')
            ->skip($offset)
            ->take($limit)
            ->get();

        return RecordPartResource::collection($records);
    }

    /**
     * @OA\Get(path="/api/record-parts/{id}", tags={"Inventory Records"}, summary="Get Record Details", security={{"apiAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Details", @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/RecordPartResource"))))
     */
    public function show(string $id): JsonResponse
    {
        $record = RecordPart::with(['part', 'modifyType'])->findOrFail($id);
        return response()->json(['data' => new RecordPartResource($record)]);
    }

    /**
     * @OA\Post(
     * path="/api/record-parts",
     * summary="Add Stock Record",
     * description="Adds a record and updates Part stock. Type 1 increases stock; 2,3,4 decrease it.",
     * tags={"Inventory Records"},
     * security={{"apiAuth":{}}},
     * @OA\RequestBody(required=true, @OA\JsonContent(
     * required={"part_id", "modify_type_id", "modified_amount", "modification_date"},
     * @OA\Property(property="part_id", type="integer", example=1),
     * @OA\Property(property="modify_type_id", type="integer", example=1, description="1=Add, 2=Consumed, 3=Wasted, 4=Refunded"),
     * @OA\Property(property="modified_amount", type="integer", example=10),
     * @OA\Property(property="modification_date", type="string", format="date", example="2025-12-06")
     * )),
     * @OA\Response(response=201, description="Created")
     * )
     */
    public function store(StoreRecordPartRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $part = Part::findOrFail($request->part_id);
            
            if ($request->modify_type_id == 1) {
                $part->increment('amount', $request->modified_amount);
            } else {
                $part->decrement('amount', $request->modified_amount);
            }

            $record = RecordPart::create($request->validated());

            return response()->json(['message' => 'Record created', 'data' => new RecordPartResource($record)], 201);
        });
    }

    /**
     * @OA\Put(
     * path="/api/record-parts/{id}",
     * summary="Update Record & Re-calculate Stock",
     * description="Updates the record and automatically adjusts the part stock based on the difference.",
     * tags={"Inventory Records"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(required=true, @OA\JsonContent(@OA\Property(property="modified_amount", type="integer"))),
     * @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(UpdateRecordPartRequest $request, string $id): JsonResponse
    {
        return DB::transaction(function () use ($request, $id) {
            $record = RecordPart::findOrFail($id);
            $part = $record->part;

            // Revert old
            if ($record->modify_type_id == 1) {
                $part->decrement('amount', $record->modified_amount);
            } else {
                $part->increment('amount', $record->modified_amount);
            }

            // Update
            $record->update($request->validated());

            // Apply new
            if ($record->modify_type_id == 1) {
                $part->increment('amount', $record->modified_amount);
            } else {
                if ($part->amount < $record->modified_amount) {
                     throw \Illuminate\Validation\ValidationException::withMessages(['amount' => 'Insufficient stock for update']);
                }
                $part->decrement('amount', $record->modified_amount);
            }

            return response()->json(['message' => 'Record updated', 'data' => new RecordPartResource($record)]);
        });
    }
}