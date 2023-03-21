<?php

namespace App\Services;

use App\Models\Receipt;
use App\Models\ReceiptDetail;


class CalculateReceipt {
    // public function calculateTotal($request, $sumSubTotal, $id)
    // {
    //     //caculate
        
    //     $caculateTax = $sumSubTotal * $request['tax'] / 100;
    //     $caculateDiscount = $sumSubTotal * $request['discount'] / 100;
    //     $sumTotal = ($sumSubTotal - $caculateDiscount) + $caculateTax;

    //     //update total Invoice
    //     $addReceipt = Receipt::find($id);
    //     $addReceipt->sub_total = $sumSubTotal;
    //     $addReceipt->total = $sumTotal;
    //     $addReceipt->save();
    // }

    public function calculateTotal_EditReceipt($request){
        // update caculate
        $sumSubTotalPrice = ReceiptDetail::where('receipt_id', $request['id'])->get()->sum('total');
        $caculateTax = $sumSubTotalPrice * $request['tax'] / 100;
        $caculateDiscount = $sumSubTotalPrice * $request['discount'] / 100;
        $sumTotal = ($sumSubTotalPrice - $caculateDiscount) + $caculateTax;

        //update total Invoice
        $editReceipt= Receipt::find($request['id']);
        $editReceipt->sub_total = $sumSubTotalPrice;
        $editReceipt->total = $sumTotal;
        $editReceipt->save();
   }
}