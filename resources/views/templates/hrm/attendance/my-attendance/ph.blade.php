<!DOCTYPE html>
<html>
<head>
    <title>Attendance</title>
</head>
<body>

<form action="{{ route('webclock.clocking') }}" method="POST">
    @csrf
    <input type="text" id="month" name="pin" required>
    <input type="text" id="month" name="type" required>
    <input type="number" id="month" name="latitude" required>
    <input type="number" id="month" name="longitude" required>
    <button type="submit">Cek</button>
</form>

</body>
</html>
