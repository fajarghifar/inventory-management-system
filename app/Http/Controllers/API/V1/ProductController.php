<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController
{
    public function index(Request $request){

        $products = Product::all();

        if ($request->has('category_id'))
        {
            $products = Product::query()
                ->where('category_id', $request->get('category_id'))
                ->get();
        }

        return response()->json($products);
    }
}
