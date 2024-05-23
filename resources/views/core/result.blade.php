<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knapsack Problem Result</title>
    <style>
        table {
            width: 70%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 18px;
            text-align: left;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Knapsack Problem Result</h1>
    <h2>Dynamic Programming Result</h2>

    <table>
        <tr>
            <th>Label</th>
            <th>Weight (g)</th>
            <th>Value</th>
        </tr>
        @foreach ($resultDP['items'] as $itemIndex)
            <tr>
                <td>{{ $labels[$itemIndex] ?? 'No Label' }}</td>
                <td>{{ $weights[$itemIndex] }}</td>
                <td>{{ $values[$itemIndex] }}</td>
            </tr>
        @endforeach
        <tr>
            <th>Total</th>
            <th>{{ $resultDP['total_weight'] }}</th>
            <th>{{ $resultDP['total_value'] }}</th>
        </tr>
    </table>

    <h2>Greedy Algorithm Result</h2>
    <table>
        <tr>
            <th>Label</th>
            <th>Weight</th>
            <th>Value (Size/Weight + Bonus)</th>
        </tr>
        @foreach ($resultGD['items'] as $item)
        <tr>
            <td>{{ $item[2] }}</td>
            <td>{{ $item[0] }}</td>
            <td>{{ $item[1] }}</td>
        </tr>
        @endforeach
        <tr>
            <th>Total</th>
            <th>{{ $resultGD['total_weight'] }}</th>
            <th>{{ $resultGD['total_value'] }}</th>
        </tr>
    </table>
    <a href="{{ route('core.index') }}">Back</a>
</body>
</html>
