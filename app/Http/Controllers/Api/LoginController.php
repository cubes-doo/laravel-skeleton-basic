<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Json as JsonResource;

use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function token()
    {
        $credentials = request()->validate([
            'email'    => 'required',
            'password' => 'required'
        ]);
        
        $guard = auth()->guard('api');
        
        if (!$guard->attempt($credentials)) {
            throw ValidationException::withMessages(['email' => __('Invalid credentials')]);
        }
        $stateful = config('auth.guards.api.driver') == 'stateful';
        $apiToken = $guard->generateToken();

        if($stateful) {
            $refreshToken = $guard->getRefreshToken();
        } else {
            $refreshToken = $guard->getRefreshToken($apiToken);
        }

        $tokens = [
            'api_token'     => $apiToken,
            'refresh_token' => $refreshToken,
        ];

        if($stateful) {
            $guard
                ->user()
                ->update($tokens);
        }

        return JsonResource::make($tokens)->withSuccess(__('Welcome!'));
    }

    public function tokenRefresh()
    {
        $data = request()->validate([
            'api_token'     => 'required',
            'refresh_token' => 'required',
        ]);
        
        $guard = auth()->guard('api');
    
        if (!$guard->attemptRefreshToken(
            $data['refresh_token'], 
            $data['api_token']
        )) {
            throw ValidationException::withMessages(['refresh_token' => __('Invalid refresh token')]);
        }
        $stateful = config('auth.guards.api.driver') == 'stateful';
        $apiToken = $guard->generateToken();

        if($stateful) {
            $refreshToken = $guard->getRefreshToken();
        } else {
            $refreshToken = $guard->getRefreshToken($apiToken);
        }

        $tokens = [
            'api_token'     => $apiToken,
            'refresh_token' => $refreshToken,
        ];

        if($stateful) {
            $guard
                ->user()
                ->update($tokens);
        }
        
        return JsonResource::make($tokens)->withSuccess(__('Token refreshed!!'));
    }
}