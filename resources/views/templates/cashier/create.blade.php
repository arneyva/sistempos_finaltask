<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('hopeui/html/assets/images/favicon.ico') }}">

    <!-- Library / Plugin Css Build -->
    <link rel="stylesheet" href="{{ asset('hopeui/html/assets/css/core/libs.min.css') }}">

    <!-- Aos Animation Css -->
    <link rel="stylesheet" href="{{ asset('hopeui/html/assets/vendor/aos/dist/aos.css') }}">

    <!-- Hope Ui Design System Css -->
    <link rel="stylesheet" href="{{ asset('hopeui/html/assets/css/hope-ui.min.css?v=4.0.0') }}">

    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('hopeui/html/assets/css/custom.min.css?v=4.0.0') }}">

    <!-- Dark Css -->
    <link rel="stylesheet" href="{{ asset('hopeui/html/assets/css/dark.min.css') }}">

    <!-- Customizer Css -->
    <link rel="stylesheet" href="{{ asset('hopeui/html/assets/css/customizer.min.css') }}">

    <!-- RTL Css -->
    <link rel="stylesheet" href="{{ asset('hopeui/html/assets/css/rtl.min.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            width: 100%;
            height: 100%;
            --size: 80px; /* control the size */
            --color: #add8e6;

            background: linear-gradient(
                to bottom,
                transparent 0%,
                transparent 40%,
                #add8e6 41%,
                #add8e6 60%,
                transparent 61%,
                transparent 100%
                ),
                linear-gradient(
                45deg,
                #add8e6 25%,
                transparent 25%,
                transparent 50%,
                #add8e6 50%,
                #add8e6 75%,
                transparent 75%,
                transparent
                );
            background-size: var(--size) var(--size);
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
        }

        .otp-Form {
            width: 100%; /* Menggunakan rem untuk lebar agar responsif */
        }

        /* Custom CSS to adjust the Bootstrap media query breakpoints */
        @media (min-width: 768px) {
            .ButtonContainer {
                gap:1.4vw;
            }

            .verifyButton {
            width: 14vw;
            max-height: 3.3vw;
            font-size:1.55vw;
            border: 0.3vw solid ;
            border-radius: 5px;
            }
            .password-input {
            -webkit-text-security: disc; /* Untuk Chrome/Safari */
            -moz-text-security: disc; /* Untuk Firefox */
            width: 25vw;
            height: 3vw;
            border-radius: 0.25vw;
            outline: none;
            border: 0.2vw solid transparent;
            border-bottom: 0.2vw solid #3f3f3f;
            caret-color: #3f3f3f;
            background-color: transparent;
            padding: 5px;
            transition: .5s linear;
            letter-spacing: 1px;
            text-align:center;
        }

        .password-input:focus {
        border: 0.22vw solid #fa4753;
        color: #fa4753;
        box-shadow: 0.28vw 0.28vw 0.6vw #6B6B6B;
        }
        
        .password-input::placeholder {
        font-size:1.35vw;
        font-weight: 600;
        transition: .5s linear;
        }
        .password-input:focus::placeholder {
        color: #fa4753;
        }
        #map {
            height: 200px;
            width: 100%;
        }
        }
        @media (min-width: 1300px) {
            .ButtonContainer {
                gap:1.6vw;
            }

            .verifyButton {
            width: 16vw;
            max-height: 2.7vw;
            font-size:1.25vw;
            border: 0.25vw solid ;
            border-radius: 6px;
            }
                .password-input {
                -webkit-text-security: disc; /* Untuk Chrome/Safari */
                -moz-text-security: disc; /* Untuk Firefox */
                width: 25vw;
                height: 3vw;
                border-radius: 0.25vw;
                outline: none;
                border: 0.2vw solid transparent;
                border-bottom: 0.2vw solid #3f3f3f;
                caret-color: #3f3f3f;
                background-color: transparent;
                padding: 5px;
                transition: .5s linear;
                letter-spacing: 1px;
                text-align:center;
            }

            .password-input:focus {
            border: 0.22vw solid #fa4753;
            color: #fa4753;
            box-shadow: 0.28vw 0.28vw 0.6vw #6B6B6B;
            }
            
            .password-input::placeholder {
            font-size:1.2vw;
            font-weight: 600;
            transition: .5s linear;
            }
            .password-input:focus::placeholder {
            color: #fa4753;
            }
        }


        .mainHeading {
        font-size: 1.1em;
        color: rgb(15, 15, 15);
        font-weight: 700;
        }

        .otpSubheading {
        font-size: 0.7em;
        color: black;
        line-height: 17px;
        text-align: center;
        }


        .verifyButton {
        background-color: transparent;
        border-color: transparent;
        color: #3E8A1B;
        font-weight: 600;
        cursor: pointer;
        transition-duration: .2s;
        }

        .verifyButton:hover {
        border-color: transparent;
        background-color: #3E8A1B;
        color: #ffffff;
        transition-duration: .2s;
        }
        .verifyButton.out {
        background-color: transparent;
        border-color: transparent;
        color: #7B2B2B;
        font-weight: 600;
        cursor: pointer;
        transition-duration: .2s;
        }

        .verifyButton.out:hover {
        border-color: transparent;
        background-color: #7B2B2B;
        color: #ffffff;
        transition-duration: .2s;
        }


        .resendNote {
        font-size: 0.7em;
        color: black;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
        }

        .resendBtn {
        background-color: transparent;
        border: none;
        color: rgb(127, 129, 255);
        cursor: pointer;
        font-size: 1.1em;
        font-weight: 700;
        }
        .inline-block {
            display: inline-block;
        }
        .fill-viewport {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-table-wrapper {
            overflow-x: auto;
        }
        .full-height {
            height: 100%;
        }

    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>


</head>

<body>
<main class="main-content">
    <div class="container-fluid content-inner mt-n5 py-0">
        .row
    </div>
</main>


    <!-- Library Bundle Script -->
    <script src="{{ asset('hopeui/html/assets/js/core/libs.min.js') }}"></script>

    <!-- External Library Bundle Script -->
    <script src="{{ asset('hopeui/html/assets/js/core/external.min.js') }}"></script>

    <!-- Widgetchart Script -->
    <script src="{{ asset('hopeui/html/assets/js/charts/widgetcharts.js') }}"></script>

    <!-- mapchart Script -->
    <script src="{{ asset('hopeui/html/assets/js/charts/vectore-chart.js') }}"></script>
    <script src="{{ asset('hopeui/html/assets/js/charts/dashboard.js') }}"></script>

    <!-- fslightbox Script -->
    <script src="{{ asset('hopeui/html/assets/js/plugins/fslightbox.js') }}"></script>

    <!-- Settings Script -->
    <script src="{{ asset('hopeui/html/assets/js/plugins/setting.js') }}"></script>

    <!-- Slider-tab Script -->
    <script src="{{ asset('hopeui/html/assets/js/plugins/slider-tabs.js') }}"></script>

    <!-- Form Wizard Script -->
    <script src="{{ asset('hopeui/html/assets/js/plugins/form-wizard.js') }}"></script>

    <!-- AOS Animation Plugin-->
    <script src="{{ asset('hopeui/html/assets/vendor/aos/dist/aos.js') }}"></script>

    <!-- App Script -->
    <script src="{{ asset('hopeui/html/assets/js/hope-ui.js') }}" defer></script>

    <!-- clock Script -->
    <script src="{{ asset('hopeui/html/assets/js/real-time-clock.js') }}"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var map = L.map('map').setView([-7.535912, 110.783188], 15); // Set view default map
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var lang_clockin = "{{ __('Clock In at') }}";
        var lang_clockout ="{{ __('Clock Out at') }}";
        var userMarker, line; // Variabel untuk menyimpan marker dan garis pengguna

        function updateLocationAndSendRequest(method, pin) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var userLatLng = L.latLng(position.coords.latitude, position.coords.longitude);

                    $.ajax({
                        url: '/webclock/clocking',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            type: method,
                            pin: pin,
                            latitude: userLatLng.lat,
                            longitude: userLatLng.lng
                        },
                        headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            if (response.error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    html: '<ol style="text-align: start">' + response.error + '</ol>',
                                });
                            } else {
                                function type(clocktype) {
                                    if (clocktype == "clockin") {
                                        return lang_clockin;
                                    } else {
                                        return lang_clockout;
                                    }
                                }
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.onmouseenter = Swal.stopTimer;
                                        toast.onmouseleave = Swal.resumeTimer;
                                    }
                                });
                                Toast.fire({
                                    icon: "success",
                                    title: response.firstname + ' ' + response.lastname + ' success ' + type(response.type) + ' ' + response.time
                                });
                            }
                            if (response.targetLat != null && response.targetLng != null) {
                                // Hapus marker dan garis sebelumnya jika ada
                                if (userMarker) {
                                    map.removeLayer(userMarker);
                                }
                                if (line) {
                                    map.removeLayer(line);
                                }

                                // Tambahkan marker untuk lokasi pengguna
                                userMarker = L.marker(userLatLng).addTo(map).bindPopup("Lokasi Anda").openPopup();

                                var targetLatLng = L.latLng(response.targetLat, response.targetLng);

                                L.marker(targetLatLng).addTo(map).bindPopup("Lokasi Kerja").openPopup();

                                line = L.polyline([targetLatLng, userLatLng], { color: 'blue' }).addTo(map);

                                map.fitBounds(line.getBounds());
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Terdapat error pada server'
                            });
                        }
                    });

                    $('input[name="pin"]').val("");
                    $('input[name="pin"]').focus();

                }, function (error) {
                    alert("Gagal mendapatkan lokasi. Pastikan GPS aktif dan berikan izin akses lokasi.");
                }, {
                    enableHighAccuracy: true, // Pastikan menggunakan GPS untuk akurasi tinggi
                    timeout: 10000, // Waktu tunggu maksimal
                    maximumAge: 0 // Jangan menggunakan cache lokasi
                });
            } else {
                alert("Browser Anda tidak mendukung geolokasi.");
            }
        }

        $('#btn-clockin, #btn-clockout').on('click', function (event) {
            event.preventDefault();
            var method = $(this).data("method");
            var pin = $('input[name="pin"]').val().toUpperCase(); // Pastikan pin diubah menjadi huruf besar
            updateLocationAndSendRequest(method, pin);
        });
    </script>

    <script>
        $(document).ready(function() {
            new AutoNumeric('#numeric-input', {
                digitGroupSeparator: '',
                decimalPlaces: 0,
                minimumValue: '0',
                maximumValue: '999999',
                modifyValueOnWheel: false,
                emptyInputBehavior: 'focus',
                showWarnings: false
            });
        });
    </script>

    <script>

    </script>

</body>

</html>
