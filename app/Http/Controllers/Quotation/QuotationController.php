<?php

namespace App\Http\Controllers\Quotation;

use App\Enums\QuotationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Quotation\StoreQuotationRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationDetails;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::with(['customer'])->get();

        return view('quotations.index', [
            'quotations' => $quotations,
        ]);
    }

    public function create()
    {
        Cart::clear(); // clear cart for current session

        return view('quotations.create', [
            'cart' => Cart::getContent(), // fetch all items
            'products' => Product::all(),
            'customers' => Customer::all(),
            // 'statuses' => QuotationStatus::cases()
        ]);
    }

    public function store(StoreQuotationRequest $request)
    {
        DB::transaction(function () use ($request) {
            $cartItems = Cart::getContent();

            $quotation = Quotation::create([
                'date' => $request->date,
                'reference' => $request->reference,
                'customer_id' => $request->customer_id,
                'customer_name' => Customer::findOrFail($request->customer_id)->name,
                'tax_percentage' => $request->tax_percentage,
                'discount_percentage' => $request->discount_percentage,
                'shipping_amount' => $request->shipping_amount,
                'total_amount' => $request->total_amount,
                'status' => $request->status,
                'note' => $request->note,
                'tax_amount' => 0, // You can calculate if needed
                'discount_amount' => 0, // You can calculate if needed
            ]);

            foreach ($cartItems as $item) {
                QuotationDetails::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item->id,
                    'product_name' => $item->name,
                    'product_code' => $item->attributes->code ?? '',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'unit_price' => $item->attributes->unit_price ?? $item->price,
                    'sub_total' => $item->getPriceSum(),
                    'product_discount_amount' => $item->attributes->product_discount ?? 0,
                    'product_discount_type' => $item->attributes->product_discount_type ?? null,
                    'product_tax_amount' => $item->attributes->product_tax ?? 0,
                ]);
            }

            Cart::clear();
        });

        return redirect()
            ->route('quotations.index')
            ->with('success', 'Quotation Created!');
    }

    public function show()
    {
        //
    }

    public function edit()
    {
        //
    }

    public function update()
    {
        //
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();

        return redirect()
            ->route('quotations.index')
            ->with('success', 'Quotation Deleted!');
    }
}
