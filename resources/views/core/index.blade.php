@extends('layout.main')

@section('content')
<h1>Knapsack Problem</h1>
        <form action="{{ route('core.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="csv_file">Upload CSV File:</label>
            <input type="file" id="csv_file" name="csv_file" accept=".csv" required>

            <label for="capacity">Enter Knapsack Capacity:</label>
            <input type="number" id="capacity" name="capacity" step="0.01" required>

            <button type="submit">Submit</button>
        </form>
@endsection
