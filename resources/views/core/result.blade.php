@extends('layout.main')

@section('content')
<div class="col-lg-12 col-sm-12">
    <div class="white-box">
        <h2 class="box-title mb-4">Informasi Jeruk</h2>
        <h4>Total Jeruk (dari file CSV) : {{ $totalItems }} buah</h4>
        <h4>Berat Keranjang yang akan digunakan untuk menampung jeruk (dalam gram) : {{ $capacity }} gram</h4>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-sm-12">
        <div class="white-box">
            <h3 class="box-title mb-4">Hasil Dynamic Programming</h3>
            <p>Total Berat (g) : {{ $resultDP['total_weight'] }}</p>
            <p>Total Value : {{ $resultDP['total_value'] }}</p>
            <p>Total Jeruk yang ditampung : {{ $resultDP['total_items'] }}</p>
            <p>Sisa Jeruk : {{ $totalItems - $resultDP['total_items'] }}</p>
            <form action="{{ route('core.remaining.items') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <input type="hidden" name="remainingItems" value="{{ json_encode($remainingItems) }}"</input>
                <button type="submit" class="btn btn-primary">Download Sisa Buah Jeruk (CSV)</button>
            </form>
        </div>
    </div>
    <div class="col-lg-3 col-sm-12">
        <div class="white-box">
            <h3 class="box-title mb-4">Hasil Greedy By Weight</h3>
            <p>Total Berat (g) : {{ $resultGDW['total_weight'] }}</p>
            <p>Total Value : {{ $resultGDW['total_value'] }}</p>
            <p>Total Jeruk yang ditampung : {{ $resultGDW['total_items'] }}</p>
            <p>Sisa Jeruk : {{ $totalItems - $resultGDW['total_items'] }}</p>
            <p></p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-12">
        <div class="white-box">
            <h3 class="box-title mb-4">Hasil Greedy By Value</h3>
            <p>Total Berat (g) : {{ $resultGDV['total_weight'] }}</p>
            <p>Total Value : {{ $resultGDV['total_value'] }}</p>
            <p>Total Jeruk yang ditampung : {{ $resultGDV['total_items'] }}</p>
            <p>Sisa Jeruk : {{ $totalItems - $resultGDV['total_items'] }}</p>
            <p></p>
        </div>
    </div>
    <div class="col-lg-3 col-sm-12">
        <div class="white-box">
            <h3 class="box-title mb-4">Hasil Greedy By Density</h3>
            <p>Total Berat (g) : {{ $resultGDD['total_weight'] }}</p>
            <p>Total Value : {{ $resultGDD['total_value'] }}</p>
            <p>Total Jeruk yang ditampung : {{ $resultGDD['total_items'] }}</p>
            <p>Sisa Jeruk : {{ $totalItems - $resultGDD['total_items'] }}</p>
            <p></p>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-sm-12">
        <div class="white-box">
            <h3 class="box-title mb-4">Hasil Dynamic Programming</h3>
            <div class="table-responsive">
                <table class="table text-nowrap">
                    <tr>
                        <th>Label</th>
                        <th>Weight (g)</th>
                        <th>Value</th>
                    </tr>
                    @foreach ($resultDP['items'] as $item)
                        <tr>
                            <td>{{ $item['label'] ?? 'No Label' }}</td>
                            <td>{{ $item['weight'] }}</td>
                            <td>{{ $item['value'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th>Total</th>
                        <th>{{ $resultDP['total_weight'] }}</th>
                        <th>{{ $resultDP['total_value'] }}</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- <!DOCTYPE html>
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
        @foreach ($resultGDW['items'] as $item)
        <tr>
            <td>{{ $item[2] }}</td>
            <td>{{ $item[0] }}</td>
            <td>{{ $item[1] }}</td>
        </tr>
        @endforeach
        <tr>
            <th>Total</th>
            <th>{{ $resultGDW['total_weight'] }}</th>
            <th>{{ $resultGDW['total_value'] }}</th>
        </tr>
    </table>
    <a href="{{ route('core.index') }}">Back</a>
</body>
</html> --}}
