<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index(Request $request)
    {
        return view('pos.index', [
            'products' => Product::with(['category', 'unit'])->get(),
            'customers' => Customer::all()->sortBy('name'),
            'carts' => Cart::content(),
        ]);
    }

    public function addCartItem(Request $request)
    {
        $request->all();

        $rules = [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'selling_price' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        //        Cart::add([
        //            'id'        => $validatedData['id'],
        //            'name'      => $validatedData['name'],
        //            'qty'       => 1,
        //            'price'     => $validatedData['selling_price'],
        //            'weight'    => 1,
        //            //'options' => []
        //        ]);

        Cart::add($validatedData['id'],
            $validatedData['name'],
            1,
            $validatedData['selling_price'],
            1,
            (array) $options = null);

        return redirect()
            ->back()
            ->with('success', 'Product has been added to cart!');
    }

    public function updateCartItem(Request $request, $rowId)
    {
        $rules = [
            'quantity' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::update($rowId, $validatedData['quantity']);

        return redirect()
            ->back()
            ->with('success', 'Product has been updated from cart!');
    }

    public function deleteCartItem(string $rowId)
    {
        Cart::remove($rowId);

        return redirect()
            ->back()
            ->with('success', 'Product has been deleted from cart!');
    }
}
