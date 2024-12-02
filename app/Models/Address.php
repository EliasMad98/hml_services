<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'non_tenant_id',
        'location_details',
    'location_name',
        'unit_number',
        'unit_type',
        'contact_name',
        'contact_mobile',
        'lat',
        'long'
    ];

    public function non_tenant()
    {
        return $this->belongsto(NonTenant::class);
    }
    public function complaints(){
        return $this->morphMany('App\Models\Complaint', 'addressable');
        }
}
