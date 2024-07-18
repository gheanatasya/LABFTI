<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Mail\Registration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
    //mengubah data semua row
    public function updateSemuaRow(UpdateUserRequest $request, User $UserID)
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

    //mengubah data user yang login
    public function update(UpdateUserRequest $request, $UserID)
    {
        $user = User::find($UserID);

        // Pastikan user yang ingin diperbarui adalah user yang sedang login
        if ($user->id !== auth()->id()) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk memperbarui pengguna lain'], 403);
        }

        // Validasi data masukan
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Update email dan password
        $user->Email = $request->email;
        $user->Password = bcrypt($request->password);
        $user->save();

        return response()->json(['message' => 'Email dan password berhasil diperbarui', 'data' => $user]);
    }
}
