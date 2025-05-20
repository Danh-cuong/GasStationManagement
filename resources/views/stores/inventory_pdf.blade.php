<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
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

        .text-center {
            text-align: center;
        }

        .header-line {
            font-weight: bold;
        }
    </style>
</head>

<body>
    {{-- Header quốc gia --}}
    <div style="text-align:center; margin-bottom: 10px;">
        <div class="header-line">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
        <div class="header-line"><em>Độc lập - Tự do - Hạnh phúc</em></div>
    </div>

    <br><br>
    <h2 style="text-align:center; margin-top:0;">
        BÁO CÁO NHẬP - XUẤT - TỒN<br>
    </h2>
    <p style="text-align:center; margin-top:0;">Từ {{ $from }} đến {{ $to }}</p>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Cửa hàng</th>
                <th>Hàng hoá</th>
                <th class="text-end">Tồn đầu kỳ (L)</th>
                <th class="text-end">Nhập trong kỳ (L)</th>
                <th class="text-end">Xuất trong kỳ (L)</th>
                <th class="text-end">Hao hụt nhập (L)</th>
                <th class="text-end">Hao hụt xuất (L)</th>
                <th class="text-end">Tồn cuối kỳ (L)</th>
            </tr>
        </thead>
        <tbody>
            @php
                // gom nhóm theo cửa hàng
                $groups = collect($rows)->groupBy(fn($r) => $r['emp']->id);
                $stt = 1;
            @endphp

            @foreach ($groups as $empId => $group)
                @foreach ($group as $i => $r)
                    <tr>
                        @if ($i === 0)
                            {{-- STT và tên cửa hàng chỉ in 1 lần, rowspan = số loại hàng --}}
                            <td class="text-center" rowspan="{{ count($group) }}">{{ $stt }}</td>
                            <td rowspan="{{ count($group) }}">{{ $r['emp']->name }}</td>
                        @endif

                        {{-- cột hàng hoá và số liệu --}}
                        <td>{{ $r['fuel'] }}</td>
                        <td class="text-end">{{ number_format($r['si'], 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($r['imp'], 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($r['exp'], 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($r['li'], 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($r['le'], 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($r['ei'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                @php $stt++; @endphp
            @endforeach

            {{-- Tổng cộng --}}
            <tr>
                <th colspan="3">Tổng cộng</th>
                @foreach ($totals as $v)
                    <th class="text-end">{{ number_format($v, 2, ',', '.') }}</th>
                @endforeach
            </tr>
        </tbody>
    </table>
</body>

</html>
