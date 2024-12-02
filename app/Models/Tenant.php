<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id'
    ];

    public function apartments()
    {
        return $this->hasMany(Apartment::class);    
    }

    public function user()
    {
        return $this->belongsto(User::class);    
    }
}
