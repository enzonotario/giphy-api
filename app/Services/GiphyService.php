<?php

namespace App\Services;

use App\Http\Exceptions\GiphyException;
use Illuminate\Support\Facades\Http;

class GiphyService
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl
    ) {}

    public function search(
        int $limit = 25,
        int $offset = 0,
        string $query = 'g',
    ): array {
        $url = $this->baseUrl.'gifs/search';

        $response = Http::get($url, [
            'api_key' => $this->apiKey,
            'limit' => $limit,
            'offset' => $offset,
            'q' => $query,
        ]);

        if ($response->failed()) {
            throw new GiphyException(
                sprintf('Failed to search for GIFs. Response: %d %s', $response->status(), $response->body())
            );
        }

        return $response->json();
    }
}
