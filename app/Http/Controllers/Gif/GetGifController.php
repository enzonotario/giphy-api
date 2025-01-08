<?php

namespace App\Http\Controllers\Gif;

use App\Http\Exceptions\GiphyException;
use App\Services\GiphyService;
use Illuminate\Http\JsonResponse;

class GetGifController
{
    public function __construct(
        private readonly GiphyService $giphyService
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $gif = $this->giphyService->getGifById($id);

            if (empty($gif)) {
                return new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
            }

            return new JsonResponse($gif);
        } catch (GiphyException $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
