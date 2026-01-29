<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class SaleException extends Exception
{
    public static function creationFailed(string $message, array $context = []): self
    {
        Log::error("Sale creation failed: {$message}", $context);
        return new self("Failed to create sale: {$message}");
    }

    public static function updateFailed(string $message, array $context = []): self
    {
        Log::error("Sale update failed: {$message}", $context);
        return new self("Failed to update sale: {$message}");
    }

    public static function cancellationFailed(string $message, array $context = []): self
    {
        Log::error("Sale cancellation failed: {$message}", $context);
        return new self("Failed to cancel sale: {$message}");
    }

    public static function invalidStatus(string $action, string $status, array $context = []): self
    {
        $message = "Cannot perform {$action} on sale with status '{$status}'.";
        Log::warning($message, $context);
        return new self($message);
    }

    public static function missingReference(string $reference, array $context = []): self
    {
        $message = "Missing required reference: {$reference}.";
        Log::warning($message, $context);
        return new self($message);
    }

    public static function insufficientStock(string $productName, int $requested, int $available): self
    {
        $message = "Insufficient stock for product '{$productName}'. Requested: {$requested}, Available: {$available}.";
        Log::warning($message);
        return new self($message);
    }
}
