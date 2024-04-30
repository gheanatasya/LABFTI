<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // mengambil semua data pada db
    public function getAllUser()
    {
        return User::all();
    }
    //ambil data sesuai id
    public function show($UserID)
    {
        return User::find($UserID);
    }
    //tambah data
    public function store(StoreUserRequest $request)
    {
        $input = $request->all();
        $user = User::create([
            'NIM_NIDN' => $input['NIM_NIDN'],
            'Email' => $input['email'],
            'Password' => bcrypt($input['password']),
            'User_role' => $input['user_role'],
            'User_priority' => $input['user_priority']
        ]);

        $userID = $user->UserID;

        return response()->json(['status' => true, 'message' => "Registration Success", 'UserID' => $userID, 'user' => $user]);
    }
    //mengubah data
    public function update(UpdateUserRequest $request, User $UserID)
    {
        $UserID->update($request->all());
        return response()->json(['message' => 'User berhasil diperbarui', 'data' => $UserID]);
    }
    //hapus data
    public function delete($UserID)
    {
        $user = User::find($UserID);
        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus'], 204);
    }
}
