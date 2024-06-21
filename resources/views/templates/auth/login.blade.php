<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Hope UI | Responsive Bootstrap 5 Admin Dashboard Template</title>

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

    <link rel="stylesheet" href="{{ asset('hopeui/html/assets/css/bootstrap-pincode-input.css') }}">



</head>

<body class=" " data-bs-spy="scroll" data-bs-target="#elements-section" data-bs-offset="0" tabindex="0">
    <!-- loader Start -->
    <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body">
            </div>
        </div>
    </div>
    <!-- loader END -->

    <div class="wrapper">
        <section class="login-content">
            <div class="row m-0 align-items-center bg-white vh-100">
                <div class="col-md-6">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                                <div class="card-body">
                                    <a href="../../dashboard/index.html"
                                        class="navbar-brand d-flex align-items-center mb-3">

                                        <!--Logo start-->
                                        <div class="logo-main">
                                            <div class="logo-normal">
                                                <img src="{{ asset('hopeui/html/assets/images/logota3.png') }}"
                                                    class="text-primary icon-30" alt="">
                                            </div>
                                            <div class="logo-mini">
                                                <img src="{{ asset('hopeui/html/assets/images/logota3.png') }}"
                                                    class="text-primary icon-30" alt="">
                                            </div>
                                        </div>
                                        <!--logo End-->
                                        <h4 class="logo-title ms-3">Project TA</h4>
                                    </a>
                                    <h2 class="mb-2 text-center">Sign In</h2>
                                    <!-- <p class="text-center">Login to stay connected.</p> -->
                                    <p class="text-center my-3">Use your PIN</p>
                                    <form method="POST" action="{{ route('login') }}" id="login">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <input type="text" name="pin" class="form-control @error('pin') is-invalid @enderror" id="pin" placeholder="PIN">
                                                @error('pin')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <p class="text-center mt-4">Or sign in with email and password</p>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" placeholder="Email"
                                                        aria-label="Email" aria-describedby="email-addon" name="email"
                                                        :value="old('email')" autofocus
                                                        autocomplete="username">
                                                </div>
                                                @error('email')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="password" class="form-label">Password</label>
                                                    <input type="password" class="form-control" placeholder="Password"
                                                        aria-label="Password" aria-describedby="password-addon"
                                                        name="password" autocomplete="current-password">
                                                </div>
                                                @error('password')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div class="col-lg-12 d-flex justify-content-between">
                                                <div class="form-check mb-3">
                                                    <input type="checkbox" class="form-check-input" id="remember_me"
                                                        name="remember">
                                                    <label class="form-check-label" for="customCheck1">Remember
                                                        Me</label>
                                                </div>
                                                <a href="#">Forgot Password?</a>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <button type="submit" class="btn btn-primary">Sign In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
                    <img src="{{ asset('hopeui/html/assets/images/auth/01.png') }}"
                        class="img-fluid gradient-main animated-scaleX" alt="images">
                </div>
            </div>
        </section>
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

    <script src="{{ asset('hopeui/html/assets/js/bootstrap-pincode-input.js') }}"></script>

    <script>
$(document).ready(function() {
    $('#pin').pincodeInput({
        inputs: 6,
        complete: function(value, e, errorElement) {
            // Check if Enter key is pressed
            if (e.keyCode === 13) {
                // Submit the form
                $('#login').submit();
            }
        }
    });
});
</script>

</body>

</html>
