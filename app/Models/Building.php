<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_details',
        'street_name',
        'building_name',
        'building_number',
        'lat',
        'long',
    ];

    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }
}
