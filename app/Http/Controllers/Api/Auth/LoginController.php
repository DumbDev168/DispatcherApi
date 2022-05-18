<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * login
     * @param LoginRequest $request
     * @param UserService $service
     * @return Response
     */
    public function login(LoginRequest $request, UserService $service) : Response{
        // other validate
        $validated = $service->validateLoginRequest($request);
        if(!$validated['status']){
            return $this->errorMsg($validated['message']);
        }
        //4. create user token
        $user = $validated['user'];
        $token = $user->createToken(User::USER_TOKEN);

        //5. return the response
        return $this->successWithMsg([
            'accessToken'=>$token->accessToken,
            'user'=>$user
        ],'login_success');
    }

    /**
     * login with token
     * @return Response
     */
    public function loginWithToken() : Response{
        return $this->successWithMsg([
            'user'=>Auth::user()
        ],'login_success');
    }

    /**
     * logout user
     * @return Response
     */
    public function logout() : Response{
        $token = Auth::user()->token();
        DB::table('oauth_refresh_tokens')->where('access_token_id',$token->id)->delete();
        DB::table('oauth_access_tokens')->where('id',$token->id)->delete();
        return $this->successWithMsg(null,'logout_success');
    }
}
