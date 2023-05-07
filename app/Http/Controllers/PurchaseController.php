<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\PurcaseDetail;
use App\Models\PurchaseDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class PurchaseController extends Controller
{
    /**
     * Display an all purchases.
     */
    public function allPurchases()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $purchases = Purchase::with(['supplier'])
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('purchases.all-purchases', [
            'purchases' => $purchases
        ]);
    }

    /**
     * Display an all approved purchases.
     */
    public function approvedPurchases()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $purchases = Purchase::with(['supplier'])
            ->where('purchase_status', 1) // 1 = approved
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        return view('purchases.approved-purchases', [
            'purchases' => $purchases
        ]);
    }

    /**
     * Display a purchase details.
     */
    public function purchaseDetails(String $purchase_id)
    {
        $purchase = Purchase::with(['supplier','user_created','user_updated'])
            ->where('id', $purchase_id)
            ->first();

        $purchaseDetails = PurchaseDetails::with('product')
            ->where('purchase_id', $purchase_id)
            ->orderBy('id')
            ->get();

        return view('purchases.details-purchase', [
            'purchase' => $purchase,
            'purchaseDetails' => $purchaseDetails,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createPurchase()
    {
        return view('purchases.create-purchase', [
            'categories' => Category::all(),
            'suppliers' => Supplier::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storePurchase(Request $request)
    {
        $rules = [
            'supplier_id' => 'required|string',
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

        // Create Purchase Details
        $pDetails = array();
        $products = count($request->product_id);
        for ($i=0; $i < $products; $i++) {
            $pDetails['purchase_id'] = $purchase_id;
            $pDetails['product_id'] = $request->product_id[$i];
            $pDetails['quantity'] = $request->quantity[$i];
            $pDetails['unitcost'] = $request->unitcost[$i];
            $pDetails['total'] = $request->total[$i];
            $pDetails['created_at'] = Carbon::now();

            PurchaseDetails::insert($pDetails);
        }

        return Redirect::route('purchases.allPurchases')->with('success', 'Purchase has been created!');
    }

    /**
     * Handle update a status purchase
     */
    public function updatePurchase(Request $request)
    {
        $purchase_id = $request->id;

        // Reduce the stock
        $products = PurchaseDetails::where('purchase_id', $purchase_id)->get();

        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                    ->update(['stock' => DB::raw('stock+'.$product->quantity)]);
        }

        Purchase::findOrFail($purchase_id)
            ->update([
                'purchase_status' => 1,
                'updated_by' => auth()->user()->id
            ]); // 1 = approved, 0 = pending

        return Redirect::route('purchases.allPurchases')->with('success', 'Purchase has been approved!');
    }

    /**
     * Handle delete a purchase
     */
    public function deletePurchase(String $purchase_id)
    {
        Purchase::findOrFail([
            'id' => $purchase_id,
            'purchase_status' => 0
        ])->delete();

        PurchaseDetails::findOrFail('purchase_id', $purchase_id)->delete();

        return Redirect::route('purchases.allPurchases')->with('success', 'Purchase has been deleted!');
    }
}
