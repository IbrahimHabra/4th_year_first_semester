<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/forgot-password",
     * summary="Send Reset Link",
     * description="Generates a reset token and logs it (or sends email). Check storage/logs/laravel.log for the link.",
     * tags={"Auth"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(required={"email"}, @OA\Property(property="email", type="string", format="email", example="admin@kawarem.com"))
     * ),
     * @OA\Response(response=200, description="Reset link sent")
     * )
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink($request->validated());

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 400);
    }

    /**
     * @OA\Post(
     * path="/api/reset-password",
     * summary="Reset Password",
     * description="Resets the user password using the token. NOTE: Stores password AS IS (Client-side hashing assumed).",
     * tags={"Auth"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"token", "email", "password"},
     * @OA\Property(property="token", type="string", example="69e2c6c7..."),
     * @OA\Property(property="email", type="string", format="email", example="admin@kawarem.com"),
     * @OA\Property(property="password", type="string", description="The ALREADY HASHED password from client")
     * )
     * ),
     * @OA\Response(response=200, description="Password has been reset"),
     * @OA\Response(response=400, description="Invalid token or email")
     * )
     */
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->validated(),
            function (User $user, string $password) {

                $user->forceFill([
                    'password' => $password
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 400);
    }

    /**
     * @OA\Post(
     * path="/api/change-password",
     * summary="Change Password (Logged In)",
     * description="Updates the user password. Requires the current password for verification.",
     * tags={"Auth"},
     * security={{"apiAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"current_password", "new_password"},
     * @OA\Property(property="current_password", type="string", example="old_hashed_password_from_client"),
     * @OA\Property(property="new_password", type="string", example="new_hashed_password_from_client")
     * )
     * ),
     * @OA\Response(response=200, description="Password updated successfully"),
     * @OA\Response(response=401, description="Incorrect current password")
     * )
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = auth()->user();

        $matchesDirectly = $user->password === $request->current_password;
        $matchesHash = Hash::check($request->current_password, $user->password);

        if (! $matchesDirectly && ! $matchesHash) {
             throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }

        $user->forceFill([
            'password' => $request->new_password
        ])->setRememberToken(Str::random(60));

        $user->save();

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }
}

