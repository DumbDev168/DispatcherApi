<?php

namespace App\Observers;

use App\Models\User\SmsVerification;
use App\Models\User\User;

class SmsVerificationObserver
{
    /**
     * @param SmsVerification $sms
     */
    public function created(SmsVerification $sms)
    {
        $user = User::where('phone_number',$sms->phone_number)->first();
        $user->sendSmsVerifyCode($sms->code);
    }

}
