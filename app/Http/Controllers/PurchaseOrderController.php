<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PurchaseOrderService;
use App\Http\Requests\PurchaseOrderRequest;

class PurchaseOrderController extends Controller
{
    public $purchaseOrderService;

    public function __construct(PurchaseOrderService $purchaseOrderService)
    {
        $this->purchaseOrderService = $purchaseOrderService;
    }
    public function listPurchase(Request $request){
        
        return $this->purchaseOrderService->listPurchase($request);
    }
    public function listPurchaseDetail(PurchaseOrderRequest $request){
        
        return $this->purchaseOrderService->listPurchaseDetail($request);
    }
    public function addPurchase(PurchaseOrderRequest $request){

        return $this->purchaseOrderService->addPurchase($request);
    }
    public function addPurchaseDetail(PurchaseOrderRequest $request){
        
        return $this->purchaseOrderService->addPurchaseDetail($request);
    }
    public function editPurchase(PurchaseOrderRequest $request){
        
        return $this->purchaseOrderService->editPurchase($request);
    }
    public function editPurchaseDetail(PurchaseOrderRequest $request){
        
        return $this->purchaseOrderService->editPurchaseDetail($request);
    }
    public function deletePurchase(PurchaseOrderRequest $request){
        
        return $this->purchaseOrderService->deletePurchase($request);
    }
    public function deletePurchaseDetail(PurchaseOrderRequest $request){
        
        return $this->purchaseOrderService->deletePurchaseDetail($request);
    }
}
