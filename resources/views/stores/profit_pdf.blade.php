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
            margin-bottom: 4px;
        }

        .header .line1 {
            font-weight: bold;
        }

        .header .line2 {
            font-style: italic;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
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

        .text-start {
            text-align: left;
        }
    </style>
</head>

<body>
    {{-- Header quốc gia + tiêu ngữ --}}
    <div class="header">
        <div class="line1">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
        <div class="line2">Độc lập – Tự do – Hạnh phúc</div>
    </div>

    <h2 class="text-center" style="margin:4px 0;">
        BÁO CÁO LỢI NHUẬN THEO CỬA HÀNG<br>
        Từ {{ $from }} đến {{ $to }}
    </h2>

    @php
        $stt = 1;
        $groups = collect($rows)->groupBy('store');
    @endphp

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Cửa hàng</th>
                <th>Hàng hoá</th>
                <th>Nhập trong kỳ<br>Doanh thu</th>
                <th>Xuất trong kỳ<br>Doanh thu</th>
                <th>Lợi nhuận</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($groups as $storeName => $group)
                @foreach ($group as $i => $r)
                    <tr>
                        @if ($i === 0)
                            <td class="text-center" rowspan="{{ count($group) }}">{{ $stt }}</td>
                            <td rowspan="{{ count($group) }}">{{ $storeName }}</td>
                        @endif

                        <td class="text-start">{{ $r['fuel'] }}</td>
                        <td class="text-end">{{ number_format($r['impRev'], 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($r['expRev'], 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($r['profit'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                @php $stt++; @endphp
            @endforeach
        </tbody>
    </table>
</body>

</html>
