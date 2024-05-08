<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
</head>
<body>
    <h2>Peminjaman Ruangan dan Alat LAB FTI UKDW</h2>
    @if ($role == "Mahasiswa" || $role == "Petugas")
        <div>
            <p>Peminjam : {{ $nama }}</p>
            <p>NIM : {{ $nim }}</p>
            <p>Fakultas : {{ $namafakultas }}</p>
            <p>Program Studi : {{ $namaprodi }}</p>
        </div>
    @elseif ($role == "Staf")
        <div>
            <p>Peminjam : {{ $nama }}</p>
            <p>NIDN : {{ $nim }}</p>
            <p>Instansi : {{ $namainstansi }}</p>
        </div>      
    @else 
        <div>
            <p>Peminjam : {{ $nama }}</p>
            <p>NIDN : {{ $nim }}</p>
            <p>Fakultas : {{ $namafakultas }}</p>
            <p>Program Studi : {{ $namaprodi }}</p>
        </div>
    @endif
    
    <p>Email : {{ $email }}</p>
    <p>Terimakasih telah melakukan peminjaman ruangan/alat pada LAB FTI Universitas Kristen Duta Wacana. Berikut ruangan/alat yang dipinjam dan telah disetujui:</p>

    @foreach ($peminjamanData as $record)    
    {{-- tampilkan peminjaman ruangan --}}
    <p><strong>Ruangan yang Dipinjam:</strong></p>
    <table>
        <tr>
            <th>No.</th>
            <th>Tanggal Pakai Awal</th>
            <th>Tanggal Pakai Akhir</th>
            <th>Ruangan</th>
        </tr>
        @foreach ($record['peminjamanRuang'] as $booking)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ $booking['tanggalawal'] }}</td>
            <td>{{ $booking['tanggalakhir'] }}</td>
            <td>{{ $booking['namaruangan'] }}</td>
        </tr>
        @endforeach
    </table>
    
    <br>
    {{-- Display equipment bookings --}}
    <p><strong>Alat yang Dipinjam:</strong></p>
    <table>
        <tr>
            <th>No.</th>
            <th>Tanggal Pakai Awal</th>
            <th>Tanggal Pakai Akhir</th>
            <th>Alat</th>
        </tr>
        @foreach ($record['peminjamanAlat'] as $booking)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ $booking['tanggalawal'] }}</td>
            <td>{{ $booking['tanggalakhir'] }}</td>
            <td>{{ $booking['namaalat'] }}</td>
        </tr>
        @endforeach
    </table>
@endforeach

    <div id="footer">
        <p>Tertanda, </p>
        <p>Laboratorium Fakultas Teknologi Informasi UKDW</p>
    </div>

</body>
</html>

<style>
    table {
        font-family:Georgia, 'Times New Roman', Times, serif;
        border-collapse: collapse;
        border: 1px solid black; 
    }

    th, td {
        border: 1px solid #ccc; 
        padding: 5px;
    }

    th {
        background-color: rgba( 2, 39, 10, 0.9);
        color: white;
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
</style>