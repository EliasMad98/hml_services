<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPasswordNotification;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'fcm_token',
        'email',
        'email_verified_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function addresses()
    {
        return $this->hasManyThrough(Address::class,NonTenant::class);

    }
    public function apartments()
    {
        return $this->hasManyThrough(Apartment::class,Tenant::class);

    }
    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }

    public function non_tenant()
    {
        return $this->hasOne(NonTenant::class);
    }
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function upload(){
        return $this->morphMany('App\Models\ComplaintAsset', 'uploadable');
        }

        public function sendMessage(){
            return $this->morphMany('App\Models\ComplaintMessage', 'senderable');
            }

    // public function sendPasswordResetNotification($token)
    // {


    //     $url ="https://www.hml.ae/reset-password?token=$token";


    //     $this->notify(new ResetPasswordNotification($url));
    // }
}
