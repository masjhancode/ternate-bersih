<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Laporan Sampah</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #222;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #1e293b;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
            font-weight: bold;
            color: #1e293b;
        }
        .text-center { text-align: center; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAPITULASI PELAPORAN PENANGANAN SAMPAH</h1>
        <p>Sistem Informasi Pelaporan dan Penanganan Sampah (SIPAS) - Ternate Bersih</p>
        <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">No. Laporan</th>
                <th width="12%">Tanggal</th>
                <th width="15%">Pelapor</th>
                <th width="15%">Kategori</th>
                <th width="20%">Alamat Lokasi</th>
                <th width="10%">Prioritas</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $index => $report)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $report->report_number }}</td>
                <td>{{ $report->created_at->format('d/m/Y') }}</td>
                <td>{{ $report->user->name ?? 'Anonim' }}</td>
                <td>{{ $report->category->name ?? '-' }}</td>
                <td>{{ $report->address }}</td>
                <td class="text-center">{{ $report->priority }}</td>
                <td class="text-center">{{ $report->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem SIPAS Ternate
    </div>
</body>
</html>
