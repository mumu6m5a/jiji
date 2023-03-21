<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationDetail;

class CalculateService {

    //calculate add quotation
    public function calculateTotal($request, $sumSubTotal, $id)
    {
        //caculate
        $caculateTax = $sumSubTotal * $request['tax'] / 100;
        $caculateDiscount = $sumSubTotal * $request['discount'] / 100;
        $sumTotal = ($sumSubTotal - $caculateDiscount) + $caculateTax;

        //update total quotation
        $addQuotation = Quotation::find($id);
        $addQuotation->sub_total = $sumSubTotal;
        $addQuotation->total = $sumTotal;
        $addQuotation->save();
    }

    public function calculateTotal_ByEdit($request){
         // update caculate
         $sumSubTotalPrice = QuotationDetail::where('quotation_id', $request['id'])->get()->sum('total');
         $caculateTax = $sumSubTotalPrice * $request['tax'] / 100;
         $caculateDiscount = $sumSubTotalPrice * $request['discount'] / 100;
         $sumTotal = ($sumSubTotalPrice - $caculateDiscount) + $caculateTax;

         //update total quotation
         $editQuotation= Quotation::find($request['id']);
         $editQuotation->sub_total = $sumSubTotalPrice;
         $editQuotation->total = $sumTotal;
         $editQuotation->save();
    }

    //delete quotation detail update the total price
    
    
}