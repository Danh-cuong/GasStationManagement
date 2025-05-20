<!doctype html>
<html>

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

        .border {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 4px;
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
        }

        .small {
            font-size: 10px;
        }
    </style>
</head>

<body>
    {{-- Header chính --}}
    <table>
        <tr>
            <td>
                <div class="title text-center">HÓA ĐƠN GIÁ TRỊ GIA TĂNG<br>(VAT INVOICE)</div>
                <div class="small text-center">Bản thể hiện hóa đơn điện tử</div>
                <div class="small text-center">
                    Ngày (Date) {{ \Carbon\Carbon::parse($from)->format('d') }}
                    tháng (month) {{ \Carbon\Carbon::parse($from)->format('m') }}
                    năm (year) {{ \Carbon\Carbon::parse($from)->format('Y') }}
                </div>
            </td>
            <td class="small text-right">
                Ký hiệu (Serial): __________<br>
                Số (No.): __________
            </td>
        </tr>
    </table>
    <br>

    {{-- Thông tin người bán --}}
    <table>
        <tr>
            <td class="small">Đơn vị bán hàng: CÔNG TY CỔ PHẦN XĂNG DẦU PETRO WEB</td>
        </tr>
        <tr>
            <td class="small">Mã số thuế (Tax code): 8931310834</td>
        </tr>
        <tr>
            <td class="small">Địa chỉ (Address): Ba La, Hà Đông, Hà Nội, Việt Nam.</td>
        </tr>
        <tr>
            <td class="small">Điện thoại (Phone): ____________________</td>
        </tr>
        <tr>
            <td class="small">Số tài khoản (Acc No.): ____________________</td>
        </tr>
    </table>
    <br>

    {{-- Thông tin người mua --}}
    <table>
        <tr>
            <td>Họ tên người mua hàng (Buyer): ____________________________________________</td>
        </tr>
        <tr>
            <td>Tên đơn vị (Company): _________________________________________________</td>
        </tr>
        <tr>
            <td>Địa chỉ (Address): _____________________________________________________</td>
        </tr>
        <tr>
            <td>Mã số thuế (Tax code): _________________________________________________</td>
        </tr>
    </table>
    <br>

    {{-- Bảng hàng hoá dịch vụ --}}
    <table class="border">
        <thead>
            <tr>
                <th class="border text-center">STT</th>
                <th class="border text-center">Tên vòi bơm, hàng hóa</th>
                <th class="border text-center">Số lượng</th>
                <th class="border text-center">Đơn giá</th>
                <th class="border text-center">Tổng tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $i => $tx)
                <tr>
                    <td class="border text-center">{{ $i + 1 }}</td>
                    <td class="border">
                        Vòi {{ $pumpId }} - {{ $fuelName ?? '' }}
                    </td>
                    <td class="border text-right">{{ number_format($tx->money / $tx->price, 3, '.', ',') }}</td>
                    <td class="border text-right">{{ number_format($tx->price, 2, ',', '.') }}</td>
                    <td class="border text-right">{{ number_format($tx->money, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            @for ($j = count($transactions); $j < 5; $j++)
                <tr>
                    <td class="border">&nbsp;</td>
                    <td class="border">&nbsp;</td>
                    <td class="border">&nbsp;</td>
                    <td class="border">&nbsp;</td>
                    <td class="border">&nbsp;</td>
                </tr>
            @endfor
        </tbody>

    </table>
    <br>

    {{-- Thông tin bổ sung --}}
    <table>
        <tr>
            <td class="small">II. THÔNG TIN BỔ SUNG</td>
        </tr>
        <tr>
            <td class="small">Hình thức thanh toán: TM/CK</td>
        </tr>
        <tr>
            <td class="small">Kho xuất: ____________________</td>
        </tr>
    </table>
    <br><br>

    {{-- Chữ ký --}}
    <table>
        <tr>
            <td class="text-center">Người mua hàng</td>
            <td class="text-center">Người bán hàng</td>
        </tr>
    </table>
</body>

</html>
