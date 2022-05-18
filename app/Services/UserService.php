<?php


namespace App\Services;


use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\VerifyPhoneNumberRequest;
use App\Models\User\SmsVerification;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function validateLoginRequest(LoginRequest $request){
        $data = $request->validated();
        $userExisted = $this->isUserExisted([
            ['username',$data['username']]
        ]);

        //1. is user existed ?
        if(!$userExisted['status']) {
            return [
                'status'=>false,
                'message'=>'Invalid username or password'
            ];
        }
        $user = $userExisted['user'];

        //2. is user account is verified
        if(!$this->isUserVerified($user)){
            return [
                'status'=>false,
                'message'=>'Please verify user to continue.'
            ];
        }
        //3. is password correct?
        $isPasswordMatch = Hash::check($data['password'],$user->password);
        if(!$isPasswordMatch){
            return [
                'status'=>false,
                'message'=>'Invalid username or password'
            ];
        }

        return [
            'status'=>true,
            'user'=>$user
        ];

    }



    /**
     * check if user is existed
     * @param array $where
     * @return array
     */
    public function isUserExisted(array $where) : array{
        $user = User::where($where)->first();
        if($user === null){
            return [
                'status'=>false,
                'message'=>'User is not existed.'
            ];
        }
        return [
            'status'=>true,
            'user'=>$user
        ];
    }

    /**
     * check if user is verified
     * @param User $user
     * @return bool
     */
    public function isUserVerified(User $user) : bool{
        return $user->verified_at !== null;
    }

    /**
     * check if sms verification is existed
     * @param string $phoneNumber
     * @param $code
     * @return array
     */
    public function isSmsVerificationExisted(string $phoneNumber, $code) : array{
        $sms = SmsVerification::where([
            ['phone_number',$phoneNumber],
            ['code',$code]
        ])->first();
        if($sms === null){
            return [
              'status'=>false,
              'message'=>'Invalid phone number or verification code'
            ];
        }
        return [
            'status'=>true,
            'sms'=>$sms
        ];

    }

    /**
     * validate verification request
     * @param VerifyPhoneNumberRequest $request
     * @return array
     */
    public function validateVerifyPhoneRequest(VerifyPhoneNumberRequest $request) : array{
        $data = $request->validated();
        $userExisted =  $this->isUserExisted([
            ['phone_number',$data['phone_number']]
        ]);
        if(!$userExisted['status']) return $userExisted;

        $userVerified = $this->isUserVerified($userExisted['user']);
        if($userVerified){
           return [
              'status'=>false,
              'message'=>'User is already verified. No need to verify again.'
           ];
        }

        $smsExisted=  $this->isSmsVerificationExisted($data['phone_number'],$data['code']);
        if(!$smsExisted['status']) return $smsExisted;

        if(Carbon::parse($smsExisted['sms']->expired_at)->isPast()){
            return [
                'status'=>false,
                'message'=>'Verification code is already expired.'
            ];
        }

        return [
            'status'=>true,
            'user'=>$userExisted['user'],
            'sms'=>$smsExisted['sms']
        ];

    }



    /**
     * add user sms confirmation code
     * @param User $user
     * @return SmsVerification
     */
    public function addUserSmsConfirmationCode(User $user) : SmsVerification{
        $code = $this->generateUniqueSmsCode();
        return SmsVerification::create([
            'phone_number'=>$user->phone_number,
            'code'=>$code,
            'expired_at'=>Carbon::now()->addMinutes(5)
        ]);
    }

    /**
     * generate unique sms code
     * @return int
     */
    private function generateUniqueSmsCode() : int{
        $code = random_int(100000, 999999);
        $smsCode = SmsVerification::where('code',$code)->first();
        if($smsCode === null){
            return $code;
        }
        return $this->generateUniqueSmsCode();
    }
}
