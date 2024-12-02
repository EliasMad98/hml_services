<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'status',
        'type',
        'path',
        'uploadable_type',
        'uploadable_id',

    ];
    public function complaint()
    {
        return $this->belongsto(Complaint::class);
    }

    public function uploadable()
    {
        return $this->morphTo();
    }
}
