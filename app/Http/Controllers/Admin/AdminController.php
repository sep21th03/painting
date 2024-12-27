<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminController extends Controller
{
    // public function login()
    // {
    //     return view('auth.login');
    // }

    public function postlogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
    
        $user = User::getByUsername($request->email);
    
        if (!$user || !Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thông tin đăng nhập không chính xác',
            ]);
        }
    
        $existingToken = $user->tokens->first();
        if ($existingToken && $user->remember_token != null) {
            $tokenResult = $user->remember_token;
        } else {
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            $save_token = User::updateToken($request->email, $tokenResult);
            if (!$save_token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lỗi truy vấn cơ sở dữ liệu',
                ]);
            }
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'Đăng nhập thành công',
            'access_token' => $tokenResult,
            'token_type' => 'Bearer'
        ]);
    }
    
}
