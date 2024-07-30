<?php

use App\Http\Controllers\AboutEmailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\TokenWebController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/generate-pdf/{UserID}/{desiredPeminjamanID}/{peminjamanruanganid}/{peminjamanalatid}', [PDFController::class, 'generatePDF']);

Route::get('/{pathMatch}', function(){
    return view('welcome');
})->where('pathMatch', ".*");

Route::post('tokenweb', TokenWebController::class);