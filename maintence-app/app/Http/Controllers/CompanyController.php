<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    /**
     * @OA\Get(path="/api/companies", tags={"Companies"}, summary="Get All Companies", security={{"apiAuth":{}}},
     * @OA\Response(response=200, description="List", @OA\JsonContent(type="object", @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CompanyResource")))))
     */
    public function index(): AnonymousResourceCollection
    {
        return CompanyResource::collection(Company::all());
    }

    /**
     * @OA\Post(path="/api/companies", tags={"Companies"}, summary="Create Company", security={{"apiAuth":{}}},
     * @OA\RequestBody(required=true, @OA\JsonContent(required={"name"}, @OA\Property(property="name", type="string"))),
     * @OA\Response(response=201, description="Created"))
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $company = Company::create($request->validated());
        return response()->json(['message' => 'Created', 'data' => new CompanyResource($company)], 201);
    }

    /**
     * @OA\Put(path="/api/companies/{id}", tags={"Companies"}, summary="Update Company", security={{"apiAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(required=true, @OA\JsonContent(required={"name"}, @OA\Property(property="name", type="string"))),
     * @OA\Response(response=200, description="Updated"))
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $company->update($request->validated());
        return response()->json(['message' => 'Updated', 'data' => new CompanyResource($company)]);
    }
}