<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Currency;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use App\Models\InvoiceDetail;
use App\Helpers\invoiceHelper;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\InvoiceRequest;
use App\Services\CalculateInvoiceService;

class InvoiceController extends Controller
{
    use ResponseAPI;

    public $calculateInvoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function listInvoice(Request $request) {

        return $this->invoiceService->listInvoice($request);
    }
    
    public function listInvoiceDetail(InvoiceRequest $request) {

        return $this->invoiceService->listInvoiceDetail($request);
    }

    public function addInvoice(InvoiceRequest $request){

        return $this->invoiceService->addInvoice($request);
    }

    public function addInvoiceDetail(InvoiceRequest $request) {

        return $this->invoiceService->addInvoiceDetail($request);
    }

    public function editInvoice(InvoiceRequest $request){

        return $this->invoiceService->editInvoice($request);
    }
    public function editInvoiceDetail(InvoiceRequest $request) {

        return $this->invoiceService->editInvoiceDetail($request);
    }

    public function deleteInvoice(InvoiceRequest $request) {

        return $this->invoiceService->deleteInvoice($request);
    }

    public function deleteInvoiceDetail(InvoiceRequest $request) {

        return $this->invoiceService->deleteInvoiceDetail($request);
    }
}
