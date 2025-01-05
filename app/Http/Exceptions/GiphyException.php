<?php

namespace App\Http\Exceptions;

use RuntimeException;

class GiphyException extends RuntimeException
{
    public function render($request)
    {
        return response()->json([
            'error' => 'Giphy API error',
            'message' => $this->getMessage(),
        ], 500);
    }
}
