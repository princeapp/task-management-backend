<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5',
            'confirm_password' => 'required|string|same:password',
        ]);
        if ($validator->passes()) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['status' =>true, 'msg'=> 'user created successfully', 'access_token' => $token, 'token_type' => 'Bearer'], 200);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ], 500);
        }
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Invalid login details'], 401);
            } else {
                $user = User::where('email', $request['email'])->firstOrFail();
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json(['access_token' => $token, 'username' => $user->name]);
            }
        }
        else{
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
        
    }

    public function profile(Request $request) {
        $userData = Auth::user();
        return response()->json([
            'status' => true,
            'data' => $userData
        ], 200);
    }
}
