<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use RuntimeException;

class OutOfStockException extends RuntimeException
{
    public function __construct(string $title, int $available)
    {
        parent::__construct($available > 0
            ? "Only {$available} left in stock for \"{$title}\""
            : "\"{$title}\" is out of stock");
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], 409);
    }
}
