<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Currency;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use App\Models\InvoiceDetail;
use App\Helpers\invoiceHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\InvoiceRequest;
use App\Services\CalculateInvoiceService;

class InvoiceService {

    use ResponseAPI;
   
    public $calculateInvoiceService;

    public function __construct(CalculateInvoiceService $calculateInvoiceService)
    {
        $this->calculateInvoiceService = $calculateInvoiceService;
    }

    public function listInvoice() {
        // $items = Invoice::orderBy('id', 'desc')->get();
        // $items->transform(function($item) {
        //     return $item->formativ();
        // });

        // return $this->success($items, 200);

        $invoices = Invoice::select(
            'invoices.*'
        )
        ->where('invoices.status', '=', 'paid')
        // ->join(
        //     'invoice_details as detail',
        //     'detail.invoice_id', 'invoices.id'
        // )
        ->orderBy('invoices.id', 'desc')
        // ->groupBy('invoices.id')
        ->get();
        $invoices->transform(function($item){
        $sumSubTotal = InvoiceDetail::where('invoice_id', $item['id'])
        ->select(DB::raw("IFNULL(sum(invoice_details.total), 0) as total"))
        ->first()->total;
            
        
        $caculateTax = $sumSubTotal * $item['tax'] / 100;
        $caculateDiscount = $sumSubTotal * $item['discount'] / 100;
        $sumTotal = ($sumSubTotal - $caculateDiscount) + $caculateTax;

        $item['sub_total1'] = $sumSubTotal;
        $item['total1'] = $sumTotal;

        // $item['countDetail1'] = InvoiceDetail::where('invoice_id', $item['id'])
        // ->select(DB::raw("IFNULL(count(invoice_details.total), 0) as total"))
        // ->first()->total;

        $item['countDetailMax....'] = InvoiceDetail::where('invoice_id', $item['id'])
        // ->where(function($q)
        // {
        //     // $q->where('total', '>', 100000);
        //     // $q->orWhere('total', '<', 100000);
        // })
        ->whereIn('order_no', [1,2])
        ->where('total', '>', 100000)
        ->select(DB::raw("IFNULL(count(invoice_details.total), 0) as total"))
        ->first()->total;

        return $item;
    });
         return $invoices;
    }
    
    public function listInvoiceDetail(InvoiceRequest $request) {
        $item = Invoice::orderBy('id', 'desc')->where('id', $request['id'])->first();
        $item['countDetail'] = InvoiceDetail::where('invoice_id', $item['id'])->get()->count();
        $item['company'] = Company::where('id', $item['company_id'])->first();
        $item['currency'] = Currency::where('id', $item['currency_id'])->first();
        $item['user'] = User::where('id', $item['created_by'])->first();

        $details = InvoiceDetail::where('invoice_id', $request['id'])->get();

        return response()->json([
            'invoice' => $item,
            'details' => $details,
        ]);
    }
    
    public function addInvoice(InvoiceRequest $request){

        DB::beginTransaction();

            $addInvoice = new Invoice();
            $addInvoice->invoice_no = invoiceHelper::generateInvoiceNumber('IV-', 8);
            $addInvoice->invoice_name = $request['invoice_name'];
            $addInvoice->start_date = $request['start_date'];
            $addInvoice->end_date = $request['end_date'];
            $addInvoice->note = $request['note'];
            $addInvoice->company_id = $request['company_id'];
            $addInvoice->currency_id = $request['currency_id'];
            $addInvoice->discount = $request['discount'];
            $addInvoice->tax = $request['tax'];
            $addInvoice->created_by = Auth::user('api')->id;
            $addInvoice->save();

            //save invoice detail
            $sumSubTotal = 0;

            if(!empty($request['invoice_details'])){
                foreach($request['invoice_details'] as $item){
                    $addDetails = new InvoiceDetail();
                    $addDetails->order_no = $item['order_no'];
                    $addDetails->invoice_id = $addInvoice['id'];
                    $addDetails->name = $item['name'];
                    $addDetails->description = $item['description'];
                    $addDetails->qty = $item['qty'];
                    $addDetails->price = $item['price'];
                    $addDetails->total = $item['qty'] * $item['price'];
                    $addDetails->save();

                    $sumSubTotal += $item['qty'] * $item['price'];
                    
                }
            }
           
            //calculate
            $this->calculateInvoiceService->calculateTotal($request, $sumSubTotal, $addInvoice['id']);  

        DB::commit();
          
        return response()->json([
                'success' => true,
                'msg' => "ເພີ່ມສຳເລັດແລ້ວ"
            ]);
    }

    public function addInvoiceDetail(InvoiceRequest $request) {

        DB::beginTransaction();

        $addInvoiceDetail = new InvoiceDetail();
        $addInvoiceDetail->order_no = $request['order_no'];
        $addInvoiceDetail->invoice_id = $request['id'];
        $addInvoiceDetail->name = $request['name'];
        $addInvoiceDetail->description = $request['description'];
        $addInvoiceDetail->qty = $request['qty'];
        $addInvoiceDetail->price = $request['price'];
        $addInvoiceDetail->total = $request['qty'] * $request['price'];
        $addInvoiceDetail->save();

        /** update the total price */
        $addInvoice = Invoice::find($request['id']);
        $this->calculateInvoiceService->calculateTotal_ByEdit($addInvoice);
        
        DB::commit();

        return response()->json([
            'success' => true,
            'msg' => 'ເພີ່ມລາຍລະອຽດສຳເລັດແລ້ວ'
        ]);
    }

    public function editInvoice(InvoiceRequest $request){

        DB::beginTransaction();

        $editInvoice = Invoice::find($request['id']);
        $editInvoice->invoice_name = $request['invoice_name'];
        $editInvoice->start_date = $request['start_date'];
        $editInvoice->end_date = $request['end_date'];
        $editInvoice->note = $request['note'];
        $editInvoice->company_id = $request['company_id'];
        $editInvoice->currency_id = $request['currency_id'];
        $editInvoice->discount = $request['discount'];
        $editInvoice->tax = $request['tax'];
        $editInvoice->status =$request['status'];
        $editInvoice->created_by = Auth::user('api')->id;
        $editInvoice->save();

        $this->calculateInvoiceService->calculateTotal_ByEdit($request);

        DB::commit();

        return response()->json([
            'success' => true,
            'msg' => "ແກ້ໄຂສຳເລັດແລ້ວ"
        ]);
    }

    public function editInvoiceDetail(InvoiceRequest $request) {

        DB::beginTransaction();

        $editInvoiceDetail = InvoiceDetail::find($request['id']);
        // $editInvoiceDetail->order_no = $request['order_no'];
        $editInvoiceDetail->name = $request['name'];
        $editInvoiceDetail->description = $request['description'];
        $editInvoiceDetail->qty = $request['qty'];
        $editInvoiceDetail->price = $request['price'];
        $editInvoiceDetail->total = $request['qty'] * $request['price'];
        $editInvoiceDetail->save();

        /** update the total price */
        $editInvoice = Invoice::find($editInvoiceDetail['invoice_id']);
        
        $this->calculateInvoiceService->calculateTotal_ByEdit($editInvoice);

        DB::commit();        
        
        return response()->json([
            'success' => true,
            'msg' => 'ແກ້ໄຊລາຍລະອຽດສຳເລັດແລ້ວ'
        ]);
    }

    public function deleteInvoice(InvoiceRequest $request) {

        $deleteInvoice = Invoice::find($request['id']);
        $deleteInvoice->delete();

        return response()->json([
            'success' => true,
            'msg' => 'ລົບສຳເລັດແລ້ວ'
        ]);
    }

    public function deleteInvoiceDetail(InvoiceRequest $request) {

        $deleteInvoiceDetail = InvoiceDetail::find($request['id']);
        $deleteInvoiceDetail->delete();

        /** update the total price */
        $updateInvoice = Invoice::find($deleteInvoiceDetail['invoice_id']);

        $this->calculateInvoiceService->calculateTotal_ByEdit($updateInvoice);

        return response()->json([
            'success' => true,
            'msg' => 'ລົບລາຍລະອຽດສຳເລັດແລ້ວ'
        ]);
    }
}