<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorHTML;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::select('id', 'name')
            ->limit(1)
            ->get();

        return view('products.index', [
            'products' => $products,
        ]);
    }

    public function create(Request $request)
    {
        $categories = Category::all(['id', 'name']);
        $units = Unit::all(['id', 'name']);

        if ($request->has('category')) {
            $categories = Category::whereSlug($request->get('category'))->get();
        }

        if ($request->has('unit')) {
            $units = Unit::whereSlug($request->get('unit'))->get();
        }

        return view('products.create', [
            'categories' => $categories,
            'units' => $units,
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->all());

        /**
         * Handle upload image
         */
        if ($request->hasFile('product_image')) {
            $file = $request->file('product_image');
            $filename = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

            $file->storeAs('products/', $filename, 'public');
            $product->update([
                'product_image' => $filename
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Product has been created!');
    }

    public function show(Product $product)
    {
        // Generate a barcode
        $generator = new BarcodeGeneratorHTML();

        $barcode = $generator->getBarcode($product->code, $generator::TYPE_CODE_128);

        return view('products.show', [
            'product' => $product,
            'barcode' => $barcode,
        ]);
    }

    public function edit(Product $product)
    {
        return view('products.edit', [
            'categories' => Category::all(),
            'units' => Unit::all(),
            'product' => $product
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->except('product_image'));

        if ($request->hasFile('product_image')) {

            // Delete Old Photo
            if ($product->product_image) {
                unlink(public_path('storage/products/') . $product->product_image);
            }

            // Prepare New Photo
            $file = $request->file('product_image');
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

            // Store an image to Storage
            $file->storeAs('products/', $fileName, 'public');

            // Save DB
            $product->update([
                'product_image' => $fileName
            ]);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Product has been updated!');
    }

    public function destroy(Product $product)
    {
        /**
         * Delete photo if exists.
         */
        if ($product->product_image) {
            unlink(public_path('storage/products/') . $product->product_image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product has been deleted!');
    }
}
