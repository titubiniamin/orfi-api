<?php

namespace App\Models;

use App\Models\Auth\OTP;
use App\Models\Payment\Subscription;
use App\Notifications\ResetPasswordNotification;
use App\Services\FileUploadInCloud;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'phone',
        'avatar',
        'address',
        'email',
        'password',
        'social_account',
        'social_id',
        'is_baned'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value, ['rounds' => 12]);
    }

    /**
     * @param $value
     */
    public function setAvatarAttribute($value)
    {
        $new_avatar = request()->file('avatar');
        $old_avatar = $this->attributes['avatar'];

        if ($old_avatar && Storage::disk('s3')->exists($old_avatar)) {
            Storage::disk('s3')->delete($old_avatar);
        }
        $this->attributes['avatar'] = $new_avatar ? FileUploadInCloud::uploadFile($new_avatar, "user/avatar") : null;
    }

    /**********************************  Relationships *************************************/


    /**
     * @return HasOne
     */
    public function otp(): HasOne
    {
        return $this->hasOne(OTP::class);
    }

    /**
     * @return HasOne
     */
    public function BlockUser(): HasOne
    {
        return $this->hasOne(BlockUser::class);
    }

    /**
     * Send token in email.
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $url = env('FRONT_END_APP_URL') . 'reset-password?token=' . $token;
        $this->notify(new ResetPasswordNotification($url));
    }


    public function openGoogle($crud = false)
    {
        if(!$this->BlockUser){
            return '<a class="btn btn-xs text-danger" href="user-ban/' . $this->id . '" data-toggle="tooltip" title="Unblock">
                    <i class="la la-ban"></i>
                Ban</a>';
        }else{
            return '<a class="btn btn-xs text-success" href="user-ban/'.$this->id.'" data-toggle="tooltip" title="Block">
                    <i class="la la-check-circle"></i>
                Active</a>';
        }

    }

}
