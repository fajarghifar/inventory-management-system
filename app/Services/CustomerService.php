<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CustomerService
{
    /**
     * Create a new customer record.
     *
     * @param array $data
     * @return Customer
     * @throws Exception
     */
    public function createCustomer(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            try {
                // Log the attempt
                Log::info('Attempting to create a new customer', ['email' => $data['email'] ?? 'unknown']);

                $customer = Customer::create([
                    'name'    => $data['name'],
                    'email'   => $data['email'],
                    'phone'   => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                    'notes'   => $data['notes'] ?? null,
                ]);

                Log::info('Customer created successfully', ['id' => $customer->id]);

                return $customer;

            } catch (Exception $e) {
                // Log the error
                Log::error('Failed to create customer: ' . $e->getMessage(), [
                    'data' => $data,
                    'trace' => $e->getTraceAsString()
                ]);

                // Re-throw to be handled by the controller or global handler
                throw new Exception('Failed to create customer. Please try again later.');
            }
        });
    }

    /**
     * Update an existing customer record.
     *
     * @param Customer $customer
     * @param array $data
     * @return Customer
     * @throws Exception
     */
    public function updateCustomer(Customer $customer, array $data): Customer
    {
        return DB::transaction(function () use ($customer, $data) {
            try {
                Log::info('Attempting to update customer', ['id' => $customer->id]);

                $customer->update([
                    'name'    => $data['name'] ?? $customer->name,
                    'email'   => $data['email'] ?? $customer->email,
                    'phone'   => $data['phone'] ?? $customer->phone,
                    'address' => $data['address'] ?? $customer->address,
                    'notes'   => $data['notes'] ?? $customer->notes,
                ]);

                Log::info('Customer updated successfully', ['id' => $customer->id]);

                return $customer->refresh();

            } catch (Exception $e) {
                Log::error('Failed to update customer: ' . $e->getMessage(), [
                    'id' => $customer->id,
                    'data' => $data
                ]);

                throw new Exception('Failed to update customer information.');
            }
        });
    }

    /**
     * Delete a customer record.
     *
     * @param Customer $customer
     * @return void
     * @throws Exception
     */
    public function deleteCustomer(Customer $customer): void
    {
        DB::transaction(function () use ($customer) {
            try {
                Log::info('Attempting to delete customer', ['id' => $customer->id]);

                $customer->delete();

                Log::info('Customer deleted successfully', ['id' => $customer->id]);

            } catch (Exception $e) {
                Log::error('Failed to delete customer: ' . $e->getMessage(), ['id' => $customer->id]);
                throw new Exception('Failed to delete customer.');
            }
        });
    }
}
