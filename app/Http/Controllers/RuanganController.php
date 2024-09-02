<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Http\Requests\StoreRuanganRequest;
use App\Http\Requests\UpdateRuanganRequest;
use App\Models\Peminjaman_Ruangan_Bridge;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Storage;

class RuanganController extends Controller
{
    public function getAllRuangan()
    {
        $ruangan = Ruangan::all();
        $ruanganReturn = [];

        foreach ($ruangan as $room) {
            if ($room->Foto !== null) {
                $gambar = explode(':', $room->Foto);
                $room->Foto = $gambar;
            }
    
            $fixRecord = [
                'RuanganID' => $room->RuanganID,
                'Kapasitas' => $room->Kapasitas,
                'Lokasi' => $room->Lokasi,
                'Kategori' => $room->Kategori,
                'Nama_ruangan' => $room->Nama_ruangan,
                'fasilitas' => $room->fasilitas,
                'Status' => $room->Status,
                'Foto' => $room->Foto
            ];

            $ruanganReturn[] = $fixRecord;
        }

        usort($ruanganReturn, function($a, $b) {
            return strcmp($a['Nama_ruangan'], $b['Nama_ruangan']);
        });
    
        return $ruanganReturn;
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
        /* $fasilitasArray = $input['fasilitas'];
        $fasilitasPostgresArray = explode(', ', $fasilitasArray);
        return $fasilitasPostgresArray; */

        $ruangan = Ruangan::create([
            'Nama_ruangan' => $input['Nama_ruangan'],
            'Kapasitas' => $input['Kapasitas'],
            'Lokasi' => $input['Lokasi'],
            'Kategori' => $input['Kategori'],
            'fasilitas' => $input['fasilitas'],
            'Foto' => null,
            'Status' => $input['Status']
        ]);

        $ruanganWithId = Ruangan::find($ruangan->RuanganID);

        return response()->json(['status' => true, 'message' => "Tambahkan Ruangan Success", 'data' => $ruanganWithId, 'ruanganid' => $ruangan->RuanganID]);
    }
    //mengubah data
    public function update(UpdateRuanganRequest $request, $RuanganID)
    {
        $ruangan = Ruangan::where('RuanganID', $RuanganID)->first();

        if ($ruangan === null) {
            return response()->json(['error' => 'Ruangan not found'], 404);
        }

        $request->validate([
            'RuanganID' => 'required',
            'Nama_ruangan' => 'required',
            'Kapasitas' => 'required',
            'Lokasi' => 'required',
            'Kategori' => 'required',
            'updatebaru' => 'required',
            'Status' => 'required'
        ]);

        $ruangan->RuanganID = $request->get('RuanganID');
        $ruangan->Nama_ruangan = $request->get('Nama_ruangan');
        $ruangan->Kapasitas = $request->get('Kapasitas');
        $ruangan->Lokasi = $request->get('Lokasi');
        $ruangan->Kategori = $request->get('Kategori');
        $ruangan->fasilitas = $request->get('updatebaru');
        $ruangan->Status = $request->get('Status');
        $ruangan->save();

        return response()->json(['message' => 'Ruangan berhasil diperbarui', 'data' => $ruangan]);
    }
    //hapus data
    public function delete($RuanganID)
    {
        $ruangan = Ruangan::find($RuanganID);
        $ruangan->Status = 'Tidak Tersedia';
        $ruangan->save();

        return response()->json(['message' => 'Ruangan berhasil dihapus', 'dataHapus' => $ruangan]);
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

    public function tambahFoto(StoreRuanganRequest $request, $RuanganID)
    {
        $data = $request->file('foto');
        $namaData = [];

        $ruangan = Ruangan::where('RuanganID', $RuanganID)->first();
        $namaruangan = $ruangan->Nama_ruangan;
        $directory = 'ruangan/' . $namaruangan;

        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        foreach ($data as $foto) {
            $fileInfo = [
                'originalName' => $foto->getClientOriginalName(),
                'size' => $foto->getSize(),
                'mimeType' => $foto->getClientMimeType(),
            ];

            $path = Storage::putFileAs($directory, $foto, $fileInfo['originalName']);
            $namaData[] = $path;
        };

        $namaDataString = implode(':', $namaData);
        $ruangan->Foto = $namaDataString;
        $ruangan->save();

        return response()->json(['message' => 'File uploaded successfully!', 'dataTambah' => $ruangan]);
    }
}
