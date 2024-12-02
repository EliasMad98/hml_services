<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class Employee extends Authenticatable implements CanResetPassword
{
    use HasApiTokens, HasFactory,Notifiable, HasRoles, \Illuminate\Auth\Passwords\CanResetPassword;

    protected $fillable = [
        'first_name',
        'last_name',
        'job_type_id',
        'email',
        'phone',
        'fcm_token',
        'password',
    ];


    public function job_type()
    {
        return $this->belongsTo(JobType::class);
    }
    public function upload(){
        return $this->morphMany('App\Models\ComplaintAsset', 'uploadable');
        }
    public function sendMessage(){
        return $this->morphMany('App\Models\ComplaintMessage', 'senderable');
        }
    public function complaints(){
            return $this->hasMany(Complaint::class);
        }
        public function visits(){
            return $this->hasMany(ComplaintVisit::class);
        }
        public function conversations()
        {
            return $this->belongsToMany(Conversation::class, 'participants');
        }

        public function messages()
        {
            return $this->hasMany(Message::class);
        }

    public function sendPasswordResetNotification($token)
    {

        $url ="https://www.hml.ae/reset-password?token=$token";

        $this->notify(new ResetPasswordNotification($url));
    }
}
