<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\Currency;
use App\Traits\ResponseAPI;
use App\Models\InvoiceDetail;
use App\Models\ReceiptDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ReceiptRequest;
// use App\Models\Receipt;
// use App\Models\ReceiptDetail;

class ReceiptService {
    use ResponseAPI;

    public $calculateReceipt;

    public function __construct(CalculateReceipt $calculateReceipt)
    {
        $this->calculateReceipt = $calculateReceipt;
    }
   
    public function listReceipt() {

        $receipts = Receipt::select(
            'receipts.*'
        )
        ->orderBy('receipts.id', 'desc')
        ->get();
        $receipts->transform(function($item){
        $sumSubTotal = ReceiptDetail::where('receipt_id', $item['id'])
        ->select(DB::raw("IFNULL(sum(receipt_details.total), 0) as total"))
        ->first()->total;
            
        
        $caculateTax = $sumSubTotal * $item['tax'] / 100;
        $caculateDiscount = $sumSubTotal * $item['discount'] / 100;
        $sumTotal = ($sumSubTotal - $caculateDiscount) + $caculateTax;

        $item['sub_total1'] = $sumSubTotal;
        $item['total1'] = $sumTotal;

        $item['countDetail1'] = ReceiptDetail::where('receipt_id', $item['id'])
        ->select(DB::raw("IFNULL(count(receipt_details.total), 0) as total"))
        ->first()->total;

        return $item;
    });
         return $receipts;
    }
    
    public function listReceiptDetail(ReceiptRequest $request) {
        $item = Receipt::orderBy('id', 'desc')->where('id', $request['id'])->first();
        $item['countDetail'] = ReceiptDetail::where('receipt_id', $item['id'])->get()->count();
        $item['invoice'] = Invoice::where('id', $item['invoice_id'])->first();
        $item['company'] = Company::where('id', $item['company_id'])->first();
        $item['currency'] = Currency::where('id', $item['currency_id'])->first();
        $item['user'] = User::where('id', $item['created_by'])->first();

        $details = ReceiptDetail::where('receipt_id', $request['id'])->get();

        return response()->json([
            'receipt' => $item,
            'details' => $details,
        ]);
    }

    public function addReceipt(ReceiptRequest $request)
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
                $addReceipt = new Receipt();
                $addReceipt->invoice_id = $getInvoice['id'];
                $addReceipt->company_id = $getInvoice['company_id'];
                $addReceipt->currency_id = $getInvoice['currency_id'];
                $addReceipt->created_by = Auth::user('api')->id;
                $addReceipt->receipt_name = $request['receipt_name'];
                $addReceipt->receipt_date = $request['receipt_date'];
                $addReceipt->note=$request['note'];
                $addReceipt->sub_total =  $getInvoice['sub_total'];
                $addReceipt->discount = $getInvoice['discount'];
                $addReceipt->tax = $getInvoice['tax'];
                $addReceipt->total = $getInvoice['total'];
                $addReceipt->save();
                
                foreach($getInvoiceDetails as $item){
                    $addReceiptDetail = new ReceiptDetail();
                    $addReceiptDetail->receipt_id = $addReceipt['id'];
                    $addReceiptDetail->order_no = $item['order_no'];
                    $addReceiptDetail->name = $item['name'];
                    $addReceiptDetail->qty = $item['qty'];
                    $addReceiptDetail->price = $item['price'];
                    $addReceiptDetail->total = $item['total'];
                    $addReceiptDetail->description = $item['description'];
                    $addReceiptDetail->save();
                }
            
            }
            DB::commit();
            return $this->success('samlet',200);
        }
        // return $this->error('pidpad',500);
    }

    public function addReceiptDetail (ReceiptRequest $request){

        $addReceiptDetail = new ReceiptDetail();
        $addReceiptDetail->order_no = $request['order_no'];
        $addReceiptDetail->receipt_id = $request['id'];
        $addReceiptDetail->name = $request['name'];
        $addReceiptDetail->description = $request['description'];
        $addReceiptDetail->qty = $request['qty'];
        $addReceiptDetail->price = $request['price'];
        $addReceiptDetail->total = $request['qty'] * $request['price'];
        $addReceiptDetail->save();

        /** update the total price */
        $addReceipt = Receipt::find($request['id']);
        // $sumSubTotal = ReceiptDetail::where('receipt_id', $request['id'])->get()->sum('total');
        // $this->calculateReceipt->calculateTotal($request, $sumSubTotal, $addReceipt['id']);
        $this->calculateReceipt->calculateTotal_EditReceipt($addReceipt);
        
        return $this->success('samlet',200);
    }
    public function editReceipt(ReceiptRequest $request){

        DB::beginTransaction();

        $editReceipt = Receipt::find($request['id']);
        $editReceipt->invoice_id = $request['invoice_id'];
        $editReceipt->receipt_name = $request['receipt_name'];
        $editReceipt->receipt_date = $request['receipt_date'];
        $editReceipt->note = $request['note'];
        $editReceipt->created_by = Auth::user('api')->id;
        $editReceipt->save();

        DB::commit();

        return $this->success('samlet',200);
    }
    public function editReceiptDetail(ReceiptRequest $request){
        $editReceiptDetail = ReceiptDetail::find($request['id']);
        $editReceiptDetail->name = $request['name'];
        $editReceiptDetail->description = $request['description'];
        $editReceiptDetail->qty = $request['qty'];
        $editReceiptDetail->price = $request['price'];
        $editReceiptDetail->total = $request['qty'] * $request['price'];
        $editReceiptDetail->save();

        /** update the total price */
        $editReceipt = Receipt::find($editReceiptDetail['receipt_id']);
        $this->calculateReceipt->calculateTotal_EditReceipt($editReceipt);

        return $this->success('samlet',200);
    }

   public function deleteReceipt(ReceiptRequest $request){
    $deleteReceipt = Receipt::find($request['id']);
    $deleteReceipt->delete();

    return $this->success('samlet',200);
   }

   public function deleteReceiptDetail(ReceiptRequest $request){
    $deleteReceiptDetail = ReceiptDetail::find($request['id']);
    $deleteReceiptDetail->delete();

    $updateReceipt = Receipt::find($deleteReceiptDetail['receipt_id']);
    $this->calculateReceipt->calculateTotal_EditReceipt($updateReceipt);

    return $this->success('samlet',200);
   }

   
}