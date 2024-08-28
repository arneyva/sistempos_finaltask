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
        --s: 82px;
        --c1: #012D86;
        --c2: #2E7AEB;
        --c3: #0945A7;

        --_g: var(--c3) 0 120deg, #0000 0;
        background: conic-gradient(from -60deg at 50% calc(100% / 3), var(--_g)),
            conic-gradient(from 120deg at 50% calc(200% / 3), var(--_g)),
            conic-gradient(
            from 60deg at calc(200% / 3),
            var(--c3) 60deg,
            var(--c2) 0 120deg,
            #0000 0
            ),
            conic-gradient(from 180deg at calc(100% / 3), var(--c1) 60deg, var(--_g)),
            linear-gradient(
            90deg,
            var(--c1) calc(100% / 6),
            var(--c2) 0 50%,
            var(--c1) 0 calc(500% / 6),
            var(--c2) 0
            );
        background-size: calc(1.732 * var(--s)) var(--s);
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
        }

        .otp-Form {
            background-color: #f9f9f9;
            padding: 2rem; /* Menggunakan rem untuk padding agar responsif */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 50rem; /* Menggunakan rem untuk lebar agar responsif */
        }

        /* Custom CSS to adjust the Bootstrap media query breakpoints */
        @media (max-width: 576px) {
            /* Adjust the large (lg) screen breakpoint */
            .otp-Form h1 {
                display: flex;
                justify-content: center;
                font-size:12vw; 
            }
            .otp-Form h2 {
                font-size: 5.1vw; 
            }
            .otp-Form h6 {
                font-size:4.4vw; 
                font-weight:1; 
            }

            .ButtonContainer {
                gap:2vw;
                margin-top:4.2vw;
            }

            .otp-Form {
                display: flex;
                justify-content: center;
                flex-direction: column;
                max-width: 97%; /* Set your desired minimum width for large screens (lg) */
                padding: 1rem 2rem 1rem 2rem;
            }

            .verifyButton {
                width: 35vw;
                height: 12vw;
                font-size: 4.3vw;
                border: 2.7px solid;
                border-radius: 5px;
            }
            .password-input {
            -webkit-text-security: disc; /* Untuk Chrome/Safari */
            -moz-text-security: disc; /* Untuk Firefox */
            width: 70vw;
            height: 11vw;
            border-radius: 0.25vw;
            outline: none;
            border: 0.6vw solid transparent;
            border-bottom: 0.6vw solid #3f3f3f;
            caret-color: #3f3f3f;
            background-color: transparent;
            padding: 0px;
            transition: .5s linear;
            letter-spacing: 0.9px;
            text-align:center;
        }

        .password-input:focus {
        border: 0.6vw solid #fa4753;
        color: #fa4753;
        box-shadow: 0.34vw 0.34vw 0.9vw #6B6B6B;
        }
        
        .password-input::placeholder {
        font-size:4vw;
        font-weight: 600;
        position: relative;
        top: -0.8px;
        transition: .5s linear;
        }
        .password-input:focus::placeholder {
        color: #fa4753;
        }
        #map {
            height: 180px;
            width: 100%;
            margin-bottom:2.8vw;
        }
        }
        @media (min-width: 577px) {
            /* Adjust the large (lg) screen breakpoint */
            .otp-Form h1 {
                font-size:4vw; 
            }
            .otp-Form h2 {
                font-size:2.3vw; 
            }
            .otp-Form h6 {
                font-size:2vw;
                font-weight:1;  
            }
            .otp-Form {
                width: 100vw; /* Set your desired minimum width for large screens (lg) */
            }
            .ButtonContainer {
                gap:1vw;
                margin-top:0.2vw;
            }

            .verifyButton {
            width: 16.7vw;
            height: 4.2vw;
            max-height: 40px;
            border: 0.3vw solid ;
            border-radius: 5px;
            font-size:1.9vw;
            }
            .password-input {
            -webkit-text-security: disc; /* Untuk Chrome/Safari */
            -moz-text-security: disc; /* Untuk Firefox */
            width: 31vw;
            height: 4vw;
            border-radius: 0.25vw;
            outline: none;
            border: 0.3vw solid transparent;
            border-bottom: 0.3vw solid #3f3f3f;
            caret-color: #3f3f3f;
            background-color: transparent;
            padding: 5px;
            transition: .5s linear;
            letter-spacing: 1px;
            text-align:center;
            margin-bottom 30px;
        }

        .password-input:focus {
        border: 0.3vw solid #fa4753;
        color: #fa4753;
        box-shadow: 0.28vw 0.28vw 0.5vw #6B6B6B;
        }
        
        .password-input::placeholder {
        font-size:1.7vw;
        font-weight: 660;
        transition: .5s linear;
        }
        .password-input:focus::placeholder {
        color: #fa4753;
        }
        #map {
            height: 250px;
            width: 90%;
        }
        }
        @media (min-width: 768px) {
            /* Adjust the large (lg) screen breakpoint */
            .otp-Form h1 {
                font-size:3vw; 
            }
            .otp-Form h2 {
                font-size:1.7vw; 
            }
            .otp-Form h6 {
                font-size:1.23vw; 
            }
            .otp-Form {
                max-width: 51%; /* Set your desired minimum width for large screens (lg) */
            }
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
                #map {
                height: 250px;
                width: 90%;
            }
            /* Adjust the large (lg) screen breakpoint */
            .otp-Form h1 {
                font-size:3vw; 
            }
            .otp-Form h2 {
                font-size:1.7vw; 
            }
            .otp-Form h6 {
                font-size:1.2vw; 
            }
            .otp-Form {
                max-width: 80%; /* Set your desired minimum width for large screens (lg) */
            }
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

        .otp-Form h1 {
            margin: 0 0 0.3em 0;
        }

        .otp-Form p {
            margin: 0;
            font-size: 1.2vw; /* Menggunakan vw untuk font-size agar responsif */
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

    </style>
</head>

<body>
    <div class="position-absolute container top-50 start-50 translate-middle font-sans">
        <form class="otp-Form" action="{{ $update_url }}" method="POST" autocomplete="off">
            @csrf
            @method('patch')
            @if($client->is_poin_activated == 1)
                <h1 id="current-time">You've Redeem your Score</h1>
            @elseif ($score < 1)
                <h1 id="current-time">Make some purchase first</h1>
            @else
                <h1 id="current-time">Congratulations!</h1>
            @endif

            <p>You have : {{$score}} score</p>
            <p>Potential Discount : Rp <strong>{{number_format($discount,0,",",".")}}</strong></p>

            @if ($score > 0 && $client->is_poin_activated == 0) <!-- Kondisi untuk menampilkan tombol -->
                <p class="mt-2">Click this button to apply your discount on the next sale</p>
                <div style="display:flex; justify-content:center;" class="ButtonContainer">
                    <button type="submit" class="verifyButton out">Redeem Points</button>
                </div>
            @endif
        </form>
    </div>


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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
        @if (session('success'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'my-custom-swal'
                },
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: "{{ session('success') }}"
            });
        @endif
        @if ($errors->any())
            let errors = {!! json_encode($errors->all()) !!};
            let errorList = '<ol>' + errors.map(function(error) {
                return '<li style="text-align: start">' + error + '</li>';
            }).join('') + '</ol>';

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: errorList,

            });
        @endif
        @if (session('warning'))
            let error = '{{ session('warning') }}';
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error,
            });
        @endif
        @if (session('error'))
            let error = '{{ session('error') }}';
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error,
            });
        @endif
    </script>
</body>

</html>
