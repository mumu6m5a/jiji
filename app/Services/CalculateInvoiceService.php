<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceDetail;

class CalculateInvoiceService {

    //calculate add Invoice
    public function calculateTotal($request, $sumSubTotal, $id)
    {
        //caculate
        $caculateTax = $sumSubTotal * $request['tax'] / 100;
        $caculateDiscount = $sumSubTotal * $request['discount'] / 100;
        $sumTotal = ($sumSubTotal - $caculateDiscount) + $caculateTax;

        //update total Invoice
        $addInvoice = Invoice::find($id);
        $addInvoice->sub_total = $sumSubTotal;
        $addInvoice->total = $sumTotal;
        $addInvoice->save();
    }

    public function calculateTotal_ByEdit($request){
         // update caculate
         $sumSubTotalPrice = InvoiceDetail::where('invoice_id', $request['id'])->get()->sum('total');
         $caculateTax = $sumSubTotalPrice * $request['tax'] / 100;
         $caculateDiscount = $sumSubTotalPrice * $request['discount'] / 100;
         $sumTotal = ($sumSubTotalPrice - $caculateDiscount) + $caculateTax;

         //update total Invoice
         $editInvoice= Invoice::find($request['id']);
         $editInvoice->sub_total = $sumSubTotalPrice;
         $editInvoice->total = $sumTotal;
         $editInvoice->save();
    }

    //delete Invoice detail update the total price
    
    
}