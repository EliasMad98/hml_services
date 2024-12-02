<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'complaint_id',
        'invoiceId',
        'invoiceURL',
        'paymentId',
        'invoice_value',
        'customer_name',
        'customer_phone',
        'card_number',
        'transaction_status'
    ];

    public function complaint()
    {
        return   $this->belongsTo(Complaint::class);
    }
}
