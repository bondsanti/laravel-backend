<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use  HasFactory, Notifiable;

    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    public function getJWTIdentifier()
    {
        //return primary key user id
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        //return a key value
        return [];
    }

    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];
}
