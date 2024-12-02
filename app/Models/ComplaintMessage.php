<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'message',
        'senderable_id',
        'senderable_type',
    ];

    public function senderable()
    {
        return $this->morphTo();
    }
    public function complaint()
    {
        return $this->belongsto(Complaint::class);
    }
}
