<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\TokenResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController
{
    const TOKEN_NAME = 'Temporal Token';

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $token = $user->createToken(self::TOKEN_NAME);

        return new JsonResponse(new TokenResource($token));
    }
}
