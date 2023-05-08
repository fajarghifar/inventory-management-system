<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Picqer\Barcode\BarcodeGeneratorHTML;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $products = Product::with(['category', 'unit'])
                ->filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query());

        return view('products.index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create', [
            'categories' => Category::all(),
            'units' => Unit::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $product_code = IdGenerator::generate([
            'table' => 'products',
            'field' => 'product_code',
            'length' => 4,
            'prefix' => 'PC'
        ]);

        $rules = [
            'product_image' => 'image|file|max:2048',
            'product_name' => 'required|string',
            'category_id' => 'required|integer',
            'unit_id' => 'required|integer',
            'stock' => 'required|integer',
            'buying_price' => 'required|integer',
            'selling_price' => 'required|integer',
        ];

        $validatedData = $request->validate($rules);

        // Save product code value
        $validatedData['product_code'] = $product_code;

        /**
         * Handle upload image
         */
        if ($file = $request->file('product_image')) {
            $fileName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
            $path = 'public/products/';

            /**
             * Upload an image to Storage
             */
            $file->storeAs($path, $fileName);
            $validatedData['product_image'] = $fileName;
        }

        Product::create($validatedData);

        return Redirect::route('products.index')->with('success', 'Product has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Generate a barcode
        $generator = new BarcodeGeneratorHTML();

        $barcode = $generator->getBarcode($product->product_code, $generator::TYPE_CODE_128);

        return view('products.show', [
            'product' => $product,
            'barcode' => $barcode,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', [
            'categories' => Category::all(),
            'units' => Unit::all(),
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $rules = [
            'product_image' => 'image|file|max:2048',
            'product_name' => 'required|string',
            'category_id' => 'required|integer',
            'unit_id' => 'required|integer',
            'stock' => 'required|integer',
            'buying_price' => 'required|integer',
            'selling_price' => 'required|integer',
        ];

        $validatedData = $request->validate($rules);

        /**
         * Handle upload an image
         */
        if ($file = $request->file('product_image')) {
            $fileName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
            $path = 'public/products/';

            /**
             * Delete photo if exists.
             */
            if($product->product_image){
                Storage::delete($path . $product->product_image);
            }

            /**
             * Store an image to Storage
             */
            $file->storeAs($path, $fileName);
            $validatedData['product_image'] = $fileName;
        }

        Product::where('id', $product->id)->update($validatedData);

        return Redirect::route('products.index')->with('success', 'Product has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        /**
         * Delete photo if exists.
         */
        if($product->product_image){
            Storage::delete('public/products/' . $product->product_image);
        }

        Product::destroy($product->id);

        return Redirect::route('products.index')->with('success', 'Product has been deleted!');
    }

    /**
     * Handle export data products.
     */
    public function import()
    {
        return view('products.import');
    }

    public function handleImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $the_file = $request->file('file');

        try{
            $spreadsheet = IOFactory::load($the_file->getRealPath());
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range( 2, $row_limit );
            $column_range = range( 'J', $column_limit );
            $startcount = 2;
            $data = array();
            foreach ( $row_range as $row ) {
                $data[] = [
                    'product_name' => $sheet->getCell( 'A' . $row )->getValue(),
                    'category_id' => $sheet->getCell( 'B' . $row )->getValue(),
                    'unit_id' => $sheet->getCell( 'C' . $row )->getValue(),
                    'product_code' => $sheet->getCell( 'D' . $row )->getValue(),
                    'stock' => $sheet->getCell( 'E' . $row )->getValue(),
                    'buying_price' => $sheet->getCell( 'F' . $row )->getValue(),
                    'selling_price' =>$sheet->getCell( 'G' . $row )->getValue(),
                    'product_image' =>$sheet->getCell( 'H' . $row )->getValue(),
                ];
                $startcount++;
            }

            Product::insert($data);

        } catch (Exception $e) {
            // $error_code = $e->errorInfo[1];
            return Redirect::route('products.index')->with('error', 'There was a problem uploading the data!');
        }
        return Redirect::route('products.index')->with('success', 'Data product has been imported!');
    }

    /**
     * Handle export data products.
     */
    function export(){
        $products = Product::all()->sortBy('product_name');

        $product_array [] = array(
            'Product Name',
            'Category Id',
            'Unit Id',
            'Product Code',
            'Stock',
            'Buying Price',
            'Selling Price',
            'Product Image',
        );

        foreach($products as $product)
        {
            $product_array[] = array(
                'Product Name' => $product->product_name,
                'Category Id' => $product->category_id,
                'Unit Id' => $product->unit_id,
                'Product Code' => $product->product_code,
                'Stock' => $product->stock,
                'Buying Price' =>$product->buying_price,
                'Selling Price' =>$product->selling_price,
                'Product Image' => $product->product_image,
            );
        }

        $this->exportExcel($product_array);
    }

    /**
     *This function loads the customer data from the database then converts it
     * into an Array that will be exported to Excel
     */
    public function exportExcel($products){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($products);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="products.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

}
