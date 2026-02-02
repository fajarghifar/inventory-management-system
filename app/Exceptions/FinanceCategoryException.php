<?php

namespace App\Exceptions;

use Exception;

use Illuminate\Support\Facades\Log;

class FinanceCategoryException extends Exception
{
    public static function creationFailed(string $message, array $context = []): self
    {
        Log::error("Finance Category creation failed: {$message}", $context);
        return new self("Failed to create finance category. {$message}");
    }

    public static function updateFailed(string $message, array $context = []): self
    {
        Log::error("Finance Category update failed: {$message}", $context);
        return new self("Failed to update finance category. {$message}");
    }

    public static function deletionFailed(string $message, array $context = []): self
    {
        Log::error("Finance Category deletion failed: {$message}", $context);
        return new self("Failed to delete finance category. {$message}");
    }
}
