<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use Illuminate\View\View;

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
}
