<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductImportController extends Controller
{
    public function create()
    {
        return view('products.import');
    }

    public function store(Request $request)
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
                    'name'          => $sheet->getCell( 'A' . $row )->getValue(),
                    'category_id'   => $sheet->getCell( 'B' . $row )->getValue(),
                    'unit_id'       => $sheet->getCell( 'C' . $row )->getValue(),
                    'code'          => $sheet->getCell( 'D' . $row )->getValue(),
                    'quantity'      => $sheet->getCell( 'E' . $row )->getValue(),
                    'buying_price'  => $sheet->getCell( 'F' . $row )->getValue(),
                    'selling_price' => $sheet->getCell( 'G' . $row )->getValue(),
                    'product_image' => $sheet->getCell( 'H' . $row )->getValue(),
                ];
                $startcount++;
            }

            Product::insert($data);

        } catch (Exception $e) {
            // $error_code = $e->errorInfo[1];
            return redirect()
                ->route('products.index')
                ->with('error', 'There was a problem uploading the data!');
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Data product has been imported!');
    }
}
