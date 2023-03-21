<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public function formativ(){
        return[
            'id'=>$this->id,
            'invoice_no' => $this->invoice_no,
            'invoice_name' => $this->invoice_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'note' => $this->note,
            'company_id' => $this->company_id,
            'currency_id' => $this->currency_id,
            'sub_total'=>$this->sub_total,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'status' => $this->status,
            'total'=>$this->total,
            'countDetail'=>$this->invoice_detail->count(),
            'currency'=>$this->currency,
            'company'=>$this->company,
            'user'=>$this->user,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
            
        ];
    }

    public function invoice_detail()
    {
        return $this->hasMany(InvoiceDetail::class);
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
