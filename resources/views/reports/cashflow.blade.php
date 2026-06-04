<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отчет по Cashflow</title>
    <style>
        @page {
            margin: 30px 40px;
            font-family: DejaVu Sans, sans-serif;
        }

        body {
            font-size: 12px;
            color: #333;
            background-color: #fff;
            line-height: 1.5;
        }

        h1 {
            margin-bottom: 15px;
            color: #000;
        }

        .row {
            display: flex;
            gap: 10px;
        }

        .w-50 {
            width: 45%;
            margin: 0;
        }

        .w-30 {
            width: 30%;
            margin: 0;
        }

        .w-35 {
            width: 35%;
            margin: 0;
        }

        .report-header {
            padding-bottom: 10px;
            box-sizing: content-box;
        }

        .block {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 6px;
        }

        .block-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 10px;
            color: #222;
        }

        .balance-line {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .balance-label {
            width: 20%;
            font-weight: bold;
            font-size: 13px;
            margin-top: 6px;
        }

        .balance-item {
            width: 35%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            text-align: left;
        }

        .table {
            display: inline-block;
            width: 49%;
            border-collapse: collapse;
        }

        .table th, .table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .table thead {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .chart {
            margin-top: 30px;
            text-align: center;
        }

        .chart img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="report-header">
        <h1>Отчет</h1>
        <div class="row">
            <p class="w-50"><strong>Период:</strong> {{ \Carbon\Carbon::parse($start)->format('d.m.Y') }} — {{ \Carbon\Carbon::parse($end)->format('d.m.Y') }}</p>
            <p class="w-50" style="text-align: right;"><strong>Сформировано:</strong> {{ now()->format('d.m.Y H:i') }}</p>
        </div>
    </div>

    <div class="block">
        <div class="balance-line">
            <div class="balance-label">Сводка</div>
            <div class="balance-item">
                <strong>Приход:</strong><br>
                {{ number_format($incomes / 100, 2, ',', ' ') }} грн
            </div>
            <div class="balance-item">
                <strong>Расход:</strong><br>
                {{ number_format($expenses / 100, 2, ',', ' ') }} грн
            </div>
        </div>
    </div>

    <div class="block">
        <div class="block-title">Таблица операций</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Категория</th>
                    <th>Кол</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expensesCategory as $tx)
                    <tr>
                        <td>{{ $tx->expense_category_id }}</td>
                        <td>{{ $tx->count}} </td>
                        <td>{{ $tx->amount }} грн</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th>Поставщик</th>
                    <th>Кол</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                {{-- @foreach($transactions as $tx)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($tx->date)->format('d.m.Y') }}</td>
                        <td>{{ ucfirst($tx->type) }}</td>
                        <td>{{ number_format($tx->amount / 100, 2, ',', ' ') }} грн</td>
                        <td>{{ $tx->description }}</td>
                    </tr>
                @endforeach --}}
            </tbody>
        </table>

    </div>

    @if(!empty($chartBase64))
        <div class="chart">
            <div class="block-title">График Cashflow</div>
            <img src="data:image/png;base64,{{ $chartBase64 }}">
        </div>
    @endif

</body>
</html>
