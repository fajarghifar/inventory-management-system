<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ProductException extends Exception
{
    public static function creationFailed(string $message, array $context = []): self
    {
        Log::error("Product creation failed: {$message}", $context);
        return new self("Failed to create product. {$message}");
    }

    public static function updateFailed(string $message, array $context = []): self
    {
        Log::error("Product update failed: {$message}", $context);
        return new self("Failed to update product. {$message}");
    }

    public static function deletionFailed(string $message, array $context = []): self
    {
        Log::error("Product deletion failed: {$message}", $context);
        return new self("Failed to delete product. {$message}");
    }
}
