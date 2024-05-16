<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $User = User::where('Email', $request->email)->first();
        $role = $User->User_role;

        if (!$User || !Hash::check($request->password, $User->Password)) {
            return response([
                'success'   => false,
                'message' => ['These credentials do not match our records.']
            ], 404);
        }

        $token = $User->createToken('ApiToken')->plainTextToken;

        $response = [
            'success'   => true,
            'user'      => $User,
            'token'     => $token,
            'UserID'    => $User->UserID,
            'User_role' => $role
        ];

        return response($response, 201);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'success'    => true
        ], 200);
    }
}
