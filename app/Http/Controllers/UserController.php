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
        
        $awalanNIM = substr($input['NIM_NIDN'], 0, 2);
        $domainEmail = substr($input['email'], strpos($input['email'], '@') + 1);

        if (($awalanNIM === '71' || $awalanNIM === '72') && ($domainEmail === 'ti.ukdw.ac.id' || $domainEmail === 'si.ukdw.ac.id') && ($input['fakultas'] !== 'Lainnya') && ($input['prodi'] !== 'Lainnya') && ($input['instansi'] === 'Lainnya')) {
            $user_role = 'Mahasiswa';
            $user_priority = 1;
        } else if (($domainEmail === 'ti.ukdw.ac.id' || $domainEmail === 'si.ukdw.ac.id') && ($awalanNIM !== '71' || $awalanNIM !== '72') && ($input['fakultas'] !== 'Lainnya') && ($input['prodi'] !== 'Lainnya') && ($input['instansi'] === 'Lainnya')) {
            $user_role = 'Dosen';
            $user_priority = 2;
        } else if ($input['prodi'] === 'Pegawai Pendukung Akademik' && $input['fakultas'] !== 'Lainnya' && $input['instansi'] === 'Lainnya'){
            $user_role = 'Staff';
            $user_priority = 2;
        } else if ($domainEmail === 'students.ukdw.ac.id' && $input['prodi'] !== 'Lainnya' && $input['fakultas'] !== 'Lainnya' && $input['instansi'] === 'Lainnya'){
            $user_role = 'Mahasiswa';
            $user_priority = 1;
        } else if ($domainEmail === 'staff.ukdw.ac.id' && $input['prodi'] !== 'Lainnya' && $input['fakultas'] !== 'Lainnya' && $input['instansi'] === 'Lainnya'){
            $user_role = 'Dosen';
            $user_priority = 2;
        } else if ($domainEmail === 'staff.ukdw.ac.id' && $input['fakultas'] === 'Lainnya' && $input['prodi'] === 'Lainnya' && $input['instansi'] !== 'Lainnya'){
            $user_role = 'Staff';
            $user_priority = 2;
        } else {
            return;
        }

        $data = [
            'NIM_NIDN' => $input['NIM_NIDN'],
            'Email' => $input['email'],
            'User_role' => $user_role,
            'User_priority' => $user_priority
        ];
        $data['Password'] = Hash::make($input['password']);
        $user = User::create($data);
                    
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

    public function updateEmail(UpdateUserRequest $request, $UserID){
        $user = User::find($UserID);

        if ($user->id !== auth()->id()) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk memperbarui pengguna lain'], 403);
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $user->Email = $request->email;
        $user->save();

        return response()->json(['message' => 'Email berhasil diperbarui', 'data' => $user]);
    }

    public function updatePassword(UpdateUserRequest $request, $UserID){
        $user = User::find($UserID);

        if ($user->id !== auth()->id()) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk memperbarui pengguna lain'], 403);
        }

        $request->validate([
            'password' => 'required',
        ]);

        $user->Password = bcrypt($request->password);
        $user->save();

        return response()->json(['message' => 'Password berhasil diperbarui', 'data' => $user]);
    }
}
