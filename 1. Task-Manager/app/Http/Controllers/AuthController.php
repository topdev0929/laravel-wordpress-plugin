<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user) {
                try {
                    $token = JWTAuth::fromUser($user);
            
                    return response()->json(compact('token'));
                } catch (JWTException $e) {
                    return response()->json(['error' => 'Could not create token'], 500);
                }
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

}
