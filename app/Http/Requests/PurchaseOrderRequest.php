<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('api')->user()->hasRole('superadmin|admin');
    }

    public function prepareForValidation()
    {
        if($this->isMethod('put') && $this->routeIs('edit.purchase') 
        || $this->isMethod('put') && $this->routeIs('edit.purchase.detail')
        || $this->isMethod('post') && $this->routeIs('add.purchase.detail')
        || $this->isMethod('delete') && $this->routeIs('delete.purchase')
        || $this->isMethod('delete') && $this->routeIs('delete.purchase.detail')
        || $this->isMethod('get') && $this->routeIs('list.purchase.detail')
    ) {  
        $this->merge([
            'id' => $this->route()->parameters['id']
        ]);
    }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        if($this->isMethod('get') && $this->routeIs('list.purchase.detail')) {
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('purchase_orders', 'id')
                ]
            ];
        }
            if($this->isMethod('post') && $this->routeIs('add.purchase'))
            {
                return [
                'purchase_name' => [
                    'required',
                    'min:5',
                    'max:2000'
                ],
                'date'=>'required|date',
                'note'=>'required',
                'invoice_id'=>[
                    'required',
                    'numeric',
                    Rule::exists('invoices', 'id'),
                    Rule::unique('purchase_orders', 'invoice_id')
                    // ->ignore($this->id)
                ],  
            ];
        }
        if($this->isMethod('post') && $this->routeIs('add.purchase.detail')) {
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('purchase_orders', 'id')
                ],
                'order_no' => 'required|numeric',
                'name' => 'required',
                'description' => 'required',
                'qty' => 'required|numeric',
                'price' => 'required|numeric',

            ];
        }
        if($this->isMethod('put') && $this->routeIs('edit.purchase')){
            return [
                'id'=>[
                    'required',
                    'numeric',
                    Rule::exists('purchase_orders', 'id')
                ],
                'purchase_name' => [
                    'required',
                    'min:5',
                    'max:2000'
                ],
                'date'=>'required|date',
                'note'=>'required',
                'invoice_id'=>[
                    'required',
                    'numeric',
                    Rule::exists('invoices', 'id'),
                    Rule::unique('purchase_orders', 'invoice_id')
                    ->ignore($this->id)
                ],  
            ];
        }
        if($this->isMethod('put') && $this->routeIs('edit.purchase.detail')) {
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('purchase_details', 'id')
                ],
                'name' => 'required',
                'description' => 'required',
                'qty' => 'required|numeric',
                'price' => 'required|numeric',

            ];
        }
        if($this->isMethod('delete') && $this->routeIs('delete.purchase')) {
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('purchase_orders', 'id')
                ]
            ];
        }

        if($this->isMethod('delete') && $this->routeIs('delete.purchase.detail')) {
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('purchase_details', 'id')
                ]
            ];
        }
    }
}
