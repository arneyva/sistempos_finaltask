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

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            background:white !important;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80vw;
            max-width: 80vw;
            min-width: 80vw;
            background-color: white;
            margin: 0 auto;
            padding: 2vw;
            box-shadow: 0 0 1vw rgba(0, 0, 0, 0.1);
        }
        .header {
            display:flex;
            justify-content:left;
            align-items:center;
            height:100% !important;
            margin-bottom: 4vw;
        }
        .header img {
            height: 6vw;
        }
        .header .company-name {
            font-size: 3.6vw;
            font-weight: bold;
            float: left;
            margin-left:4.4vw;
        }
        .content {
            margin-bottom: 2vw;
            font-size: 1.4vw !important;
        }
        .barcode {
            display:none;
        }
        .button {
            background-color: #dbdbdb;
            color: white;
            padding: 2vw 3vw;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 1.2vw;
            margin: 1.5vw 1vw 1vw 1vw;
            cursor: pointer;
            border-radius: 0.8vw;
        }
        .pin {
            display:none;
        }
        .signature {
            text-align: right;
            font-size:1.3vw !important;
            margin-top: 2vw;
        }
        pre {
            font-family: Arial, sans-serif;
            line-height: 1.5;
        }
        .password-input {
            -webkit-text-security: disc; /* Untuk Chrome/Safari */
            -moz-text-security: disc; /* Untuk Firefox */
        }

        @media print {
            /* Menyembunyikan elemen input, textarea, dan button saat mencetak */
            input, textarea, button, select {
                display: none !important;
            }

            /* Mengatur ukuran halaman menjadi A4 */
            @page {
                size: A4;
                margin-top: 13mm; /* Memberikan jarak 30mm antara konten dan tepi kertas */
                margin-bottom: 13mm; /* Memberikan jarak 30mm antara konten dan tepi kertas */
                margin-left: 6mm; /* Memberikan jarak 30mm antara konten dan tepi kertas */
                margin-right: 6mm; /* Memberikan jarak 30mm antara konten dan tepi kertas */
            }



            /* Menampilkan nilai dari input dan textarea sebagai teks biasa saat mencetak */
            .print-value {
                display: inline !important;
            }
            .barcode {
                display:flex !important;
                justify-content:center !important;
            }

            .barcode img {
                height: 7.5vw !important;
            }
            .header img {
                height: 8vw !important;
            }
            .pin {
                display:block !important;
                text-align: center !important;
                font-size: 2.6vw !important;
                margin-bottom: 1.4vw !important;
                margin-top: 0.3vw !important;
            }
            .content {
                margin-bottom: 2vw !important;
                font-size: 2.4vw !important;
            }
        }
        
        .print-value {
            display: none;
        }

    </style>


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
                <div class="col-12">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card px-1">
                                <div class="card-body">
                                        <div class="header">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/9/9d/Logo_Indomaret.png" alt="Company Logo">
                                            <div class="company-name">{{ $settings->CompanyName }}</div>
                                        </div>
                                        <div class="content">
                                            <p style="margin-bottom: 0.15vw;">Dear {{ $supplier->name }},</p>
                                            <p style="margin-top: 0.15vw;margin-bottom: 1.15vw;">{{ $supplier->adresse }}</p>
                                            <p style="margin-bottom: 1.15vw;">{{ $purchase->date->format('d, F Y') }}</p>
                                            <p>Our company initiate an agreement to purchase order with the following information</p>
                                            <div class="barcode">
                                                <img src="{{$barcode_img}}" alt="barcode">
                                            </div>
                                            <p class="pin"style="margin-top: 0.15vw;margin-bottom: 1.15vw;">{{$purchase->Ref}}</p>
                                        </div>
                                        <form id="purchase_order" enctype="multipart/form-data">
                                        @csrf
                                        @method('PATCH')
                                <div class="row">
                                    <div class="form-group col-sm-12 mb-1">
                                    <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table table-striped">
                    <thead>
                      <tr>
                          <th>Name</th>
                          <th class="col-2 text-right">QTY Order</th>
                          <th class="col-2 text-right">Price</th>
                          <th class="col-2 text-right">Subtotal</th>
                        </tr>
                      </thead>
                      <tbody>
                        @php
                          $totalQty = 0;
                        @endphp
                        @foreach($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex">
                                    <div class="ml-2">
                                        {{$product->product->name}} @if($product->product_variant_id){{$product->product_variant->name}}@endif 
                                        <div class="text-sm text-gray">{{$product->product_variant_id ? $product->product_variant->code : $product->product->code}}</div>
                                    </div>
                            </div>
                            </td>
                            <td class="text-right">{{$product->quantity}}</td>
                            <td class="text-right"><span class=" text-bold">Rp </span>{{$product->cost}}</td>
                            <td class="text-right"><span class=" text-bold">Rp </span>{{$product->total}}</td>
                        </tr>
                        @php
                            $totalQty += $product->quantity; // Add the total to the grand total
                        @endphp
                        @endforeach
                        <tr>
                            <td class="text-left" style="padding-top:1vw !important;padding-left:14vw !important; "><strong>Total Order</strong></td>
                            <td class="text-right" style="padding-top:1vw !important"><strong>{{ number_format($totalQty) }}</strong></td>
                            <td class="col-1" style="padding-top:1vw !important"></td>
                            <td class="text-right" style="padding-top:1vw !important"><strong><span class=" text-bold">Rp </span>{{number_format($purchase->subtotal,0,",",".")}}</strong></td>
                        </tr>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Order Tax</strong></td>
                            <td class="text-right" style="padding-top:1vw !important"><span class=" text-bold">Rp </span><strong id="order_tax">{{number_format($purchase->TaxNet,0,",",".")}}</strong></td>
                            <input type="hidden" name="order_tax_input" id="order_tax_input">
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Order Discount</strong></td>
                            <td class="text-right" style="padding-top:1vw !important"><span class=" text-bold">Rp </span><strong id="order_discount">{{number_format($purchase->discount,0,",",".")}}</strong></td>
                            <input type="hidden" name="order_discount_input" id="order_discount_input">
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Shipping</strong></td>
                            <td class="text-right" style="padding-top:1vw !important"><span class=" text-bold">Rp </span><strong id="order_shipping">{{number_format($purchase->shipment_cost,0,",",".")}}</strong></td>
                            <input type="hidden" name="order_shipping_input" id="order_shipping_input">
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Grand Total</strong></td>
                            <td class="text-right" style="padding-top:1vw !important"><span class=" text-bold">Rp </span><strong id="order_total">{{number_format($purchase->GrandTotal,0,",",".")}}</strong></td>
                            <input type="hidden" name="order_total_input" id="order_total_input">
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Down Payment</strong></td>
                            <td class="text-right" style="padding-top:1vw !important"><span class=" text-bold">Rp </span><strong id="order_down_payment">{{number_format($purchase->down_payment_net,0,",",".")}}</strong></td>
                            <input type="hidden" name="order_down_payment_input" id="order_down_payment_input">
                        </tr>
                    </tbody>
                </table>
                </div>
                <!-- /.col -->
                </div>
                        </div>
                        <div class="col-sm-6 mb-1">
                            <div class="card-header d-flex justify-content-between px-0">
                                <div class="header-title">
                                    <h6 class="card-title">Payment Information</h6>
                                </div>
                            </div>
                            <div class="card-body py-3" style="padding-left:0px;">
                                <div class="new-user-info">
                                    <div class="row">
                                    <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Tax</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <div class="input-group input-group-sm">
                                                    <input type="tel"  aria-describedby="basic-addon2" class="form-control form-control-sm @error('date') is-invalid @enderror" id="tax" name="tax" value="{{old('tax') ?? $purchase->tax_rate}}" required>
                                                    <span class="input-group-text" id="basic-addon2">%</span>
                                                    <span class="print-value" id="print-tax"></span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Discount</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text" id="basic-addon2">Rp</span>
                                                    <input type="tel"  aria-describedby="basic-addon2" class="form-control form-control-sm @error('date') is-invalid @enderror" id="discount" name="discount" value="{{old('discount') ?? $purchase->discount}}" required>
                                                    <span class="print-value" id="print-discount"></span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Down Payment</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <div class="input-group input-group-sm">
                                                    <input type="tel" aria-describedby="basic-addon2"  class="form-control form-control-sm @error('date') is-invalid @enderror" id="down_payment" name="down_payment" value="{{old('down_payment') ?? $purchase->down_payment_rate}}" required>
                                                    <span class="input-group-text" id="basic-addon2">%</span>
                                                    <span class="print-value" id="print-down_payment"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($purchase->statut !== "pending")
                            <div class="card-header d-flex justify-content-between p-0" id="shipping-1">
                                <div class="header-title">
                                    <h6 class="card-title">Shipping Information</h6>
                                </div>
                            </div>
                            <div class="card-body py-3" style="padding-left:0px;" id="shipping-2">
                                <div class="new-user-info">
                                    <div class="row">
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Destination Address</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <textarea class="form-control" id="address" name="address" disabled>{{ $purchase->address ?? '' }}</textarea>
                                                <span class="print-value" id="print-address"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important" >Request Arrive date</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="text" value="{{ $purchase->req_arrive_date ? $purchase->req_arrive_date->translatedFormat('d, F Y') : '-' }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="req_arrive_date" name="req_arrive_date" disabled>
                                                <span class="print-value" id="print-req_arrive_date"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Courier</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <select name="courier" id="courier" class="form-control form-control-sm" required >
                                                    <option value="" selected disabled hidden>Courier</option>
                                                    <option value="jne" {{ old('courier', $purchase->courier) == 'jne' ? 'selected' : '' }} >JNE</option>
                                                    <option value="j&t" {{ old('courier', $purchase->courier) == 'j&t' ? 'selected' : '' }} >J&T</option>
                                                    <option value="sicepat" {{ old('courier', $purchase->courier) == 'sicepat' ? 'selected' : '' }} >SiCepat</option>
                                                    <option value="anteraja" {{ old('courier', $purchase->courier) == 'anteraja' ? 'selected' : '' }} >Anteraja</option>
                                                    <option value="posindo" {{ old('courier', $purchase->courier) == 'posindo' ? 'selected' : '' }} >Pos Infonesia</option>
                                                    <option value="own" {{ old('courier', $purchase->courier) == 'own' ? 'selected' : '' }} >Own Courier</option>
                                                </select> 
                                                <span class="print-value" id="print-courier"></span>                                           
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: none; align-items: center;" id="driver_phone">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Driver Contact</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel" value="{{$purchase->driver_contact ?? '' }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="input_driver_phone" name="driver_phone" >
                                                <span class="print-value" id="print-driver_phone"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: none; align-items: center;" id="shipment_number">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Number</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel" value="{{$purchase->shipment_number ?? '' }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="input_shipment_number" name="shipment_number" >
                                                <span class="print-value" id="print-shipment_number"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="col-sm-6 mb-1">
                            <div class="card-header d-flex justify-content-between px-0">
                                <div class="header-title">
                                    <h6 class="card-title mb-2"></h6>
                                </div>
                            </div>
                            <div class="card-body py-3" style="padding-left:0px;" id="shipping-3">
                                <div class="new-user-info">
                                    <div class="row">
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Payment Term</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <select name="payment_term" id="payment_term" class="form-control form-control-sm" required>
                                                <option value="" selected disabled hidden>Payment Term</option>
                                                <option value="on_invoice" {{ old('payment_term', $purchase->payment_term) == 'on_invoice' ? 'selected' : '' }}>Due on invoice</option>
                                                <option value="7_invoice" {{ old('payment_term', $purchase->payment_term) == '7_invoice' ? 'selected' : '' }}>7 days after invoice</option>
                                                <option value="14_invoice" {{ old('payment_term', $purchase->payment_term) == '14_invoice' ? 'selected' : '' }}>14 Days after Invoice</option>
                                                <option value="on_arrive" {{ old('payment_term', $purchase->payment_term) == 'on_arrive' ? 'selected' : '' }}>Due on arrive</option>
                                                <option value="7_arrive" {{ old('payment_term', $purchase->payment_term) == '7_arrive' ? 'selected' : '' }}>7 days after arrive</option>
                                                <option value="14_arrive" {{ old('payment_term', $purchase->payment_term) == '14_arrive' ? 'selected' : '' }}>14 Days after arrive</option>
                                                </select>
                                                <span class="print-value" id="print-payment_term"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Payment Method</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                            <select name="payment_method" id="payment_method" class="form-control form-control-sm" required>
                                                <option value="" selected disabled hidden>Payment Method</option>
                                                <option value="bni" {{ old('payment_method', $purchase->payment_method) == 'bni' ? 'selected' : '' }}>BNI</option>
                                                <option value="bri" {{ old('payment_method', $purchase->payment_method) == 'bri' ? 'selected' : '' }}>BRI</option>
                                                <option value="mandiri" {{ old('payment_method', $purchase->payment_method) == 'mandiri' ? 'selected' : '' }}>Mandiri</option>
                                                <option value="permata" {{ old('payment_method', $purchase->payment_method) == 'permata' ? 'selected' : '' }}>Permata</option>
                                                <option value="bca" {{ old('payment_method', $purchase->payment_method) == 'bca' ? 'selected' : '' }}>BCA</option>
                                                <option value="gopay" {{ old('payment_method', $purchase->payment_method) == 'gopay' ? 'selected' : '' }}>Gopay</option>
                                                <option value="ovo" {{ old('payment_method', $purchase->payment_method) == 'ovo' ? 'selected' : '' }}>OVO</option>
                                                <option value="cash" {{ old('payment_method', $purchase->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                            </select>    
                                            <span class="print-value" id="print-payment_method"></span>                                       
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: none; align-items: center; " id="supplier_ewalet">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style="margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">E-Walet Number</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel" value="{{$purchase->supplier_ewalet ?? '' }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="input_ewalet" name="supplier_ewalet" >
                                                <span class="print-value" id="print-input_ewalet"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: none; align-items: center;" id="supplier_bank_account">
                                            <div class="col-sm-3 p-0" >
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Bank Account</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel" value="{{$purchase->supplier_bank_account ?? '' }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="input_bank_account" name="supplier_bank_account">
                                                <span class="print-value" id="print-input_bank_account"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($purchase->statut !== "pending")
                            <div class="card-body py-3" style="padding-left:0px; padding-top:2vw !important;">
                                <div class="new-user-info">
                                    <div class="row">
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Cost</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel" value="{{$purchase->shipment_cost ?? '' }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="shipment_cost" name="shipment_cost" required>
                                                <span class="print-value" id="print-shipment_cost"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Estimate Arrive Date</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="date" value="{{$purchase->est_arrive_date ?? '' }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="est_arrive_date" name="est_arrive_date" >
                                                <span class="print-value" id="print-est_arrive_date"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label custom-file-input" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Delivery File</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="file" onchange="checkFileSize(this)" class="form-control form-control-sm @error('date') is-invalid @enderror" id="delivery_file" name="delivery_file" >
                                                <span class="print-value" id="print-delivery_file"></span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label" for="name">Order Note:</label>
                            <textarea class="form-control" id="notes" name="notes" disabled>{{ $purchase->notes ?? '' }}</textarea> 
                            <span class="print-value" id="print-notes"></span>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label" for="name">Supplier Note:</label>
                            <textarea class="form-control" id="supplier_notes" name="supplier_notes" required>{{ $purchase->supplier_notes ?? '' }}</textarea> 
                            <span class="print-value" id="print-supplier_notes"></span>
                        </div>

                    </div>
                    @if($purchase->statut !== "pending")
                    <div class="card-footer d-flex justify-content-start pt-0 px-0">
                        <button type="button" class="btn btn-secondary" style="background-color: #dbdbdb; color: white;" onclick="printPage()">Print Receipt</button>
                    </div>
                    @endif
                    @if($purchase->statut == "pending" || $purchase->statut == "ordered" )
                    <div class="card-footer d-flex justify-content-center pt-0">                        
                        <button type="button" id="accept" data-response="accept" class="btn btn-success me-4">@if($purchase->statut == "pending") Accept @elseif($purchase->statut == "ordered") Confirm Shipped @endif</button>
                        <button type="button" id="refuse" data-response="refuse" class="btn btn-danger ">@if($purchase->statut == "pending") Refuse @elseif($purchase->statut == "ordered") Cancel @endif</button>
                    </div>
                    @endif
                </div>
            </form>
                            </div>
                        </div>
                    </div>
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

    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0"></script>

    {{-- sweetalert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        $('#accept, #refuse').click(function() {
            var form = $('#purchase_order')[0];
            var formData = new FormData(form); // Membuat objek FormData dari form
            var response= $(this).data('response');

             // Menambahkan data tambahan ke FormData
            formData.append('response', response);

            // Cek apakah tombol yang ditekan adalah #accept
            if (this.id === 'accept') {
                // Mentrigger semua required input ketika tombol #accept ditekan
                const requiredInputs = document.querySelectorAll('input[required]');
                const requiredSelects = document.querySelectorAll('select[required]');
                const requiredText = document.querySelectorAll('textarea[required]');
                for (const input of requiredInputs) {
                    if (!input.checkValidity()) {
                        input.reportValidity();
                        return; // Keluar dari fungsi jika ada input yang tidak valid
                    }
                }
                for (const select of requiredSelects) {
                    if (!select.checkValidity()) {
                        select.reportValidity();
                        return; // Keluar dari fungsi jika ada input yang tidak valid
                    }
                }
                for (const text of requiredText) {
                    if (!text.checkValidity()) {
                        text.reportValidity();
                        return; // Keluar dari fungsi jika ada input yang tidak valid
                    }
                }
            } else {
                const supplier_notes=document.getElementById('supplier_notes');
                if (!supplier_notes.checkValidity()) {
                    supplier_notes.reportValidity();
                    return; // Keluar dari fungsi jika ada input yang tidak valid
                }
            }

            // Mengirimkan data menggunakan AJAX
            $.ajax({
                type: "post",
                url: "{{ $update_url }}",
                data: formData,
                processData: false, // Jangan memproses data menjadi string
                contentType: false, // Jangan menetapkan jenis konten
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PATCH' // For Laravel's method spoofing
                },
                success: function(response) {
                    if (response.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: '<ol style="text-align: start">' + response.error + '</ol>',
                        });
                    }
                    else {
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
                            title: response.success
                        });
                        location.reload();
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'there is error in your server code'
                    });
                    // Log the error for debugging
                    console.error('Error: ', error);
                    console.error('Response: ', xhr.responseText);
                }
            });
        });
    });
</script>

<script>
        function printPage() {
            // Dapatkan semua elemen input dan textarea dalam form
            var inputs = document.querySelectorAll('#purchase_order input');
            var selects = document.querySelectorAll('#purchase_order select');
            var textareas = document.querySelectorAll('#purchase_order textarea');
            
            // Loop melalui semua input dan buat elemen span untuk nilai cetak
            inputs.forEach(function(input) {
                var printSpan = document.getElementById('print-' + input.id);
                if (printSpan) {
                    printSpan.textContent = input.value;
                }
            });

            // Loop melalui semua textarea dan buat elemen span untuk nilai cetak
            textareas.forEach(function(textarea) {
                var printSpan = document.getElementById('print-' + textarea.id);
                if (printSpan) {
                    printSpan.textContent = textarea.value;
                }
            });

            // Loop melalui semua textarea dan buat elemen span untuk nilai cetak
            selects.forEach(function(select) {
                var printSpan = document.getElementById('print-' + select.id);
                if (printSpan) {
                    var selectedOptionText = select.options[select.selectedIndex].text;
                    printSpan.textContent = selectedOptionText;
                }
            });

            // Memanggil fungsi print
            window.print();
        }
    </script>

<script>
    $(document).ready(function() {
        var subtotal = {{$purchase->subtotal}};

        function numberFormat(number) {
            return number.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        function setTablePayment() {
            // 2. Hitung nilai pajak (tax) dari subtotal
            var taxValue = parseFloat($('#tax').val());
            var taxPercentage = isNaN(taxValue) ? 0 : taxValue / 100;
            var tax = subtotal * taxPercentage;

            // 3. Ambil nilai diskon
            var discountValue = parseFloat($('#discount').val());
            var discount = isNaN(discountValue) ? 0 : discountValue;

            // 3. Ambil nilai shipment
            var shipment_cost_value = parseFloat($('#shipment_cost').val());
            var shipment_cost = isNaN(shipment_cost_value) ? 0 : shipment_cost_value;

            // 4. Hitung grandtotal
            var grandtotal = subtotal + tax - discount + shipment_cost;

            if (grandtotal < 0) {
                grandtotal = 0;
            }

            // 5. Hitung down payment (dp) dari grandtotal
            var downPaymentValue = parseFloat($('#down_payment').val());
            var downPaymentPercentage = isNaN(downPaymentValue) ? 0 : downPaymentValue / 100;
            var downPayment = grandtotal * downPaymentPercentage;

            $('#order_down_payment').text(numberFormat(downPayment));
            $('#order_discount').text(numberFormat(discount));
            $('#order_tax').text(numberFormat(tax));
            $('#order_shipping').text(numberFormat(shipment_cost));
            $('#order_total').text(numberFormat(grandtotal));
            
            $('#order_down_payment_input').val(downPayment);
            $('#order_shipping_input').val(shipment_cost);
            $('#order_discount_input').val(discount);
            $('#order_tax_input').val(tax);
            $('#order_total_input').val(grandtotal);
        }

        $('#tax').change(function() {
            setTablePayment();
        });

        $('#discount').change(function() {
            setTablePayment();
        });

        $('#down_payment').change(function() {
            setTablePayment();
        });
        $('#shipment_cost').change(function() {
            setTablePayment();
        });
    });
</script>

<script>
    document.querySelectorAll('.form-control-sm').forEach(function(element) {
        element.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });
</script>
<script>
    // Deklarasikan fungsi setColumn di luar, agar bisa digunakan di kedua script
    function setColumn() {
        const banks = ['bni', 'bri', 'permata', 'mandiri', 'bca'];
        const ewalets = ['ovo', 'gopay'];
        var payment_method = document.getElementById('payment_method');
        var supplier_bank_account = document.getElementById('supplier_bank_account');
        var supplier_ewalet = document.getElementById('supplier_ewalet');
        var input_supplier_bank_account = document.getElementById('input_bank_account'); // Koreksi ID
        var input_supplier_ewalet = document.getElementById('input_ewalet'); // Koreksi ID
        var add_bottom_padding = document.getElementById('shipping-3'); // Koreksi ID

        const own_courier = 'own';
        var courier = document.getElementById('courier');
        var driver_phone = document.getElementById('driver_phone');
        var shipment_number = document.getElementById('shipment_number');
        var input_driver_phone = document.getElementById('input_driver_phone'); // Koreksi ID
        var input_shipment_number = document.getElementById('input_shipment_number'); // Koreksi ID

        if (banks.includes(payment_method.value)) {
            supplier_bank_account.style.display = 'flex';
            supplier_ewalet.style.display = 'none';
            input_supplier_bank_account.setAttribute('required', 'required');
            input_supplier_ewalet.removeAttribute('required');
            add_bottom_padding.style.setProperty('padding-bottom', '1.24vw', 'important');
        } else if (ewalets.includes(payment_method.value)) {
            supplier_ewalet.style.display = 'flex';
            supplier_bank_account.style.display = 'none';
            input_supplier_ewalet.setAttribute('required', 'required');
            input_supplier_bank_account.removeAttribute('required');
            add_bottom_padding.style.setProperty('padding-bottom', '1.24vw', 'important');
        } else {
            supplier_bank_account.style.display = 'none';
            supplier_ewalet.style.display = 'none';
            add_bottom_padding.style.setProperty('padding-bottom', '4.96vw', 'important');
            input_supplier_bank_account.removeAttribute('required');
            input_supplier_ewalet.removeAttribute('required');
        }

        if (own_courier == courier.value) {
            driver_phone.style.display = 'flex';
            shipment_number.style.display = 'none';
            input_driver_phone.setAttribute('required', 'required');
            input_shipment_number.removeAttribute('required');
        } else {
            shipment_number.style.display = 'flex';
            driver_phone.style.display = 'none';
            input_shipment_number.setAttribute('required', 'required');
            input_driver_phone.removeAttribute('required');
        }
    }

    // Event listener untuk ketika DOM sudah selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        var payment_method = document.getElementById('payment_method');
        var courier = document.getElementById('courier');
        
        // Jalankan setColumn saat halaman pertama kali dimuat
        setColumn();
    });

    // Event listener untuk ketika DOM sudah selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        var payment_method = document.getElementById('payment_method');
        var courier = document.getElementById('courier');
        

        // Tambahkan event listener untuk perubahan metode pembayaran
        payment_method.addEventListener('change', function() {
            console.log('pppp');
            setColumn();
        });

    });

    // Event listener untuk ketika DOM sudah selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        var payment_method = document.getElementById('payment_method');
        var courier = document.getElementById('courier');

        // Tambahkan event listener untuk perubahan kurir
        courier.addEventListener('change', function() {
            setColumn();
        });
    });
</script>




</body>

</html>
