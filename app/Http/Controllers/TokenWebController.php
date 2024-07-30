<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenWebController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = User::where('UserID', $request->UserID)->first();
        $user->web_token = $request->web_token;
        $user->save();

        return response()->json(['success' => true], 201);
    }
}
