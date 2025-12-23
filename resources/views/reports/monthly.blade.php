<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Bulanan</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .info {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .info p {
            margin: 0;
            font-weight: normal;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        th.right {
            text-align: right;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #dee2e6;
        }
        td.right {
            text-align: right;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Laporan Keuangan Bulanan</h2>
</div>

<div class="info">
    <p><strong>Bulan:</strong> {{ $month }}</p>
    <p><strong>Tahun:</strong> {{ $year }}</p>
</div>

<table>
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
                <td class="right">
                    Rp {{ number_format($trx->amount, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <p>Laporan ini dihasilkan secara otomatis pada {{ date('d-m-Y H:i:s') }}.</p>
    <p>Untuk pertanyaan lebih lanjut, hubungi departemen keuangan.</p>
</div>

</body>
</html>