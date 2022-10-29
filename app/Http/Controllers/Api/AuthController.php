<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'username' => 'required|string',
            'password' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->code = 400;
            $this->response->status = false;
            $this->response->message = $validator->errors();
            $this->response->errors = $validator->fails();
            return $this->json();
        }

        // Check username
        $user = User::where('username', $request->username)->first();
        if(!$user){
            $this->code = 404;
            $this->response->status = false;
            $this->response->message = __('auth.failed');
            return $this->json();
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            $this->code = 404;
            $this->response->status = false;
            $this->response->message = __('auth.failed');
            return $this->json();
        }

        $token = $user->createToken('token')->plainTextToken;

        $data = [
            'user' => $user,
            'token' => $token
        ];
        $this->code = 201;
        $this->response->data = $data;
        return $this->json();
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }
}
