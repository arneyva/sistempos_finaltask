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
            font-size: 1.3vw !important;
        }
        .button-container {
            text-align: center;
            margin: 2vw 0;
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
            text-align: center;
            font-size: 1.5vw;
            margin-bottom: 1.4vw;
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
                            <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
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
                                </div>
                                <form method="POST" action="{{ route('purchases.store') }}" id="purchase_order" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="form-group col-sm-12 mb-1">
                                    <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table table-striped">
                    <thead>
                      <tr>
                          <th>Furniture dipesan</th>
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
                            <td class="text-right" style="padding-top:1vw !important"><strong><span class=" text-bold">Rp </span>{{number_format($purchase->subtotal)}}</strong></td>
                        </tr>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Order Tax</strong></td>
                            <td class="text-right" style="padding-top:1vw !important"><strong><span class=" text-bold">Rp </span>{{number_format($purchase->TaxNet)}}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Order Discount</strong></td>
                            <td class="text-right" style="padding-top:1vw !important"><strong><span class=" text-bold">Rp </span>{{number_format($purchase->discount)}}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Shipping</strong></td>
                            <td class="text-right" style="padding-top:1vw !important"><strong><span class=" text-bold">Rp </span>{{number_format($purchase->shipment_cost)}}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Grand Total</strong></td>
                            <td class="text-right" style="padding-top:1vw !important"><strong><span class=" text-bold">Rp </span>{{number_format($purchase->GrandTotal)}}</strong></td>
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
                                                <input type="tel"  class="form-control form-control-sm @error('date') is-invalid @enderror" id="tax" name="tax" value="{{old('tax') ?? $purchase->tax_rate}}">
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Discount</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel"  class="form-control form-control-sm @error('date') is-invalid @enderror" id="discount" name="discount" value="{{old('discount') ?? $purchase->discount}}">
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Down Payment</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel"  class="form-control form-control-sm @error('date') is-invalid @enderror" id="down_payment" name="down_payment" value="{{old('down_payment') ?? $purchase->down_payment}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                                <textarea class="form-control form-control-sm @error('date') is-invalid @enderror" id="address" name="address" disabled>{{ $warehouse->address }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important" >Request Arrive date</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="date" value="{{ $purchase->req_arrive_date }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="req_arrive_date" name="req_arrive_date" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Courier</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <select name="courier" id="courier" class="form-control form-control-sm" >
                                                    <option value="" selected disabled hidden>Courier</option>
                                                    <option value="jne" >JNE</option>
                                                    <option value="j&t">J&T</option>
                                                    <option value="sicepat">SiCepat</option>
                                                    <option value="anteraja">Anteraja</option>
                                                    <option value="posindo">Pos Infonesia</option>
                                                    <option value="own">Own Courier</option>
                                                </select>                                            
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Driver Contact</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel" value="{{ old('driver_phone') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="driver_phone" name="driver_phone" >
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
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
                                                <select name="payment_term" id="payment_term" class="form-control form-control-sm">
                                                <option value="" selected disabled hidden>Payment Term</option>
                                                <option value="on_invoice" {{ old('payment_term', $purchase->payment_term) == 'on_invoice' ? 'selected' : '' }}>Due on invoice</option>
                                                <option value="7_invoice" {{ old('payment_term', $purchase->payment_term) == '7_invoice' ? 'selected' : '' }}>7 days after invoice</option>
                                                <option value="14_invoice" {{ old('payment_term', $purchase->payment_term) == '14_invoice' ? 'selected' : '' }}>14 Days after Invoice</option>
                                                <option value="on_arrive" {{ old('payment_term', $purchase->payment_term) == 'on_arrive' ? 'selected' : '' }}>Due on arrive</option>
                                                <option value="7_arrive" {{ old('payment_term', $purchase->payment_term) == '7_arrive' ? 'selected' : '' }}>7 days after arrive</option>
                                                <option value="14_arrive" {{ old('payment_term', $purchase->payment_term) == '14_arrive' ? 'selected' : '' }}>14 Days after arrive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Payment Method</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                            <select name="payment_method" id="payment_method" class="form-control form-control-sm">
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
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: none; align-items: center; " id="supplier_ewalet">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style="margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">E-Walet Number</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel" value="{{ old('supplier_ewalet') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="input_ewalet" name="supplier_ewalet" required>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: none; align-items: center;" id="supplier_bank_account">
                                            <div class="col-sm-3 p-0" >
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Bank Account</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel" value="{{ old('supplier_bank_account') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="input_bank_account" name="supplier_bank_account" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-3" style="padding-left:0px; padding-top:2vw !important;">
                                <div class="new-user-info">
                                    <div class="row">
                                        
                                    <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Number</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel" value="{{ old('shipment_number') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="shipment_number" name="shipment_number" >
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Cost</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel" value="{{ old('shipment_cost') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="shipment_cost" name="shipment_cost" >
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Estimate Arrive Date</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="date" value="{{ old('est_arrive_date') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="est_arrive_date" name="est_arrive_date" >
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label custom-file-input" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Delivery File</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="file" onchange="checkFileSize(this)" class="form-control form-control-sm @error('date') is-invalid @enderror" id="delivery_file" name="delivery_file" >
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label" for="name">Order Note:</label>
                            <textarea  class="form-control form-control-sm @error('date') is-invalid @enderror" id="notes" name="notes" disabled>{{ $purchase->notes }}</textarea> 
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label" for="name">Supplier Note:</label>
                            <textarea  class="form-control form-control-sm @error('date') is-invalid @enderror" id="supplier_notes" name="supplier_notes" >{{ old('supplier_notes') }}</textarea> 
                        </div>

                    </div>
                    <div class="card-footer d-flex justify-content-end pt-0">
                        <button type="submit" class="btn btn-primary ms-2">Accept</button>
                        <button type="submit" class="btn btn-primary ms-2">Cancel</button>
                    </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const banks = ['bni', 'bri', 'permata', 'mandiri', 'bca'];
        const ewalets = ['ovo', 'gopay'];
        var payment_method = document.getElementById('payment_method');
        var supplier_bank_account = document.getElementById('supplier_bank_account');
        var supplier_ewalet = document.getElementById('supplier_ewalet');
        var input_supplier_bank_account = document.getElementById('input_bank_account'); // Koreksi ID
        var input_supplier_ewalet = document.getElementById('input_ewalet'); // Koreksi ID

        function setColumn() {
            if (banks.includes(payment_method.value)) {
                supplier_bank_account.style.display = 'flex';
                supplier_ewalet.style.display = 'none';
            } else if (ewalets.includes(payment_method.value)) {
                supplier_ewalet.style.display = 'flex';
                supplier_bank_account.style.display = 'none';
            } else {
                supplier_bank_account.style.display = 'none';
                supplier_ewalet.style.display = 'none';
            }
        }

        setColumn();

        payment_method.addEventListener('change', function() {
            setColumn();
        });
    });
</script>


</body>

</html>
