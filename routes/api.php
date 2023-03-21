<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\PurchaseOrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    // Route::post('logout', 'AuthController@logout');
    // Route::post('refresh', 'AuthController@refresh');
    // Route::post('me', 'AuthController@me');

});
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'middleware' => [
        'auth.jwt',
        'set.locale'
    ],
    'role:superadmin|admin',
], function () {

    Route::get('list-currencies', [CurrencyController::class, 'listCurrencies']);
    Route::post('add-currency', [CurrencyController::class, 'addCurrency'])->name('add.currency');
    Route::put('edit-currency/{id}', [CurrencyController::class, 'editCurrency'])->name('edit.currency');
    Route::delete('delete-currency/{id}', [CurrencyController::class, 'deleteCurrency'])->name('delete.currency');

    Route::get('list-company', [CompanyController::class, 'listCompany']);
    Route::post('add-company', [CompanyController::class, 'addCompany'])->name('add.company');
    Route::post('edit-company/{id}', [CompanyController::class, 'editCompany'])->name('edit.company');
    Route::delete('delete-company/{id}', [CompanyController::class, 'deleteCompany'])->name('delete.company');

    Route::get('list-quotation', [QuotationController::class, 'listQuotation']);
    Route::get('list-quotation-detail/{id}', [QuotationController::class, 'listQuotationDetail'])->name('list.quotation.detail');
    Route::post('add-quotation', [QuotationController::class, 'addQuotation'])->name('add.quotation');
    Route::post('add-quotation-detail/{id}', [QuotationController::class, 'addQuotationDetail'])->name('add.quotation.detail');
    Route::put('edit-quotation/{id}', [QuotationController::class, 'editQuotation'])->name('edit.quotation');
    Route::put('edit-quotation-detail/{id}', [QuotationController::class, 'editQuotationDetail'])->name('edit.quotation.detail');
    Route::delete('delete-quotation/{id}', [QuotationController::class, 'deleteQuotation'])->name('delete.quotation');
    Route::delete('delete-quotation-detail/{id}', [QuotationController::class, 'deleteQuotationDetail'])->name('delete.quotation.detail');

    Route::get('list-invoice', [InvoiceController::class, 'listInvoice']);
    Route::get('list-invoice-detail/{id}', [InvoiceController::class, 'listInvoiceDetail'])->name('list.invoice.detail');
    Route::post('add-invoice', [InvoiceController::class, 'addInvoice'])->name('add.invoice');
    Route::post('add-invoice-detail/{id}', [InvoiceController::class, 'addInvoiceDetail'])->name('add.invoice.detail');
    Route::put('edit-invoice/{id}', [InvoiceController::class, 'editInvoice'])->name('edit.invoice');
    Route::put('edit-invoice-detail/{id}', [InvoiceController::class, 'editInvoiceDetail'])->name('edit.invoice.detail');
    Route::delete('delete-invoice/{id}', [InvoiceController::class, 'deleteInvoice'])->name('delete.invoice');
    Route::delete('delete-invoice-detail/{id}', [InvoiceController::class, 'deleteInvoiceDetail'])->name('delete.invoice.detail');

    Route::get('list-receipt', [ReceiptController::class, 'listReceipt']);
    Route::get('list-receipt-detail/{id}', [ReceiptController::class, 'listReceiptDetail'])->name('list.receipt.detail');
    Route::post('add-receipt', [ReceiptController::class, 'addReceipt'])->name('add.receipt');
    Route::post('add-receipt-detail/{id}', [ReceiptController::class, 'addReceiptDetail'])->name('add.receipt.detail');
    Route::put('edit-receipt/{id}', [ReceiptController::class, 'editReceipt'])->name('edit.receipt');
    Route::put('edit-receipt-detail/{id}', [ReceiptController::class, 'editReceiptDetail'])->name('edit.receipt.detail');
    Route::delete('delete-receipt/{id}', [ReceiptController::class, 'deleteReceipt'])->name('delete.receipt');
    Route::delete('delete-receipt-detail/{id}', [ReceiptController::class, 'deleteReceiptDetail'])->name('delete.receipt.detail');

    Route::get('list-purchase', [PurchaseOrderController::class, 'listPurchase']);
    Route::get('list-purchase-detail/{id}', [PurchaseOrderController::class, 'listPurchaseDetail'])->name('list.purchase.detail');
    Route::post('add-purchase', [PurchaseOrderController::class, 'addPurchase'])->name('add.purchase');
    Route::post('add-purchase-detail/{id}', [PurchaseOrderController::class, 'addPurchaseDetail'])->name('add.purchase.detail');
    Route::put('edit-purchase/{id}', [PurchaseOrderController::class, 'editPurchase'])->name('edit.purchase');
    Route::put('edit-purchase-detail/{id}', [PurchaseOrderController::class, 'editPurchaseDetail'])->name('edit.purchase.detail');
    Route::delete('delete-purchase/{id}', [PurchaseOrderController::class, 'deletePurchase'])->name('delete.purchase');
    Route::delete('delete-purchase-detail/{id}', [PurchaseOrderController::class, 'deletePurchaseDetail'])->name('delete.purchase.detail');

});