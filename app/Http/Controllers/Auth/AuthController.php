<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        //user not found or password not correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->notFoundResponse(
                __('messages.user.incorrect_email_or_password'),
                Response::HTTP_BAD_REQUEST
            );
        }

        // user found and password match
        return $this->response(
            __('messages.user.login_success'),
            Response::HTTP_OK,
            [
                'token' => $user->createToken($request->password)->plainTextToken,
            ],
        );
    }
}
