<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
            'building_id',
            'tenant_id',
            'location_name',
            'unit_number',
            'unit_type',
    ];
    public function tenant()
    {
        return $this->belongsto(Tenant::class);
    }
    public function building()
    {
        return $this->belongsto(Building::class);
    }
    public function complaints(){
        return $this->morphMany('App\Models\Complaint', 'addressable');
        }

}
