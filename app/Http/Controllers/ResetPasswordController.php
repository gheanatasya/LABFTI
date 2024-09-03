<?php

namespace App\Http\Controllers;

use App\Mail\newPassword;
use App\Mail\ResetPassword;
use App\Models\Peminjam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('Email', $request->email)->first();
        if ($user){
            $token = $user->createToken('ApiToken')->plainTextToken;
            $peminjam = Peminjam::where('UserID', $user->UserID)->first();
            $peminjam->resetpass_token = $token;
            $validTime = date('Y-m-d H:i:s', strtotime('+1 hour'));   
            $peminjam->validTime_token = $validTime;
            $peminjam->save();
            
            $url = 'http://localhost:8000/confirmNewPass/' . $token . '/' . $request->email;
            $dataEmail = [
                'email' => $request->email,
                'subject' => 'Reset Password',
                'reset_password_link' => $url
            ];

            Mail::to($request->email)->send(new ResetPassword($dataEmail));

            return response([
                'success'   => true,
                'message' => ['Reset password link sent to your email.']
            ]);
        }
    }

    public function resetPassword(Request $request){
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $user = User::where('Email', $request->email)->first();
        $peminjam = Peminjam::where('UserID', $user->UserID)->first();
        if ($peminjam->resetpass_token == $request->token && $peminjam->validTime_token >= date('Y-m-d H:i:s')) {
            $user->Password = Hash::make($request->password);
            $user->save();
            $peminjam->resetpass_token = null;
            $peminjam->validTime_token = null;
            $peminjam->save();

            $dataEmail = [
                'email' => $request->email,
                'password' => $request->password
            ];

            Mail::to($request->email)->send(new newPassword($dataEmail));

            return response([
                'success'   => true,
                'message' => ['Password changed successfully.']
            ]);
        } elseif ($peminjam->validTime_token < date('Y-m-d H:i:s')) {
            $peminjam->resetpass_token = null;
            $peminjam->validTime_token = null;
            $peminjam->save();
            return response([
                'success'   => false,
                'message' => ['Token expired.']
            ]);
        } else {
            $peminjam->resetpass_token = null;
            $peminjam->validTime_token = null;
            $peminjam->save();
            return response([
                'success'   => false,
                'message' => ['Invalid token.']
            ]);
        }
    }
}
