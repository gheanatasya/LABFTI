<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Http\Requests\StoreRuanganRequest;
use App\Http\Requests\UpdateRuanganRequest;
use App\Models\Peminjaman_Ruangan_Bridge;

class RuanganController extends Controller
{
    public function getAllRuangan()
    {
        return Ruangan::all();
    }
    //ambil data sesuai id
    public function show($RuanganID)
    {
        return Ruangan::find($RuanganID);
    }
    //tambah data
    public function store(StoreRuanganRequest $request)
    {
        $input = $request->all();
       /*  $fasilitasArray = $input['fasilitas']; */
       /*  $fasilitasPostgresArray = implode("', '", $fasilitasArray); */
       /*  $fasilitasPostgresArray = "{'$fasilitasPostgresArray'}"; */
        
        //return $input;
        $ruangan = Ruangan::create([
            'RuanganID' => $input['RuanganID'],
            'Nama_ruangan' => $input['Nama_ruangan'],
            'Kapasitas' => $input['Kapasitas'],
            'Lokasi' => $input['Lokasi'],
            'Kategori' => $input['Kategori'],
            'fasilitas' => $input['fasilitas'],
            'Foto' => $input['Foto'],
            'Status' => $input['Status']
        ]);

        $ruanganWithId = Ruangan::find($ruangan->RuanganID);

        return response()->json(['status' => true, 'message' => "Tambahkan Ruangan Success", 'data' => $ruanganWithId]);
    }
    //mengubah data
    public function update(UpdateRuanganRequest $request, Ruangan $RuanganID)
    {
        $RuanganID->update($request->all());
        return response()->json(['message' => 'Ruangan berhasil diperbarui', 'data' => $RuanganID]);
    }
    //hapus data
    public function delete($RuanganID)
    {
        $ruangan = Ruangan::find($RuanganID);
        $ruangan->Status = 'Tidak Tersedia';
        $ruangan->save();

        return response()->json(['message' => 'Ruangan berhasil dihapus'], 204);
    }

    //ambil data untuk grafik 
    public function totalPerbulan()
    {
        $daftarruangan = Ruangan::all();
        $fixData = [];

        foreach ($daftarruangan as $ruangan) {
            $ruanganid = $ruangan->RuanganID;
            $peminjamanruangan = Peminjaman_Ruangan_Bridge::where('RuanganID', $ruanganid)->get();
            $dataBulan = [];

            for ($i = 1; $i <= 12; $i++) {
                $bulanString = str_pad($i, 2, '0', STR_PAD_LEFT);
                $namaBulan = date('F', strtotime("01-$bulanString-2024"));

                $dataBulan[$bulanString] = [
                    'nama_bulan' => $namaBulan,
                    'jumlah_peminjaman' => 0,
                ];
            }

            foreach ($peminjamanruangan as $peminjaman) {
                $bulan = date('m', strtotime($peminjaman->Tanggal_pakai_awal));
                $dataBulan[$bulan]['jumlah_peminjaman']++;
            }

            ksort($dataBulan);

            $record = [
                'label' => $ruangan->Nama_ruangan,
                'dataperbulan' => $dataBulan
            ];

            $fixData[] = $record;
        }

        return $fixData;
    }
}
