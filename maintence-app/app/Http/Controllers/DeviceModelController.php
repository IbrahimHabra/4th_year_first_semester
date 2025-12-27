<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Model\StoreModelRequest;
use App\Http\Requests\Model\UpdateModelRequest;
use App\Http\Resources\DeviceModelResource;
use App\Models\DeviceModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeviceModelController extends Controller
{
    /**
     * @OA\Get(path="/api/models", tags={"Models"}, summary="Get All Models", security={{"apiAuth":{}}},
     * @OA\Response(response=200, description="List", @OA\JsonContent(type="object", @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/DeviceModelResource")))))
     */
    public function index(): AnonymousResourceCollection
    {
        // Eager load company to prevent N+1 query problem
        $models = DeviceModel::with('company')->get();
        return DeviceModelResource::collection($models);
    }

    /**
     * @OA\Get(path="/api/models/{id}", tags={"Models"}, summary="Get Model Details", security={{"apiAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Details", @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/DeviceModelResource"))))
     */
    public function show(string $id): JsonResponse
    {
        $model = DeviceModel::with('company')->findOrFail($id);
        return response()->json(['data' => new DeviceModelResource($model)]);
    }

    /**
     * @OA\Post(path="/api/models", tags={"Models"}, summary="Create Model", security={{"apiAuth":{}}},
     * @OA\RequestBody(required=true, @OA\JsonContent(required={"name", "company_id"}, @OA\Property(property="name", type="string"), @OA\Property(property="company_id", type="integer"))),
     * @OA\Response(response=201, description="Created"))
     */
    public function store(StoreModelRequest $request): JsonResponse
    {
        $model = DeviceModel::create($request->validated());
        return response()->json(['message' => 'Created', 'data' => new DeviceModelResource($model->load('company'))], 201);
    }

    /**
     * @OA\Put(path="/api/models/{id}", tags={"Models"}, summary="Update Model", security={{"apiAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(required=true, @OA\JsonContent(@OA\Property(property="name", type="string"), @OA\Property(property="company_id", type="integer"))),
     * @OA\Response(response=200, description="Updated"))
     */
    public function update(UpdateModelRequest $request, string $id): JsonResponse
    {
        $model = DeviceModel::findOrFail($id);
        $model->update($request->validated());
        return response()->json(['message' => 'Updated', 'data' => new DeviceModelResource($model->load('company'))]);
    }
}