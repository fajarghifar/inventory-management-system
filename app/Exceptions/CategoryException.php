<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class CategoryException extends Exception
{
    public static function creationFailed(string $message, array $context = []): self
    {
        Log::error("Category creation failed: {$message}", $context);
        return new self("Failed to create category. {$message}");
    }

    public static function updateFailed(string $message, array $context = []): self
    {
        Log::error("Category update failed: {$message}", $context);
        return new self("Failed to update category. {$message}");
    }

    public static function deletionFailed(string $message, array $context = []): self
    {
        Log::error("Category deletion failed: {$message}", $context);
        return new self("Failed to delete category. {$message}");
    }
}
