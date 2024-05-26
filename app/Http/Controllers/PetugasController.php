<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use App\Http\Requests\StorePetugasRequest;
use App\Http\Requests\UpdatePetugasRequest;

class PetugasController extends Controller
{
    // data untuk halaman petugas
    public function allPetugas(){
        $semuaPetugas = Petugas::all();
        $allRecordPetugas = [];

        foreach ($semuaPetugas as $petugas){
            
        }
    }

}
