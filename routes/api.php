<?php

use App\Http\Controllers\AboutEmailController;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\DetailAlatController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PeminjamanAlatBridgeController;
use App\Http\Controllers\PeminjamanRuanganBridgeController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PeminjamController;
use App\Http\Controllers\PersetujuanController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\ProgramStudiController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\UserController;
use App\Models\Instansi;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('send', [NotificationController::class, 'sendnotification']);

//route login
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout']);

// route untuk fakultas
Route::get('/fakultas', [FakultasController::class, 'getAllFakultas']);
Route::get('/fakultas/{FakultasID}', [FakultasController::class, 'show']);
Route::post('/fakultas', [FakultasController::class, 'store']);
Route::put('/fakultas/{FakultasID}', [FakultasController::class, 'update']);
Route::delete('/fakultas/{FakultasID}', [FakultasController::class, 'delete']);
Route::get('/fakultas/getIdByName/{Nama_fakultas}', [FakultasController::class, 'getFakultasIDByName']);

// route untuk prodi
Route::get('/prodi', [ProgramStudiController::class, 'getAllProdi']);
Route::get('/prodi/{ProdiID}', [ProgramStudiController::class, 'show']);
Route::post('/prodi', [ProgramStudiController::class, 'store']);
Route::put('/prodi/{ProdiID}', [ProgramStudiController::class, 'update']);
Route::delete('/prodi/{ProdiID}', [ProgramStudiController::class, 'delete']);
Route::get('/prodi/getNameByFakultas/{FakultasID}', [ProgramStudiController::class, 'getProdiNameByFakultas']);
Route::get('/prodi/getProdiNameByID/{ProdiID}', [ProgramStudiController::class, 'getProdiNameByID']);
Route::get('/prodi/getFakultasNameByID/{FakultasID}', [ProgramStudiController::class, 'getFakultasNameByID']);
Route::get('/prodi/getIdByName/{Nama_prodi}', [ProgramStudiController::class, 'getProdiIDByName']);

// route untuk instansi
Route::get('/instansi', [InstansiController::class, 'getAllInstansi']);
Route::get('/instansi/{InstansiID}', [InstansiController::class, 'show']);
Route::post('/instansi', [InstansiController::class, 'store']);
Route::put('/instansi/{InstansiID}', [InstansiController::class, 'update']);
Route::delete('/instansi/{InstansiID}', [InstansiController::class, 'delete']);
Route::get('/instansi/getIdByName/{Nama_instansi}', [InstansiController::class, 'getInstansiIDByName']);
Route::get('/instansi/getInstansiNameByID/{InstansiID}', [InstansiController::class, 'getInstansiNameByID']);

// route untuk user
Route::get('/user', [UserController::class, 'getAllUser']);
Route::get('/user/{UserID}', [UserController::class, 'show']);
Route::post('/user', [UserController::class, 'store']);
Route::put('/user/{UserID}', [UserController::class, 'update']);
Route::delete('/user/{UserID}', [UserController::class, 'delete']);
Route::put('/userEmail/{UserID}', [UserController::class, 'updateEmail']);
Route::put('/userPassword/{UserID}', [UserController::class, 'updatePassword']);

// route untuk peminjam
Route::get('/peminjam', [PeminjamController::class, 'getAllPeminjam']);
Route::get('/peminjam/{PeminjamID}', [PeminjamController::class, 'show']);
Route::post('/peminjam', [PeminjamController::class, 'store']);
Route::put('/peminjam/{UserID}', [PeminjamController::class, 'update']);
Route::delete('/peminjam/{PeminjamID}', [PeminjamController::class, 'delete']);
Route::get('/peminjam/getIDByUserID/{UserID}', [PeminjamController::class, 'getIDByUserID']);
Route::get('/peminjam/byUserID/{UserID}', [PeminjamController::class, 'byUserID']);
Route::get('/peminjam/getDataforProfil/{UserID}', [PeminjamController::class, 'getDataforProfil']);
Route::post('/peminjam/resetTotalBatal', [PeminjamController::class, 'resetTotalBatal']);

// route untuk alat
Route::get('/alat', [AlatController::class, 'getAllAlat']);
Route::get('/alat/{AlatID}', [AlatController::class, 'show']);
Route::post('/alat', [AlatController::class, 'store']);
Route::put('/alat/{AlatID}', [AlatController::class, 'update']);
Route::delete('/alat/{AlatID}', [AlatController::class, 'delete']);
Route::get('/alatforDaftarAlat', [AlatController::class, 'forDaftarAlat']);
Route::get('/alattotalPerbulan', [AlatController::class, 'totalPerbulan']);


// route untuk detail alat
Route::get('/detail', [DetailAlatController::class, 'getAllDetailAlat']);
Route::get('/detail/{DetailAlatID}', [DetailAlatController::class, 'show']);
Route::post('/detail', [DetailAlatController::class, 'store']);
Route::put('/detail/{DetailAlatID}', [DetailAlatController::class, 'update']);
Route::delete('/detail/{DetailAlatID}', [DetailAlatController::class, 'delete']);
Route::post('/detail/tambahFoto/{DetailAlatID}', [DetailAlatController::class, 'tambahFoto']);

// route untuk ruangan
Route::get('/ruangan', [RuanganController::class, 'getAllRuangan']);
Route::get('/ruangan/{RuanganID}', [RuanganController::class, 'show']);
Route::post('/ruangan', [RuanganController::class, 'store']);
Route::put('/ruangan/{RuanganID}', [RuanganController::class, 'update']);
Route::delete('/ruangan/{RuanganID}', [RuanganController::class, 'delete']);
Route::get('/ruangantotalPerbulan', [RuanganController::class, 'totalPerbulan']);
Route::post('/ruangan/tambahFoto/{RuanganID}', [RuanganController::class, 'tambahFoto']);

//route untuk peminjaman
Route::get('/peminjaman', [PeminjamanController::class, 'getAllPeminjaman']);
Route::get('/peminjaman/{PeminjamanID}', [PeminjamanController::class, 'show']);
Route::post('/peminjaman', [PeminjamanController::class, 'store']);
Route::put('/peminjaman/{PeminjamanID}', [PeminjamanController::class, 'update']);
Route::delete('/peminjaman/{PeminjamanID}', [PeminjamanController::class, 'delete']);
Route::get('/peminjaman/getIDByPeminjamID/{PeminjamID}', [PeminjamanController::class, 'getIDByPeminjamID'])->middleware('throttle:500,1');
Route::get('/peminjaman/getAllPeminjamanByPeminjam/{PeminjamID}', [PeminjamanController::class, 'getAllPeminjamanByPeminjam'])->middleware('throttle:500,1');

//route untuk peminjaman alat bridge
Route::get('/peminjamanAlat', [PeminjamanAlatBridgeController::class, 'getAllPeminjamanAlat']);
Route::get('/peminjamanAlat/{Peminjaman_Alat_ID}', [PeminjamanAlatBridgeController::class, 'show']);
Route::post('/peminjamanAlat', [PeminjamanAlatBridgeController::class, 'store']);
Route::put('/peminjamanAlat/{Peminjaman_Alat_ID}', [PeminjamanAlatBridgeController::class, 'update']);
Route::delete('/peminjamanAlat/{Peminjaman_Alat_ID}', [PeminjamanAlatBridgeController::class, 'delete']);
Route::get('/peminjamanAlat/getNameByID/{AlatID}', [PeminjamanAlatBridgeController::class, 'getNameByID']);
Route::get('/peminjamanAlat/getKeterangan/{PeminjamanID}', [PeminjamanAlatBridgeController::class, 'getKeterangan'])->middleware('throttle:500,1');
Route::get('/peminjamanAlat/checkRelation/{PeminjamanID}', [PeminjamanAlatBridgeController::class, 'checkRelation']);
Route::get('/peminjamanAlat/getAllPeminjamanAlatByPeminjamanID/{PeminjamanID}', [PeminjamanAlatBridgeController::class, 'getAllPeminjamanAlatByPeminjamanID'])->middleware('throttle:500,1');
Route::get('/peminjamanAlat/getPeminjamanAlat/{UserID}', [PeminjamanAlatBridgeController::class, 'getPeminjamanAlat']);
Route::get('/peminjamanAlat/jadwalAlat/{Tanggal_pakai_awal}/{Tanggal_pakai_akhir}', [PeminjamanAlatBridgeController::class, 'jadwalAlat'])->middleware('throttle:500,1');
Route::get('/peminjamanAlat/checkAlat/{peminjamanid}', [PeminjamanAlatBridgeController::class, 'checkMoreTools'])->middleware('throttle:500,1');

//route untuk peminjaman ruangan bridge
Route::get('/peminjamanRuangan', [PeminjamanRuanganBridgeController::class, 'getAllPeminjamanRuangan']);
Route::get('/peminjamanRuangan/{Peminjaman_Ruangan_ID}', [PeminjamanRuanganBridgeController::class, 'show']);
Route::post('/peminjamanRuangan', [PeminjamanRuanganBridgeController::class, 'store']);
Route::put('/peminjamanRuangan/{Peminjaman_Ruangan_ID}', [PeminjamanRuanganBridgeController::class, 'update']);
Route::delete('/peminjamanRuangan/{Peminjaman_Ruangan_ID}', [PeminjamanRuanganBridgeController::class, 'delete']);
Route::get('/peminjamanRuangan/getAllPeminjamanRuanganByPeminjamanID/{PeminjamanID}', [PeminjamanRuanganBridgeController::class, 'getAllPeminjamanRuanganByPeminjamanID'])->middleware('throttle:500,1');
Route::get('/peminjamanRuangan/getNameByID/{RuanganID}', [PeminjamanRuanganBridgeController::class, 'getNameByID'])->middleware('throttle:500,1');
Route::get('/peminjamanRuangan/getKeterangan/{PeminjamanID}', [PeminjamanRuanganBridgeController::class, 'getKeterangan'])->middleware('throttle:500,1');
Route::get('/peminjamanRuangan/checkRelation/{PeminjamanID}', [PeminjamanRuanganBridgeController::class, 'checkRelation']);
Route::get('/peminjamanRuangan/jadwalPeminjaman/{Tanggal_pakai_awal}/{Tanggal_pakai_akhir}', [PeminjamanRuanganBridgeController::class, 'jadwalPeminjaman'])->middleware('throttle:500,1');
Route::get('/peminjamanRuangan/getPeminjamanRuangan/{UserID}', [PeminjamanRuanganBridgeController::class, 'getPeminjamanRuangan']);
Route::get('/getAllPeminjamanforAccRuangan', [PeminjamanRuanganBridgeController::class, 'getAllPeminjamanforAccRuangan']);
Route::get('/getAllPeminjamanforAccRuanganDekan', [PeminjamanRuanganBridgeController::class, 'getAllPeminjamanforAccRuanganDekan']);
Route::get('/getAllPeminjamanforAccRuanganWD2', [PeminjamanRuanganBridgeController::class, 'getAllPeminjamanforAccRuanganWD2']);
Route::get('/getAllPeminjamanforAccRuanganWD3', [PeminjamanRuanganBridgeController::class, 'getAllPeminjamanforAccRuanganWD3']);
Route::get('/getAllPeminjamanforAccAlat', [PeminjamanRuanganBridgeController::class, 'getAllPeminjamanforAccAlat']);
Route::get('/getAllPeminjamanforAccAlatDekan', [PeminjamanRuanganBridgeController::class, 'getAllPeminjamanforAccAlatDekan']);
Route::get('/getAllPeminjamanforAccAlatWD2', [PeminjamanRuanganBridgeController::class, 'getAllPeminjamanforAccAlatWD2']);
Route::get('/getAllPeminjamanforAccAlatWD3', [PeminjamanRuanganBridgeController::class, 'getAllPeminjamanforAccAlatWD3']);
Route::get('/getAllforPDF/{UserID}/{desiredPeminjamanID}/{peminjamanruanganid}', [PeminjamanRuanganBridgeController::class, 'generatePDF']);
Route::get('/getDataforCalender', [PeminjamanRuanganBridgeController::class, 'getDataforCalender']);

//route untuk petugas
Route::get('/petugas', [PetugasController::class, 'allPetugas']);
Route::put('/petugas/{UserID}', [PetugasController::class, 'update']);
Route::delete('/petugas/{UserID}', [PetugasController::class, 'delete']);
Route::post('/petugas', [PetugasController::class, 'store']);
Route::post('/petugas/tambahFoto', [PetugasController::class, 'tambahFoto']);

//route untuk persetujuan
Route::put('/persetujuan/confirmBookingRuangan/{Peminjaman_Ruangan_ID}/{User_role}/{NamaStatus}/{Catatan}', [PersetujuanController::class, 'confirmBookingRuangan']);
Route::put('/persetujuan/confirmBookingAlat/{Peminjaman_Alat_ID}/{User_role}/{NamaStatus}/{Catatan}', [PersetujuanController::class, 'confirmBookingAlat']);
Route::put('/persetujuan/confirmBookingAlat2/{Peminjaman_Alat_ID}/{User_role}/{NamaStatus}/{Catatan}', [PersetujuanController::class, 'confirmBookingAlat2']);
Route::get('/coba', [PersetujuanController::class, 'coba']);

//dokumen
Route::post('/dokumen', [DokumenController::class, 'forDokumenPeminjaman']);

//notifikasi
Route::get('/notifikasi/{UserID}', [NotificationController::class, 'getNotifications']);
Route::get('/notifikasiRead/{id}/{UserID}', [NotificationController::class, 'markAsRead']);
Route::get('/notifikasiTest/{Peminjaman_Ruangan_ID}', [NotificationController::class, 'testRuangan']);
Route::get('/notifikasiNewRoom/{Peminjaman_Ruangan_ID}', [NotificationController::class, 'newBookingNotificationRuangan']);
Route::get('/notifikasiNewTools/{Peminjaman_Alat_ID}', [NotificationController::class, 'newBookingNotificationAlat']);



