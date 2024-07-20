@extends('templates.main')

@section('pages_title')
<h1>Edit Purchase</h1>
<p>Edit the purchase</p>
@endsection

@push('style')
<link rel="stylesheet" href="{{ asset('hopeui/html/assets/css/checkbox.css') }}">
@endpush

@section('content')
<style type="text/css">
.send-email {
    font-family: inherit;
    font-size: 1.15vw;
    background: royalblue;
    color: white;
    padding: 0.7em 1em;
    padding-left: 0.9em;
    display: flex;
    align-items: center;
    border: none;
    border-radius: 3.5px;
    overflow: hidden;
    transition: all 0.2s;
    cursor: pointer;
    position: relative;
}

.send-email span {
    display: block;
    margin-left: 0.46em;
    transition: all 0.27s ease-in-out;
}

.send-email svg {
    display: block;
    transform-origin: center center;
    transition: transform 0.1s ease-in-out;
}

.send-email:hover svg {
    transform: translateX(0em) rotate(45deg) scale(1.1);
}

.send-email:hover span {
    transform: translateX(0em);
}

.send-email.loading .svg-wrapper {
    animation: fly-1 0.4s ease-in-out infinite alternate;
}

.send-email.loading svg {
    transform: translateX(5em) rotate(45deg) scale(1.1);
}

.send-email.loading span {
    transform: translateX(12em);
}

@keyframes fly-1 {
    from {
        transform: translateY(-0.1em);
    }

    to {
        transform: translateY(0.1em);
    }
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
            <form method="POST" action="{{ route('purchases.update', $purchase['id']) }}" id="purchase_order" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="form-label" for="name">Date:</label>
                        <input type="date" value="{{ old('date') ?? $purchase->date->format('Y-m-d') }}" class="form-control @error('date') is-invalid @enderror" id="date" name="date" required>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="form-label"for="location">Supplier:</label>
                        <select class="form-control" id="supplier" name="supplier" >
                            <option selected  hidden value="{{$purchase->provider->id}}">{{$purchase->provider->name}}</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="form-label"for="location">Destination:</label>
                        <select class="form-control" id="location" name="location" >
                                <option selected  hidden value="{{ $purchase->warehouse->id }}" selected>{{ $purchase->warehouse->name }}</option>
                        </select>
                    </div>
                    @if($purchase->statut == 'pending' || $purchase->statut == 'ordered' || $purchase->statut == 'canceled'|| $purchase->statut == 'refused')
                    <div class="form-group col-sm-12 mt-4">
                        <select id="itemDropdown" style="width: 100%;">
                            <option value=""></option>
                            @foreach($products as $product)
                                @if ($product['variant']->isEmpty())
                                    <option 
                                        value="{{ $product['productData']->id}}" 
                                        data-image="{{ $product['productData']->image }}" 
                                        data-unitpurchase="{{ $product['productData']->unitPurchase->ShortName ?? '' }}" 
                                        data-unitsale="{{ $product['productData']->unitSale->ShortName ?? '' }}" 
                                        data-code="{{$product['productData']->code}}" 
                                        data-onorder="{{ $product['quantity_on_order'] }}" 
                                        data-available="{{ $product['quantity_available'] }}" 
                                        data-remainder="{{ $product['quantityRemainder'] }}" 
                                        data-cost="{{$product['productData']->cost }}">
                                    {{ $product['productData']->name }}
                                    </option>
                                @else
                                    @foreach($product['variant'] as $variant)
                                        <option 
                                            value="{{ $product['productData']->id}}" 
                                            data-image="{{ $product['productData']->image }}" 
                                            data-unitpurchase="{{ $product['productData']->unitPurchase->ShortName ?? '' }}" 
                                            data-unitsale="{{ $product['productData']->unitSale->ShortName ?? '' }}" 
                                            data-code="{{$variant['variantData']->code}}" 
                                            data-onorder="{{ $variant['variantOnOrder']}}" 
                                            data-available="{{ $variant['variantAvailable']}}" 
                                            data-remainder="{{ $variant['variantRemainder']}}" 
                                            data-cost="{{ $variant['variantData']->cost }}"
                                            data-id="{{ $variant['variantData']->id}}">
                                        {{ $product['productData']->name }} {{  $variant['variantData']->name }}
                                        </option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <input type="hidden" id="products" name="products">
                    <input type="hidden" id="products_with_variant" name="products_with_variant">
                    <input type="hidden" id="barcode_variant_id" name="barcode_variant_id">
                    <div class="form-group col-sm-12 mb-3">
                    @if($purchase->statut !== 'pending' && $purchase->statut !== 'ordered' && $purchase->statut !== 'canceled' && $purchase->statut !== 'refused')
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
                        @foreach($products_selected as $product)
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
                            <td class="text-right">{{$product->cost}}</td>
                            <td class="text-right subtotal">{{$product->total}}</td>
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
                            <td class="text-right" style="padding-top:1vw !important"><strong id="order_tax"></strong></td>
                            <input type="hidden" name="order_tax_input" id="order_tax_input">
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Order Discount</strong></td>
                            <td class="text-right" style="padding-top:1vw !important"><strong id="order_discount"></strong></td>
                            <input type="hidden" name="order_discount_input" id="order_discount_input">
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Shipping</strong></td>
                            <td class="text-right" style="padding-top:1vw !important"><strong id="order_shipping"></strong></td>
                            <input type="hidden" name="order_shipping_input" id="order_shipping_input">
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Grand Total</strong></td>
                            <td class="text-right" style="padding-top:1vw !important"><strong id="order_total"></strong></td>
                            <input type="hidden" name="order_total_input" id="order_total_input">
                        </tr>
                        <tr>
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Down Payment</strong></td>
                            <td class="text-right" style="padding-top:1vw !important"><strong id="order_down_payment"></strong></td>
                            <input type="hidden" name="order_down_payment_input" id="order_down_payment_input">
                        </tr>
                    </tbody>
                </table>
                    @else
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
                            @foreach ($products_selected as $data)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex flex-column"> 
                                                <div style="margin-bottom: 5px; word-wrap: break-word; word-break: break-all; white-space: normal;">
                                                    @if (!empty($data->product_variant_id))
                                                    <div>{{ $data->product->name }} {{ $data->product_variant->name }}</div>
                                                    @else
                                                    <div>{{ $data->product->name }}</div>
                                                    @endif
                                                </div>
                                                @if (!empty($data->product_variant_id))
                                                <div>{{ $data->product_variant->code }}</div>
                                                @else
                                                <div>{{ $data->product->code }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @if (!empty($data->product_variant_id))
                                    <td class="cost">{{ $data->product_variant->cost  }}</td>
                                    @else
                                    <td class="cost">{{ $data->product->cost  }}</td>
                                    @endif
                                    @if (!empty($data->product_variant_id))
                                        <td class="variant-id" data-variant="{{ $data->product_variant->id  }}" style="display:none;" hidden></td>
                                    @endif
<td>-</td>
<td>-</td>
                                    <td style="text-align: start;">
                                        <input type="number" class="form-control qty px-0" value="1" style="width: 5vw; display: inline-block; text-align: center;">
                                        <span>{{ $data->unit->ShortName }}</span>
                                    </td>
                                    <td class="subtotal">{{ $data->total }}</td>
                                    <td>
                                        <div class="flex align-items-center list-user-action">
                                        <a class="btn btn-sm btn-icon btn-danger delete" 
                                            data-value="{{ $data->product_id }}" 
                                            data-code="{{ !empty($data->product_variant_id) ? $data->product_variant->code : $data->product->code }}">
                                                <span class="btn-inner">
                                                    <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor">
                                                        <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </svg>
                                                </span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @endif
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
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important" required>Email</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float: right;">
                                            <input type="email" value="{{old('email') ?? $purchase->email}}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="email" name="email" required>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Contact Person</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float: right;">
                                            <input type="text" value="{{$supplier->nama_kontak_person}}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="contact_person" name="contact_person" >
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">CP Phone</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float: right;">
                                            <input type="tel" value="{{$supplier->nomor_kontak_person}}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="cp_phone" name="cp_phone" >
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
                                            <textarea class="form-control form-control-sm @error('date') is-invalid @enderror" id="address" name="address" required>{{ old('address') ?? $purchase->address }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" placeholder="-" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Request Arrive date</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="date" value="{{ old('req_arrive_date') ? old('req_arrive_date') : ($purchase->req_arrive_date ? $purchase->req_arrive_date->format('Y-m-d') : '') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="req_arrive_date" name="req_arrive_date" >
                                        </div>
                                    </div>
                                    @if ($purchase->statut !== "pending" && $purchase->statut !== "ordered")
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Courier</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <select name="courier" id="courier" class="form-control form-control-sm"  >
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
                                        <div class="col-sm-9 p-0" id="driver_phone" style="float:right;">
                                            <input type="tel" value="{{old('driver_phone') ? old('driver_phone') : ($purchase->driver_contact ? $purchase->driver_contact : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="input_driver_phone" name="driver_phone">
                                            <span class="print-value" id="print-driver_phone"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none; align-items: center;" id="shipment_number">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Number</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{ old('shipment_number') ? old('shipment_number') : ($purchase->shipment_number ? $purchase->shipment_number : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="input_shipment_number" name="shipment_number">
                                            <span class="print-value" id="print-shipment_number"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Cost</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{old('shipment_cost') ? old('shipment_cost') : ($purchase->shipment_cost ? $purchase->shipment_cost : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="shipment_cost" name="shipment_cost">
                                            <span class="print-value" id="print-shipment_cost"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Estimate Arrive Date</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="date" value="{{  old('est_arrive_date') ? old('est_arrive_date') : ($purchase->estimate_arrive_date ? $purchase->estimate_arrive_date->format('Y-m-d') : '') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="est_arrive_date" name="est_arrive_date">
                                            <span class="print-value" id="print-est_arrive_date"></span>
                                        </div>
                                    </div>
                                    <div class="form-group mb-2" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label custom-file-input" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Delivery File</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="file" onchange="checkFileSize(this)" class="form-control form-control-sm @error('date') is-invalid @enderror" id="delivery_file" name="delivery_file" >
                                            <span class="print-value" id="print-delivery_file"></span>
                                        </div>
                                    </div>
                                    @if ($purchase->delivery_file)
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <p><strong>Download File: </strong><a href="{{route('purchases.file', $purchase['id'])}}">Download</a></p>
                                        </div>
                                    </div>
                                    @endif
                                    @endif
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
                                            <input type="tel" value="{{ old('tax') ? old('tax') : ($purchase->tax_rate ? $purchase->tax_rate : '') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="tax" name="tax" >
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Discount</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{ old('discount') ? old('discount') : ($purchase->discount ? $purchase->discount : '') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="discount" name="discount" >
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Payment Method</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                        <select name="payment_method" id="payment_method" class="form-control">
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
                                            <input type="tel" value="{{old('supplier_ewalet') ? old('supplier_ewalet') : ($purchase->supplier_ewalet ? $purchase->supplier_ewalet : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="input_ewalet" name="supplier_ewalet" >
                                            <span class="print-value" id="print-input_ewalet"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none; align-items: center;" id="supplier_bank_account">
                                        <div class="col-sm-3 p-0" >
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Bank Account</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{ old('supplier_bank_account') ? old('supplier_bank_account') : ($purchase->supplier_bank_account ? $purchase->supplier_bank_account : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="input_bank_account" name="supplier_bank_account">
                                            <span class="print-value" id="print-input_bank_account"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Payment Term</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                        <select name="payment_term" id="payment_term" class="form-control">
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
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Down Payment</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{ old('down_payment') ? old('down_payment') : ($purchase->down_payment_rate ? $purchase->down_payment_rate : '') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="down_payment" name="down_payment" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($purchase->statut == 'pending'|| $purchase->statut == 'ordered')
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
                                        <td class="col-3">Shipping</td>
                                        <td id="order_shipping" class="col-7"style="text-align:right;">Rp 0</td>
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
                        @endif
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label" for="name">Order Note:</label>
                        <textarea  class="form-control @error('date') is-invalid @enderror" id="notes" name="notes" >{{ old('notes') ? old('notes') : ($purchase->notes ? $purchase->notes : '') }}</textarea> 
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label" for="name">Supplier Note:</label>
                        <textarea  class="form-control @error('date') is-invalid @enderror" id="supplier_notes" name="supplier_notes" >{{ old('supplier_notes') ? old('supplier_notes') : ($purchase->supplier_notes ? $purchase->supplier_notes : '') }}</textarea> 
                    </div>
                    <div class="form-group col-sm-12">
                    <select name="statut" id="statut" class="form-control" required>
                        @if($purchase->statut == 'pending' || $purchase->statut == 'ordered' || $purchase->statut == 'refused' || $purchase->statut == 'canceled')
                            @if($purchase->statut == 'refused')
                                <option value="refused" selected>Refused</option>
                            @endif
                            <option value="pending" {{ old('statut', $purchase->statut) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="ordered" {{ old('statut', $purchase->statut) == 'ordered' ? 'selected' : '' }}>Ordered</option>
                                <option value="canceled" {{ old('statut', $purchase->statut) == 'canceled' ? 'selected' : '' }}>Canceled</option>
                        @endif
                        <option value="shipped" {{ old('statut', $purchase->statut) == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="arrived" {{ old('statut', $purchase->statut) == 'arrived' ? 'selected' : '' }}>Arrived</option>
                        <option value="complete" {{ old('statut', $purchase->statut) == 'completed' ? 'selected' : '' }}>Complete</option>
                    </select> 
                    </div>
                </div>
                <div class="card-footer d-flex" style="float: right;">
                    <button type="button" class="send-email" data-send="send_email" id="send-email" autofocus>
                        <div class="svg-wrapper-1">
                            <div class="svg-wrapper">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                width="24"
                                height="24"
                            >
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path
                                fill="currentColor"
                                d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z"
                                ></path>
                            </svg>
                            </div>
                        </div>
                        <span>Save and Send Email</span>
                    </button>

                    <button type="submit" class="btn btn-primary ms-2">Save</button>
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
        $('#send-email').click(function() {
            var button = this;
            button.classList.add('loading');
            $(button).attr('disabled', true);

            var form = $('#purchase_order')[0];
            var formData = new FormData(form); // Membuat objek FormData dari form
            var send= $(this).data('send');

             // Menambahkan data tambahan ke FormData
            formData.append('send', send);


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
            

            // Mengirimkan data menggunakan AJAX
            $.ajax({
                type: "POST",
                url: `/purchases/update/{{$purchase->id}}}`,
                data: formData,
                processData: false, // Jangan memproses data menjadi string
                contentType: false, // Jangan menetapkan jenis konten
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PATCH' // For Laravel's method spoofing
                },
                success: function(response) {
                    if (response.errors) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: '<ol style="text-align: start">' + response.errors + '</ol>',
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
                        text: 'company Email or app password not valid'
                    });
                    // Log the error for debugging
                    console.error('Error: ', error);
                    console.error('Response: ', xhr.responseText);
                },
                complete: function() {
                    $(button).attr('disabled', false);
                    button.classList.remove('loading');
                }
            });
        });
    });
</script>

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

        function numberFormat(number) {
            return number.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        setTablePayment();

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

                // 3. Ambil nilai shipment
                var shipment_cost_value = parseFloat($('#shipment_cost').val());
                var shipment_cost = isNaN(shipment_cost_value) ? 0 : shipment_cost_value;

                // 4. Hitung grandtotal
                grandtotal = subtotal + tax - discount +shipment_cost;

                if (grandtotal < 0) {
                    grandtotal = 0;
                };

                // 5. Hitung down payment (dp) dari grandtotal
                var downPaymentValue = parseFloat($('#down_payment').val());
                var downPaymentPercentage = isNaN(downPaymentValue) ? 0 : downPaymentValue / 100;
                downPayment = grandtotal * downPaymentPercentage

                

                $('#order_down_payment').text('Rp ' + numberFormat(downPayment));
                $('#order_discount').text('Rp ' + numberFormat(discount));
                $('#order_tax').text('Rp ' + numberFormat(tax));
                $('#order_shipping').text('Rp ' + numberFormat(shipment_cost));
                $('#order_total').text('Rp ' + numberFormat(grandtotal));
                $('#order_subtotal').text('Rp ' + numberFormat(subtotal));
                
                $('#order_down_payment_input').val(downPayment);
                $('#order_subtotal_input').val(subtotal);
                $('#order_discount_input').val(discount);
                $('#order_tax_input').val(tax);
                $('#order_total_input').val(grandtotal);
            }, 700); // Jeda 0,7 detik
        };
        $('#itemDropdown').change(function() {
        setTablePayment();
        });

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
        $(document).on('change', '.qty', function() {
            setTablePayment();
        });
        $(document).on('click', '.delete', function() {
            setTablePayment();
        });
    });
</script>

<script>
    $(document).ready(function() {
        var products = [];
        var products_with_variant = [];
        var productsObj = {};
        var productsWithVariantObj = {};
        
        $('#selectedItemsTable tbody tr').each(function() {
            var variant_id = $(this).find('.variant-id').data('variant');
            var currentQty=parseFloat($(this).find('.qty').val());
            var product_id = $(this).find('.delete').data('value');
            if (!variant_id ){
                products.push({ key: product_id, value: currentQty });
                $.each(products, function (i, value) {
                    productsObj[value.key] = value.value;
                })
                $('#products').val(JSON.stringify(productsObj));
            } else {
                products_with_variant.push({ key: variant_id, value: currentQty });
                $.each(products_with_variant, function (i, value) {
                    productsWithVariantObj[value.key] = value.value;
                })
                $('#products_with_variant').val(JSON.stringify(productsWithVariantObj));
            }
        });
        console.log(JSON.stringify(productsObj));
        console.log(JSON.stringify(productsWithVariantObj));

        $('#itemDropdown').change(function() {
            var selectedValue = $(this).val();
            if (selectedValue) {
                var name = $('#itemDropdown option:selected').text();
                var image = $('#itemDropdown option:selected').data('image');
                var code = $('#itemDropdown option:selected').data('code');
                var cost = $('#itemDropdown option:selected').data('cost');
                var onorder = $('#itemDropdown option:selected').data('onorder');
                var available = $('#itemDropdown option:selected').data('available');
                var remainder = $('#itemDropdown option:selected').data('remainder');
                var unitpurchase = $('#itemDropdown option:selected').data('unitpurchase');
                var unitsale = $('#itemDropdown option:selected').data('unitsale');
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
                            '<td>' + available + '<span class="badge bg-secondary" style="margin-left:0.5vw">+' + remainder + ' ' + unitsale + '</span></td>' +
                            '<td style="text-align: start;">' +
                                '<input type="number" class="form-control qty px-0" value="1" style="width: 5vw; display: inline-block; text-align: center;"> ' +
                                '<span>' + unitpurchase + '</span> ' +
                            '</td>' +
                            '<td class="subtotal">' + cost + '</td>' +
                            '<td>' +
                            '<div class="flex align-items-center list-user-action"> ' +
                            '<a class="btn btn-sm btn-icon btn-danger delete"data-value="'+selectedValue+'" data-code="'+code+'">' +
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
                        var pastElement = parseFloat(subtotalElement.text());
                        var currentValue = parseFloat(qtyElement.val()); // Ambil nilai saat ini dari input dan konversi ke integer
                        qtyElement.val(currentValue + 1); // Tambah satu nilai ke input
                        //jika ada tambahkan subtotal dengan cost karena plus satu jadinya sama dengan cost
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

        $(document).on('change', '.qty', function() {
            var currentQty=parseFloat($(this).val());
            var row = $(this).closest('tr');
            var cost = $(this).closest('tr').find('.cost').text();
            var subtotalElement = $(this).closest('tr').find('.subtotal');
            var value = row.find('.delete').data('value');
            var variant = row.find('.variant-id').data('variant');

            //kalau isnan, berarti untuk menagani input kosong 
            if (isNaN(currentQty) || currentQty == 0) {
                $(this).val(1);
                currentQty = 1;
            }
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
            console.log(JSON.stringify(productsObj));
            console.log(JSON.stringify(productsWithVariantObj));
        });


        $('#selectedItemsTable').on('click', '.delete', function() {
            var row = $(this).closest('tr');
            var value = $(this).data('value');
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

                // Menghapus key dari productsObj yang tidak ada di products
                Object.keys(productsObj).forEach(function(key) {
                    if (!products.some(product => product.key === key)) {
                        delete productsObj[key];
                    }
                });
                $('#products').val(JSON.stringify(productsObj));

            } else {
                // Menghapus produk bervarian dari array products_with_variant
                for (var i = 0; i < products_with_variant.length; i++) {
                    if (products_with_variant[i].key == variant) {
                        products_with_variant.splice(i, 1);
                        break; // Hentikan loop setelah menemukan dan menghapus item
                    }
                }

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
    function checkFileSize(input) {
        const maxFileSize = 10 * 1024 * 1024; // 10MB
        if (input.files[0].size > maxFileSize) {
            alert("Max File Size is 10 MB");
            input.value = "";
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const banks = ['bni', 'bri', 'permata', 'mandiri', 'bca'];
        const ewalets = ['ovo', 'gopay'];
        var payment_method = document.getElementById('payment_method');
        var supplier_bank_account = document.getElementById('supplier_bank_account');
        var supplier_ewalet = document.getElementById('supplier_ewalet');
        var input_supplier_bank_account = document.getElementById('input_bank_account'); // Koreksi ID
        var input_supplier_ewalet = document.getElementById('input_ewalet'); // Koreksi ID

        const own_courier = 'own';
        var courier = document.getElementById('courier');
        var driver_phone = document.getElementById('driver_phone');
        var shipment_number = document.getElementById('shipment_number');
        var input_driver_phone = document.getElementById('input_driver_phone'); // Koreksi ID
        var input_shipment_number = document.getElementById('input_shipment_number'); // Koreksi ID

        function setColumn() {
            if (banks.includes(payment_method.value)) {
                supplier_bank_account.style.display = 'flex';
                supplier_ewalet.style.display = 'none';
                input_supplier_bank_account.setAttribute('required', 'required');
                input_supplier_ewalet.removeAttribute('required');
            } else if (ewalets.includes(payment_method.value)) {
                supplier_ewalet.style.display = 'flex';
                supplier_bank_account.style.display = 'none';
                input_supplier_ewalet.setAttribute('required', 'required');
                input_supplier_bank_account.removeAttribute('required');
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

        setColumn();

        payment_method.addEventListener('change', function() {
            setColumn();
        });

        courier.addEventListener('change', function() {
            setColumn();
        });
    });
</script>
@endpush
