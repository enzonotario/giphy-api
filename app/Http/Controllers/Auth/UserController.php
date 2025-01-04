<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lcobucci\JWT\Parser;

class UserController
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($this->checkTokenExpired($request)) {
            return response()->json(['message' => 'Unauthenticated.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse(new UserResource($user));
    }

    private function checkTokenExpired(Request $request): bool
    {
        $requestToken = $request->bearerToken();

        if (! $requestToken) {
            return true;
        }

        $tokenId = app(Parser::class)->parse($requestToken)->claims()->get('jti');

        $token = $request->user()->tokens()->where('id', $tokenId)->first();

        if (! $token) {
            return true;
        }

        $isExpired = $token->expires_at->isPast();

        if ($isExpired) {
            $token->delete();
        }

        return $isExpired;
    }
}
