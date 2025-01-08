<?php

namespace App\Http\Middleware;

use App\Models\RequestLog;

class RequestsLogMiddleware
{
    public function handle($request, $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $user = $request->user();

        if (! $user) {
            return;
        }

        RequestLog::create([
            'user_id' => $user->id,
            'method' => $request->method(),
            'path' => $this->ensureStartingSlash($request->path()),
            'request_body' => json_encode($request->all()),
            'response_code' => $response->getStatusCode(),
            'response_body' => $response->getContent(),
            'ip_address' => $request->ip(),
        ]);
    }

    private function ensureStartingSlash(string $path): string
    {
        return strpos($path, '/') === 0 ? $path : "/{$path}";
    }
}
