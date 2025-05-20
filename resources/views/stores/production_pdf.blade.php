<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
        }

        th {
            background: #eee;
        }

        .text-end {
            text-align: right;
        }
    </style>
</head>

<body>
    <h2 style="text-align:center;">BÁO CÁO TỔNG SẢN LƯỢNG<br>{{ $from }} - {{ $to }}</h2>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Cửa hàng</th>
                <th class="text-end">Tổng Lít</th>
                <th class="text-end">Doanh thu (₫)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $i => $r)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $r['store'] }}</td>
                    <td class="text-end">{{ number_format($r['lit'], 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($r['revenue'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
