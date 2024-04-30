<?php

namespace App\Http\Controllers;

use App\Models\Program_Studi;
use App\Http\Requests\StoreProgram_StudiRequest;
use App\Http\Requests\UpdateProgram_StudiRequest;
use App\Http\Controllers\FakultasController;
use App\Models\Fakultas;

class ProgramStudiController extends Controller
{
    // mengambil semua data pada db
    public function getAllProdi()
    {
        return Program_Studi::all();
    }
    //ambil data sesuai id
    public function show($ProdiID)
    {
        return Program_Studi::find($ProdiID);
    }
    //tambah data
    public function store(StoreProgram_StudiRequest $request)
    {
        $prodi = Program_Studi::create($request->only(['ProdiID', 'Nama_prodi', 'FakultasID']));
        if ($prodi) {
            return response()->json(['message' => 'Program studi berhasil ditambahkan', 'data' => $prodi], 201);
        } else {
            return response()->json(['message' => 'Program studi gagal ditambahkan'], 500);
        }
    }
    //mengubah data
    public function update(UpdateProgram_StudiRequest $request, Program_Studi $ProdiID)
    {
        $ProdiID->update($request->all());
        return response()->json(['message' => 'Program studi berhasil diperbarui', 'data' => $ProdiID]);
    }
    //hapus data
    public function delete($ProdiID)
    {
        $prodi = Program_Studi::find($ProdiID);
        $prodi->delete();
        return response()->json(['message' => 'Program studi berhasil dihapus'], 204);
    }

    public function getProdiIDByName($Nama_prodi)
    {
        $prodi = Program_Studi::where('Nama_prodi', $Nama_prodi)->first();
        if ($prodi) {
            return response()->json(['ProdiID' => $prodi->ProdiID]);
        } else {
            return response()->json(['Error' => 'Prodi not found'], 404);
        }
    }

    public function getProdiNameByFakultas($FakultasID)
    {
        try {
            // Mengambil nama-nama program studi berdasarkan FakultasID
            $prodi = Program_Studi::where('FakultasID', $FakultasID)->pluck('Nama_prodi');
            
            // Jika berhasil, kirimkan nama-nama program studi sebagai respons
            return response()->json($prodi);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, kirimkan pesan error
            return response()->json(['error' => 'Failed to fetch Prodi names'], 500);
        }
    }

    public function getProdiNameByID($ProdiID){
        $prodi = Program_Studi::where('ProdiID', $ProdiID)->first();
        if ($prodi) {
            return response()->json(['Nama_prodi' => $prodi->Nama_prodi, 'FakultasID' => $prodi->FakultasID]);
        } else {
            return response()->json(['Error' => 'Prodi not found'], 404);
        }
    }

    public function getFakultasNameByID($FakultasID){
        $fakultas = Fakultas::where('FakultasID', $FakultasID)->first();
        if ($fakultas) {
            return response()->json(['Nama_fakultas' => $fakultas->Nama_fakultas]);
        } else {
            return response()->json(['Error' => 'Fakultas not found'], 404);
        }
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    /*   public function store(StoreProgram_StudiRequest $request) */
    /*   { */
    /*        */
    /*   } */

    /**
     * Display the specified resource.
     */
    /*   public function show(Program_Studi $program_Studi) */
    /*   { */
    /*       */
    /*   } */

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program_Studi $program_Studi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    /* public function update(UpdateProgram_StudiRequest $request, Program_Studi $program_Studi) */
    /* { */
    /*      */
    /* } */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program_Studi $program_Studi)
    {
        //
    }
}
