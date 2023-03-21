<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    public function formatrc(){
        return[
            'id'=>$this->id,
            'invoice_id' => $this->invoice_id,
            'company_id' => $this->company_id,
            'currency_id' => $this->currency_id,
            'receipt_name' => $this->receipt_name,
            'receipt_date' => $this->receipt_date,
            'note' => $this->note,
            'sub_total'=>$this->sub_total,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'total'=>$this->total,
            'countDetail'=>$this->receipt_detail->count(),
            'currency'=>$this->currency,
            'company'=>$this->company,
            'user'=>$this->user,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
            
        ];
    }

    public function receipt_detail()
    {
        return $this->hasMany(ReceiptDetail::class);
    }
    public function invoice()
    {
        return $this->belongsTo(invoice::class);
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
