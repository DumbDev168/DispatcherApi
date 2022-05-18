<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\VerifyPhoneNumberRequest;
use App\Models\User\SmsVerification;
use App\Models\User\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    /**
     * register new user
     * @param RegisterRequest $request
     * @param UserService $service
     * @return Response
     */
    public function register(RegisterRequest $request, UserService $service) : Response{
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $service->addUserSmsConfirmationCode($user);
        return $this->successWithMsg($user,'sms_code_send');
    }


    /**
     * verify phone number
     * @param VerifyPhoneNumberRequest $request
     * @param UserService $service
     * @return Response
     */
    public function verifyPhoneNumber(VerifyPhoneNumberRequest $request, UserService $service) : Response{

        //0 check if user existed
        //1 check if user is already verified?
        //2 check if given sms or phone number is existed in sms verifiation table?
        //3 check if expired at is in the past?
        $validated = $service->validateVerifyPhoneRequest($request);
        if(!$validated['status']){
            return $this->errorMsg($validated['message']);
        }

        //4. update user to verified
        $user = $validated['user'];
        $user->verified_at = now();
        $user->save();

        //5. remove sms code from table
        $validated['sms']->delete();

        //6. authorized the token back to response
        $token = $user->createToken(User::USER_TOKEN);
        return $this->successWithMsg([
            'accessToken'=>$token->accessToken,
            'user'=>$user
        ],'user_verified');
    }



}
