<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use App\DTOs\CustomerData;
use Illuminate\Http\Request;
use App\Services\CustomerService;
use App\Http\Controllers\Controller;
use App\Exceptions\CustomerException;
use Illuminate\Support\Facades\Cache;

class CustomerController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q') ?? $request->input('search');
        $cacheKey = 'customers_search_' . md5($query);

        $customers = Cache::remember($cacheKey, 300, function () use ($query) {
            return Customer::query()
                ->when($query, function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%");
                })
                ->limit(20)
                ->get()
                ->map(function ($customer) {
                    return [
                        'value' => $customer->id,
                        'text' => $customer->name . ($customer->phone ? ' | ' . $customer->phone : ''),
                        'name' => $customer->name,
                        'phone' => $customer->phone,
                    ];
                });
        });

        return response()->json($customers);
    }

    public function store(Request $request, CustomerService $customerService)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:500',
                'notes' => 'nullable|string|max:500',
            ]);

            $data = array_merge([
                'phone' => null,
                'email' => null,
                'address' => null,
                'notes' => null,
            ], $validated);

            $customerData = CustomerData::fromArray($data);
            $customer = $customerService->createCustomer($customerData);

            return response()->json($customer, 201);

        } catch (CustomerException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create customer: ' . $e->getMessage()], 500);
        }
    }
}
