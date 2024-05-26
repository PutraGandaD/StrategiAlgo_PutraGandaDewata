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
<div class="col-lg-12 col-sm-12">
    <div class="white-box">
        <h3 class="box-title mb-4">Hasil Dynamic Programming</h3>
        <div class="table-responsive">
            <table class="table text-nowrap">
                <tr>
                    <th>Label</th>
                    <th>Weight (g)</th>
                    <th>Blemishes (Y/N)</th>
                    <th>Quality (1-5)</th>
                    <th>Value</th>
                </tr>
                @foreach ($resultDP['items'] as $item)
                    <tr>
                        <td>{{ $item['labels'] ?? 'No Label' }}</td>
                        <td>{{ $item['Weight (g)'] }}</td>
                        <td>{{ $item['Blemishes (Y/N)'] }}</td>
                        <td>{{ $item['Quality (1-5)'] }}</td>
                        <td>{{ $item['value'] }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th>Total</th>
                    <th>{{ $resultDP['total_weight'] }}</th>
                    <th></th>
                    <th></th>
                    <th>{{ $resultDP['total_value'] }}</th>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="col-lg-12 col-sm-12">
    <div class="white-box">
        <h3 class="box-title mb-4">Hasil Greedy By Weight</h3>
        <div class="table-responsive">
            <table class="table text-nowrap">
                <tr>
                    <th>Label</th>
                    <th>Weight (g)</th>
                    <th>Blemishes (Y/N)</th>
                    <th>Quality (1-5)</th>
                    <th>Value</th>
                </tr>
                @foreach ($resultGDW['items'] as $item)
                    <tr>
                        <td>{{ $item['labels'] ?? 'No Label' }}</td>
                        <td>{{ $item['Weight (g)'] }}</td>
                        <td>{{ $item['Blemishes (Y/N)'] }}</td>
                        <td>{{ $item['Quality (1-5)'] }}</td>
                        <td>{{ $item['value'] }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th>Total</th>
                    <th>{{ $resultGDW['total_weight'] }}</th>
                    <th></th>
                    <th></th>
                    <th>{{ $resultGDW['total_value'] }}</tnh>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
