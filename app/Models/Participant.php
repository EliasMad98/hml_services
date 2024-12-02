<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'employee_id'
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

}
