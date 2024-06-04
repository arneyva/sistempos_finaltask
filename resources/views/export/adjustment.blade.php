<!DOCTYPE html>
<html>

<head>
    <title>Export Adjustments</title>
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
    <h1>Adjustment Data</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Added By</th>
                <th>Date</th>
                <th>Reference</th>
                <th>Warehouse Name</th>
                <th>Total Items</th>
                <th>Notes</th>
                <th>CreatedAt</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($adjustments as $adjustment)
                <tr>
                    <td>{{ $adjustment->id }}</td>
                    <td>{{ $adjustment->user->firstname }}</td>
                    <td>{{ $adjustment->date }}</td>
                    <td>{{ $adjustment->Ref }}</td>
                    <td>{{ $adjustment->warehouse->name ?? 'deleted' }}</td>
                    <td>{{ $adjustment->items }}</td>
                    <td>{{ $adjustment->notes }}</td>
                    <td>{{ $adjustment->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
