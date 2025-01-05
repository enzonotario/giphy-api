<?php

namespace App\Http\Controllers\Gif;

use App\Http\Requests\SearchGifsRequest;
use App\Services\GiphyService;
use Illuminate\Http\JsonResponse;

class SearchGifsController
{
    public function __construct(
        private readonly GiphyService $giphyService
    ) {}

    public function __invoke(SearchGifsRequest $request): JsonResponse
    {
        return new JsonResponse(
            $this->giphyService->search(
                $request->input('limit', 25),
                $request->input('offset', 1),
                $request->input('query')
            )
        );
    }
}
