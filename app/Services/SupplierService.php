<?php

namespace App\Services;

use Exception;
use App\DTOs\SupplierData;
use App\Models\Supplier;
use App\Exceptions\SupplierException;
use Illuminate\Support\Facades\DB;

class SupplierService
{
    /**
     * Create a new supplier record.
     */
    public function createSupplier(SupplierData $data): Supplier
    {
        return DB::transaction(function () use ($data) {
            try {
                return Supplier::create([
                    'name' => $data->name,
                    'contact_person' => $data->contact_person,
                    'email' => $data->email,
                    'phone' => $data->phone,
                    'address' => $data->address,
                    'notes' => $data->notes,
                ]);

            } catch (Exception $e) {
                throw SupplierException::creationFailed($e->getMessage(), [
                    'data' => (array) $data,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });
    }

    /**
     * Update an existing supplier record.
     */
    public function updateSupplier(Supplier $supplier, SupplierData $data): Supplier
    {
        return DB::transaction(function () use ($supplier, $data) {
            try {
                $supplier->update([
                    'name' => $data->name,
                    'contact_person' => $data->contact_person,
                    'email' => $data->email,
                    'phone' => $data->phone,
                    'address' => $data->address,
                    'notes' => $data->notes,
                ]);

                return $supplier->refresh();

            } catch (Exception $e) {
                throw SupplierException::updateFailed($e->getMessage(), [
                    'id'   => $supplier->id,
                    'data' => (array) $data
                ]);
            }
        });
    }

    /**
     * Delete a supplier record.
     */
    public function deleteSupplier(Supplier $supplier): void
    {
        DB::transaction(function () use ($supplier) {
            try {
                if ($supplier->purchases()->exists()) {
                    throw new Exception('Cannot delete supplier because there are purchases associated with this supplier.');
                }

                $supplier->delete();

            } catch (Exception $e) {
                throw SupplierException::deletionFailed($e->getMessage(), ['id' => $supplier->id]);
            }
        });
    }
}
