@extends('templates.main')

@section('pages_title')
<h1>Create Purchase</h1>
<p>Create new user purchase</p>
@endsection

@push('style')
<link rel="stylesheet" href="{{ asset('hopeui/html/assets/css/checkbox.css') }}">
@endpush

@section('content')
<style type="text/css">
    .background {
        position: fixed; /* atau 'absolute', tergantung kebutuhan */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Warna gelap dengan transparansi */
        z-index: 1; /* Pastikan lebih tinggi dari elemen lain kecuali modal */
    }

    .overlay {
        z-index: 2; /* Pastikan lebih tinggi dari elemen lain kecuali modal */
    }

    img {
        display: block;
        max-width: 100%;
    }

    .image-container {
        overflow: hidden;
        max-width: 510px !important;
        max-height: 370px !important;
    }

    .preview {
        display: none;
    }

    /* Custom CSS to adjust the Bootstrap media query breakpoints */
    @media (min-width: 768px) and (max-width: 1300px) {
        /* Adjust the large (lg) screen breakpoint */
        .modal-lg {
            --bs-modal-width: 700px; /* Set your desired minimum width for large screens (lg) */
        }

        .preview {
        display: block;
        overflow: hidden;
        width: 210px;
        height: 210px;
        border: 1px solid red;
        }
        .btn-sm {
            padding: 0.08rem 0.2rem !important;
        }
    }

    .select2-container .select2-selection--single {
    height: 54px; /* Atur tinggi sesuai kebutuhan */
    display: flex;
    align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 54px; /* Sesuaikan dengan tinggi yang diatur */
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 52px; /* Sesuaikan dengan tinggi yang diatur - 2px untuk padding */
    }

    .select2-container .select2-dropdown .select2-results__options {
    max-height: 220px; /* Atur tinggi maksimum sesuai kebutuhan */
    }

    .btn-sm {
        padding: 0.08rem 0.6rem;
    }
</style>
<div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="d-flex col justify-content-left">
                    <ul class=" nav nav-pills mb-0 text-center profile-tab " data-toggle="slider-tab" id="profile-pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active show" data-bs-toggle="tab" href="#order" role="tab" aria-selected="false">Order</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#return" role="tab" aria-selected="false">Return</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#payment" role="tab" aria-selected="false">Payment</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div class="card-body py-5 tab-pane fade active show" id="order">
            <form method="POST" action="{{ route('purchases.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="form-label" for="name">Date:</label>
                        <input type="date" value="{{ old('date') }}" class="form-control @error('date') is-invalid @enderror" id="date" name="date" >
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="form-label"for="location">Supplier:</label>
                        <select class="form-control" id="supplier" name="supplier">
                            <option selected disabled hidden value="">Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="form-label"for="location">Destination:</label>
                        <select class="form-control" id="location" name="location">
                                <option value="{{ $warehouse->id }}" selected>{{ $warehouse->name }}</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-12 mt-4">
                        <select id="itemDropdown" name="products[]" style="width: 100%;">
                            <option value=""></option>
                            @foreach($products as $product)
                                @if ($product['variant']->isEmpty())
                                    <option 
                                        value="{{ $product['productData']->id}}" 
                                        data-image="{{ $product['productData']->image }}" 
                                        data-unit="{{ $product['productData']->unitPurchase->ShortName ?? '' }}" 
                                        data-code="{{$product['productData']->code}}" 
                                        data-onorder="{{ $product['quantity_on_order'] }}" 
                                        data-available="{{ $product['quantity_available'] }}" 
                                        data-cost="{{$product['productData']->cost }}">
                                    {{ $product['productData']->name }}
                                    </option>
                                @else
                                    @foreach($product['variant'] as $variant)
                                        <option 
                                            value="{{ $product['productData']->id}}" 
                                            data-image="{{ $product['productData']->image }}" 
                                            data-unit="{{ $product['productData']->unitPurchase->ShortName ?? '' }}" 
                                            data-code="{{$variant['variantData']->code}}" 
                                            data-onorder="{{ $variant['variantOnOrder']}}" 
                                            data-available="{{ $variant['variantAvailable']}}" 
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
                    <div class="form-group col-sm-12 mb-3">
                        <table id="selectedItemsTable" class="table table-borderless">
                            <thead>
                                <tr>
                                    <th class="col-3">Name</th>
                                    <th class="col-1">Price</th>
                                    <th class="col-1">on Order</th>
                                    <th class="col-1">Available</th>
                                    <th class="col-2">Qty</th>
                                    <th class="col-1">Subtotal</th>
                                    <th class="col-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-6 mb-1">
                        <div class="card-header d-flex justify-content-between px-0">
                            <div class="header-title">
                                <h6 class="card-title">Supplier Information</h6>
                            </div>
                        </div>
                        <div class="card-body py-3" style="padding-left:0px;">
                            <div class="new-user-info">
                                <div class="row">
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Email</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float: right;">
                                            <input type="email" value="{{ old('date') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="email" name="email" >
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Contact Person</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float: right;">
                                            <input type="text" value="{{ old('date') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="contact_person" name="contact_person" >
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">CP Phone</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float: right;">
                                            <input type="tel" value="{{ old('date') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="cp_phone" name="cp_phone" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header d-flex justify-content-between p-0">
                            <div class="header-title">
                                <h6 class="card-title">Shipping Information</h6>
                            </div>
                        </div>
                        <div class="card-body py-3" style="padding-left:0px;">
                            <div class="new-user-info">
                                <div class="row">
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Destination Address</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <textarea class="form-control form-control-sm @error('date') is-invalid @enderror" id="address" name="address" >{{ old('address') ?? $warehouse->address }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Request Arrive date</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="date" value="{{ old('req_arrive_date') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="req_arrive_date" name="req_arrive_date" >
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Courier</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <select name="courier" id="courier" class="form-control" >
                                                <option value="" select disabled hidden>Courier</option>
                                                <option value="jne">JNE</option>
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
                                            <input type="tel" value="{{ old('tax') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="tax" name="tax" >
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Discount</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{ old('discount') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="discount" name="discount" >
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Payment Method</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <select name="payment_method" id="payment_method" class="form-control">
                                                <option value="" select disabled hidden>Payment Method</option>
                                                <option value="bni">BNI</option>
                                                <option value="bri">BRI</option>
                                                <option value="mandiri">Mandiri</option>
                                                <option value="permata">Permata</option>
                                                <option value="bca">BCA</option>
                                                <option value="gopay">Gopay</option>
                                                <option value="ovo">OVO</option>
                                                <option value="cash">Cash</option>
                                            </select>                                            
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Bank Account</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{ old('supplier_bank_account') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="supplier_bank_account" name="supplier_bank_account" >
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">E-Walet Number</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{ old('supplier_ewalet') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="supplier_ewalet" name="supplier_ewalet" >
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Payment Term</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <select name="payment_term" id="payment_term" class="form-control">
                                                <option value="" select disabled hidden>Payment Term</option>
                                                <option value="on_invoice">Due on invoice</option>
                                                <option value="7_invoice">7 days after invoice</option>
                                                <option value="14_invoice">14 Days after Invoice</option>
                                                <option value="on_arrive">Due on arrive</option>
                                                <option value="7_arrive">7 days after arrive</option>
                                                <option value="14_arrive">14 Days after arrive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Down Payment</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{ old('down_payment') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="down_payment" name="down_payment" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body py-0" style="padding-left:0px;">
                            <table id="basic-table" class="table table-bordered table-sm"
                                role="grid">
                                <tbody>
                                    <tr>
                                        <td class="col-3">Order Subtotal</td>
                                        <td id="order_subtotal" class="col-7"style="text-align:right;">Rp 0</td>
                                        <input type="hidden" name="order_subtotal_input" id="order_subtotal_input">
                                    </tr>
                                    <tr>
                                        <td class="col-3">Order Tax</td>
                                        <td id="order_tax" class="col-7"style="text-align:right;">Rp 0</td>
                                        <input type="hidden" name="order_tax_input" id="order_tax_input">
                                    </tr>
                                    <tr>
                                        <td class="col-3">Discount</td>
                                        <td id="order_discount" class="col-7"style="text-align:right;">Rp 0</td>
                                        <input type="hidden" name="order_discount_input" id="order_discount_input">
                                    </tr>
                                    <tr>
                                        <td class="col-3">Grand Total</td>
                                        <td id="order_total" class="col-7"style="text-align:right;">Rp 0</td>
                                        <input type="hidden" name="order_total_input" id="order_total_input">
                                    </tr>
                                    <tr>
                                        <td class="col-3">Down Payment</td>
                                        <td id="order_down_payment" class="col-7"style="text-align:right;"> Rp 0</td>
                                        <input type="hidden" name="order_down_payment_input" id="order_down_payment_input">
                                    </tr>
                            </table>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label" for="name">Order Note:</label>
                        <textarea  class="form-control @error('date') is-invalid @enderror" id="notes" name="notes" >{{ old('notes') }}</textarea> 
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label" for="name">Supplier Note:</label>
                        <textarea  class="form-control @error('date') is-invalid @enderror" id="supplier_notes" name="supplier_notes" >{{ old('supplier_notes') }}</textarea> 
                    </div>
                    <div class="form-group col-sm-12">
                        <select name="statut" id="statut" class="form-control" >
                            <option value="pending">Pending</option>
                            <option value="ordered">Ordered</option>
                            <option value="shipped">Shipped</option>
                            <option value="arrived">Arrived</option>
                            <option value="complete">Complete</option>
                        </select>   
                    </div>
                </div>
                <div class="card-footer" style="float: right;">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
            </div>
            <div class="card-body py-5 tab-pane fade" id="return">
                <div class="card-footer" style="float: right;">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
            <div class="card-body py-5 tab-pane fade" id="payment">
                <div class="card-footer" style="float: right;">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script type="text/javascript" src="{{ asset('hopeui/html/assets/js/multiselect-dropdown.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#itemDropdown').select2({
            placeholder: "Scan/Search Product by Code or Name",
            templateResult: formatUser,
            templateSelected: formatUser,
            matcher: customMatcher
        });
        $('#itemDropdown').on('select2:open', function() {
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
            setTimeout(function() {
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
            }, 700); // Jeda 0,7 detik
        };
        $('#itemDropdown').change(function() {
        setTablePayment();
        console.log('itemDropdown');
        });

        $('#tax').change(function() {
            setTablePayment();
            console.log('tax');
        });

        $('#discount').change(function() {
            setTablePayment();
            console.log('discount');
        });

        $('#down_payment').change(function() {
            setTablePayment();
            console.log('down_payment');
        });

        $(document).on('click', '.add', function() {
            setTablePayment();
            console.log('add');
        });

        $(document).on('click', '.subtract', function() {
            setTablePayment();
            console.log('subtract');
        });

        $(document).on('click', '.delete', function() {
            setTablePayment();
            console.log('delete');
        });
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
                var onorder = $('#itemDropdown option:selected').data('onorder');
                var available = $('#itemDropdown option:selected').data('available');
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
                            '</div></div></div></td>' +
                            '<td class="cost">' + cost + '</td>';

                        if (id) {
                            newRow += '<td class="variant-id" data-variant="'+id+'" style="display:none;" hidden></td>';
                        }

                    newRow += '<td>' + onorder + '</td>' +
                            '<td>' + available + '</td>' +
                            '<td style="text-align: center;">' +
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
<script>
    $(document).ready(function() {
        // Mendefinisikan fungsi getSupplier
        function getSupplier() {
            var selectedValue = $('#supplier').val();

            $.ajax({
                url: `/purchases/supplier/${selectedValue}`,
                type: 'post',
                dataType: 'json',
                data: {
                    // Tambahkan data yang diperlukan di sini
                },
                headers: { 
                    'X-CSRF-TOKEN': csrfToken // Pastikan csrfToken sudah didefinisikan
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
                        $('#email').val(response.email);
                        $('#contact_person').val(response.nama_kontak_person);
                        $('#cp_phone').val(response.nomor_kontak_person);
                        $('#payment_method').val(response.payment_method);
                        $('#payment_term').val(response.payment_term);
                        $('#courier').val(response.courier);
                        $('#down_payment').val(response.down_payment).trigger('change');
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

        // Menjalankan fungsi getSupplier ketika halaman pertama kali dimuat
        // getSupplier();

        // Menetapkan event listener untuk perubahan pada elemen #supplier
        $('#supplier').change(getSupplier);
    });
</script>
<script>
    function checkFileSize(input) {
        const maxFileSize = 10 * 1024 * 1024; // 2MB
        if (input.files[0].size > maxFileSize) {
            alert("Max File Size is 10 MB");
            input.value = ""; // 입력을 초기화합니다.
        }
    }
</script>
@endpush
