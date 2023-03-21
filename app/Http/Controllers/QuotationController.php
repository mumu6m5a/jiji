<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Quotation;
use App\Helpers\appHelper;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use App\Models\QuotationDetail;
use App\Services\CalculateService;
use App\Services\QuotationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\QuotationRequest;


class QuotationController extends Controller
{
    use ResponseAPI;

    public $quotationService;

    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }
    public function listQuotation(Request $request) {

        return $this->quotationService->listQuotation($request);
    }
    
    public function listQuotationDetail(QuotationRequest $request) {
        
        return $this->quotationService->listQuotationDetail($request);
    }
    
    public function addQuotation(QuotationRequest $request){

        return $this->quotationService->addQuotation($request);
    }

    public function addQuotationDetail(QuotationRequest $request) {

        return $this->quotationService->addQuotationDetail($request);
    }

    public function editQuotation(QuotationRequest $request){

        return $this->quotationService->editQuotation($request);
    }
    public function editQuotationDetail(QuotationRequest $request) {

        return $this->quotationService->editQuotationDetail($request);
    }

    public function deleteQuotation(QuotationRequest $request) {

        return $this->quotationService->deleteQuotation($request);
    }

    public function deleteQuotationDetail(QuotationRequest $request) {

        return $this->quotationService->deleteQuotationDetail($request);
    }
}
