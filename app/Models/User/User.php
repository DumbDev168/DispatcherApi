<?php

namespace App\Models\User;


use App\Notifications\SmsVerifyNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens, Notifiable;
    protected  $table = "users";
    protected  $guarded = ['id'];

    public const USER_TOKEN = "UserToken";

    /**
     * send sms verify code ot user
     * @param $code
     * @param null $title
     */
    public function sendSmsVerifyCode($code,$title = null) : void{
        $this->notify(new SmsVerifyNotification($code,$title));
    }

    public function smsVerification(): HasOne{
        return $this->hasOne(SmsVerification::class,'phone_number')->latest('created_at');
    }


}
