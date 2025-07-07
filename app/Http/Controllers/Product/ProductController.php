<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorHTML; // ✅ Make sure this class is installed via Composer

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
        $existingProduct = Product::where('code', $request->get('code'))->first();

        if ($existingProduct) {
            $newCode = $this->generateUniqueCode();
            $request->merge(['code' => $newCode]);
        }

        try {
            $product = Product::create($request->all());

            if ($request->hasFile('product_image')) {
                $file = $request->file('product_image');
                $filename = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

                if ($file->isValid()) {
                    $file->storeAs('products/', $filename, 'public');
                    $product->update(['product_image' => $filename]);
                } else {
                    return back()->withErrors(['product_image' => 'Invalid image file']);
                }
            }

            return redirect()->back()->with('success', 'Product has been created with code: ' . $product->code);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Something went wrong while creating the product']);
        }
    }

    private function generateUniqueCode()
    {
        do {
            $code = 'PC' . strtoupper(uniqid());
        } while (Product::where('code', $code)->exists());

        return $code;
    }

    public function show(Product $product)
    {
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
            if ($product->product_image) {
                \Storage::disk('public')->delete('products/' . $product->product_image);
            }

            $file = $request->file('product_image');
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('products/', $fileName, 'public');
            $product->update(['product_image' => $fileName]);
        }

        return redirect()->route('products.index')->with('success', 'Product has been updated!');
    }

    public function destroy(Product $product)
    {
        if ($product->product_image) {
            \Storage::disk('public')->delete('products/' . $product->product_image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product has been deleted!');
    }
}
