<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonTenant extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id'
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class);    
    }

    public function user()
    {
        return $this->belongsto(User::class);    
    }
}
