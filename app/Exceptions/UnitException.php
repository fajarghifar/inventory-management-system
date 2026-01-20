<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class UnitException extends Exception
{
    public static function creationFailed(string $message, array $context = []): self
    {
        Log::error("Unit creation failed: {$message}", $context);
        return new self("Failed to create unit. {$message}");
    }

    public static function updateFailed(string $message, array $context = []): self
    {
        Log::error("Unit update failed: {$message}", $context);
        return new self("Failed to update unit. {$message}");
    }

    public static function deletionFailed(string $message, array $context = []): self
    {
        Log::error("Unit deletion failed: {$message}", $context);
        return new self("Failed to delete unit. {$message}");
    }
}
