<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Peminjaman Ruangan dan Alat FTI UKDW</title>
</head>

<body>
    <p>Peminjaman yang kamu lakukan pada LAB FTI Universitas Kristen Duta Wacana telah <strong>{{ strtoupper($dataEmail['namastatus']) }}</strong>
        oleh <strong>{{ strtoupper($dataEmail['role']) }}</strong>.</p>

    <p>Berikut peminjaman yang kamu lakukan: </p>
    @if (count($dataEmail['detailruangan']) > 0)
        <ol type="1">
            <li>
                <p>Ruangan : {{ $dataEmail['detailruangan']['namaruangan'] }}</p>
                <p>Tanggal Penggunaan : {{ $dataEmail['detailruangan']['tanggalawal'] }} -
                    {{ $dataEmail['detailruangan']['tanggalakhir'] }}</p>
                @if (count($dataEmail['addOnAlat']) > 0)
                    <p>Tambahan Alat : @foreach ($dataEmail['addOnAlat'] as $tambahan)
                            {{ $tambahan['namaalat'] }} {{ $tambahan['jumlahPinjam'] }}
                        @endforeach
                    </p>
                @endif
            </li>
        </ol>
    @elseif (count($dataEmail['detailruangan']) === 0)
        @if (count($dataEmail['addOnAlat']) === 1)
            <ol type="1">
                <li>
                    <p>Alat               : {{ $dataEmail['addOnAlat'][0]['namaalat'] }}</p>
                    <p>Tanggal Penggunaan : {{ $dataEmail['addOnAlat'][0]['tanggalawal'] }} - {{ $dataEmail['addOnAlat'][0]['tanggalakhir'] }}</p>
                </li>
            </ol>
        @elseif (count($dataEmail['addOnAlat']) > 1)
            <ol type="1">
                @foreach ($dataEmail['addOnAlat'] as $tambahan)
                    <li>
                        <p>Alat               : {{ $tambahan['namaalat'] }} {{ $tambahan['jumlahPinjam'] }}
                        </p>
                        <p>Tanggal Penggunaan : {{ $tambahan['tanggalawal'] }} - {{ $tambahan['tanggalakhir'] }}</p>
                    </li>
                @endforeach
            </ol>
        @endif
    @endif
    <br>
    <p>Laboratorium Fakultas Teknologi Informasi UKDW</p>
</body>

</html>
