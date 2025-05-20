<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .header .line1 {
            font-weight: bold;
        }

        .header .line2 {
            font-style: italic;
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

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }
    </style>
</head>

<body>
    {{-- Header quốc gia + tiêu ngữ --}}
    <div class="header">
        <div class="line1">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
        <div class="line2">Độc lập - Tự do - Hạnh phúc</div>
    </div>

    <br><br>
    <h2 style="text-align:center; margin-top:0;">
        BÁO CÁO TỔNG HỢP NHẬP HÀNG<br>
    </h2>
    <span>Từ {{ $from }} đến {{ $to }}</span>

    @php $stt = 1; @endphp
    @foreach ($stores as $s)
        <table style="margin-bottom:16px;">
            <thead>
                <tr>
                    <th rowspan="2">STT</th>
                    <th rowspan="2">Cửa hàng</th>
                    <th colspan="2">Chứng từ</th>
                    <th rowspan="2">Loại nhiên liệu</th>
                    <th rowspan="2">ĐVT</th>
                    <th rowspan="2">SL</th>
                    <th rowspan="2">Giá</th>
                    <th rowspan="2">VAT %</th>
                    <th rowspan="2">Tiền hàng</th>
                </tr>
                <tr>
                    <th>Thời gian nhập</th>
                    <th>Mã chứng từ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($s['entries'] as $i => $e)
                    <tr>
                        @if ($i === 0)
                            <td class="text-center" rowspan="{{ count($s['entries']) }}">{{ $stt }}</td>
                            <td rowspan="{{ count($s['entries']) }}">{{ $s['store'] }}</td>
                        @endif

                        <td>{{ $e['time'] }}</td>
                        <td>{{ $e['doc'] }}</td>
                        <td>{{ $e['fuel'] }}</td>
                        <td>{{ $e['unit'] }}</td>
                        <td class="text-end">{{ number_format($e['qty'], 3, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($e['price'], 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($e['vat'], 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($e['total'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach

                {{-- Tổng theo cửa hàng --}}
                <tr>
                    <th colspan="6" class="text-center">Tổng</th>
                    <th class="text-end">{{ number_format($s['sum_qty'], 3, ',', '.') }}</th>
                    <th colspan="2"></th>
                    <th class="text-end">{{ number_format($s['sum_tot'], 2, ',', '.') }}</th>
                </tr>
            </tbody>
        </table>
        @php $stt++; @endphp
    @endforeach
</body>

</html>
