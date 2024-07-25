<html>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
    <form action="{{ route('webclock.clocking') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="pin">Pin</label>
                <input type="text" class="form-control" id="pin" name="pin" placeholder="Masukkan pin" value="{{ old('pin') }}">
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <input type="text" class="form-control" id="type" name="type" placeholder="Masukkan type" value="{{ old('type') }}">
            </div>
            <div class="form-group">
                <label for="latitude">Latitude</label>
                <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Masukkan latitude" value="{{ old('latitude') }}">
            </div>
            <div class="form-group">
                <label for="longitude">Longitude</label>
                <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Masukkan longitude" value="{{ old('longitude') }}">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    </body>
</html>