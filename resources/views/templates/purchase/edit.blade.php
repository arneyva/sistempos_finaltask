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

    #payment_table td, 
    #payment_table th {
        padding: 7px 5px;
    }

    .input-disabled-transparent {
        background-color: transparent !important;
        color: black;
        border-color: transparent;
    }
</style>
<div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="d-flex col justify-content-left">
                    <ul class=" nav nav-pills mb-0 text-center profile-tab " data-toggle="slider-tab" id="profile-pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active show" data-bs-toggle="tab" href="#order" role="tab" aria-selected="true">Order</a>
                        </li>
                        @if ($purchase->statut == "completed" || $purchase->purchase_returns->isNotEmpty())
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#return" role="tab" aria-selected="false">Return</a>
                        </li>
                        @endif
                        @if ($purchase->statut !== "pending" && 
                                $purchase->statut !== "canceled" && 
                                $purchase->statut !== "refused")
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#payment" role="tab" aria-selected="false">Payment</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div class="card-body py-5 tab-pane fade active show" id="order" role="tabpanel">
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
                    <input type="hidden" id="products_checked" name="products_checked">
                    <input type="hidden" id="products_with_variant_checked" name="products_with_variant_checked">
                    <input type="hidden" id="barcode_variant_id" name="barcode_variant_id">
                    <div class="form-group col-sm-12 mb-3">
                    @if($purchase->statut !== 'pending' && $purchase->statut !== 'ordered' && $purchase->statut !== 'canceled' && $purchase->statut !== 'refused')
                    <table class="table table-striped" id="checkingtable">
                    <thead>
                      <tr>
                      @if($purchase->statut !== 'shipped')
                          <th style="width:11%;">Checked</th>
                      @endif
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
                            @if($purchase->statut !== 'shipped')
                            <td>
                                <div class="checkbox-wrapper-46" id="checked-product">
                                    @if ($product->status == "passed")
                                    <input type="checkbox" id="{{$product->product_variant_id ? $product->product_variant->code : $product->product->code}}" name="{{$product->product_variant_id ? $product->product_variant->code : $product->product->code}}" value="1" class="inp-cbx" checked=""/>
                                    @else
                                    <input type="checkbox" id="{{$product->product_variant_id ? $product->product_variant->code : $product->product->code}}" name="{{$product->product_variant_id ? $product->product_variant->code : $product->product->code}}" value="1" class="inp-cbx" />
                                    @endif
                                    <label for="{{$product->product_variant_id ? $product->product_variant->code : $product->product->code}}" class="cbx">
                                        <span>
                                            <svg viewBox="0 0 12 10" height="10px" width="12px">
                                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                            </svg>
                                        </span>
                                    </label>
                                </div>
                            </td>
                            @endif
                            <td>
                                <div class="d-flex">
                                    <div class="ml-2">
                                        {{$product->product->name}} @if($product->product_variant_id){{$product->product_variant->name}}@endif 
                                        <div class="text-sm text-gray">{{$product->product_variant_id ? $product->product_variant->code : $product->product->code}}</div>
                                    </div>
                            </div>
                            </td>
                            @if (!empty($product->product_variant_id))
                                <td class="variant-id" data-variant="{{ $product->product_variant->id  }}" style="display:none;" hidden></td>
                            @endif
                                <td class="product-id" data-product="{{ $product->product_id }}" style="display:none;" hidden></td>
                            <td class="text-right">{{$product->quantity}}</td>
                            @if (!empty($product->product_variant_id))
                            <td class="text-right">{{ $product->product_variant->cost  }}</td>
                            @else
                            <td class="text-right">{{ $product->product->cost }}</td>
                            @endif
                            <td class="text-right subtotal">{{$product->total}}</td>
                        </tr>
                        @php
                            $totalQty += $product->quantity; // Add the total to the grand total
                        @endphp
                        @endforeach
                        <tr>
                        @if($purchase->statut !== 'shipped')
                            <td colspan="2" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important; "><strong>Total Order</strong></td>
                        @else
                            <td class="text-left" style="padding-top:1vw !important;padding-left:14vw !important; "><strong>Total Order</strong></td>
                            @endif
                            <td class="text-right" style="padding-top:1vw !important"><strong>{{ number_format($totalQty) }}</strong></td>
                            <td class="col-1" style="padding-top:1vw !important"></td>
                            <td class="text-right" style="padding-top:1vw !important"><strong><span class=" text-bold">Rp </span>{{number_format($purchase->subtotal,0,",",".")}}</strong></td>
                        </tr>
                        </tr>
                        <tr>
                        @if($purchase->statut !== 'shipped')
                            <td colspan="4" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Order Tax</strong></td>
                            @else
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Order Tax</strong></td>
                            @endif
                            <td class="text-right" style="padding-top:1vw !important"><strong id="order_tax"></strong></td>
                            <input type="hidden" name="order_tax_input" id="order_tax_input">
                        </tr>
                        <tr>
                        @if($purchase->statut !== 'shipped')
                            <td colspan="4" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Order Discount</strong></td>
                            @else
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Order Discount</strong></td>
                            @endif
                            <td class="text-right" style="padding-top:1vw !important"><strong id="order_discount"></strong></td>
                            <input type="hidden" name="order_discount_input" id="order_discount_input">
                        </tr>
                        <tr>
                        @if($purchase->statut !== 'shipped')
                            <td colspan="4" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Shipping</strong></td>
                            @else
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Shipping</strong></td>
                            @endif
                            <td class="text-right" style="padding-top:1vw !important"><strong id="order_shipping"></strong></td>
                            <input type="hidden" name="order_shipping_input" id="order_shipping_input">
                        </tr>
                        <tr>
                        @if($purchase->statut !== 'shipped')
                            <td colspan="4" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Grand Total</strong></td>
                            @else
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Grand Total</strong></td>
                            @endif
                            <td class="text-right" style="padding-top:1vw !important"><strong id="order_total"></strong></td>
                            <input type="hidden" name="order_total_input" id="order_total_input">
                        </tr>
                        <tr>
                        @if($purchase->statut !== 'shipped')
                            <td colspan="4" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Down Payment</strong></td>
                            @else
                            <td colspan="3" class="text-left" style="padding-top:1vw !important;padding-left:14vw !important;"><strong>Down Payment</strong></td>
                            @endif
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
                                            <input type="email" value="{{old('email') ?? $purchase->email}}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="email" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required>
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
                                    @if ($purchase->statut !== "pending" && 
                                            $purchase->statut !== "ordered" && 
                                            $purchase->statut !== "canceled" && 
                                            $purchase->statut !== "refused")
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
                                        <div class="col-sm-9 p-0" style="float:right;">
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
                                    @if ($purchase->statut !== "pending" && 
                                            $purchase->statut !== "canceled" && 
                                            $purchase->statut !== "refused")
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
                                    @endif
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
                    <label class="form-label" for="name">Status:</label>
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
                        <option value="completed" {{ old('statut', $purchase->statut) == 'completed' ? 'selected' : '' }}>Complete</option>
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

            @if ($purchase->statut == "completed" || $purchase->purchase_returns->isNotEmpty())
            <div class="card-body py-5 tab-pane fade" id="return" role="tabpanel">
            @if (isset($returpurchase)) 
            <form method="POST" action="{{ route('purchases.updateReturn', $returpurchase['id']) }}" id="returpurchase_order" enctype="multipart/form-data">
            @else 
            <form method="POST" action="{{ route('purchases.makeReturn', $purchase['id']) }}" id="returpurchase_order" enctype="multipart/form-data">
            @endif 
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="form-label" for="name">Date:</label>
                        <input type="date" value="{{  old('returdate') ?: (isset($returpurchase) ? $returpurchase->date->format('Y-m-d') : '') }}" class="form-control @error('date') is-invalid @enderror" id="returdate" name="returdate" required>
                    </div>
                    <div class="form-group col-sm-12 mb-3">
                        <table id="returTable" class="table table-borderless">
                            <thead>
                                <tr>
                                    <th class="col-4">Name</th>
                                    <th class="col-1">Qty Unpassed</th>
                                    <th class="col-1">Qty Return</th>
                                    <th class="col-1">Qty Request</th>
                                    <th class="col-1">Price</th>
                                    <th class="col-1">Subtotal</th>
                                    <th class="col-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($products_returned as $data)
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
                                        <td class="variant-id" data-variant="{{ $data->product_variant->id }}" style="display:none;" hidden></td>
                                    @endif
                                    <td style="text-align: start;">
                                        <input type="number" class="form-control unpassedqty px-0" data-previous-qty="{{$data->purchase_return_id ? $data->qty_unpassed : 1 }}" value="{{$data->purchase_return_id ? $data->qty_unpassed : 1 }}" style="width: 5vw; display: inline-block; text-align: center;" name="qty_unpassed[{{ $data->product_variant_id ? $data->product_variant_id : $data->product_id }}]">
                                        <span>{{ $data->unit->ShortName }}</span>
                                    </td>
                                    <td style="text-align: start;">
                                        <input type="number" class="form-control returnqty px-0" data-previous-qty="{{$data->purchase_return_id ? $data->qty_return : 0 }}" value="{{$data->purchase_return_id ? $data->qty_return : 0 }}" style="width: 5vw; display: inline-block; text-align: center;" name="qty_return[{{ $data->product_variant_id ? $data->product_variant_id : $data->product_id }}]">
                                        <span>{{ $data->unit->ShortName }}</span>
                                    </td>
                                    <td style="text-align: start;">
                                        <input type="number" class="form-control requestqty px-0" data-previous-qty="{{$data->purchase_return_id ? $data->qty_request : 0 }}" value="{{$data->purchase_return_id ? $data->qty_request : 0 }}" style="width: 5vw; display: inline-block; text-align: center;" name="qty_request[{{ $data->product_variant_id ? $data->product_variant_id : $data->product_id }}]">
                                        <span>{{ $data->unit->ShortName }}</span>
                                    </td>
                                    @if (!empty($data->product_variant_id))
                                    <td class="returcost">{{ $data->product_variant->cost }}</td>
                                    @else
                                    <td class="returcost">{{ $data->product->cost }}</td>
                                    @endif
                                    @if (!empty($data->product_variant_id))
                                    <td class="retursubtotal">{{$data->purchase_return_id ? -floatval($data->total) : -floatval($data->product_variant->cost) }}</td>
                                    @else
                                    <td class="retursubtotal">{{$data->purchase_return_id ? -floatval($data->total) : -floatval($data->product->cost) }}</td>
                                    @endif
                                    <td>
                                        <div class="flex align-items-center list-user-action">
                                        <a class="btn btn-sm btn-icon btn-danger returdelete" 
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
                    </div>
                    <div class="col-sm-6 mb-0">
                        <div class="card-header d-flex justify-content-between p-0">
                            <div class="header-title">
                                <h6 class="card-title">Payment Information</h6>
                            </div>
                        </div>
                        <div class="card-body py-3" style="padding-left:0px;">
                            <div class="new-user-info">
                                <div class="row">
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Payment Method</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                        <select name="returpayment_method" id="returpayment_method" class="form-control">
                                            <option value="" selected disabled hidden>Payment Method</option>
                                            <option value="bni" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'bni' ? 'selected' : '' }}>BNI</option>
                                            <option value="bri" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'bri' ? 'selected' : '' }}>BRI</option>
                                            <option value="mandiri" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'mandiri' ? 'selected' : '' }}>Mandiri</option>
                                            <option value="permata" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'permata' ? 'selected' : '' }}>Permata</option>
                                            <option value="bca" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'bca' ? 'selected' : '' }}>BCA</option>
                                            <option value="gopay" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'gopay' ? 'selected' : '' }}>Gopay</option>
                                            <option value="ovo" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'ovo' ? 'selected' : '' }}>OVO</option>
                                            <option value="cash" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'cash' ? 'selected' : '' }}>Cash</option>
                                        </select>                                            
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none; align-items: center; " id="retursupplier_ewalet">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style="margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">E-Walet Number</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{old('retursupplier_ewalet') ? old('retursupplier_ewalet') : (isset($returpurchase->supplier_ewalet) ? $returpurchase->supplier_ewalet : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="returinput_ewalet" name="retursupplier_ewalet" >
                                            <span class="print-value" id="returprint-input_ewalet"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none; align-items: center;" id="retursupplier_bank_account">
                                        <div class="col-sm-3 p-0" >
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Bank Account</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{ old('retursupplier_bank_account') ? old('retursupplier_bank_account') : (isset($returpurchase->supplier_bank_account) ? $returpurchase->supplier_bank_account : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="returinput_bank_account" name="retursupplier_bank_account">
                                            <span class="print-value" id="returprint-input_bank_account"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 mb-0" id="shipping-request">
                        <div class="card-header d-flex justify-content-between p-0">
                            <div class="header-title">
                                <h6 class="card-title">Shipping Request Information</h6>
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
                                            <textarea class="form-control form-control-sm @error('date') is-invalid @enderror" id="retur_requestaddress" name="retur_requestaddress" required>{{ old('retur_requestaddress') ?: (isset($returpurchase) ? $returpurchase->request_address : $purchase->address)}}</textarea>
                                        </div>
                                    </div>  
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" placeholder="-" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Request Arrive date</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="date" value="{{ old('retur_requestreq_arrive_date') ? old('retur_requestreq_arrive_date') : (isset($returpurchase->request_req_arrive_date) ? $returpurchase->request_req_arrive_date->format('Y-m-d') : '') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="retur_requestreq_arrive_date" name="retur_requestreq_arrive_date" >
                                        </div>
                                    </div>
                                    @if (isset($returpurchase)) 
                                    @if ($returpurchase->statut !== "pending" && 
                                            $returpurchase->statut !== "ordered" && 
                                            $returpurchase->statut !== "canceled" && 
                                            $returpurchase->statut !== "refused")
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Courier</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <select name="retur_requestcourier" id="retur_requestcourier" class="form-control form-control-sm"  >
                                                <option value="" selected disabled hidden>Courier</option>
                                                <option value="jne" {{ old('retur_requestcourier', isset($returpurchase) ? $returpurchase->request_courier : null) == 'jne' ? 'selected' : '' }} >JNE</option>
                                                <option value="j&t" {{ old('retur_requestcourier', isset($returpurchase) ? $returpurchase->request_courier : null) == 'j&t' ? 'selected' : '' }} >J&T</option>
                                                <option value="sicepat" {{ old('retur_requestcourier', isset($returpurchase) ? $returpurchase->request_courier : null) == 'sicepat' ? 'selected' : '' }} >SiCepat</option>
                                                <option value="anteraja" {{ old('retur_requestcourier', isset($returpurchase) ? $returpurchase->request_courier : null) == 'anteraja' ? 'selected' : '' }} >Anteraja</option>
                                                <option value="posindo" {{ old('retur_requestcourier', isset($returpurchase) ? $returpurchase->request_courier : null) == 'posindo' ? 'selected' : '' }} >Pos Infonesia</option>
                                                <option value="own" {{ old('retur_requestcourier', isset($returpurchase) ? $returpurchase->request_courier : null) == 'own' ? 'selected' : '' }} >Own Courier</option>
                                            </select> 
                                            <span class="print-value" id="retur_requestprint-courier"></span>                                           
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none; align-items: center;" id="retur_requestdriver_phone">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Driver Contact</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{old('retur_requestdriver_phone') ? old('retur_requestdriver_phone') : (isset($returpurchase->request_driver_contact) ? $returpurchase->request_driver_contact : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="retur_requestinput_driver_phone" name="retur_requestdriver_phone">
                                            <span class="print-value" id="retur_requestprint-driver_phone"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none; align-items: center;" id="retur_requestshipment_number">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Number</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{ old('retur_requestshipment_number') ? old('retur_requestshipment_number') : (isset($returpurchase->request_shipment_number) ? $returpurchase->request_shipment_number : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="retur_requestinput_shipment_number" name="retur_requestshipment_number">
                                            <span class="print-value" id="retur_requestprint-shipment_number"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Cost</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{old('retur_requestshipment_cost') ? old('retur_requestshipment_cost') : (isset($returpurchase->request_shipment_cost) ? $returpurchase->request_shipment_cost : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="retur_requestshipment_cost" name="retur_requestshipment_cost">
                                            <span class="print-value" id="retur_requestprint-shipment_cost"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Estimate Arrive Date</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="date" value="{{  old('retur_requestest_arrive_date') ? old('retur_requestest_arrive_date') : (isset($returpurchase->request_estimate_arrive_date) ? $returpurchase->request_estimate_arrive_date->format('Y-m-d') : '') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="retur_requestest_arrive_date" name="retur_requestest_arrive_date">
                                            <span class="print-value" id="retur_requestprint-est_arrive_date"></span>
                                        </div>
                                    </div>
                                    <div class="form-group mb-2" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label custom-file-input" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Delivery File</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="file" onchange="checkFileSize(this)" class="form-control form-control-sm @error('date') is-invalid @enderror" id="retur_requestdelivery_file" name="retur_requestdelivery_file" >
                                            <span class="print-value" id="request_print-delivery_file"></span>
                                        </div>
                                    </div>
                                    @if (isset($returpurchase))
                                    @if ($returpurchase->delivery_file)
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <p><strong>Download File: </strong><a href="{{route('purchases.file', $returpurchase['id'])}}">Download</a></p>
                                        </div>
                                    </div>
                                    @endif
                                    @endif
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                </div>
                <div class="col-sm-6 mb-3" id="shipping-return">
                        <div class="card-header d-flex justify-content-between p-0">
                            <div class="header-title">
                                <h6 class="card-title">Shipping Return Information</h6>
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
                                            <textarea class="form-control form-control-sm @error('date') is-invalid @enderror" id="returaddress" name="returaddress" required>{{  old('returaddress') ? old('returaddress') : (isset($returpurchase->address) ? $returpurchase->address : $purchase->provider->adresse) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Courier</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <select name="returcourier" id="returcourier" class="form-control form-control-sm"  >
                                                <option value="" selected disabled hidden>Courier</option>
                                                <option value="jne" {{ old('returcourier', isset($returpurchase) ? $returpurchase->courier : null) == 'jne' ? 'selected' : '' }} >JNE</option>
                                                <option value="j&t" {{ old('returcourier', isset($returpurchase) ? $returpurchase->courier : null) == 'j&t' ? 'selected' : '' }} >J&T</option>
                                                <option value="sicepat" {{ old('returcourier', isset($returpurchase) ? $returpurchase->courier : null) == 'sicepat' ? 'selected' : '' }} >SiCepat</option>
                                                <option value="anteraja" {{ old('returcourier', isset($returpurchase) ? $returpurchase->courier : null) == 'anteraja' ? 'selected' : '' }} >Anteraja</option>
                                                <option value="posindo" {{ old('returcourier', isset($returpurchase) ? $returpurchase->courier : null) == 'posindo' ? 'selected' : '' }} >Pos Infonesia</option>
                                                <option value="own" {{ old('returcourier', isset($returpurchase) ? $returpurchase->courier : null) == 'own' ? 'selected' : '' }} >Own Courier</option>
                                            </select> 
                                            <span class="print-value" id="print-courier"></span>                                           
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none; align-items: center;" id="returdriver_phone">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Driver Contact</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{old('returdriver_phone') ? old('returdriver_phone') : (isset($returpurchase->driver_contact) ? $returpurchase->driver_contact : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="returinput_driver_phone" name="returdriver_phone">
                                            <span class="print-value" id="returprint-driver_phone"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none; align-items: center;" id="returshipment_number">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Number</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{ old('returshipment_number') ? old('returshipment_number') : (isset($returpurchase->shipment_number) ? $returpurchase->shipment_number : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="returinput_shipment_number" name="returshipment_number">
                                            <span class="print-value" id="returprint-shipment_number"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Cost</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{old('returshipment_cost') ? old('returshipment_cost') : (isset($returpurchase->shipment_cost) ? $returpurchase->shipment_cost : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="returshipment_cost" name="returshipment_cost">
                                            <span class="print-value" id="returprint-shipment_cost"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                    <div class="card-body pb-0 pt-3" style="padding-left:0px;">
                            <table id="returbasic-table" class="table table-bordered table-sm"
                                role="grid">
                                <tbody>
                                    <tr>
                                        <td class="col-3">Order Subtotal</td>
                                        <td id="returorder_subtotal" class="col-7"style="text-align:right;">Rp 0</td>
                                        <input type="hidden" name="returorder_subtotal_input" id="returorder_subtotal_input">
                                    </tr>
                                    
                                    
                                    <tr>
                                        <td class="col-3">Shipping</td>
                                        <td id="returorder_shipping" class="col-7"style="text-align:right;">Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3">Grand Total</td>
                                        <td id="returorder_total" class="col-7"style="text-align:right;">Rp 0</td>
                                        <input type="hidden" name="returorder_total_input" id="returorder_total_input">
                                    </tr>
                                    
                            </table>
                        </div>
                        </div>
                        
                    
                        
                        
                        
                    
                    <div class="form-group col-sm-12 mb-2">
                        <label class="form-label custom-file-input mb-2" for="name">Return Proof:</label>
                        <input type="file" onchange="checkFileSize(this)" class="form-control form-control-sm @error('date') is-invalid @enderror mb-2" id="retur_proof" name="retur_proof" >
                        @if (isset($returpurchase))
                        @if ($returpurchase->delivery_file)
                                <p><strong>Download File: </strong><a href="{{route('purchases.file', $returpurchase['id'])}}">Download</a></p>
                        @endif
                        @endif
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label" for="name">Retur Note:</label>
                        <textarea  class="form-control @error('date') is-invalid @enderror" id="returnotes" name="returnotes" >{{ old('returnotes') ? old('returnotes') : (isset($returpurchase->notes) ? $returpurchase->notes : '') }}</textarea> 
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label" for="name">Supplier Note:</label>
                        <textarea  class="form-control @error('date') is-invalid @enderror" id="retursupplier_notes" name="retursupplier_notes" >{{ old('retursupplier_notes') ? old('retursupplier_notes') : (isset($returpurchase->supplier_notes) ? $returpurchase->supplier_notes : '') }}</textarea> 
                    </div>
                    @if (isset($returpurchase))
                    <div class="form-group col-sm-12">
                    <label class="form-label" for="name">Status:</label>
                    <select name="returstatut" id="returstatut" class="form-control" required>
                        @if($returpurchase->statut == 'pending' || $returpurchase->statut == 'ordered' || $returpurchase->statut == 'refused' || $returpurchase->statut == 'canceled')
                            @if($returpurchase->statut == 'refused')
                                <option value="refused" selected>Refused</option>
                            @endif
                            <option value="pending" {{ old('returstatut', $returpurchase->statut) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="ordered" {{ old('returstatut', $returpurchase->statut) == 'ordered' ? 'selected' : '' }}>Ordered</option>
                            <option value="canceled" {{ old('returstatut', $returpurchase->statut) == 'canceled' ? 'selected' : '' }}>Canceled</option>
                        @endif
                        <option value="shipped" {{ old('returstatut', $returpurchase->statut) == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="arrived" {{ old('returstatut', $returpurchase->statut) == 'arrived' ? 'selected' : '' }}>Arrived</option>
                        <option value="completed" {{ old('returstatut', $returpurchase->statut) == 'completed' ? 'selected' : '' }}>Complete</option>
                    </select> 
                    </div>
                    @endif
                </div>
                <div class="card-footer d-flex" style="float: right;">
                    <button type="button" class="send-email" data-send="send_email" id="retursend-email" autofocus>
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
                    <button type="submit" id="saveReturn" class="btn btn-primary ms-2">Save</button>
                </div>
                <input type="hidden" id="returproducts" name="returproducts">
                <input type="hidden" id="returproducts_with_variant" name="returproducts_with_variant">
            </form>
            </div>
            @endif

            @if ($purchase->statut !== "pending" && 
                    $purchase->statut !== "canceled" && 
                    $purchase->statut !== "refused")
            <div class="card-body py-5 tab-pane fade" id="payment" role="tabpanel">
                <form method="POST" class="mb-5" action="{{ route('purchases.makePayment', $purchase['id']) }}" id="purchase_payment" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                        <div class="form-group col-sm-12">
                            <label class="form-label" for="name">Note:</label>
                            <input type="text" class="form-control form-control-sm" id="payment_note" name="payment_note" ></input> 
                        </div>
                        <div class="d-flex mb-1">
                            <div class="form-group col-sm-6 mb-1">
                                <label class="mb-0" >Balance Total:</label>
                            </div>
                            <div class="form-group col-sm-6 mb-1">
                                <label for="total-pay" class="mb-0">Paying Amount:</label>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="form-group col-sm-6 pe-2 mb-0">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text input-disabled-transparent"  id="basic-addon2">Rp</span>
                                    <input type="tel" aria-describedby="basic-addon2" value="{{number_format($total_balance,0,',','.')}}" class="form-control form-control-sm @error('date') is-invalid @enderror input-disabled-transparent" name="total_pay" id="total_pay" disabled>
                                </div>
                                <small id="p" class="text-danger font-italic"></small>
                            </div>
                            <div class="form-group col-sm-6 ps-2 mb-0">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"  id="basic-addon2">Rp</span>
                                    <input type="tel" aria-describedby="basic-addon2"  class="form-control form-control-sm @error('date') is-invalid @enderror" name="paying_amount" id="paying_amount">
                                </div>
                                <small id="excess" class="text-danger font-italic">Payment is Excess</small>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="total-pay" class="mb-2">Due:</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text input-disabled-transparent" id="basic-addon2">Rp</span>
                                <input type="tel" aria-describedby="basic-addon2" class="form-control form-control-sm @error('date') is-invalid @enderror input-disabled-transparent" id="due" name="due" disabled>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="total-pay" class="mb-2">Payment Proof:</label>
                            <div class="input-group input-group-sm">
                                <input type="file" onchange="checkFileSize(this)" class="form-control form-control-sm @error('date') is-invalid @enderror" id="payment_proof" name="payment_proof">                            
                            </div>
                        </div>
                        <div class="form-group col-sm-12 d-flex justify-content-end mb-4">
                            <button type="submit" id="pay-cash" class="btn btn-primary">Save</button>
                        </div>
                </form>
                <div class="d-flex">
                    <div class="col-sm-5 pe-3">
                    <table id="basic-table" class="table table-bordered table-sm" role="grid">
                        <tbody>
                            <tr>
                                <td class="col-3">Order Subtotal</td>
                                <td id="order_subtotalp" class="col-7"style="text-align:right;">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="col-3">Order Tax</td>
                                <td id="order_taxp" class="col-7"style="text-align:right;">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="col-3">Discount</td>
                                <td id="order_discountp" class="col-7"style="text-align:right;">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="col-3">Shipping</td>
                                <td id="order_shippingp" class="col-7"style="text-align:right;">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="col-3">Down Payment</td>
                                <td id="order_down_paymentp" class="col-7"style="text-align:right;"> Rp 0</td>
                            </tr>
                            <tr>
                                <td class="col-3">Grand Total</td>
                                <td id="order_totalp" class="col-7"style="text-align:right;">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="col-3">PAID</td>
                                <td id="paid" class="col-7"style="text-align:right;"> Rp {{number_format(-($total_paid),0,',','.')}}</td>
                            </tr>
                            <tr>
                                <td class="col-3">Return Total</td>
                                <td id="retur_total" class="col-7"style="text-align:right;"> Rp {{number_format((isset($returpurchase) ? -($returpurchase->GrandTotal) : 0),0,',','.')}}</td>
                            </tr>
                            <tr>
                                <td class="col-3">Return Paid</td>
                                <td id="retur_paid" class="col-7"style="text-align:right;"> Rp {{number_format(($total_return_paid),0,',','.')}}</td>
                            </tr>
                            <tr>
                                <td class="col-3">BALANCE</td>
                                <td id="balance" class="col-7"style="text-align:right;"> Rp {{number_format($total_balance,0,',','.')}}</td>
                            </tr>
                    </table>
                    </div>
                    <div class="col-sm-7 ps-3">
                        <table class="table table-striped" id="payment_table">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th class=" text-right">Note</th>
                            <th class=" text-right">Reference</th>
                            <th class=" text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment) 
                                <tr>
                                    <td>{{$payment->date->translatedFormat('d, F Y')}}</td>
                                    <td>{{$payment->notes}}</td>
                                    <td>{{$payment->Ref}}</td>
                                    <td>{{$payment->montant}}</td>
                                    
                                </tr>
                            @endforeach
                        </tbody>
                        
                        </table>
                    </div>
                </div>
            </div>
            @endif
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
            var requiredInputs = $(form).find('input[required]');
            var requiredSelects = $(form).find('select[required]');
            var requiredText = $(form).find('textarea[required]');
            for (const input of requiredInputs) {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    $(button).attr('disabled', false);
                    button.classList.remove('loading');
                    return; // Keluar dari fungsi jika ada input yang tidak valid
                }
            }
            for (const select of requiredSelects) {
                if (!select.checkValidity()) {
                    select.reportValidity();
                    $(button).attr('disabled', false);
                    button.classList.remove('loading');
                    return; // Keluar dari fungsi jika ada input yang tidak valid
                }
            }
            for (const text of requiredText) {
                if (!text.checkValidity()) {
                    text.reportValidity();
                    $(button).attr('disabled', false);
                    button.classList.remove('loading');
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
        $('#retursend-email, #saveReturn').click(function(e) {
            e.preventDefault();
            var button = this;
            if (button.id == 'retursend-email') {
            button.classList.add('loading');
            $(button).attr('disabled', true);
            }

            var form = $('#returpurchase_order')[0];
            var formData = new FormData(form); // Membuat objek FormData dari form
            var send= $(this).data('send');

             // Menambahkan data tambahan ke FormData
             if (button.id == 'retursend-email') {
                formData.append('send', send);
             }
            // Mengambil data requestQty
            var requestData = []; // Array untuk menyimpan semua data request

            // Loop melalui setiap baris tabel
            $('#returTable tbody tr').each(function() {
                var row = $(this);
                var itemId = row.find('.unpassedqty').attr('name').match(/\d+/)[0]; // Ambil ID item dari nama input
                var variant = row.find('.variant-id').data('variant') // Ambil ID item dari nama input
                var retursubtotal = parseFloat(row.find('.retursubtotal').text()) // Ambil ID item dari nama input

                if (variant) {
                    var isVariant = "true"
                } else {
                    var isVariant = 'false' // Ambil nilai request qty
                }
                var requestQty = parseFloat(row.find('.requestqty').val()) || 0; // Ambil nilai request qty
                var unpassedQty = parseFloat(row.find('.unpassedqty').val()) || 1; // Ambil nilai unpassed qty
                var returnQty = parseFloat(row.find('.returnqty').val()) || 0; // Ambil nilai alasan

                requestData.push({
                    id: itemId,
                    requestQty: requestQty,
                    isVariant: isVariant,
                    unpassedQty: unpassedQty,
                    retursubtotal: retursubtotal,
                    returnQty: returnQty
                });
            });

            formData.append('allQty', JSON.stringify(requestData));


            // Mentrigger semua required input ketika tombol #accept ditekan
            var requiredInputs = $(form).find('input[required]');
            var requiredSelects = $(form).find('select[required]');
            var requiredText = $(form).find('textarea[required]');
            for (const input of requiredInputs) {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    if (button.id == 'retursend-email') {
                    $(button).attr('disabled', false);
                    button.classList.remove('loading');
                    }
                    return; // Keluar dari fungsi jika ada input yang tidak valid
                }
            }
            for (const select of requiredSelects) {
                if (!select.checkValidity()) {
                    select.reportValidity();
                    if (button.id == 'retursend-email') {
                    $(button).attr('disabled', false);
                    button.classList.remove('loading');
                    }
                    return; // Keluar dari fungsi jika ada input yang tidak valid
                }
            }
            for (const text of requiredText) {
                if (!text.checkValidity()) {
                    text.reportValidity();
                    if (button.id == 'retursend-email') {
                    $(button).attr('disabled', false);
                    button.classList.remove('loading');
                    }
                    return; // Keluar dari fungsi jika ada input yang tidak valid
                }
            }
            

            // Mengirimkan data menggunakan AJAX
            $.ajax({
                type: "POST",
                url: `@if (isset($returpurchase)) /purchases/updatereturn/{{$returpurchase->id}} @else /purchases/makereturn/{{$purchase->id}} @endif`,
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
                        text: 'company Email or app password not valid'
                    });
                    // Log the error for debugging
                    console.error('Error: ', error);
                    console.error('Response: ', xhr.responseText);
                },
                complete: function() {
                    if (button.id == 'retursend-email') {
                    $(button).attr('disabled', false);
                    button.classList.remove('loading');
                    }
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
        var due = 0;
        var pay = 0;
        var bal = {{$total_balance}} ;

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

                // 3. Ambil nilai paying amount
                var paying_amount = parseFloat($('#paying_amount').val());
                pay = isNaN(paying_amount) ? 0 : paying_amount;
                const payCash = document.getElementById('pay-cash');
                const unenough = document.getElementById('excess');

                if (pay <= bal) {
                    due = bal - pay;
                    //tampil tombol bayar
                    payCash.style.display = 'block';
                    //sembunyi danger text kecil
                    unenough.style.display = 'none';
                } else {
                    due = 0;
                    //sembunyikan tombol bayar
                    payCash.style.display = 'none';
                    //beri danger text kecil
                    unenough.style.display = 'block';
                };

                $('#order_down_payment').text('Rp ' + numberFormat(downPayment));
                $('#order_discount').text('Rp ' + numberFormat(discount));
                $('#order_tax').text('Rp ' + numberFormat(tax));
                $('#order_shipping').text('Rp ' + numberFormat(shipment_cost));
                $('#order_total').text('Rp ' + numberFormat(grandtotal));
                $('#order_subtotal').text('Rp ' + numberFormat(subtotal));

                $('#order_down_paymentp').text('Rp ' + numberFormat(downPayment));
                $('#order_discountp').text('Rp ' + numberFormat(discount));
                $('#order_taxp').text('Rp ' + numberFormat(tax));
                $('#order_shippingp').text('Rp ' + numberFormat(shipment_cost));
                $('#order_totalp').text('Rp ' + numberFormat(grandtotal));
                $('#due').val(numberFormat(due));
                $('#order_subtotalp').text('Rp ' + numberFormat(subtotal));
                
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

        $('#paying_amount').change(function() {
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
        var retursubtotal = 0;
        var returgrandtotal = 0;

        function numberFormat(number) {
            return number.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        returTablePayment();

        function returTablePayment() {
            setTimeout(function() {
                // 1. Akumulasi nilai dari setiap .subtotal
                retursubtotal = 0;
                $('.retursubtotal').each(function() {
                    var value = parseFloat($(this).text());
                    retursubtotal += isNaN(value) ? 0 : value;
                });

                // 3. Ambil nilai shipment
                var returshipment_cost_value = parseFloat($('#returshipment_cost').val());
                var returshipment_cost = isNaN(returshipment_cost_value) ? 0 : -returshipment_cost_value;
                // 3. Ambil nilai shipment
                var retur_requestshipment_cost_value = parseFloat($('#retur_requestshipment_cost').val());
                var retur_requestshipment_cost = isNaN(retur_requestshipment_cost_value) ? 0 : -retur_requestretur_requestshipment_cost_value;

                // 4. Hitung grandtotal
                returgrandtotal = retursubtotal +returshipment_cost + retur_requestshipment_cost;

                $('#returorder_shipping').text('Rp ' + numberFormat(returshipment_cost+retur_requestshipment_cost));
                $('#returorder_total').text('Rp ' + numberFormat(returgrandtotal));
                $('#returorder_subtotal').text('Rp ' + numberFormat(retursubtotal));
                
                $('#returorder_subtotal_input').val(retursubtotal);
                $('#returorder_total_input').val(returgrandtotal);
            }, 700); // Jeda 0,7 detik
        };
        $('#returshipment_cost').change(function() {
            returTablePayment();
        });
        $('#retur_requestshipment_cost').change(function() {
            returTablePayment();
        });
        $(document).on('change', '.unpassedqty', function() {
            returTablePayment();
        });
        $(document).on('change', '.requestqty', function() {
            returTablePayment();
        });
        $(document).on('click', '.returdelete', function() {
            returTablePayment();
        });
    });
</script>

<script>
    $(document).ready(function() {
        var products = [];
        var products_with_variant = [];
        var productsObj = {};
        var productsWithVariantObj = {};
        
        var products_checked = [];
        var products_with_variant_checked = [];
        var products_checkedObj = {};
        var productsWithVariant_checkedObj = {};
        
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
        $('#checkingtable tbody tr').each(function() {
            var variant_id = $(this).find('.variant-id').data('variant');
            var product_id = $(this).find('.product-id').data('product');
            if ($(this).find('.inp-cbx').prop('checked')) {
                if (!variant_id ){
                    products_checked.push({ key: product_id, value: 1 });
                    $.each(products_checked, function (i, value) {
                        products_checkedObj[value.key] = value.value;
                    })
                    $('#products_checked').val(JSON.stringify(products_checkedObj));
                } else {
                    products_with_variant_checked.push({ key: variant_id, value: 1 });
                    $.each(products_with_variant_checked, function (i, value) {
                        productsWithVariant_checkedObj[value.key] = value.value;
                    })
                    $('#products_with_variant_checked').val(JSON.stringify(productsWithVariant_checkedObj));
                }
            }
        });
        console.log(JSON.stringify(productsObj));
        console.log(JSON.stringify(productsWithVariantObj));
        console.log(JSON.stringify(products_checkedObj));
        console.log(JSON.stringify(productsWithVariant_checkedObj));

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


        $(document).on('change', '.unpassedqty', function() {
            var row = $(this).closest('tr');
            var cost = $(this).closest('tr').find('.returcost').text();
            var subtotalElement = $(this).closest('tr').find('.retursubtotal');
            var value = row.find('.returdelete').data('value');
            var variant = row.find('.variant-id').data('variant');

            // Ambil nilai saat ini dari unpassedqty
            var currentQtyUnpassed = parseFloat($(this).val());


            // Jika NaN atau kurang dari 1, set ke 1
            if (isNaN(currentQtyUnpassed) || currentQtyUnpassed <= 0) {
                $(this).val(1);
                currentQtyUnpassed = 1;
            }

            // Hitung subtotal baru
            var currentSubtotal = parseFloat(subtotalElement.text()) || 0; // Pastikan currentSubtotal tidak NaN
            var previousQtyUnpassed = parseFloat($(this).data('previous-qty')); // Mengambil qty sebelumnya dari data

            // Hitung perubahan
            var costUnpassedPrevious = (cost * previousQtyUnpassed);
            var costUnpassedNew = (cost * currentQtyUnpassed);
            console.log(costUnpassedNew);

            // Update subtotal dengan mempertimbangkan perubahan
            var newSubtotal = currentSubtotal + costUnpassedPrevious - costUnpassedNew; // Unpassed qty mengurangi subtotal
            subtotalElement.text(newSubtotal);

            // Simpan qty saat ini untuk digunakan nanti
            $(this).data('previous-qty', currentQtyUnpassed);

            
        });


        $(document).on('change', '.requestqty', function() {
            var row = $(this).closest('tr');
            var cost = $(this).closest('tr').find('.returcost').text();
            var subtotalElement = $(this).closest('tr').find('.retursubtotal');
            var value = row.find('.returdelete').data('value');
            var variant = row.find('.variant-id').data('variant');

            // Ambil nilai saat ini dari requestqty
            var currentQtyRequest = parseFloat($(this).val());

            // Jika NaN atau kurang dari 1, set ke 1
            if (isNaN(currentQtyRequest)) {
                $(this).val(1);
                currentQtyRequest = 1;
            }

            // Hitung subtotal baru
            var currentSubtotal = parseFloat(subtotalElement.text()) || 0; // Pastikan currentSubtotal tidak NaN
            var previousQtyRequest = parseFloat($(this).data('previous-qty')); // Mengambil qty sebelumnya dari data

            // Hitung perubahan
            var costRequestPrevious = (cost * previousQtyRequest);
            var costRequestNew = (cost * currentQtyRequest);

            // Update subtotal dengan mempertimbangkan perubahan
            var newSubtotal = currentSubtotal + costRequestNew - costRequestPrevious; // Request qty menambah subtotal
            subtotalElement.text(newSubtotal);

            // Simpan qty saat ini untuk digunakan nanti
            $(this).data('previous-qty', currentQtyRequest);

            
        });

        $(document).on('change', '.inp-cbx', function() {
            var row = $(this).closest('tr');

            var variant = row.find('.variant-id').data('variant');
            var value = row.find('.product-id').data('product');

            if (this.checked) {
                if (!variant){
                    products_checked.push({ key: value, value: 1 });
                    $.each(products_checked, function (i, value) {
                        products_checkedObj[value.key] = value.value;
                    })
                    $('#products_checked').val(JSON.stringify(products_checkedObj));
                } else {
                    products_with_variant_checked.push({ key: variant, value: 1 });
                    $.each(products_with_variant_checked, function (i, value) {
                        productsWithVariant_checkedObj[value.key] = value.value;
                    })
                    $('#products_with_variant_checked').val(JSON.stringify(productsWithVariant_checkedObj));
                }
            } else {
                    if (!variant) {
                    for (var i = 0; i < products_checked.length; i++) {
                        if (products_checked[i].key == value) {
                            products_checked.splice(i, 1);
                            break;
                        }
                    }

                    // Menghapus key dari productsObj yang tidak ada di products
                    Object.keys(products_checkedObj).forEach(function(key) {
                        if (!products_checked.some(product => product.key == key)) {
                            delete products_checkedObj[key];
                        }
                    });
                    $('#products_checked').val(JSON.stringify(products_checkedObj));

                } else {
                    // Menghapus produk bervarian dari array products_with_variant
                    for (var i = 0; i < products_with_variant_checked.length; i++) {
                        if (products_with_variant_checked[i].key == variant) {
                            products_with_variant_checked.splice(i, 1);
                            break; // Hentikan loop setelah menemukan dan menghapus item
                        }
                    }

                    // Menghapus key dari productsWithVariantObj yang tidak ada di products_with_variant
                    Object.keys(productsWithVariant_checkedObj).forEach(function(key) {
                        if (!products_with_variant_checked.some(product => product.key == key)) {
                            delete productsWithVariant_checkedObj[key];
                        }
                    });
                    $('#products_with_variant_checked').val(JSON.stringify(productsWithVariant_checkedObj));
                }
            }
            console.log(JSON.stringify(products_checkedObj));
            console.log(JSON.stringify(productsWithVariant_checkedObj));
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
                    if (!products.some(product => product.key == key)) {
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
                    if (!products_with_variant.some(product => product.key == key)) {
                        delete productsWithVariantObj[key];
                    }
                });
                $('#products_with_variant').val(JSON.stringify(productsWithVariantObj));
            }

            console.log(JSON.stringify(productsObj));
            console.log(JSON.stringify(productsWithVariantObj));
        });

        $('#returTable').on('click', '.returdelete', function() {
            var row = $(this).closest('tr');
            var value = $(this).data('value');
            var variant = row.find('.variant-id').data('variant');

            //hapus item di tabel
            row.remove();
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
                input_supplier_bank_account.removeAttribute('required');
                input_supplier_ewalet.removeAttribute('required');
            }

            if (own_courier == courier.value) {
                driver_phone.style.display = 'flex';
                shipment_number.style.display = 'none';
                input_driver_phone.setAttribute('required', 'required');
                input_shipment_number.removeAttribute('required');
            } else if ("" == courier.value) {
                shipment_number.style.display = 'none';
                driver_phone.style.display = 'none';
                input_driver_phone.removeAttribute('required');
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const returbanks = ['bni', 'bri', 'permata', 'mandiri', 'bca'];
        const returewalets = ['ovo', 'gopay'];
        var returpayment_method = document.getElementById('returpayment_method');
        var retursupplier_bank_account = document.getElementById('retursupplier_bank_account');
        var retursupplier_ewalet = document.getElementById('retursupplier_ewalet');
        var returinput_supplier_bank_account = document.getElementById('returinput_bank_account'); // Koreksi ID
        var returinput_supplier_ewalet = document.getElementById('returinput_ewalet'); // Koreksi ID

        const returown_courier = 'own';
        var returcourier = document.getElementById('returcourier');
        var returdriver_phone = document.getElementById('returdriver_phone');
        var returshipment_number = document.getElementById('returshipment_number');
        var returinput_driver_phone = document.getElementById('returinput_driver_phone'); // Koreksi ID
        var returinput_shipment_number = document.getElementById('returinput_shipment_number'); // Koreksi ID
        
        const retur_requestown_courier = 'own';
        var retur_requestcourier = document.getElementById('retur_requestcourier');
        var retur_requestdriver_phone = document.getElementById('retur_requestdriver_phone');
        var retur_requestshipment_number = document.getElementById('retur_requestshipment_number');
        var retur_requestinput_driver_phone = document.getElementById('retur_requestinput_driver_phone'); // Koreksi ID
        var retur_requestinput_shipment_number = document.getElementById('retur_requestinput_shipment_number'); // Koreksi ID

        function returColumn() {
            if (returbanks.includes(returpayment_method.value)) {
                retursupplier_bank_account.style.display = 'flex';
                retursupplier_ewalet.style.display = 'none';
                returinput_supplier_bank_account.setAttribute('required', 'required');
                returinput_supplier_ewalet.removeAttribute('required');
            } else if (returewalets.includes(returpayment_method.value)) {
                retursupplier_ewalet.style.display = 'flex';
                retursupplier_bank_account.style.display = 'none';
                returinput_supplier_ewalet.setAttribute('required', 'required');
                returinput_supplier_bank_account.removeAttribute('required');
            } else {
                retursupplier_bank_account.style.display = 'none';
                retursupplier_ewalet.style.display = 'none';
                returinput_supplier_bank_account.removeAttribute('required');
                returinput_supplier_ewalet.removeAttribute('required');
            }

            if (returown_courier == returcourier.value) {
                returdriver_phone.style.display = 'flex';
                returshipment_number.style.display = 'none';
                returinput_driver_phone.setAttribute('required', 'required');
                returinput_shipment_number.removeAttribute('required');
            } else if ("" == returcourier.value) {
                returshipment_number.style.display = 'none';
                returdriver_phone.style.display = 'none';
                returinput_driver_phone.removeAttribute('required');
                returinput_shipment_number.removeAttribute('required');
            } else {
                returshipment_number.style.display = 'flex';
                returdriver_phone.style.display = 'none';
                returinput_shipment_number.setAttribute('required', 'required');
                returinput_driver_phone.removeAttribute('required');
            }

            if (retur_requestcourier !== 'undefined' && retur_requestcourier !== null) {
                if (retur_requestown_courier == retur_requestcourier.value) {
                    retur_requestdriver_phone.style.display = 'flex';
                    retur_requestshipment_number.style.display = 'none';
                    retur_requestinput_driver_phone.setAttribute('required', 'required');
                    retur_requestinput_shipment_number.removeAttribute('required');
                } else if ("" == retur_requestcourier.value) {
                    retur_requestshipment_number.style.display = 'none';
                    retur_requestdriver_phone.style.display = 'none';
                    retur_requestinput_driver_phone.removeAttribute('required');
                    retur_requestinput_shipment_number.removeAttribute('required');
                } else {
                    retur_requestshipment_number.style.display = 'flex';
                    retur_requestdriver_phone.style.display = 'none';
                    retur_requestinput_shipment_number.setAttribute('required', 'required');
                    retur_requestinput_driver_phone.removeAttribute('required');
                }
            }
        }

        returColumn();

        returpayment_method.addEventListener('change', function() {
            returColumn();
        });

        returcourier.addEventListener('change', function() {
            returColumn();
        });
        retur_requestcourier.addEventListener('change', function() {
            returColumn();
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mengambil hash dari URL
        var hash = window.location.hash;

        // Jika ada hash, tampilkan tab yang sesuai
        if (hash) {
            var tab = document.querySelector(`a[data-bs-toggle="tab"][href="${hash}"]`);
            if (tab) {
                tab.click(); // Mengklik tab jika ada
            }
        }
    });
</script>

<script>
    $(document).ready(function() {
    function checkInputs() {
        console.log('checkInputs')
        // Array pasangan kelas dan elemen id
        const pairs = [
            { inputClass: '.returnqty', elementId: '#shipping-return' },
            { inputClass: '.requestqty', elementId: '#shipping-request' }
        ];

        // Loop melalui setiap pasangan
        pairs.forEach(function(pair) {
            let shouldDisplay = false;

            // Loop melalui semua elemen dengan kelas tertentu
            $(pair.inputClass).each(function() {
                // Ambil nilai dari input
                let value = parseFloat($(this).val());

                // Jika nilai lebih dari 0, set flag untuk menampilkan elemen
                if (value > 0) {
                    shouldDisplay = true;
                    return false; // Keluar dari loop saat menemukan nilai lebih dari 0
                }
            });

            // Tampilkan atau sembunyikan elemen yang sesuai
            if (shouldDisplay) {
                $(pair.elementId).css('display', 'block');
            } else {
                $(pair.elementId).css('display', 'none');
            }
        });
    }

    // Panggil fungsi checkInputs saat input berubah
    $('.returnqty, .requestqty').on('input', function() {
        checkInputs();
    });

    // Panggil fungsi checkInputs pada awalnya jika perlu
    checkInputs();
});
</script>

<script>
    $(document).ready(function() {
    function checkInputs() {
        // Array pasangan kelas dan elemen id
        const pairs = [
            { inputClass: '.retursubtotal', elementId: '#return-payment' },
        ];

        // Loop melalui setiap pasangan
        pairs.forEach(function(pair) {
            let shouldDisplay = false;

            // Loop melalui semua elemen dengan kelas tertentu
            $(pair.inputClass).each(function() {
                // Ambil nilai dari input
                let value = parseFloat($(this).val());

                // Jika nilai lebih dari 0, set flag untuk menampilkan elemen
                if (value > 0) {
                    shouldDisplay = true;
                    return false; // Keluar dari loop saat menemukan nilai lebih dari 0
                }
            });

            // Tampilkan atau sembunyikan elemen yang sesuai
            if (shouldDisplay) {
                $(pair.elementId).css('display', 'block');
            } else {
                $(pair.elementId).css('display', 'none');
            }
        });
    }

    // Panggil fungsi checkInputs saat input berubah
    $('.returnqty, .requestqty').on('input', function() {
        checkInputs();
    });

    // Panggil fungsi checkInputs pada awalnya jika perlu
    checkInputs();
});
</script>
@endpush
