<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'employee_id',
        'date',
        'time'
    ];


    public function complaint()
    {
        return $this->belongsto(Complaint::class);
    } 
    public function employee()
    {
        return $this->belongsto(Employee::class);
    }
}
