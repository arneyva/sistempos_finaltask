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

    <!-- font aldrich -->
    <link href="https://fonts.googleapis.com/css2?family=Aldrich&display=swap" rel="stylesheet">

    {{-- select2 css --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>

        /* Custom CSS to adjust the Bootstrap media query breakpoints */
        body, html, .modal-content {
            background: linear-gradient(to right, #94d4f9, #C6E5F8);
            height: 100%;
            margin: 0;
            overflow: hidden; /* Mencegah halaman dari scrolling */
            font-size: 1.2vw;
        }
        .modal-header {
            border-color:grey;
        }
        .row.full-height {
            height: 100%;
            padding: 1.6vw; /* Menambahkan padding untuk seluruh container */
            box-sizing: border-box; /* Memastikan padding termasuk dalam ukuran total */
        }

        .card {
                height: 100%; /* Mengurangi padding atas dan bawah dari tinggi total */
                width: 100%; /* Mengatur lebar kartu untuk mengisi kolom sepenuhnya */
                background-color: rgba(255, 255, 255, 0); /* Warna latar belakang putih dengan transparansi 50% */
                border: none;
        }
        .card .card-body {
                height: 100% !important; /* Mengurangi padding atas dan bawah dari tinggi total */
                width: 100% !important; /* Mengatur lebar kartu untuk mengisi kolom sepenuhnya */
                overflow-x: auto !important;
                background-color: rgba(255, 255, 255, 0.37);
                border-radius: 0px 0px 10px 10px ;
        }
        .card-body.search-product {
            height: 10% !important;
            border-radius: 10px 10px 0px 0px !important;
        }
        .card .card-header {
            background-color: rgba(255, 255, 255, 0);
        }
        .card-right{
            padding-right:0.9vw;
            padding-left:0.5vw;
        }
        .card-left{
            padding-left:0.9vw;
            padding-right:0.5vw;
        }
        .card-table-wrapper {
            overflow-x: auto;
        }
        .full-height {
            height: 100%;
        }
        .full-width {
            width: 100%;
        }
        .d-flex {
            display: flex;
        }
        .align-items-center {
            align-items: center;
        }
        .justify-content-center {
            justify-content: center;
        }
        .card-header.for-information {
            height: 11%;
        }
        .card-header.for-calculation {
            height: 60%;
        }
        .card-header.for-customer {
            height: 11%;
        }
        .card-header.for-pay {
            height: 28%;
        }
        .card-header.for-sales {
            height: 20%;
        }
        .card-header.for-gap {
            height: 37%;
        }
        .card-header.for-logout {
            height: 15%;
        }
        .col-custom {
            border-radius: 4px; /* Opsional: untuk membuat sudut kolom lebih halus */
            height: 100% !important; /* Mengurangi padding atas dan bawah dari tinggi total */
            width: 100% !important; /* Mengatur lebar kartu untuk mengisi kolom sepenuhnya */
            box-sizing: border-box;
        }
        .col-custom-header {
            background-color: #415C86;
            height: 38% !important;
            color: #fff;
            border-radius: 4px 4px 0px 0px;
            font-size:1vw !important;
            padding-left:1vw;
            
        }
        .col-staff {
            height: 62% !important;
            border-radius: 0px 0px 4px 4px;
        }
        .col-custom.for-calculation, .for-text-calculation {
            background-color: black !important;
            color: #A5FF5F !important;
            color: #A5FF5F !important;
            padding: 0vw 1vw 0vw 1vw;
            font-family: 'Aldrich', sans-serif;
            border-radius:0.7vw;
        }
        .logo-title {
            font-size:2.47vw !important;
            color:antiquewhite;
        }
        .icon-30 {
            width:3.6vw;
            height:3.6vw;
        }
        td {
            background-color: rgba(255, 255, 255, 0.37) !important;
            font-weight: 300 !important;
        }
        .for-header-product {
            background-color: rgba(255, 255, 255, 0) !important;
        }
        th {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: black !important;
        }
        .for-table-product {
            border-color: rgba(156, 156, 156, 0.4) !important;
        }
        .background-color-1 {
            background-color: #DBF2FC !important;
        }
        #customer {
            background-color: #DBF2FC !important;
            border: 0px !important;
            border-radius: 20px 0px 0px 20px !important;
            padding-left:1.5vw;
        }
        .for-customer-button {
        border: none;
        color: #fff;
        background-image: linear-gradient(30deg, #0400ff, #4ce3f7);
        border-radius: 0px 20px 20px 0px;
        padding: 0.6em 1.5em;
        background-position: right center;
        background-size: 200% auto;
        font-size:1.1vw;
        font-weight:500;
        }

        .for-pay-button {
            border-radius: 4px 4px 4px 4px !important;
            font-size:2.5vw !important;
            font-weight:600 !important;
            color:black;
            font-family: 'Aldrich', sans-serif;
            background-image: linear-gradient(160deg, #bde9ca, #BDE8C8, #82D4B2) !important;
            letter-spacing: 0.3vw;
        }

        .for-sales .for-pay-button {
            letter-spacing: 0vw !important;
            font-size:1.7vw !important;
            font-weight:600 !important;
        }

        .for-customer-button:focus {
        background-size: 100% auto;
        background-position: center;
        background-image: linear-gradient(6deg, #0400ff, #4ce3f7);
        }
        .for-pay-button:focus {
        background-image: linear-gradient(6deg, #c0e9cc, #9ddd89) !important;
        }


        .col-staff .select2-container--default,
        .search-product .select2-container--default {
            background-color: transparent; /* Warna latar belakang select */
            color: black; /* Warna teks select */
            border: none; /* Hilangkan border */
            height: 100%; /* Tinggi penuh */
            overflow: hidden;
        }

        .col-staff .select2-container--default .select2-selection--single,
        .search-product .select2-container--default .select2-selection--single {
            background-color: transparent; /* Warna latar belakang select */
            color: black; /* Warna teks select */
            border: none; /* Hilangkan border */
            height: 100%; /* Tinggi penuh */
            display: flex;
            align-items: center; /* Atur posisi vertikal ke tengah */
            overflow: hidden;
        }


        .col-staff .select2-container--default .select2-selection--single .select2-selection__rendered 
        ..search-product .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal; /* Atur line-height agar teks berada di tengah */
            display: flex;
            align-items: center; /* Atur posisi vertikal konten ke tengah */
            overflow: hidden;
        }
        .col-staff .select2-container--default .select2-selection--single .select2-selection__rendered img {
            min-width: 2.5vw !important; 
            width: 2.5vw !important; 
            height: 2.5vw !important;
            margin-top: 0.2vw;
            margin-bottom: 0.2vw;
        }
        .search-product .select2-container--default .select2-selection--single {
            background-color: rgba(255, 255, 255, 0.37) !important;        
        }
        .select2-selection__arrow {
            height: 100% !important;
            display: flex !important;
            align-items: center !important; /* Atur posisi vertikal panah ke tengah */
        }
        .staff-avatar {
            min-width: 2.5vw !important; 
            width: 2.5vw !important; 
            height: 2.5vw !important; 
        }
        .select2-selection__rendered .caption-title {
            flex-shrink: 0; /* Pastikan header tidak menyusut */
            overflow: hidden; /* Sembunyikan overflow jika teks terlalu banyak */
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1; /* Batas jumlah baris (ganti sesuai kebutuhan) */
            text-overflow: ellipsis;
            white-space: normal;
        }
        .select2-selection__placeholder {
            color:black !important;
        }
        
    </style>
</head>
<body>
    <div class="row full-height">
        <div class="col-7 full-height d-flex  justify-content-center card-left">
            <div class="card">
                <div class="card-header for-information d-flex  justify-content-center p-0 mb-3">
                    <div class="row no-gutters full-height full-width p-0 ">
                        <div class="col px-0 d-flex align-items-center">
                            <!-- Konten kolom 1 -->
                            <img src="{{ asset('hopeui/html/assets/images/logota3.png') }}" class="text-primary icon-30 me-2"
                            alt="">
                            <h3 class="logo-title">Project TA</h3>
                        </div>
                        <div class="col col-custom mx-2 px-0  background-color-1">
                            <div class="col col-custom-header"><p class="mb-0">Staff</p></div>
                                <div class="col col-staff background-color-1">
                                    <select id="staffDropdown" class="form-control" name="staff" style="width: 100%;">
                                        <option value=""></option>
                                        @foreach($staff as $data)
                                            <option 
                                                value="{{$data->id}}" 
                                                data-avatar="{{$data->avatar}}">
                                                {{$data->firstname}} {{$data->lastname}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        <div class="col col-custom px-0 background-color-1">
                            <div class="col col-custom-header">Transaction Code</div>
                            <div class="col col-staff d-flex align-items-center ps-3" style="color:black;">
                                Konten Kolom 4
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body search-product p-0">
                    <select id="productDropdown" class="form-control" style="width: 100%;">
                        <option value=""></option>
                        @foreach($products as $product)
                            @if ($product['variant']->isEmpty())
                                <option 
                                    value="{{ $product['productData']->id}}" 
                                    data-image="{{ $product['productData']->image }}" 
                                    data-unit="{{ $product['productData']->unitPurchase->ShortName ?? '' }}" 
                                    data-code="{{$product['productData']->code}}" 
                                    data-cost="{{$product['productData']->cost }}">
                                {{ $product['productData']->name }}
                                </option>
                            @else
                                @foreach($product['variant'] as $variant)
                                    <option 
                                        value="{{ $product['productData']->id}}" 
                                        data-image="{{ $product['productData']->image }}" 
                                        data-unit="{{ $product['productData']->unitSale->ShortName ?? '' }}" 
                                        data-code="{{$variant['variantData']->code}}" 
                                        data-cost="{{ $variant['variantData']->cost }}"
                                        data-id="{{ $variant['variantData']->id}}">
                                    {{ $product['productData']->name }} {{  $variant['variantData']->name }}
                                    </option>
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                    <input type="hidden" id="products" name="products">
                    <input type="hidden" id="products_with_variant" name="products_with_variant">
                    <input type="hidden" id="barcode_variant_id" name="barcode_variant_id">
                </div>
                <div class="card-body p-0">
                    <div class="card-table-wrapper">
                        <table id="selectedItemsTable" class="table table-bordered for-table-product ">
                            <thead>
                                <tr class="for-header-product text-center ">
                                    <th class="col-6">Product Info</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th></th>

                                    <!-- Tambahkan header lain jika perlu -->
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Data 1</td>
                                    <td>Data 2</td>
                                    <td>Data 3</td>
                                    <td></td>
                                </tr>
                                <!-- Tambahkan baris lain jika perlu -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-5 full-height d-flex  justify-content-center card-right">
            <div class="card">
                <div class="card-header for-calculation d-flex  justify-content-center p-0 mb-3">
                    <div class="row no-gutters full-height full-width p-0 ">
                        <div class="col col-custom for-calculation px-0">
                            <div class="for-text-calculation d-flex justify-content-between">
                                <p style="font-size:1.17vw; margin-bottom:0.5vw;margin-top:1.5vw;" >subtotal</p>
                                <p id="order_subtotal" style="font-size:2.8vw;">Rp 40.000</p>
                                <input type="hidden" name="order_subtotal_input" id="order_subtotal_input">
                            </div>
                            <div class="for-text-calculation d-flex justify-content-between">
                                <p style="font-size:1.17vw;margin-bottom:0.3vw;">tax</p>
                                <p id="order_tax" style="font-size:1.17vw;margin-bottom:0.3vw;">10 %</p>
                                <input type="hidden" name="order_tax_input" id="order_tax_input">
                            </div>
                            <div class="for-text-calculation d-flex justify-content-between">
                                <p style="font-size:1.17vw;margin-bottom:0.1vw;">discount</p>
                                <p id="order_discount" style="font-size:1.17vw;margin-bottom:0.1vw;">Rp 25.000</p>
                                <input type="hidden" name="order_discount_input" id="order_dicount_input">
                            </div>
                            <div class="for-text-calculation d-flex justify-content-between">
                                <p style="font-size:1.17vw;margin-bottom:0.7vw;margin-top:0.64vw;">grandtotal</p>
                                <p id="order_total" style="font-size:1.8vw;margin-bottom:0.7vw;">Rp 70.000</p>
                                <input type="hidden" name="order_total_input" id="order_total_input">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header for-customer d-flex  justify-content-center p-0 mb-2">
                    <div class="row no-gutters full-height full-width p-0 ">
                        <div class="col-8 px-0 d-flex">
                            <input type="text" class="full-height full-width" name="customer" id="customer" disabled>
                        </div>
                        <div class="col-4  px-0">
                            <button type="button" class="full-height full-width for-customer-button" data-bs-toggle="modal" data-bs-target="#addClient">add customer</button>
                        </div>
                    </div>
                </div>
                <div class="card-header for-pay d-flex  justify-content-center p-0 mb-2">
                    <div class="row no-gutters full-height full-width p-0 ">
                        <div class="col col-custom px-0">
                            <button type="button" class="full-height full-width for-customer-button for-pay-button" data-bs-toggle="modal" data-bs-target="#addClient">PAY</button>
                        </div>
                    </div>
                </div>
                <div class="card-header for-sales d-flex  justify-content-center p-0 mb-2">
                    <div class="row no-gutters full-height full-width p-0 ">
                        <div class="col col-custom me-2 px-0">
                            <button type="button" class="full-height full-width for-customer-button for-pay-button" data-bs-toggle="modal" data-bs-target="#addClient">New Sale</button>
                        </div>
                        <div class="col col-custom ms-2 px-0">
                            <button type="button" class="full-height full-width for-customer-button for-pay-button" data-bs-toggle="modal" data-bs-target="#addClient">All Sales</button>
                        </div>
                    </div>
                </div>
                <div class="card-header for-gap">
                </div>
                <div class="card-header for-logout d-flex  justify-content-center p-0 mt-2">
                    <div class="row no-gutters full-height full-width p-0 ">
                        <div class="col col-custom me-1 px-0">
                        </div>
                        <div class="col col-custom ms-1 px-0">
                        </div>
                        <div class="col col-custom ms-1 px-0">
                        </div>
                        <div class="col col-custom ms-1 px-0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="addClient" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Add Customer Membership</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
            </div>
        </div>
    </div>
</div>

{{-- jquery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- bootsrap js --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
<script src="{{ asset('/sw.js') }}"></script>
{{-- selecet2 js --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
{{-- sweetalert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#staffDropdown').select2({
            placeholder: "Add Staff...",
            templateResult: formatUser,
            templateSelection: formatUser,
            minimumResultsForSearch: Infinity // Menonaktifkan pencarian
        });

        function formatUser (user) {
            if (!user.id) {
                return user.text;
            }
            var $user = $(
                '<div class="d-flex align-items-center">'+ 
                    '<img class="img-fluid avatar avatar-50 avatar-rounded staff-avatar" src="/hopeui/html/assets/images/avatars/'+ $(user.element).data('avatar') + '"alt="profile">'+
                    '<a style="margin-right:10px;">'+'<a/>'+
                    '<div>'+
                        '<h6 class="mb-0 caption-title">'+ user.text + '</h6>'+
                    '</div>'+
                '</div>'
            );
            return $user;
        };
    });
</script>

<script>
    $(document).ready(function() {
        $('#productDropdown').select2({
            placeholder: "Scan/Search Product by Code or Name",
            templateResult: formatUser,
            templateSelection: formatUser,
            matcher: customMatcher
        });
        $('#productDropdown').on('select2:open', function() {
            $('.select2-search__field').select(); // Mengatur fokus ke kotak pencarian
        });

        function formatUser (user) {
            if (!user.id) {
                return user.text;
            }
            var $user = $(
                '<div class="d-flex align-items-center">'+ 
                    '<a style="margin-right:10px;">'+'<a/>'+
                    '<div>'+
                        '<h6 class="mb-0 caption-title">'+ user.text + '</h6>'+
                        '<p class="mb-0 caption-sub-title">' +  $(user.element).data('code') + '</p>'+
                    '</div>'+
                '</div>'
            );
            return $user;
        };

        function customMatcher(params, data) {
            // If there are no search terms, return all data
            if ($.trim(params.term) === '') {
                return data;
            }

            // `params.term` should be the term that is used for searching
            // `data.text` is the text that is displayed for the data object
            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                return data;
            }

            var code = $(data.element).data('code');
            if (code && code.toString().toLowerCase().indexOf(params.term.toLowerCase()) >= 0) {
                return data;
                }
                
            // Return `null` if the term should not be displayed
            return null;
        }
        });
</script>

<script>
    $(document).ready(function() {
        var subtotal = 0;
        var tax = 0;
        var discount = 0;
        var grandtotal = 0;
        var downPayment = 0;

        function setTablePayment() {
            // 1. Akumulasi nilai dari setiap .subtotal
            subtotal = 0;
            $('.subtotal').each(function() {
                var value = parseFloat($(this).text());
                subtotal += isNaN(value) ? 0 : value;
            });

            // 2. Hitung nilai pajak (tax) dari subtotal
            var taxValue = parseFloat($('#tax').val());
            var taxPercentage = isNaN(taxValue) ? 0 : taxValue / 100;
            tax = subtotal * taxPercentage;

            // 3. Ambil nilai diskon
            var discountValue = parseFloat($('#discount').val());
            discount = isNaN(discountValue) ? 0 : discountValue;

            // 4. Hitung grandtotal
            grandtotal = subtotal + tax - discount;

            if (grandtotal < 0) {
                grandtotal = 0;
            };

            // 5. Hitung down payment (dp) dari grandtotal
            var downPaymentValue = parseFloat($('#down_payment').val());
            var downPaymentPercentage = isNaN(downPaymentValue) ? 0 : downPaymentValue / 100;
            downPayment = grandtotal * downPaymentPercentage

            $('#order_down_payment').text('Rp ' + downPayment);
            $('#order_subtotal').text('Rp ' + subtotal);
            $('#order_discount').text('Rp ' + discount);
            $('#order_tax').text('Rp ' + tax);
            $('#order_total').text('Rp ' + grandtotal);
            
            $('#order_down_payment_input_input').val(downPayment);
            $('#order_subtotal_input').val(subtotal);
            $('#order_discount_input').val(discount);
            $('#order_tax_input').val(tax);
            $('#order_total_input').val(grandtotal);
        };
    });
</script>

<script>
    $(document).ready(function() {
        var products = [];
        var products_with_variant = [];
        var productsObj = {};
        var productsWithVariantObj = {};

        $('#itemDropdown').change(function() {
            var selectedValue = $(this).val();
            if (selectedValue) {
                var name = $('#itemDropdown option:selected').text();
                var image = $('#itemDropdown option:selected').data('image');
                var code = $('#itemDropdown option:selected').data('code');
                var cost = $('#itemDropdown option:selected').data('cost');
                var unit = $('#itemDropdown option:selected').data('unit');
                var id = $('#itemDropdown option:selected').data('id');
                if ( $('#barcode_variant_id').val().trim() !== '') {
                    id = $('#barcode_variant_id').val();
                }
                $('#barcode_variant_id').val('');

                var newRow = '<tr>' +
                            '<td><div class="d-flex align-items-center">' +
                            '<div class="d-flex flex-column">'+ 
                            '<div style="margin-bottom: 5px; word-wrap: break-word; word-break: break-all;white-space: normal;">'+
                            '<h6>' + name + '</h6>'+
                            '</div> <div>' + code +
                            '</div></div></div></td>'
                        if (id) {
                            newRow += '<td class="variant-id" data-variant="'+id+'" style="display:none;" hidden></td>';
                        }
                    newRow += '<td style="text-align: center;">' +
                            '<button type="button" class="btn btn-sm btn-danger subtract" style="float: left;"><h5 style="color:white;">-</h5></button> ' +
                            '<span class="qty">1</span> ' +
                            '<span>' + unit + '</span> ' +
                            '<button type="button" class="btn btn-sm btn-primary add" style="float: right;"><h5 style="color:white;">+</h5></button>' +
                            '</td>' +
                            '<td class="subtotal">' + cost + '</td>' +
                            '<td>' +
                            '<div class="flex align-items-center list-user-action"> ' +
                            '<a class="btn btn-sm btn-icon btn-danger delete" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" data-value="'+selectedValue+'" data-code="'+code+'">' +
                            '<span class="btn-inner"><svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor"><path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>' +
                            '</a>' +
                            '</div>' +
                            '</td>' +
                            '</tr>';

                //paremeter untuk apakah item sudah ada atau belum
                var itemExists = false;

                //cek setiap row
                $('#selectedItemsTable tbody tr').each(function() {
                    var rowCode = $(this).find('.delete').data('code');
                    if (rowCode == code) {
                        var qtyElement = $(this).find('.qty');
                        var subtotalElement = $(this).find('.subtotal');
                        var pastQty = parseInt(qtyElement.text());
                        var pastElement = parseFloat(subtotalElement.text());
                        //jika ada tambahkan qty dengan 1
                        qtyElement.text(pastQty + 1);
                        //jika ada tambahkan elemen dengan cost
                        subtotalElement.text(pastElement + parseFloat(cost));
                        // parameter menjadi true
                        itemExists = true;
                        // Break the loop
                        return false; 
                    }
                });

                if(id){
                    for (var i = 0; i < products_with_variant.length; i++) {
                        if (products_with_variant[i].key == id) {
                            currentQty=parseInt(products_with_variant[i].value) + 1;
                            products_with_variant[i].value = currentQty;
                            $.each(products_with_variant, function (i, value) {
                                productsWithVariantObj[value.key] = value.value;
                            })
                            break;
                        }
                    }
                    $('#products_with_variant').val(JSON.stringify(productsWithVariantObj));
                } else {
                    for (var i = 0; i < products.length; i++) {
                        if (products[i].key == selectedValue) {
                            currentQty=parseInt(products[i].value) + 1;
                            products[i].value = currentQty;
                            $.each(products, function (i, value) {
                                productsObj[value.key] = value.value;
                            })
                            break;
                        }
                    }
                    $('#products').val(JSON.stringify(productsObj));
                }


                //mengacu jika parameter false maka masukan ke tabel
                if (!itemExists) {
                    $('#selectedItemsTable tbody').append(newRow);
                    if (!id){
                        products.push({ key: selectedValue, value: 1 });
                        $.each(products, function (i, value) {
                            productsObj[value.key] = value.value;
                        })
                        $('#products').val(JSON.stringify(productsObj));
                    } else {
                        products_with_variant.push({ key: id, value: 1 });
                        $.each(products_with_variant, function (i, value) {
                            productsWithVariantObj[value.key] = value.value;
                        })
                        $('#products_with_variant').val(JSON.stringify(productsWithVariantObj));
                    }
                }
                //set table info hasil perhitungan
                setTablePayment();
                $('#itemDropdown').val(null).trigger('change');
            console.log(JSON.stringify(productsObj));
            console.log(JSON.stringify(productsWithVariantObj));
            }
        });

        $(document).on('click', '.add', function() {
            var row = $(this).closest('tr');
            var qtyElement = $(this).siblings('.qty');
            var pastQty = parseInt(qtyElement.text());
            var cost = $(this).closest('tr').find('.cost').text();
            var subtotalElement = $(this).closest('tr').find('.subtotal');
            var value = row.find('.delete').data('value');
            var variant = row.find('.variant-id').data('variant');

            currentQty= pastQty+1;
            if (currentQty > 0) {
                qtyElement.text(currentQty);
                subtotalElement.text(cost * currentQty);

                if (!variant) {
                    for (var i = 0; i < products.length; i++) {
                        if (products[i].key == value) {
                            products[i].value = currentQty;
                                $.each(products, function (i, value) {
                                productsObj[value.key] = value.value;
                            })
                            break;
                        }
                    }
                $('#products').val(JSON.stringify(productsObj));
                } else {
                    for (var i = 0; i < products_with_variant.length; i++) {
                        if (products_with_variant[i].key == variant) {
                            products_with_variant[i].value = currentQty;
                            $.each(products_with_variant, function (i, value) {
                                productsWithVariantObj[value.key] = value.value;
                            })
                            break;
                        }
                    }
                $('#products_with_variant').val(JSON.stringify(productsWithVariantObj));
                }
            }
            //set table info hasil perhitungan
            setTablePayment();
            console.log(JSON.stringify(productsObj));
            console.log(JSON.stringify(productsWithVariantObj));
        });

        $(document).on('click', '.subtract', function() {
            var row = $(this).closest('tr');
            var qtyElement = $(this).siblings('.qty');
            var pastQty = parseInt(qtyElement.text());
            var cost = row.find('.cost').text();
            var subtotalElement = row.find('.subtotal');
            var value = row.find('.delete').data('value');
            var variant = row.find('.variant-id').data('variant');

            currentQty= pastQty-1;
            if (currentQty > 0) {
                qtyElement.text(currentQty);
                subtotalElement.text(cost * currentQty);

                if (!variant) {
                    for (var i = 0; i < products.length; i++) {
                    if (products[i].key == value) {
                        products[i].value = currentQty;
                        $.each(products, function (i, value) {
                            productsObj[value.key] = value.value;
                        })
                        break;
                    }
                }
                $('#products').val(JSON.stringify(productsObj));
                } else {
                    for (var i = 0; i < products_with_variant.length; i++) {
                    if (products_with_variant[i].key == variant) {
                        products_with_variant[i].value = currentQty;
                        $.each(products_with_variant, function (i, value) {
                            productsWithVariantObj[value.key] = value.value;
                        })
                        break;
                    }
                }
                $('#products_with_variant').val(JSON.stringify(productsWithVariantObj));
                }
            } 
            else {
                //hapus item di tabel
                row.remove();
                if(!variant) {
                    for (var i = 0; i < products.length; i++) {
                    if (products[i].key == value) {
                        products.splice(i, 1);
                        $.each(products, function (i, value) {
                            productsObj[value.key] = value.value;
                        })
                        // Menghapus key dari productsObj yang tidak ada di products
                        Object.keys(productsObj).forEach(function(key) {
                        if (!products.some(product => product.key === key)) {
                            delete productsObj[key];
                        }
                        });
                        break;
                    }
                }
                $('#products').val(JSON.stringify(productsObj));
                } else {
                    for (var i = 0; i < products_with_variant.length; i++) {
                        if (products_with_variant[i].key == variant) {
                            products_with_variant.splice(i, 1);
                            $.each(products_with_variant, function (i, value) {
                                productsWithVariantObj[value.key] = value.value;
                            })
                            // Menghapus key dari productsWithVariantObj yang tidak ada di products_with_variant
                            Object.keys(productsWithVariantObj).forEach(function(key) {
                            if (!products_with_variant.some(product => product.key === key)) {
                                delete productsWithVariantObj[key];
                            }
                            });
                            break;
                        }
                    }
                    $('#products_with_variant').val(JSON.stringify(productsWithVariantObj));
                }

            }
            //set table info hasil perhitungan
            setTablePayment();
            console.log(JSON.stringify(productsObj));
            console.log(JSON.stringify(productsWithVariantObj));
        });

        $('#selectedItemsTable').on('click', '.delete', function() {
            var row = $(this).closest('tr');
            var value = $(this).data('value');
            var qty = row.find('.qty').text()
            var variant = row.find('.variant-id').data('variant');

            //hapus item di tabel
            row.remove();

            if (!variant) {
                for (var i = 0; i < products.length; i++) {
                    if (products[i].key == value) {
                        products.splice(i, 1);
                        break;
                    }
                }
                $.each(products, function (i, value) {
                    productsObj[value.key] = value.value;
                })
                // Menghapus key dari productsObj yang tidak ada di products
                Object.keys(productsObj).forEach(function(key) {
                    if (!products.some(product => product.key === key)) {
                        delete productsObj[key];
                    }
                });
                $('#products').val(JSON.stringify(productsObj));
            } else {
                // Menghapus produk dengan varian tertentu dari array products_with_variant
                for (var i = 0; i < products_with_variant.length; i++) {
                    if (products_with_variant[i].key == variant) {
                        products_with_variant.splice(i, 1);
                        break; // Hentikan loop setelah menemukan dan menghapus item
                    }
                }

                // Mengisi objek productsWithVariantObj dengan nilai dari array yang telah dimodifikasi
                $.each(products_with_variant, function (i, value) {
                    productsWithVariantObj[value.key] = value.value;
                });
                // Menghapus key dari productsWithVariantObj yang tidak ada di products_with_variant
                Object.keys(productsWithVariantObj).forEach(function(key) {
                if (!products_with_variant.some(product => product.key === key)) {
                    delete productsWithVariantObj[key];
                }
                });
                $('#products_with_variant').val(JSON.stringify(productsWithVariantObj));
            }
            //set table info hasil perhitungan
            setTablePayment();
            console.log(JSON.stringify(productsObj));
            console.log(JSON.stringify(productsWithVariantObj));
        });

    });
</script>

<script>
    var barcode='';
    var interval;
    document.addEventListener('keydown', function(evt) {
        if (interval)
            clearInterval(interval);
        if (evt.code == 'Enter') {
            if (barcode)
                handleBarcode(barcode);
            barcode='';
            return;
        } if (evt.key != 'Shift')
            barcode += evt.key;
        interval = setInterval(() => barcode ='', 20);
    });

    function handleBarcode(scanned_barcode) {
        //kirim ajax untuk mendapatkan id produk
        //habis itu trigger change itemsdropdown dengan value id yang didapat 
        $.ajax({
            url: `/purchases/scanner/${scanned_barcode}`,
            type: 'post',
            dataType: 'json',
            data: {
            },
            headers: { 
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (response) {
                if (response.error) {
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
                        icon: "error",
                        title: response.error
                    });
                } else {
                    if (response.product_id != null) {
                        $('#barcode_variant_id').val('');
                        $('#barcode_variant_id').val(response.id);
                        $('#itemDropdown').val(response.product_id).trigger('change');
                    } else {
                        $('#itemDropdown').val(response.id).trigger('change');
                    }
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terdapat error pada server'
                });
                // Log the error for debugging
                console.error('Error: ', error);
                console.error('Response: ', xhr.responseText);
            }
        });
    }
</script>
</body>
</html>
