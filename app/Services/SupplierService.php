<?php

namespace App\Services;

use Exception;
use App\Models\Supplier;
use App\DTOs\SupplierData;
use Illuminate\Support\Facades\DB;
use App\Exceptions\SupplierException;
use Illuminate\Support\Facades\Cache;

class SupplierService
{
    /**
     * Create a new supplier record.
     */
    public function createSupplier(SupplierData $data): Supplier
    {
        return DB::transaction(function () use ($data) {
            try {
                $supplier = Supplier::create([
                    'name' => $data->name,
                    'contact_person' => $data->contact_person,
                    'email' => $data->email,
                    'phone' => $data->phone,
                    'address' => $data->address,
                    'notes' => $data->notes,
                ]);

                Cache::forget('suppliers_list_all');

                return $supplier;

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

                Cache::forget('suppliers_list_all');

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

                Cache::forget('suppliers_list_all');

            } catch (Exception $e) {
                throw SupplierException::deletionFailed($e->getMessage(), ['id' => $supplier->id]);
            }
        });
    }
}
