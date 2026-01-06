<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Customer;
use Illuminate\View\View;
use App\Services\CustomerService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;

class CustomerController extends Controller
{
    /**
     * The customer service instance.
     *
     * @var CustomerService
     */
    protected CustomerService $customerService;

    /**
     * Create a new controller instance.
     *
     * @param CustomerService $customerService
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('customers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        try {
            $this->customerService->createCustomer($request->validated());

            return redirect()
                ->route('customers.index')
                ->with('success', 'Customer created successfully.');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): View
    {
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        try {
            $this->customerService->updateCustomer($customer, $request->validated());

            return redirect()
                ->route('customers.index')
                ->with('success', 'Customer updated successfully.');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        try {
            $this->customerService->deleteCustomer($customer);

            return redirect()
                ->route('customers.index')
                ->with('success', 'Customer deleted successfully.');
        } catch (Exception $e) {
            return redirect()
                ->route('customers.index')
                ->with('error', $e->getMessage());
        }
    }
}
