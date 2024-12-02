<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LaracraftTech\LaravelDateScopes\DateScopes;
class Complaint extends Model
{
    use HasFactory , DateScopes ;

    protected $fillable = [
        'user_id',
        'user_type',
        'employee_id',
        'title',
        'description',
        'date',
        'time',
        'service_started',
        'service_ended',
        'needs_spare',
        'determine_price',
        'urgent',
        'needs_visit',
        'price',
        'payment_method',
        'paid',
        'job_finished',
        'rate',
        'canceled',
        'addressable_type',
        'addressable_id',
    ];

    protected $casts = [
        'needs_spare'=> 'boolean',
        'determine_price'=> 'boolean',
        'urgent'=> 'boolean',
        'needs_visit'=> 'boolean',
        'job_finished'=> 'boolean',
        'canceled'=> 'boolean',
    ];
    public function user()
    {
        return $this->belongsto(User::class);
    }
    public function employee()
    {
        return $this->belongsto(Employee::class);
    }
    public function assets()
    {
        return $this->hasMany(ComplaintAsset::class);
    }
    public function visit()
    {
        return $this->hasOne(ComplaintVisit::class);
    }
    public function addressable()
    {
        return $this->morphTo();
    }
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
    public function messages()
    {
        return $this->hasMany(ComplaintMessage::class);
    }

}
