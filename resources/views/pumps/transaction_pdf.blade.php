<!doctype html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Giao dịch #{{ $tx->id }} — Trụ {{ $pumpId }}</h2>
        <p>Thời gian: {{ $tx->dateTimeCreated }}</p>
    </div>
    <table>
        <tr>
            <th>Giá</th>
            <td>{{ number_format($tx->price) }}</td>
        </tr>
        <tr>
            <th>Lít</th>
            <td>{{ number_format($tx->money / $tx->price, 3, '.', ',') }}</td>
        </tr>
        <tr>
            <th>Tổng tiền</th>
            <td>{{ number_format($tx->money) }}</td>
        </tr>
        <tr>
            <th>Millis</th>
            <td>{{ number_format($tx->millis) }}</td>
        </tr>
        <tr>
            <th>Số giao dịch</th>
            <td>{{ $tx->pickUpTimes }}</td>
        </tr>
        <tr>
            <th>Tổng F3</th>
            <td>{{ number_format($tx->totalF3) }}</td>
        </tr>
    </table>
</body>

</html>
