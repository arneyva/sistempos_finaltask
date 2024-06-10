<!DOCTYPE html>
<html>

<head>
    <title>Export Sales</title>
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
    <h1>Sale Data</h1>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Added By</th>
                <th>Customer</th>
                <th>Warehouse</th>
                <th>Status</th>
                <th>Grand Total</th>
                <th>Payment Status</th>
                <th>Shipping Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $Sale)
                <tr>
                    <td>{{ $Sale->date }}</td>
                    <td>{{ $Sale->Ref }}</td>
                    <td>{{ $Sale->user->username ?? 'deleted' }}</td>
                    <td>{{ $Sale->client->name ?? 'deleted' }}</td>
                    <td>{{ $Sale->warehouse->name ?? 'deleted' }}</td>
                    <td>{{ $Sale->statut }}</td>
                    <td>{{ $Sale->GrandTotal }}</td>
                    <td>{{ $Sale->payment_statut }}</td>
                    <td>{{ $Sale->shipping_status ?? 'Without shiiping' }}</td>
                    <td>{{ $Sale->notes }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
