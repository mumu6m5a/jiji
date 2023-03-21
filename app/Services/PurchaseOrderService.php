<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Currency;
use App\Traits\ResponseAPI;
use App\Models\InvoiceDetail;
use App\Models\PurchaseDetail;
use App\Models\PurchaseOrders;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrdersDetail;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseOrderRequest;
use App\Http\Requests\PurchaseOrdersRequest;
// use App\Models\PurchaseOrders;
// use App\Models\PurchaseOrdersDetail;

class PurchaseOrderService {
    use ResponseAPI;

    public $calculatePurchase;

    public function __construct(CalculatePurchase $calculatePurchase)
    {
        $this->calculatePurchase = $calculatePurchase;
    }
   
    public function listPurchase() {

        $purchases = PurchaseOrders::select(
            'purchase_orders.*'
        )
        ->orderBy('purchase_orders.id', 'desc')
        ->get();
        $purchases->transform(function($item){
        $sumSubTotal = PurchaseDetail::where('purchase_id', $item['id'])
        ->select(DB::raw("IFNULL(sum(purchase_details.total), 0) as total"))
        ->first()->total;
            
        
        $caculateTax = $sumSubTotal * $item['tax'] / 100;
        $caculateDiscount = $sumSubTotal * $item['discount'] / 100;
        $sumTotal = ($sumSubTotal - $caculateDiscount) + $caculateTax;

        $item['sub_total1'] = $sumSubTotal;
        $item['total1'] = $sumTotal;

        $item['countDetail1'] = PurchaseDetail::where('purchase_id', $item['id'])
        ->select(DB::raw("IFNULL(count(purchase_details.total), 0) as total"))
        ->first()->total;

        return $item;
    });
         return $purchases;
    }
    
    public function listPurchaseDetail(PurchaseOrderRequest $request) {
        $item = PurchaseOrders::orderBy('id', 'desc')->where('id', $request['id'])->first();
        $item['countDetail'] = PurchaseDetail::where('purchase_id', $item['id'])->get()->count();
        $item['invoice'] = Invoice::where('id', $item['invoice_id'])->first();
        $item['company'] = Company::where('id', $item['company_id'])->first();
        $item['currency'] = Currency::where('id', $item['currency_id'])->first();
        $item['user'] = User::where('id', $item['created_by'])->first();

        $details = PurchaseDetail::where('purchase_id', $request['id'])->get();

        return response()->json([
            'purchase' => $item,
            'details' => $details,
        ]);
    }

   public function addPurchase (PurchaseOrderRequest $request)
   {
    $getInvoice = Invoice::find($request['invoice_id']);
    if(isset($getInvoice)){
        $getInvoiceDetails = InvoiceDetail::select(
            'invoice_details.*'
        )
        ->join(
            'invoices as invoice',
            'invoice.id',
            'invoice_details.invoice_id'
        )
        ->where('invoice_details.invoice_id', $getInvoice['id'])
        ->where('invoice.status', 'paid')
        ->get(); 
        
        if(count($getInvoiceDetails) > 0 ){
            DB::beginTransaction();
            $addPurchase = new PurchaseOrders();
            $addPurchase->invoice_id = $getInvoice['id'];
            $addPurchase->company_id = $getInvoice['company_id'];
            $addPurchase->currency_id = $getInvoice['currency_id'];
            $addPurchase->created_by = Auth::user('api')->id;
            $addPurchase->purchase_name = $request['purchase_name'];
            $addPurchase->date = $request['date'];
            $addPurchase->note=$request['note'];
            $addPurchase->sub_total =  $getInvoice['sub_total'];
            $addPurchase->discount = $getInvoice['discount'];
            $addPurchase->tax = $getInvoice['tax'];
            $addPurchase->total = $getInvoice['total'];
            $addPurchase->save();
            
            foreach($getInvoiceDetails as $item){
                $addPurchaseDetail = new PurchaseDetail();
                $addPurchaseDetail->purchase_id = $addPurchase['id'];
                $addPurchaseDetail->order_no = $item['order_no'];
                $addPurchaseDetail->name = $item['name'];
                $addPurchaseDetail->qty = $item['qty'];
                $addPurchaseDetail->price = $item['price'];
                $addPurchaseDetail->total = $item['total'];
                $addPurchaseDetail->description = $item['description'];
                $addPurchaseDetail->save();
            }
           
        }
        DB::commit();
        return $this->success('samlet',200);
    }
    // return $this->error('pidpad',500);
   }

    public function addPurchaseDetail (PurchaseOrderRequest $request){

        $addPurchaseDetail = new PurchaseDetail();
        $addPurchaseDetail->order_no = $request['order_no'];
        $addPurchaseDetail->purchase_id = $request['id'];
        $addPurchaseDetail->name = $request['name'];
        $addPurchaseDetail->description = $request['description'];
        $addPurchaseDetail->qty = $request['qty'];
        $addPurchaseDetail->price = $request['price'];
        $addPurchaseDetail->total = $request['qty'] * $request['price'];
        $addPurchaseDetail->save();

        /** update the total price */
        $addPurchase = PurchaseOrders::find($request['id']);
        $this->calculatePurchase->calculateTotal_EditPurchase($addPurchase);
        
        return $this->success('samlet',200);
    }
    public function editPurchase(PurchaseOrderRequest $request){

        DB::beginTransaction();

        $editPurchase = PurchaseOrders::find($request['id']);
        $editPurchase->invoice_id = $request['invoice_id'];
        $editPurchase->purchase_name = $request['purchase_name'];
        $editPurchase->date = $request['date'];
        $editPurchase->note = $request['note'];
        $editPurchase->created_by = Auth::user('api')->id;
        $editPurchase->save();

        DB::commit();

        return $this->success('samlet',200);
    }
    public function editPurchaseDetail(PurchaseOrderRequest $request){
        $editReceiptDetail = PurchaseDetail::find($request['id']);
        $editReceiptDetail->name = $request['name'];
        $editReceiptDetail->description = $request['description'];
        $editReceiptDetail->qty = $request['qty'];
        $editReceiptDetail->price = $request['price'];
        $editReceiptDetail->total = $request['qty'] * $request['price'];
        $editReceiptDetail->save();

        /** update the total price */
        $editPurchase = PurchaseOrders::find($editReceiptDetail['purchase_id']);
        $this->calculatePurchase->calculateTotal_EditPurchase($editPurchase);

        return $this->success('samlet',200);
    }

   public function deletePurchase(PurchaseOrderRequest $request){
    $deletePurchase = PurchaseOrders::find($request['id']);
    $deletePurchase->delete();

    return $this->success('samlet',200);
   }
   
   public function deletePurchaseDetail(PurchaseOrderRequest $request){
    $deletePurchaseDetail = PurchaseDetail::find($request['id']);
    $deletePurchaseDetail->delete();

    $updatePurchase = PurchaseOrders::find($deletePurchaseDetail['purchase_id']);
    $this->calculatePurchase->calculateTotal_EditPurchase($updatePurchase);

    return $this->success('samlet',200);
   }

   
}