@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
        <div class="white-box">
            <h2 class="box-title mb-4">Perhitungan Jeruk di dalam Keranjang untuk Transport</h2>
            <form action="{{ route('core.process') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group mb-3">
                    <label for="csv_file" class="col-md-12 p-0">Upload CSV File:</label>
                    <input class="form-control p-0 border-0" type="file" id="csv_file" name="csv_file" accept=".csv" required>
                </div>

                <div class="form-group mb-4">
                    <label for="capacity" class="col-md-12 p-0">Berat Maksimal Keranjang (dalam gram)</label>
                    <div class="col-md-12 border-bottom p-0">
                        <input type="number" placeholder="Input berat maksimal keranjang disini..." step="0.01" class="form-control p-0 border-0" name="capacity" id="capacity">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <button class="btn btn-primary" type="submit">Hitung dengan DP dan GD</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="white-box">
            <h2 class="box-title mb-4">Dataset</h2>
            <a href="https://firebasestorage.googleapis.com/v0/b/fir-withcreapi.appspot.com/o/updated_items.csv?alt=media&token=402f63eb-59e8-4b58-87b8-78524ddc2bda">
                <button class="btn btn-primary">Download Dataset</button>
            </a>
            <a href="https://www.kaggle.com/datasets/shruthiiiee/orange-quality">
                <button class="btn btn-primary">Sumber Dataset</button>
            </a>
        </div>
    </div>
</div>
@endsection
