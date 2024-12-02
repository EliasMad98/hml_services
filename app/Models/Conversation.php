<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [

    ];
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'participants');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }


}
