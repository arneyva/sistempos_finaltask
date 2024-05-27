<!DOCTYPE html>
<html>

<head>
    <title>Export products</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
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
                    <td>{{ $product->cost }}</td>
                    <td>{{ $product->price }}</td>
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
