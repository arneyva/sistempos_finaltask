<!DOCTYPE html>
<html>

<head>
    <title>Export products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            word-break: break-word;
            /* Prevents overflow of long text */
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>
    <h1>Product Data</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Type Product</th>
                <th>Code</th>
                <th>Name</th>
                <th>Cost</th>
                <th>Price</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Unit</th>
                <th>TaxNet</th>
                <th>Note</th>
                <th>Is Active</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->type }}</td>
                    <td>{{ $product->code }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ 'Rp ' . number_format($product->cost, 2, ',', '.') }}</td>
                    <td>{{ 'Rp ' . number_format($product->price, 2, ',', '.') }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>{{ $product->brand->name }}</td>
                    <td>{{ $product->unit->name }}</td>
                    <td>{{ $product->TaxNet }}</td>
                    <td>{{ $product->note }}</td>
                    <td>{{ $product->is_active }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
