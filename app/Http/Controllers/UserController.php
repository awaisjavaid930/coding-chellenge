<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendVerficationEmail;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

use App\Models\User;

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        Mail::to($user->email)->send(new SendVerficationEmail($user));
        return $this->response(200, 'record saved!');
        
    }
    
    public function verifyEmail(Request $request)
    {
        $user = User::where('email',$request['email'])->first();
        $user->update(['email_verified_at' => date('Y-m-d H:i:s')]);
        return $this->response(200, 'record verified!');
        
    }
    
    public function login(LoginRequest $request) 
    {
        $validated = $request->validated();
        $user = User::where('email',$validated['email'])->first();
        if ($user) {
            if ( $user->email_verified_at != null) {
                if (Hash::check($validated['password'], $user->password)) {
                    $token = Auth::attempt($validated);
                    return $this->response(200, 'login sucessfully!', $token);
                } else {
                    return $this->response(401, 'authentication failed!');
                }
            } else {
                return $this->response(200, 'please verified account first!');
            }
        } else  {
            return $this->response(200, 'user not exist!');
        }
    }
    
    
    public function logout(Request $request)
    {
        JWTAuth::invalidate($request->token);
        return $this->response(200, 'User has been logged out!');        
    }
}
