<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MemberController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/members",
     * summary="Get All Members",
     * description="Retrieve a list of all maintenance staff members.",
     * tags={"Members"},
     * security={{"apiAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="List of members",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(
     * property="data",
     * type="array",
     * @OA\Items(ref="#/components/schemas/MemberResource")
     * )
     * )
     * )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $members = Member::all();
        return MemberResource::collection($members);
    }

    /**
     * @OA\Post(
     * path="/api/members",
     * summary="Add New Member",
     * description="Create a new technician. Start date defaults to today if not provided.",
     * tags={"Members"},
     * security={{"apiAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name"},
     * @OA\Property(property="name", type="string", example="Ibrahim Habra"),
     * @OA\Property(property="birth_date", type="string", format="date", example="1998-05-20"),
     * @OA\Property(property="start_date", type="string", format="date", example="2025-01-01", description="Defaults to today if omitted")
     * )
     * ),
     * @OA\Response(response=201, description="Member created successfully")
     * )
     */
    public function store(StoreMemberRequest $request): JsonResponse
    {
        $member = Member::create([
            'name' => $request->name,
            'birth_date' => $request->birth_date,
            // Logic: Use provided date OR default to today (now)
            'start_date' => $request->start_date ?? now(), 
        ]);

        return response()->json([
            'message' => 'Member created successfully',
            'data' => new MemberResource($member),
        ], 201);
    }

    /**
     * @OA\Put(
     * path="/api/members/{id}",
     * summary="Update Member",
     * description="Update member details including exit date.",
     * tags={"Members"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string", example="Ibrahim Habra Updated"),
     * @OA\Property(property="birth_date", type="string", format="date", example="1998-05-20"),
     * @OA\Property(property="start_date", type="string", format="date", example="2025-01-01"),
     * @OA\Property(property="exit_date", type="string", format="date", example="2026-01-01")
     * )
     * ),
     * @OA\Response(response=200, description="Member updated successfully")
     * )
     */
    public function update(UpdateMemberRequest $request, string $id): JsonResponse
    {
        $member = Member::findOrFail($id);
        
        $member->update($request->validated());

        return response()->json([
            'message' => 'Member updated successfully',
            'data' => new MemberResource($member),
        ]);
    }
}   