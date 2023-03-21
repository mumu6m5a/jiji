<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class QuotationRequest extends FormRequest
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
        if($this->isMethod('put') && $this->routeIs('edit.quotation') 
        || $this->isMethod('put') && $this->routeIs('edit.quotation.detail')
        || $this->isMethod('post') && $this->routeIs('add.quotation.detail')
        || $this->isMethod('delete') && $this->routeIs('delete.quotation')
        || $this->isMethod('delete') && $this->routeIs('delete.quotation.detail')
        || $this->isMethod('get') && $this->routeIs('list.quotation.detail')
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
        if($this->isMethod('get') && $this->routeIs('list.quotation.detail')) {
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('quotations', 'id')
                ]
            ];
        }
        /** validate add quotation */
        if($this->isMethod('post') && $this->routeIs('add.quotation')){
            return [
                'quotation_name' => [
                    'required',
                    'min:5',
                    'max:2000'
                ],
                'start_date'=>'required|date',
                'end_date'=>'required|date',
                'note'=>'required',
                'company_id'=>[
                    'required',
                    'numeric',
                    Rule::exists('companies', 'id')
                ],
                'currency_id'=>[
                    'required',
                    'numeric',
                    Rule::exists('currencies', 'id')
                ],
                'tax'=>'required|numeric',
                'discount'=>'required|numeric',

                'quotation_details'=>'required|array',
                'quotation_details.*.order_no'=>'required|numeric',
                'quotation_details.*.name'=>'required',
                'quotation_details.*.qty'=>'required|numeric',
                'quotation_details.*.price'=>'required|numeric',
                'quotation_details.*.description'=>'required',
            ];
        }
        if($this->isMethod('post') && $this->routeIs('add.quotation.detail')) {
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('quotations', 'id')
                ],
                'order_no' => 'required|numeric',
                'name' => 'required',
                'description' => 'required',
                'qty' => 'required|numeric',
                'price' => 'required|numeric',

            ];
        }

         /** validate edit quotation */
         if($this->isMethod('put') && $this->routeIs('edit.quotation')){
            return [
                'id'=>[
                    'required',
                    'numeric',
                    Rule::exists('quotations', 'id')
                ],
                'quotation_name' => [
                    'required',
                    'min:5',
                    'max:2000'
                ],
                'start_date'=>'required|date',
                'end_date'=>'required|date',
                'note'=>'required',
                'company_id'=>[
                    'required',
                    'numeric',
                    Rule::exists('companies', 'id')
                ],
                'currency_id'=>[
                    'required',
                    'numeric',
                    Rule::exists('currencies', 'id')
                ],
                'tax'=>'required|numeric',
                'discount'=>'required|numeric'
            ];
         }

         /** validate edit quotation.detail */
         if($this->isMethod('put') && $this->routeIs('edit.quotation.detail')) {
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('quotation_details', 'id')
                ],
                'name' => 'required',
                'description' => 'required',
                'qty' => 'required|numeric',
                'price' => 'required|numeric',

            ];
        }

        if($this->isMethod('delete') && $this->routeIs('delete.quotation')) {
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('quotations', 'id')
                ]
            ];
        }

        if($this->isMethod('delete') && $this->routeIs('delete.quotation.detail')) {
            return [
                'id' => [
                    'required',
                    'numeric',
                    Rule::exists('quotation_details', 'id')
                ]
            ];
        }
    }
    public function messages(){
        return[
            'start_date.required'=> __('validation.required')
        ];
    }
}
