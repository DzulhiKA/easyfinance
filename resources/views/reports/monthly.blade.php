<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan Bulanan</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }

        .info table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .info td {
            padding: 4px;
            font-size: 12px;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table.report th, table.report td {
            border: 1px solid #000;
            padding: 6px;
        }

        table.report th {
            background-color: #eee;
            font-weight: bold;
        }

        .right {
            text-align: right;
        }
    </style>
</head>
<body>

<div class="header">
    <h3>Laporan Keuangan Bulanan</h3>
</div>

<div class="info">
    <table>
        <tr>
            <td><strong>Bulan:</strong> {{ $month }}</td>
            <td><strong>Tahun:</strong> {{ $year }}</td>
        </tr>
    </table>
</div>

<table class="report">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Kategori</th>
            <th>Tipe</th>
            <th>Deskripsi</th>
            <th class="right">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $trx)
        <tr>
            <td>{{ $trx->date }}</td>
            <td>{{ $trx->category }}</td>
            <td>{{ ucfirst($trx->type) }}</td>
            <td>{{ $trx->description }}</td>
            <td class="right">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
