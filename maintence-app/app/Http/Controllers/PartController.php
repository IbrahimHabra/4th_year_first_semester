<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Part\StorePartRequest;
use App\Http\Requests\Part\UpdatePartRequest;
use App\Http\Resources\PartResource;
use App\Models\Part;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PartController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/parts",
     * summary="Get All Parts (Chunked)",
     * description="Retrieve parts in chunks of 10. Use 'page' parameter to navigate.",
     * tags={"Parts"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(
     * name="page",
     * in="query",
     * description="Chunk number (default 1)",
     * required=false,
     * @OA\Schema(type="integer", default=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="List of parts",
     * @OA\JsonContent(type="object", @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PartResource")))
     * )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $page = (int) $request->input('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $parts = Part::latest('id')
            ->skip($offset)
            ->take($limit)
            ->get();

        return PartResource::collection($parts);
    }

    /**
     * @OA\Post(
     * path="/api/parts",
     * summary="Create New Part",
     * tags={"Parts"},
     * security={{"apiAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "price_default", "amount", "warning_amount"},
     * @OA\Property(property="name", type="string", example="Compressor Valve"),
     * @OA\Property(property="price_default", type="number", format="float", example=150.50),
     * @OA\Property(property="amount", type="integer", example=50),
     * @OA\Property(property="warning_amount", type="integer", example=10)
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Part created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Created"),
     * @OA\Property(property="data", ref="#/components/schemas/PartResource")
     * )
     * )
     * )
     */
    public function store(StorePartRequest $request): JsonResponse
    {
        $part = Part::create($request->validated());
        return response()->json(['message' => 'Created', 'data' => new PartResource($part)], 201);
    }

    /**
     * @OA\Put(
     * path="/api/parts/{id}",
     * summary="Update Part",
     * tags={"Parts"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string", example="Compressor Valve Updated"),
     * @OA\Property(property="price_default", type="number", format="float", example=160.00),
     * @OA\Property(property="amount", type="integer", example=45),
     * @OA\Property(property="warning_amount", type="integer", example=5)
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Part updated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Updated"),
     * @OA\Property(property="data", ref="#/components/schemas/PartResource")
     * )
     * )
     * )
     */
    public function update(UpdatePartRequest $request, Part $part): JsonResponse
    {
        $part->update($request->validated());
        return response()->json(['message' => 'Updated', 'data' => new PartResource($part)]);
    }

    /**
     * @OA\Delete(
     * path="/api/parts/{id}",
     * summary="Delete Part",
     * tags={"Parts"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Part deleted successfully",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Part deleted successfully")
     * )
     * )
     * )
     */
    public function destroy(Part $part): JsonResponse
    {
        $part->delete();
        return response()->json(['message' => 'Part deleted successfully']);
    }
}