<?php

namespace App\Models;

use App\Models\QuotationDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quotation extends Model
{
    use HasFactory;

    public function format(){
        return[
            'id'=>$this->id,
            'quotation_no' => $this->quotation_no,
            'quotation_name' => $this->quotation_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'note' => $this->note,
            'company_id' => $this->company_id,
            'currency_id' => $this->currency_id,
            'sub_total'=>$this->sub_total,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'total'=>$this->total,
            'countDetail'=>$this->quotation_detail->count(),
            'currency'=>$this->currency,
            'company'=>$this->company,
            'user'=>$this->user,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
            
        ];
    }

    public function quotation_detail()
    {
        return $this->hasMany(QuotationDetail::class);
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
