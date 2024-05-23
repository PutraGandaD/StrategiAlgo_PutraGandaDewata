<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knapsack Problem Input</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="file"],
        input[type="number"],
        button {
            padding: 10px;
            margin-bottom: 20px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Knapsack Problem</h1>
        <form action="{{ route('core.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="csv_file">Upload CSV File:</label>
            <input type="file" id="csv_file" name="csv_file" accept=".csv" required>

            <label for="capacity">Enter Knapsack Capacity:</label>
            <input type="number" id="capacity" name="capacity" step="0.01" required>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
