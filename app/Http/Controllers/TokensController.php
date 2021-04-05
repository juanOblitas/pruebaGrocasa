<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon;
use DB;

class TokensController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $validator = Validator::make($credentials, [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'code' => 1,
                    'message' => 'Wrong validation',
                    'errors' => $validator->errors()
                ]
            ], 422);
        }

        $token = JWTAuth::attempt($credentials);

        if ($token) {
            $expirationTime=JWTAuth::factory()->getTTL();
            $myTime = Carbon\Carbon::now();
            $userUpdateFieldLastLogin=User::where('username',$request->username)->get()->first();
            DB::table('users')
                ->where('username', $request->username)
                ->update(['last_login' => $myTime->toDateTimeString()]);
            return response()->json([
                'meta' => [
                    'success' => true,
                    'errors' => []
                ],
                'data' => [
                    'token' => $token,
                    'minutes_to_expire' => $expirationTime,
                ]
                
            ], 200);
        } else {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ['Password incorrect for: '.$request->username]
                ]
            ], 401);
        }
    }

    public function refreshToken()
    {

        $token = JWTAuth::getToken();

        try {
            $token = JWTAuth::refresh($token);
            return response()->json(['success' => true, 'token' => $token], 200);
        } catch (TokenExpiredException $ex) {
            // We were unable to refresh the token, our user needs to login again
            return response()->json([
                'code' => 3, 'success' => false, 'message' => 'Need to login again, please (expired)!'
            ]);
        } catch (TokenBlacklistedException $ex) {
            // Blacklisted token
            return response()->json([
                'code' => 4, 'success' => false, 'message' => 'Need to login again, please (blacklisted)!'
            ], 422);
        }

    }
    
    public function logout()
    {        
        $token = JWTAuth::getToken();

        try {
            $token = JWTAuth::invalidate($token);
            return response()->json([
                'code' => 5, 'success' => true, 'message' => "You have successfully logged out."
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'code' => 6, 'success' => false, 'message' => 'Failed to logout, please try again.'
            ], 422);
        }

    }

}
