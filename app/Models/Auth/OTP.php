<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class OTP extends Model
{
    use HasFactory,Notifiable;

    protected $fillable = ['user_id','otp','expired_at'];

    protected $table = 'otps';
}
