<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
        }

        .header-right {
            text-align: right;
            font-size: 12px;
            margin-bottom: 0.5cm;
        }

        .header-right strong {
            display: block;
        }

        h2.title {
            text-align: center;
            margin: 0;
            font-size: 16px;
        }

        .subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 0.5cm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0.5cm;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
        }

        th {
            background: #eee;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        tfoot th {
            background: #ddd;
        }

        .footer-sign {
            width: 100%;
            border: none;
            margin-top: 2cm;
        }

        .footer-sign td {
            border: none;
            text-align: center;
            vertical-align: top;
            padding-top: 1cm;
        }
    </style>
</head>

<body>

    {{-- Quốc gia & tiêu ngữ góc trên phải --}}
    <div class="header-right">
        <strong>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</strong>
        <em>Độc lập - Tự do - Hạnh phúc</em>
    </div>

    {{-- Tiêu đề --}}
    <h2 class="title">BÁO CÁO NHẬP - XUẤT - TỒN XĂNG DẦU</h2>
    <div class="subtitle">
        Từ ngày {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }}
        đến ngày {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}
    </div>

    {{-- Bảng dữ liệu --}}
    <table>
        <thead>
            <tr>
                <th rowspan="2">STT</th>
                <th rowspan="2">Hàng hóa</th>
                <th rowspan="2">Tồn đầu kỳ<br>(LÍT)</th>
                <th rowspan="2">Nhập trong kỳ<br>(LÍT)</th>
                <th rowspan="2">Xuất trong kỳ<br>(LÍT)</th>
                <th colspan="2">Hao hụt<br>(LÍT)</th>
                <th rowspan="2">Tồn cuối kỳ<br>(LÍT)</th>
            </tr>
            <tr>
                <th>Nhập</th>
                <th>Xuất</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $i => $r)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $r['label'] }}</td>
                    <td class="right">{{ number_format($r['start_inv'], 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($r['imp_period'], 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($r['exp_period'], 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($r['loss_imp'], 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($r['loss_exp'], 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($r['end_inv'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Tổng</th>
                <th class="right">{{ number_format(collect($rows)->sum('start_inv'), 2, ',', '.') }}</th>
                <th class="right">{{ number_format(collect($rows)->sum('imp_period'), 2, ',', '.') }}</th>
                <th class="right">{{ number_format(collect($rows)->sum('exp_period'), 2, ',', '.') }}</th>
                <th class="right">{{ number_format(collect($rows)->sum('loss_imp'), 2, ',', '.') }}</th>
                <th class="right">{{ number_format(collect($rows)->sum('loss_exp'), 2, ',', '.') }}</th>
                <th class="right">{{ number_format(collect($rows)->sum('end_inv'), 2, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    {{-- Ký tên --}}
    <table class="footer-sign">
        <tr>
            <td><strong>ĐẠI DIỆN CÔNG TY</strong></td>
            <td><strong>CỬA HÀNG TRƯỞNG</strong></td>
        </tr>
    </table>

</body>

</html>
