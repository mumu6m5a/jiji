<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Quotation;
use App\Helpers\appHelper;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use App\Models\QuotationDetail;
use App\Services\CalculateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\QuotationRequest;

class QuotationService {

    use ResponseAPI;
   
    public $calculateService;

    public function __construct(CalculateService $calculateService)
    {
        $this->calculateService = $calculateService;
    }

    public function listQuotation(Request $request) {
        $items = Quotation::orderBy('id', 'desc')->get();
        $items->transform(function($item) {
            return $item->format();
        });

        return $this->success($items, 200);
    }
    
    public function listQuotationDetail(QuotationRequest $request) {
        
        $item = Quotation::orderBy('id', 'desc')->where('id', $request['id'])->first();
        $item['countDetail'] = QuotationDetail::where('quotation_id', $item['id'])->get()->count();
        $item['company'] = Company::where('id', $item['company_id'])->first();
        $item['currency'] = Currency::where('id', $item['currency_id'])->first();
        $item['user'] = User::where('id', $item['created_by'])->first();
        
        $details = QuotationDetail::where('quotation_id', $request['id'])->get();

        return response()->json([
            'quotation' => $item,
            'details' => $details,
        ]);
    }
    
    public function addQuotation(QuotationRequest $request){

        DB::beginTransaction();

            $addQuotation = new Quotation();
            $addQuotation->quotation_no = appHelper::generateQuotationNumber('QT-', 8);
            $addQuotation->quotation_name = $request['quotation_name'];
            $addQuotation->start_date = $request['start_date'];
            $addQuotation->end_date = $request['end_date'];
            $addQuotation->note = $request['note'];
            $addQuotation->company_id = $request['company_id'];
            $addQuotation->currency_id = $request['currency_id'];
            $addQuotation->discount = $request['discount'];
            $addQuotation->tax = $request['tax'];
            $addQuotation->created_by = Auth::user('api')->id;
            $addQuotation->save();

            //save quotation detail
            $sumSubTotal = 0;

            if(!empty($request['quotation_details'])){
                foreach($request['quotation_details'] as $item){
                    $addQuotationDetail = new QuotationDetail();
                    $addQuotationDetail->order_no = $item['order_no'];
                    $addQuotationDetail->quotation_id = $addQuotation['id'];
                    $addQuotationDetail->name = $item['name'];
                    $addQuotationDetail->description = $item['description'];
                    $addQuotationDetail->qty = $item['qty'];
                    $addQuotationDetail->price = $item['price'];
                    $addQuotationDetail->total = $item['qty'] * $item['price'];
                    $addQuotationDetail->save();

                    $sumSubTotal += $item['qty'] * $item['price'];
                    
                }
            }
           
            //calculate
            $this->calculateService->calculateTotal($request, $sumSubTotal, $addQuotation['id']);  

        DB::commit();
          
        return response()->json([
                'success' => true,
                'msg' => "ເພີ່ມສຳເລັດແລ້ວ"
            ]);
    }

    public function addQuotationDetail(QuotationRequest $request) {

        DB::beginTransaction();

        $addQuotationDetail = new QuotationDetail();
        $addQuotationDetail->order_no = $request['order_no'];
        $addQuotationDetail->quotation_id = $request['id'];
        $addQuotationDetail->name = $request['name'];
        $addQuotationDetail->description = $request['description'];
        $addQuotationDetail->qty = $request['qty'];
        $addQuotationDetail->price = $request['price'];
        $addQuotationDetail->total = $request['qty'] * $request['price'];
        $addQuotationDetail->save();

        /** update the total price */
        $addQuotation = Quotation::find($request['id']);
        $this->calculateService->calculateTotal_ByEdit($addQuotation);
        
        DB::commit();

        return response()->json([
            'success' => true,
            'msg' => 'ເພີ່ມລາຍລະອຽດສຳເລັດແລ້ວ'
        ]);
    }

    public function editQuotation(QuotationRequest $request){

        DB::beginTransaction();

        $editQuotation = Quotation::find($request['id']);
        $editQuotation->quotation_name = $request['quotation_name'];
        $editQuotation->start_date = $request['start_date'];
        $editQuotation->end_date = $request['end_date'];
        $editQuotation->note = $request['note'];
        $editQuotation->company_id = $request['company_id'];
        $editQuotation->currency_id = $request['currency_id'];
        $editQuotation->discount = $request['discount'];
        $editQuotation->tax = $request['tax'];
        $editQuotation->created_by = Auth::user('api')->id;
        $editQuotation->save();

        $this->calculateService->calculateTotal_ByEdit($request);

        DB::commit();

        return response()->json([
            'success' => true,
            'msg' => "ແກ້ໄຊສຳເລັດແລ້ວ"
        ]);
    }
    public function editQuotationDetail(QuotationRequest $request) {

        DB::beginTransaction();

        $editQuotationDetail = QuotationDetail::find($request['id']);
        $editQuotationDetail->order_no = $request['order_no'];
        $editQuotationDetail->name = $request['name'];
        $editQuotationDetail->description = $request['description'];
        $editQuotationDetail->qty = $request['qty'];
        $editQuotationDetail->price = $request['price'];
        $editQuotationDetail->total = $request['qty'] * $request['price'];
        $editQuotationDetail->save();

        /** update the total price */
        $editQuotation = Quotation::find($editQuotationDetail['quotation_id']);
        
        $this->calculateService->calculateTotal_ByEdit($editQuotation);

        DB::commit();        
        
        return response()->json([
            'success' => true,
            'msg' => 'ແກ້ໄຂລາຍລະອຽດສຳເລັດແລ້ວ'
        ]);
    }

    public function deleteQuotation(QuotationRequest $request) {

        $deleteQuotation = Quotation::find($request['id']);
        $deleteQuotation->delete();

        return response()->json([
            'success' => true,
            'msg' => 'ລົບສຳເລັດແລ້ວ'
        ]);
    }

    public function deleteQuotationDetail(QuotationRequest $request) {

        $deleteQuotationDetail = QuotationDetail::find($request['id']);
        $deleteQuotationDetail->delete();

        /** update the total price */
        $updateQuotation = Quotation::find($deleteQuotationDetail['quotation_id']);

        $this->calculateService->calculateTotal_ByEdit($updateQuotation);

        return response()->json([
            'success' => true,
            'msg' => 'ລົບລາຍລະອຽດສຳເລັດແລ້ວ'
        ]);
    }  
}