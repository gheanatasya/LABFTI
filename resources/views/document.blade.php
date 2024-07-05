<!DOCTYPE html>
<html>

<head>
    <title>{{ $title }}</title>
</head>

<body>
    <h2>Peminjaman Ruangan dan Alat LAB FTI UKDW</h2>
    @if (count($peminjamanDataRuangan) > 0)
        @if ($role == 'Mahasiswa' || $role == 'Petugas')
            @if ($peminjamanDataRuangan['organisasi'] === true)
                <div>
                    <p>Peminjam : {{ $nama }} (Digunakan sebagai organisasi)</p>
                    <p>NIM : {{ $nim }}</p>
                    <p>Fakultas : {{ $namafakultas }}</p>
                    <p>Program Studi : {{ $namaprodi }}</p>
                </div>
            @elseif ($peminjamanDataRuangan['eksternal'] === true)
                <div>
                    <p>Peminjam : {{ $nama }} (Digunakan dengan pihak eksternal)</p>
                    <p>NIM : {{ $nim }}</p>
                    <p>Fakultas : {{ $namafakultas }}</p>
                    <p>Program Studi : {{ $namaprodi }}</p>
                </div>
            @elseif ($peminjamanDataRuangan['personal'] === true)
                <div>
                    <p>Peminjam : {{ $nama }}</p>
                    <p>NIM : {{ $nim }}</p>
                    <p>Fakultas : {{ $namafakultas }}</p>
                    <p>Program Studi : {{ $namaprodi }}</p>
                </div>
            @endif
        @elseif ($role == 'Staff')
            @if ($peminjamanDataRuangan['organisasi'] === true)
                <div>
                    <p>Peminjam : {{ $nama }} (Digunakan sebagai organisasi)</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Instansi : {{ $namainstansi }}</p>
                </div>
            @elseif ($peminjamanDataRuangan['eksternal'] === true)
                <div>
                    <p>Peminjam : {{ $nama }} (Digunakan dengan pihak eksternal)</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Instansi : {{ $namainstansi }}</p>
                </div>
            @elseif ($peminjamanDataRuangan['personal'] === true)
                <div>
                    <p>Peminjam : {{ $nama }}</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Instansi : {{ $namainstansi }}</p>
                </div>
            @else
                <div>
                    <p>Peminjam : {{ $nama }}</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Instansi : {{ $namainstansi }}</p>
                </div>
            @endif
        @else
            @if ($peminjamanDataRuangan['organisasi'] === true)
                <div>
                    <p>Peminjam : {{ $nama }} (Digunakan sebagai organisasi)</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Fakultas : {{ $namafakultas }}</p>
                    <p>Program Studi : {{ $namaprodi }}</p>
                </div>
            @elseif ($peminjamanDataRuangan['eksternal'] === true)
                <div>
                    <p>Peminjam : {{ $nama }} (Digunakan dengan pihak eksternal)</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Fakultas : {{ $namafakultas }}</p>
                    <p>Program Studi : {{ $namaprodi }}</p>
                </div>
            @elseif ($peminjamanDataRuangan['personal'] === true)
                <div>
                    <p>Peminjam : {{ $nama }}</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Fakultas : {{ $namafakultas }}</p>
                    <p>Program Studi : {{ $namaprodi }}</p>
                </div>
            @else
                <div>
                    <p>Peminjam : {{ $nama }}</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Fakultas : {{ $namafakultas }}</p>
                    <p>Program Studi : {{ $namaprodi }}</p>
                </div>
            @endif
        @endif
    @elseif (count($peminjamanDataAlat) > 0)
        @if ($role == 'Mahasiswa' || $role == 'Petugas')
            @if ($peminjamanDataAlat[0]['organisasi'] === true)
                <div>
                    <p>Peminjam : {{ $nama }} (Digunakan sebagai organisasi)</p>
                    <p>NIM : {{ $nim }}</p>
                    <p>Fakultas : {{ $namafakultas }}</p>
                    <p>Program Studi : {{ $namaprodi }}</p>
                </div>
            @elseif ($peminjamanDataAlat[0]['eksternal'] === true)
                <div>
                    <p>Peminjam : {{ $nama }} (Digunakan dengan pihak eksternal)</p>
                    <p>NIM : {{ $nim }}</p>
                    <p>Fakultas : {{ $namafakultas }}</p>
                    <p>Program Studi : {{ $namaprodi }}</p>
                </div>
            @elseif ($peminjamanDataAlat[0]['personal'] === true)
                <div>
                    <p>Peminjam : {{ $nama }}</p>
                    <p>NIM : {{ $nim }}</p>
                    <p>Fakultas : {{ $namafakultas }}</p>
                    <p>Program Studi : {{ $namaprodi }}</p>
                </div>
            @endif
        @elseif ($role == 'Staff')
            @if ($peminjamanDataAlat[0]['organisasi'] === true)
                <div>
                    <p>Peminjam : {{ $nama }} (Digunakan sebagai organisasi)</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Instansi : {{ $namainstansi }}</p>
                </div>
            @elseif ($peminjamanDataAlat[0]['eksternal'] === true)
                <div>
                    <p>Peminjam : {{ $nama }} (Digunakan dengan pihak eksternal)</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Instansi : {{ $namainstansi }}</p>
                </div>
            @elseif ($peminjamanDataAlat[0]['personal'] === true)
                <div>
                    <p>Peminjam : {{ $nama }}</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Instansi : {{ $namainstansi }}</p>
                </div>
            @else
                <div>
                    <p>Peminjam : {{ $nama }}</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Instansi : {{ $namainstansi }}</p>
                </div>
            @endif
        @else
            @if ($peminjamanDataAlat[0]['organisasi'] === true)
                <div>
                    <p>Peminjam : {{ $nama }} (Digunakan sebagai organisasi)</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Fakultas : {{ $namafakultas }}</p>
                    <p>Program Studi : {{ $namaprodi }}</p>
                </div>
            @elseif ($peminjamanDataAlat[0]['eksternal'] === true)
                <div>
                    <p>Peminjam : {{ $nama }} (Digunakan dengan pihak eksternal)</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Fakultas : {{ $namafakultas }}</p>
                    <p>Program Studi : {{ $namaprodi }}</p>
                </div>
            @elseif ($peminjamanDataAlat[0]['personal'] === true)
                <div>
                    <p>Peminjam : {{ $nama }}</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Fakultas : {{ $namafakultas }}</p>
                    <p>Program Studi : {{ $namaprodi }}</p>
                </div>
            @else
                <div>
                    <p>Peminjam : {{ $nama }}</p>
                    <p>NIDN : {{ $nim }}</p>
                    <p>Fakultas : {{ $namafakultas }}</p>
                    <p>Program Studi : {{ $namaprodi }}</p>
                </div>
            @endif
        @endif
    @endif

    <p>Email : {{ $email }}</p>
    <p>Terimakasih telah melakukan peminjaman ruangan/alat pada LAB FTI Universitas Kristen Duta Wacana. Berikut
        ruangan/alat yang dipinjam dan telah disetujui:</p>

    {{-- tampilkan peminjaman ruangan dan alat --}}
    @if ($peminjamanDataRuangan != null && $peminjamanDataAlat != null)
        <p><strong>Ruangan dan Alat yang Dipinjam:</strong></p>
        <table>
            <tr>
                <th>No.</th>
                <th>Tanggal Penggunaan</th>
                <th>Ruangan</th>
                <th>Tambahan Alat</th>
                <th>Keterangan</th>
            </tr>
            <tr>
                <td id="tengah">1</td>
                <td>{{ $peminjamanDataRuangan['tanggalawal'] }} - {{ $peminjamanDataRuangan['tanggalakhir'] }} </td>
                <td>{{ $peminjamanDataRuangan['namaruangan'] }}</td>
                <td>
                    @foreach ($peminjamanDataAlat as $booking)
                        {{ $booking['namaalat'] }} ({{ $booking['jumlahPinjam'] }})<br>
                    @endforeach
                </td>
                <td>{{ $peminjamanDataRuangan['keterangan'] }}</td>
            </tr>
        </table>
        <p id="notes">*Harap menunjukkan file ini kepada petugas yang sedang bertugas ketika selesai menggunakan
            ruangan dan alat</p>
        <br>

        {{-- tampilkan peminjaman alat --}}
    @elseif ($peminjamanDataAlat != null && count($peminjamanDataRuangan) == 0)
        <p><strong>Alat yang Dipinjam:</strong></p>
        <table>
            <tr>
                <th>No.</th>
                <th>Tanggal Penggunaan</th>
                <th>Alat</th>
                <th>Jumlah Pinjam</th>
                <th>Keterangan</th>
            </tr>
            <tr>
                <td id="tengah">1</td>
                <td>{{ $peminjamanDataAlat[0]['tanggalawalAlat'] }} - {{ $peminjamanDataAlat[0]['tanggalakhirAlat'] }}
                </td>
                <td>
                    @foreach ($peminjamanDataAlat as $booking)
                        {{ $booking['namaalat'] }}<br>
                    @endforeach
                </td>
                <td id="tengah">
                    @foreach ($peminjamanDataAlat as $booking)
                        {{ $booking['jumlahPinjam'] }}<br>
                    @endforeach
                </td>

                <td>{{ $peminjamanDataAlat[0]['keteranganAlat'] }}</td>
            </tr>
        </table>
        <p id="notes">*Harap menunjukkan file ini kepada petugas yang sedang bertugas ketika selesai menggunakan alat
        </p>
        <br>

        {{-- tampilkan peminjaman ruangan --}}
    @elseif (count($peminjamanDataAlat) == 0 && $peminjamanDataRuangan != null)
        <p><strong>Ruangan yang Dipinjam:</strong></p>
        <table>
            <tr>
                <th>No.</th>
                <th>Tanggal Penggunaan</th>
                <th>Ruangan</th>
                <th>Keterangan</th>
            </tr>
            <tr>
                <td id="tengah">1</td>
                <td>{{ $peminjamanDataRuangan['tanggalawal'] }} - {{ $peminjamanDataRuangan['tanggalakhir'] }} </td>
                <td>{{ $peminjamanDataRuangan['namaruangan'] }}</td>
                <td>{{ $peminjamanDataRuangan['keterangan'] }}</td>
            </tr>
        </table>
        <p id="notes">*Harap menunjukkan file ini kepada petugas yang sedang bertugas ketika selesai menggunakan
            ruangan</p>
    @endif

    <br>
    <div id="footer">
        <p>Yogyakarta, {{ $tanggaldownload }} </p>
        <p>Laboratorium Fakultas Teknologi Informasi UKDW</p>
    </div>

</body>

</html>

<style>
    table {
        font-family: Georgia, 'Times New Roman', Times, serif;
        border-collapse: collapse;
        border: 1px solid black;
        width: 100%;
    }

    th,
    td {
        border: 1px solid black;
        padding: 5px;
    }

    th {
        background-color: white;
        color: black;
        text-align: center;
        font-size: 15px;
    }

    #footer {
        text-align: right;
    }

    td {
        text-align: left;
    }

    h2 {
        text-align: center;
    }

    #tengah {
        text-align: center;
    }

    #notes {
        font-size: 12px;
        font-style: italic;
    }
</style>
