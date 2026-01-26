<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class PurchaseException extends Exception
{
    public static function creationFailed(string $message, array $context = []): self
    {
        Log::error("Purchase creation failed: {$message}", $context);
        return new self("Failed to create purchase: {$message}");
    }

    public static function updateFailed(string $message, array $context = []): self
    {
        Log::error("Purchase update failed: {$message}", $context);
        return new self("Failed to update purchase: {$message}");
    }

    public static function deletionFailed(string $message, array $context = []): self
    {
        Log::error("Purchase deletion failed: {$message}", $context);
        return new self("Failed to delete purchase. {$message}");
    }

    public static function invalidStatus(string $action, string $status, array $context = []): self
    {
        $message = "Cannot {$action} purchase with status '{$status}'.";
        Log::warning($message, $context);
        return new self($message);
    }

    public static function missingReference(string $reference, array $context = []): self
    {
        $message = "Missing required reference: {$reference}.";
        Log::warning($message, $context);
        return new self($message);
    }
}
