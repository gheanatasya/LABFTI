<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Http\Requests\StoreAlatRequest;
use App\Http\Requests\UpdateAlatRequest;
use App\Models\Detail_Alat;
use App\Models\Peminjaman_Alat_Bridge;
use Psy\Readline\Hoa\Console;

use function Laravel\Prompts\error;

class AlatController extends Controller
{
    public function getAllAlat()
    {
        $alat = Alat::all()->where('Status', 'Tersedia');
        $alatReturn = [];
        foreach ($alat as $tool){
            $gambar = [];
            $alatid = $tool->AlatID;
            $detail = Detail_Alat::where('AlatID', $alatid)->get();
            foreach ($detail as $det){
                if ($det->Foto !== null){
                    $image = explode(':', $det->Foto);
                    $gambar[] = $image;
                }
            }

            $gambarGabung = [];
            foreach ($gambar as $img){
                $gambarGabung = array_merge($gambarGabung, $img);
            }
            
            $fixRecord = [
                'AlatID' => $tool->AlatID,
                'Nama' => $tool->Nama,
                'Status' => $tool->Status,
                'Jumlah_ketersediaan' => $tool->Jumlah_ketersediaan,
                'Foto' => $gambarGabung
            ];

            $alatReturn[] = $fixRecord;
        }

        return $alatReturn;
    }

    //ambil data sesuai id
    public function show($AlatID)
    {
        return Alat::find($AlatID);
    }

    //tambah data
    public function store(StoreAlatRequest $request)
    {
        $input = $request->all();
        Alat::create([
            'AlatID' => $input['kodeAlat'],
            'Nama' => $input['namaAlat'],
            'Status' => $input['statusAlat'],
            'Jumlah_ketersediaan' => 0
        ]);
        return response()->json(['status' => true, 'message' => "Tambahkan Alat Success"]);
    }

    //edit data alat 
    public function update(UpdateAlatRequest $request, $AlatID)
    {
        $alat = Alat::where('AlatID', $AlatID)->first();

        if ($alat === null) {
            return response()->json(['error' => 'Alat not found'], 404);
        }

        $request->validate([
            'namaAlat' => 'required',
            'statusAlat' => 'required',
        ]);

        $alat->Nama = $request->get('namaAlat');
        $alat->Status = $request->get('statusAlat');
        $alat->save();

        return response()->json(['message' => 'Alat berhasil diperbarui', 'data' => $alat]);
    }

    //hapus data alat
    public function delete($AlatID)
    {
        $alat = Alat::find($AlatID);
        $detailAlat = Detail_Alat::where('AlatID', $AlatID)->get();
        $semuaRusak = true;

        foreach ($detailAlat as $detail) {
            if ($detail->Status_Kebergunaan != 'Rusak') {
                $semuaRusak = false;
                break;
            }
        }

        if ($semuaRusak) {
            $alat->Status = 'Tidak Tersedia';
            $alat->save();
            return response()->json(['message' => 'Alat berhasil dihapus'], 204);
        } else {
            return 'Gagal';
        }
    }

    //data detail alat untuk halaman daftar alat
    public function forDaftarAlat()
    {
        $alat = Alat::all();
        $semuaData = [];

        foreach ($alat as $tool) {
            $namaAlat = $tool->Nama;
            $kodeAlat = $tool->AlatID;
            $statusAlat = $tool->Status;
            $detailAlat = Detail_Alat::where('AlatID', $kodeAlat)->get();
            $jumlahAlat = count($detailAlat);
            $jumlahRusak = $detailAlat->where('Status_Kebergunaan', 'Rusak')->count();
            $recordData = [
                'Nama' => $namaAlat,
                'KodeAlat' => $kodeAlat,
                'JumlahAlat' => $jumlahAlat,
                'JumlahRusak' => $jumlahRusak,
                'StatusAlat' => $statusAlat,
                'detailAlat' => [],
            ];

            foreach ($detailAlat as $detail) {
                $kodeDetailAlat = $detail->DetailAlatID;
                $namaDetailAlat = $detail->Nama_alat;
                $statusKebergunaan = $detail->Status_Kebergunaan;
                $statusPeminjaman = $detail->Status_Peminjaman;
                $foto = $detail->Foto;

                $recordPerDetail = [
                    'KodeDetailAlat' => $kodeDetailAlat,
                    'NamaDetailAlat' => $namaDetailAlat,
                    'StatusKebergunaan' => $statusKebergunaan,
                    'StatusPeminjaman' => $statusPeminjaman,
                    'Foto' => $foto
                ];

                $recordData['detailAlat'][] = $recordPerDetail;
            }

            $semuaData[] = $recordData;
        }

        return $semuaData;
    }

    //ambil data jumlah peminjaman perbulan
    public function totalPerbulan()
    {
        $daftaralat = Alat::all();
        $fixData = [];

        foreach ($daftaralat as $alat) {
            $alatid = $alat->AlatID;
            $peminjamanalat = Peminjaman_Alat_Bridge::where('AlatID', $alatid)->get();
            $dataBulan = [];

            for ($i = 1; $i <= 12; $i++) {
                $bulanString = str_pad($i, 2, '0', STR_PAD_LEFT); 
                $namaBulan = date('F', strtotime("01-$bulanString-2024")); 
            
                $dataBulan[$bulanString] = [
                    'nama_bulan' => $namaBulan,
                    'jumlah_peminjaman' => 0,
                ];
            }
            
            foreach ($peminjamanalat as $peminjaman) {
                $bulan = date('m', strtotime($peminjaman->Tanggal_pakai_awal));            
                $dataBulan[$bulan]['jumlah_peminjaman']++;
            }
            
            ksort($dataBulan);

            $record = [
                'label' => $alat->Nama,
                'dataperbulan' => $dataBulan
            ];

            $fixData[] = $record;
        }

        return $fixData;
    }
}
