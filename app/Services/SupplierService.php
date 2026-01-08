<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SupplierService
{
    /**
     * Create a new supplier record.
     *
     * @param array $data
     * @return Supplier
     * @throws Exception
     */
    public function createSupplier(array $data): Supplier
    {
        return DB::transaction(function () use ($data) {
            try {
                Log::info('Attempting to create a new supplier', ['email' => $data['email'] ?? 'unknown']);

                $supplier = Supplier::create([
                    'name'           => $data['name'],
                    'contact_person' => $data['contact_person'],
                    'email'          => $data['email'],
                    'phone'          => $data['phone'] ?? null,
                    'address'        => $data['address'] ?? null,
                    'notes'          => $data['notes'] ?? null,
                ]);

                Log::info('Supplier created successfully', ['id' => $supplier->id]);

                return $supplier;

            } catch (Exception $e) {
                Log::error('Failed to create supplier: ' . $e->getMessage(), [
                    'data'  => $data,
                    'trace' => $e->getTraceAsString()
                ]);

                throw new Exception('Failed to create supplier. Please try again later.');
            }
        });
    }

    /**
     * Update an existing supplier record.
     *
     * @param Supplier $supplier
     * @param array $data
     * @return Supplier
     * @throws Exception
     */
    public function updateSupplier(Supplier $supplier, array $data): Supplier
    {
        return DB::transaction(function () use ($supplier, $data) {
            try {
                Log::info('Attempting to update supplier', ['id' => $supplier->id]);

                $supplier->update([
                    'name'           => $data['name'] ?? $supplier->name,
                    'contact_person' => $data['contact_person'] ?? $supplier->contact_person,
                    'email'          => $data['email'] ?? $supplier->email,
                    'phone'          => $data['phone'] ?? $supplier->phone,
                    'address'        => $data['address'] ?? $supplier->address,
                    'notes'          => $data['notes'] ?? $supplier->notes,
                ]);

                Log::info('Supplier updated successfully', ['id' => $supplier->id]);

                return $supplier->refresh();

            } catch (Exception $e) {
                Log::error('Failed to update supplier: ' . $e->getMessage(), [
                    'id'   => $supplier->id,
                    'data' => $data
                ]);

                throw new Exception('Failed to update supplier information.');
            }
        });
    }

    /**
     * Delete a supplier record.
     *
     * @param Supplier $supplier
     * @return void
     * @throws Exception
     */
    public function deleteSupplier(Supplier $supplier): void
    {
        DB::transaction(function () use ($supplier) {
            try {
                Log::info('Attempting to delete supplier', ['id' => $supplier->id]);

                if ($supplier->purchases()->exists()) {
                    throw new Exception('Cannot delete supplier because there are purchases associated with this supplier.');
                }

                $supplier->delete();

                Log::info('Supplier deleted successfully', ['id' => $supplier->id]);

            } catch (Exception $e) {
                Log::error('Failed to delete supplier: ' . $e->getMessage(), ['id' => $supplier->id]);
                throw new Exception('Failed to delete supplier.');
            }
        });
    }
}
