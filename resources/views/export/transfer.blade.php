<!DOCTYPE html>
<html>

<head>
    <title>Export Transfers</title>
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
    <h1>Transfer Data</h1>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>From Warehouse</th>
                <th>To Warehouse</th>
                <th>Total Items</th>
                <th>Grand Total</th>
                <th>Notes</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transfers as $transfer)
                <tr>
                    <td>{{ $transfer->date }}</td>
                    <td>{{ $transfer->Ref }}</td>
                    <td>{{ $transfer->from_warehouse->name ?? 'deleted' }}</td>
                    <td>{{ $transfer->to_warehouse->name ?? 'deleted' }}</td>
                    <td>{{ $transfer->items }}</td>
                    <td>{{ $transfer->GrandTotal }}</td>
                    <td>{{ $transfer->notes }}</td>
                    <td>{{ $transfer->statut }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
