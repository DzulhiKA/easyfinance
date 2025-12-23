<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tahunan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
        }
        th {
            background: #f2f2f2;
        }
        .right {
            text-align: right;
        }
    </style>
</head>
<body>

<h2>Laporan Keuangan Tahunan</h2>
<p>Tahun: {{ $year }}</p>

<table>
    <thead>
        <tr>
            <th>Bulan</th>
            <th class="right">Income</th>
            <th class="right">Expense</th>
            <th class="right">Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
            <tr>
                <td>{{ $row['month'] }}</td>
                <td class="right">Rp {{ number_format($row['income'], 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($row['expense'], 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($row['balance'], 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
