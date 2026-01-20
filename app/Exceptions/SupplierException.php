<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class SupplierException extends Exception
{
    public static function creationFailed(string $message, array $context = []): self
    {
        Log::error("Supplier creation failed: {$message}", $context);
        return new self("Failed to create supplier. {$message}");
    }

    public static function updateFailed(string $message, array $context = []): self
    {
        Log::error("Supplier update failed: {$message}", $context);
        return new self("Failed to update supplier. {$message}");
    }

    public static function deletionFailed(string $message, array $context = []): self
    {
        Log::error("Supplier deletion failed: {$message}", $context);
        return new self("Failed to delete supplier. {$message}");
    }
}
