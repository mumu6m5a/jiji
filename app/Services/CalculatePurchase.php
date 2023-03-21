<?php

namespace App\Services;

use App\Models\PurchaseDetail;
use App\Models\PurchaseOrders;


class CalculatePurchase {

    public function calculateTotal_EditPurchase($request){
        // update caculate
        $sumSubTotalPrice = PurchaseDetail::where('purchase_id', $request['id'])->get()->sum('total');
        $caculateTax = $sumSubTotalPrice * $request['tax'] / 100;
        $caculateDiscount = $sumSubTotalPrice * $request['discount'] / 100;
        $sumTotal = ($sumSubTotalPrice - $caculateDiscount) + $caculateTax;

        //update total Invoice
        $editReceipt= PurchaseOrders::find($request['id']);
        $editReceipt->sub_total = $sumSubTotalPrice;
        $editReceipt->total = $sumTotal;
        $editReceipt->save();
   }
}