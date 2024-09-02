<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Http\Requests\StoreAlatRequest;
use App\Http\Requests\UpdateAlatRequest;
use App\Models\Detail_Alat;
use App\Models\Peminjaman_Alat_Bridge;
use Google\Service\CloudSearch\Id;
use Psy\Readline\Hoa\Console;

use function Laravel\Prompts\error;

class AlatController extends Controller
{
    public function getAllAlat()
    {
        $alat = Alat::all()->where('Status', 'Tersedia');
        $alatReturn = [];
        foreach ($alat as $tool) {
            $gambar = [];
            $alatid = $tool->AlatID;
            $detail = Detail_Alat::where('AlatID', $alatid)->get();
            foreach ($detail as $det) {
                if ($det->Foto !== null) {
                    $image = explode(':', $det->Foto);
                    $gambar[] = $image;
                }
            }

            $gambarGabung = [];
            foreach ($gambar as $img) {
                $gambarGabung = array_merge($gambarGabung, $img);
            }

            $fixRecord = [
                'AlatID' => $tool->AlatID,
                'KodeAlat' => $tool->KodeAlat,
                'Nama' => $tool->Nama,
                'Status' => $tool->Status,
                'Jumlah_ketersediaan' => $tool->Jumlah_ketersediaan,
                'Foto' => $gambarGabung,
                'WajibSurat' => $tool->WajibSurat
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
        if ($input['wajibSurat'] === 'Perlu Surat') {
            $surat = true;
        } elseif ($input['wajibSurat'] === 'Tidak Perlu Surat') {
            $surat = false;
        }

        $alat = Alat::create([
            'KodeAlat' => $input['kodeAlat'],
            'Nama' => $input['namaAlat'],
            'Status' => $input['statusAlat'],
            'Jumlah_ketersediaan' => 0,
            'WajibSurat' => $surat
        ]);

        $newalat = [
            'Nama' => $alat->Nama,
            'KodeAlat' => $alat->KodeAlat,
            'AlatID' => $alat->AlatID,
            'JumlahAlat' => 0,
            'JumlahRusak' => 0,
            'StatusAlat' => $alat->Status,
            'detailAlat' => [],
            'WajibSurat' => $input['wajibSurat']
        ];

        return response()->json(['status' => true, 'message' => "Tambahkan Alat Success", 'data' => $newalat]);
    }

    //edit data alat 
    public function update(UpdateAlatRequest $request, $AlatID)
    {
        $alat = Alat::where('AlatID', $AlatID)->first();
        $detailAlat = Detail_Alat::where('AlatID', $AlatID)->get();
        $jumlahAlat = count($detailAlat);
        $jumlahRusak = $detailAlat->where('Status_Kebergunaan', 'Rusak')->count();

        if ($alat === null) {
            return response()->json(['error' => 'Alat not found'], 404);
        }

        if ($request['wajibSurat'] === 'Perlu Surat') {
            $surat = true;
        } elseif ($request['wajibSurat'] === 'Tidak Perlu Surat') {
            $surat = false;
        }

        $request->validate([
            'namaAlat' => 'required',
            'statusAlat' => 'required',
            'kodeAlat' => 'required',
            'wajibSurat' => 'required'
        ]);

        $alat->Nama = $request->get('namaAlat');
        $alat->Status = $request->get('statusAlat');
        $alat->KodeAlat = $request->get('kodeAlat');
        $alat->WajibSurat = $surat;
        $alat->save();

        $newalat = [
            'Nama' => $alat->Nama,
            'KodeAlat' => $alat->KodeAlat,
            'AlatID' => $alat->AlatID,
            'JumlahAlat' => $jumlahAlat,
            'JumlahRusak' => $jumlahRusak,
            'StatusAlat' => $alat->Status,
            'detailAlat' => [],
            'WajibSurat' => $request['wajibSurat']
        ];

        foreach ($detailAlat as $detail) {
            $DetailAlatID = $detail->DetailAlatID;
            $kodeDetailAlat = $detail->KodeDetailAlat;
            $namaDetailAlat = $detail->Nama_alat;
            $statusKebergunaan = $detail->Status_Kebergunaan;
            $statusPeminjaman = $detail->Status_Peminjaman;
            $foto = $detail->Foto;

            $recordPerDetail = [
                'KodeDetailAlat' => $kodeDetailAlat,
                'DetailAlatID' => $DetailAlatID,
                'NamaDetailAlat' => $namaDetailAlat,
                'StatusKebergunaan' => $statusKebergunaan,
                'StatusPeminjaman' => $statusPeminjaman,
                'Foto' => $foto,
                'AlatID' => $detail->AlatID
            ];

            $newalat['detailAlat'][] = $recordPerDetail;
        }

        return response()->json(['message' => 'Alat berhasil diperbarui', 'data' => $newalat]);
    }

    //hapus data alat
    public function delete($AlatID)
    {
        $alat = Alat::find($AlatID);
        $detailAlat = Detail_Alat::where('AlatID', $AlatID)->get();
        $jumlahAlat = count($detailAlat);
        $jumlahRusak = $detailAlat->where('Status_Kebergunaan', 'Rusak')->count();
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

            if ($alat->WajibSurat === true) {
                $surat = 'Perlu Surat';
            } elseif ($alat->WajibSurat === false) {
                $surat = 'Tidak Perlu Surat';
            }

            $newalat = [
                'Nama' => $alat->Nama,
                'KodeAlat' => $alat->KodeAlat,
                'AlatID' => $alat->AlatID,
                'JumlahAlat' => $jumlahAlat,
                'JumlahRusak' => $jumlahRusak,
                'StatusAlat' => $alat->Status,
                'detailAlat' => [],
                'WajibSurat' => $surat
            ];

            foreach ($detailAlat as $detail) {
                $DetailAlatID = $detail->DetailAlatID;
                $kodeDetailAlat = $detail->KodeDetailAlat;
                $namaDetailAlat = $detail->Nama_alat;
                $statusKebergunaan = $detail->Status_Kebergunaan;
                $statusPeminjaman = $detail->Status_Peminjaman;
                $foto = $detail->Foto;

                $recordPerDetail = [
                    'KodeDetailAlat' => $kodeDetailAlat,
                    'DetailAlatID' => $DetailAlatID,
                    'NamaDetailAlat' => $namaDetailAlat,
                    'StatusKebergunaan' => $statusKebergunaan,
                    'StatusPeminjaman' => $statusPeminjaman,
                    'Foto' => $foto,
                    'AlatID' => $detail->AlatID
                ];

                $newalat['detailAlat'][] = $recordPerDetail;
            }
            return response()->json(['message' => 'Alat berhasil dihapus', 'data' => $newalat]);
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
            $AlatID = $tool->AlatID;
            $kodeAlat = $tool->KodeAlat;
            $statusAlat = $tool->Status;
            $detailAlat = Detail_Alat::where('AlatID', $AlatID)->get();
            $jumlahAlat = count($detailAlat);
            $jumlahRusak = $detailAlat->where('Status_Kebergunaan', 'Rusak')->count();
            $surat = $tool->WajibSurat;
            if ($surat === true) {
                $wajibSurat = 'Perlu Surat';
            } elseif ($surat === false) {
                $wajibSurat = 'Tidak Perlu Surat';
            }
            $recordData = [
                'Nama' => $namaAlat,
                'KodeAlat' => $kodeAlat,
                'AlatID' => $AlatID,
                'JumlahAlat' => $jumlahAlat,
                'JumlahRusak' => $jumlahRusak,
                'StatusAlat' => $statusAlat,
                'detailAlat' => [],
                'WajibSurat' => $wajibSurat
            ];

            foreach ($detailAlat as $detail) {
                $DetailAlatID = $detail->DetailAlatID;
                $kodeDetailAlat = $detail->KodeDetailAlat;
                $namaDetailAlat = $detail->Nama_alat;
                $statusKebergunaan = $detail->Status_Kebergunaan;
                $statusPeminjaman = $detail->Status_Peminjaman;
                $foto = $detail->Foto;

                $recordPerDetail = [
                    'KodeDetailAlat' => $kodeDetailAlat,
                    'DetailAlatID' => $DetailAlatID,
                    'NamaDetailAlat' => $namaDetailAlat,
                    'StatusKebergunaan' => $statusKebergunaan,
                    'StatusPeminjaman' => $statusPeminjaman,
                    'Foto' => $foto,
                    'AlatID' => $detail->AlatID
                ];

                $recordData['detailAlat'][] = $recordPerDetail;
            }

            $semuaData[] = $recordData;
        }

        usort($semuaData, function ($a, $b) {
            return strcmp($a['Nama'], $b['Nama']);
        });

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
