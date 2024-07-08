<!DOCTYPE html>
<html>
<head>
    <title>Attendance</title>
</head>
<body>

@if(isset($message))
    <h2>{{ $message }}</h2>
@else
    <form action="{{ route('hrm.myattendances.check') }}" method="POST">
        @csrf
        <label for="month">Pilih Bulan:</label>
        <input value="@if(isset($month)){{ $month }}@endif" type="month" id="month" name="month" required>
        <button type="submit">Cek</button>
    </form>
@endif

@if(isset($attendances))
    <table border="1">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Hari</th>
                <th>Jadwal Masuk</th>
                <th>Jadwal Keluar</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $attendance)
                <tr>
                    <td>{{ $attendance['date'] }}</td>
                    <td>{{ $attendance['day'] }}</td>
                    <td>{{ $attendance['schedule_in'] }}</td>
                    <td>{{ $attendance['schedule_out'] }}</td>
                    <td>{{ $attendance['clock_in'] }}</td>
                    <td>{{ $attendance['clock_out'] }}</td>
                    <td>{{ $attendance['status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

</body>
</html>
