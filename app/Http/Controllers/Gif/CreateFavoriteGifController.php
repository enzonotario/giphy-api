<?php

namespace App\Http\Controllers\Gif;

use App\Http\Exceptions\GiphyException;
use App\Http\Requests\CreateFavoriteGifRequest;
use App\Models\Favorite;
use Illuminate\Http\JsonResponse;

class CreateFavoriteGifController
{
    public function __invoke(CreateFavoriteGifRequest $request): JsonResponse
    {
        try {
            $favorite = Favorite::create([
                'user_id' => $request->user()->id,
                'gif_id' => $request->input('gif_id'),
            ]);

            return new JsonResponse($favorite, JsonResponse::HTTP_CREATED);
        } catch (GiphyException $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
