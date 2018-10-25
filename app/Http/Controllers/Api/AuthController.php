<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;

class AuthController extends Controller
{
	use AuthenticatesUsers;
	
    public function login(Request $request)
    {
    	$this->validateLogin($request);

    	if ($this->attemptLogin($request)) {
    		$user = Auth::user();
    		return $this->authenticated($user);
    	}

    	return $this->sendFailedLoginResponse($request);
    }

    private function authenticated($user)
    {
		$token = $user->createToken($user->email)->accessToken;
		$user->passport_token = $token;
		$user->save();
		return response()->json([
			'message' => __('Logged in successfully'),
			'user' => $user,
		], 200);
    }

    public function logout()
    {
    	$user = Auth::user();
    	$user->AauthAccessToken()->delete();
		$user->passport_token = null;
		$user->save();
		return response()->json([
			'message' => __('Logged out successfully')
		], 200);
    }
}
