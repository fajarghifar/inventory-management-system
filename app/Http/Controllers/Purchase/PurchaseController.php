<?php

namespace App\Http\Controllers\Purchase;


use App\Enums\PurchaseStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StorePurchaseRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Str;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('purchases.index', [
            'purchases' => Purchase::where("user_id",auth()->id())->count()
        ]);
    }

    public function approvedPurchases()
    {
        $purchases = Purchase::with(['supplier'])
            ->where('status', PurchaseStatus::APPROVED)->get(); // 1 = approved

        return view('purchases.approved-purchases', [
            'purchases' => $purchases
        ]);
    }

    public function show($uuid)
    {
        $purchase = Purchase::where("uuid",$uuid)->firstOrFail();
        // N+1 Problem if load 'createdBy', 'updatedBy',
        $purchase->loadMissing(['supplier', 'details'])->get();

        return view('purchases.show', [
            'purchase' => $purchase
        ]);
    }

    public function edit($uuid)
    {
        $purchase = Purchase::where("uuid",$uuid)->firstOrFail();
        // N+1 Problem if load 'createdBy', 'updatedBy',
        $purchase->with(['supplier', 'details'])->get();

        return view('purchases.edit', [
            'purchase' => $purchase,
        ]);
    }

    public function create()
    {
        return view('purchases.create', [
            'categories' => Category::where("user_id",auth()->id())->select(['id', 'name'])->get(),
            'suppliers' => Supplier::where("user_id",auth()->id())->select(['id', 'name'])->get(),
        ]);
    }

    public function store(StorePurchaseRequest $request)
    {
        $purchase = Purchase::create([
            'purchase_no' => IdGenerator::generate([
                'table' => 'purchases',
                'field' => 'purchase_no',
                'length' => 10,
                'prefix' => 'PRS-'
            ]),
            'status'     => PurchaseStatus::PENDING->value,
            'created_by' => auth()->user()->id,
            'supplier_id.required' =>$request->required,
            'supplier_id'   =>$request->supplier_id,
            'date'          =>$request->date,
            'total_amount'  =>$request->total_amount,
            "uuid"=>Str::uuid(),
            "user_id"=>auth()->id()
        ]);

        /*
         * TODO: Must validate that
         */
        if (! $request->invoiceProducts == null)
        {
            $pDetails = [];

            foreach ($request->invoiceProducts as $product)
            {
                $pDetails['purchase_id']    = $purchase['id'];
                $pDetails['product_id']     = $product['product_id'];
                $pDetails['quantity']       = $product['quantity'];
                $pDetails['unitcost']       = $product['unitcost'];
                $pDetails['total']          = $product['total'];
                $pDetails['created_at']     = Carbon::now();

                //PurchaseDetails::insert($pDetails);
                $purchase->details()->insert($pDetails);
            }
        }

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase has been created!');
    }

    public function update($uuid, Request $request)
    {
        $purchase =Purchase::where("uuid",$uuid)->firstOrFail();
        $products = PurchaseDetails::where('purchase_id', $purchase->id)->get();

        foreach ($products as $product)
        {
            Product::where('id', $product->product_id)
                    ->update(['quantity' => DB::raw('quantity+'.$product->quantity)]);
        }

        Purchase::findOrFail($purchase->id)
            ->update([
                'status' => PurchaseStatus::APPROVED,
                'updated_by' => auth()->user()->id
            ]);

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase has been approved!');
    }

    public function destroy($uuid)
    {
        $purchase = Purchase::where("uuid",$uuid)->firstOrFail();
        $purchase->delete();

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase has been deleted!');
    }


    public function dailyPurchaseReport()
    {
        $purchases = Purchase::with(['supplier'])
            //->where('purchase_status', 1)
            ->where('date', today()->format('Y-m-d'))->get();

        return view('purchases.details-purchase', [
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
