<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\PurchaseDetails;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('supplier')->get();

        return view('purchases.index', [
            'purchases' => $purchases
        ]);
    }

    public function approvedPurchases()
    {
        $purchases = Purchase::with(['supplier'])
            ->where('purchase_status', 1)->get(); // 1 = approved

        return view('purchases.approved-purchases', [
            'purchases' => $purchases
        ]);
    }

    public function show(Purchase $purchase)
    {
        // N+1 Problem if load 'createdBy', 'updatedBy',
        $purchase->loadMissing(['supplier', 'details'])->get();

        return view('purchases.show', [
            'purchase' => $purchase
        ]);
    }

    public function edit(Purchase $purchase)
    {
        // N+1 Problem if load 'createdBy', 'updatedBy',
        $purchase->with(['supplier', 'details'])->get();

        return view('purchases.edit', [
            'purchase' => $purchase,
        ]);
    }

    public function create()
    {
        return view('purchases.create', [
            'categories' => Category::select(['id', 'name'])->get(),
            'suppliers' => Supplier::select(['id', 'name'])->get(),
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'supplier_id' => 'required',
            'purchase_date' => 'required|string',
            'total_amount' => 'required|numeric'
        ];

        $purchase_no = IdGenerator::generate([
            'table' => 'purchases',
            'field' => 'purchase_no',
            'length' => 10,
            'prefix' => 'PRS-'
        ]);

        $validatedData = $request->validate($rules);

        $validatedData['purchase_status'] = 0; // 0 = pending, 1 = approved
        $validatedData['purchase_no'] = $purchase_no;
        $validatedData['created_by'] = auth()->user()->id;
        $validatedData['created_at'] = Carbon::now();

        $purchase_id = Purchase::insertGetId($validatedData);

        //dd($request->all());

        $pDetails = [];

        foreach ($request->invoiceProducts as $product)
        {
            $pDetails['purchase_id'] = $purchase_id;
            $pDetails['product_id'] = $product['product_id'];
            $pDetails['quantity'] = $product['quantity'];
            $pDetails['unitcost'] = $product['unitcost'];
            $pDetails['total'] = $product['total'];
            $pDetails['created_at'] = Carbon::now();

            PurchaseDetails::insert($pDetails);
        }


        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase has been created!');
    }

    public function update(Request $request)
    {
        $purchase_id = $request->id;

        // after purchase approved, add stock product
        $products = PurchaseDetails::where('purchase_id', $purchase_id)->get();

        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                    ->update(['quantity' => DB::raw('quantity+'.$product->quantity)]);
        }

        Purchase::findOrFail($purchase_id)
            ->update([
                'purchase_status' => 1,
                'updated_by' => auth()->user()->id
            ]); // 1 = approved, 0 = pending

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase has been approved!');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase has been deleted!');
    }


    public function dailyPurchaseReport()
    {
        $purchases = Purchase::with(['supplier'])
            //->where('purchase_status', 1)
            ->where('purchase_date', today()->format('Y-m-d'))->get();

        return view('purchases.index', [
            'purchases' => $purchases
        ]);
    }

    public function getPurchaseReport()
    {
        return view('purchases.report-purchase');
    }

    public function exportPurchaseReport(Request $request)
    {
        $rules = [
            'start_date' => 'required|string|date_format:Y-m-d',
            'end_date' => 'required|string|date_format:Y-m-d',
        ];

        $validatedData = $request->validate($rules);

        $sDate = $validatedData['start_date'];
        $eDate = $validatedData['end_date'];

        $purchases = DB::table('purchase_details')
            ->join('products', 'purchase_details.product_id', '=', 'products.id')
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->whereBetween('purchases.purchase_date',[$sDate,$eDate])
            ->where('purchases.purchase_status','1')
            ->select( 'purchases.purchase_no', 'purchases.purchase_date', 'purchases.supplier_id','products.code', 'products.name', 'purchase_details.quantity', 'purchase_details.unitcost', 'purchase_details.total')
            ->get();

        $purchase_array [] = array(
            'Date',
            'No Purchase',
            'Supplier',
            'Product Code',
            'Product',
            'Quantity',
            'Unitcost',
            'Total',
        );

        foreach($purchases as $purchase)
        {
            $purchase_array[] = array(
                'Date' => $purchase->purchase_date,
                'No Purchase' => $purchase->purchase_no,
                'Supplier' => $purchase->supplier_id,
                'Product Code' => $purchase->product_code,
                'Product' => $purchase->product_name,
                'Quantity' => $purchase->quantity,
                'Unitcost' => $purchase->unitcost,
                'Total' => $purchase->total,
            );
        }

        $this->exportExcel($purchase_array);
    }

    public function exportExcel($products)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($products);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="purchase-report.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return $e;
        }
    }
}
