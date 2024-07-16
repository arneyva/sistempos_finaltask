@extends('templates.main')

@section('pages_title')
    <h1>{{ __('All Sales') }}</h1>
    <p>{{ __('Look All your sales') }}</p>
@endsection

<style>
    .status-completed {
        padding: 7px;
        border-radius: 7px;
        background-color: #c9f1c4;
        color: #0f4c11;
        border: 1px solid #0f4c11;
        /* Outline color */
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        /* Shadow */
    }

    .status-ordered {
        padding: 7px;
        border-radius: 7px;
        background-color: #eff1c4;
        color: #4c4b0f;
        border: 1px solid #4c4b0f;
        /* Outline color */
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        /* Shadow */
    }

    .status-pending {
        padding: 7px;
        border-radius: 7px;
        background-color: #f1c4c4;
        color: #4c0f0f;
        border: 1px solid #4c0f0f;
        /* Outline color */
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        /* Shadow */
    }

    .payment-paid {
        padding: 7px;
        border-radius: 7px;
        background-color: #c4d9f1;
        color: #105e7f;
        border: 1px solid #105e7f;
        /* Outline color */
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        /* Shadow */
    }

    .payment-unpaid {
        padding: 7px;
        border-radius: 7px;
        background-color: #f0c4f1;
        color: #7f107b;
        border: 1px solid #7f107b;
        /* Outline color */
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        /* Shadow */
    }

    .payment-partial {
        padding: 7px;
        border-radius: 7px;
        background-color: #f1dcc4;
        color: #7f6710;
        border: 1px solid #7f6710;
        /* Outline color */
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        /* Shadow */
    }

    .shipping-shipped {
        padding: 7px;
        border-radius: 7px;
        background-color: #c4c8f1;
        color: #33107f;
        border: 1px solid #33107f;
        /* Outline color */
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        /* Shadow */
    }

    .shipping-delivered {
        padding: 7px;
        border-radius: 7px;
        background-color: #c4f1d1;
        color: #107f2c;
        border: 1px solid #107f2c;
        /* Outline color */
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        /* Shadow */
    }

    .shipping-packed {
        padding: 7px;
        border-radius: 7px;
        background-color: #b19785;
        color: #583606;
        border: 1px solid #583606;
        /* Outline color */
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        /* Shadow */
    }

    .shipping-cancelled {
        padding: 7px;
        border-radius: 7px;
        background-color: #f1c4e1;
        color: #7f104f;
        border: 1px solid #7f104f;
        /* Outline color */
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        /* Shadow */
    }

    .warehousedeleted {
        padding: 7px;
        border-radius: 7px;
        background-color: #ffefef;
        color: #F24D4D;
    }
</style>
@section('content')

    <div class="col-sm-12">
        <div class="mt-3" style="justify-content-center">
            <!-- @include('templates.alert') -->
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">{{ __('All Sales') }}
                    </h4>
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-warning" data-bs-toggle="modal"
                        data-bs-target="#createModal">{{ __('Filter') }}</button>
                    @role('superadmin|inventaris')
                        <a href="{{ route('sale.pdf', request()->query()) }}" class="btn btn-soft-success">PDF</a>
                        <a href="{{ route('sale.export', request()->query()) }}" class="btn btn-soft-danger">Excel</a>
                    @endrole
                    <a href="{{ route('sale.create') }}"><button type="button"
                            class="btn btn-soft-primary">{{ __('Create +') }}</button></a>
                    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createModalLabel">{{ __('Filter') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('sale.index') }}" method="GET" id="filterForm">
                                        <div class="col mb-3">
                                            <label class="form-label" for="date">{{ __('Date') }}</label>
                                            <input type="date" class="form-control" id="date" name="date"
                                                value="{{ request()->input('date') }}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="Ref">{{ __('Reference') }}</label>
                                            <input type="text" class="form-control" id="Ref" name="Ref"
                                                value="{{ request()->input('Ref') }}"
                                                placeholder="{{ __('Input Ref ...') }}">
                                        </div>
                                        @role('superadmin|inventaris')
                                            <div class="col mb-3">
                                                <label class="form-label" for="warehouse_id">{{ __('Warehouse/Outlet') }}
                                                </label>
                                                <select class="form-select" id="warehouse_id" name="warehouse_id">
                                                    <option selected disabled value="">{{ __('Choose...') }}</option>
                                                    @foreach ($warehouse as $wh)
                                                        <option value="{{ $wh->id }}"
                                                            {{ request()->input('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                                            {{ $wh->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endrole
                                        <div class="col mb-3">
                                            <label class="form-label" for="client_id">{{ __('Customer') }}
                                                *</label>
                                            <select class="form-select" id="client_id" name="client_id">
                                                <option selected disabled value="">{{ __('Choose...') }}</option>
                                                @foreach ($client as $wh)
                                                    <option value="{{ $wh->id }}"
                                                        {{ request()->input('client_id') == $wh->id ? 'selected' : '' }}>
                                                        {{ $wh->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="statut">{{ __('Status') }}</label>
                                            <select class="form-select" id="statut" name="statut">
                                                <option selected disabled value="">{{ __('Choose...') }}</option>
                                                <option value="completed"
                                                    {{ request()->input('statut') == 'completed' ? 'selected' : '' }}>
                                                    {{ __('Completed') }}</option>
                                                <option value="pending"
                                                    {{ request()->input('statut') == 'pending' ? 'selected' : '' }}>
                                                    {{ __('Pending') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label"
                                                for="shipping_status">{{ __('Shipping Status') }}</label>
                                            <select class="form-select" id="shipping_status" name="shipping_status">
                                                <option selected disabled value="">{{ __('Choose...') }}</option>
                                                <option value="shipped"
                                                    {{ request()->input('shipping_status') == 'shipped' ? 'selected' : '' }}>
                                                    {{ __('Shipped') }}</option>
                                                <option value="delivered"
                                                    {{ request()->input('shipping_status') == 'delivered' ? 'selected' : '' }}>
                                                    {{ __('Delivered') }}
                                                </option>
                                                <option value="cancelled"
                                                    {{ request()->input('shipping_status') == 'cancelled' ? 'selected' : '' }}>
                                                    {{ __('Cancelled') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label"
                                                for="payment_statut">{{ __('Payment Status') }}</label>
                                            <select class="form-select" id="payment_statut" name="payment_statut">
                                                <option selected disabled value="">{{ __('Choose...') }}</option>
                                                <option value="paid"
                                                    {{ request()->input('payment_statut') == 'paid' ? 'selected' : '' }}>
                                                    {{ __('Paid') }}</option>
                                                <option value="unpaid"
                                                    {{ request()->input('payment_statut') == 'unpaid' ? 'selected' : '' }}>
                                                    {{ __('Unpaid') }}
                                                </option>
                                            </select>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" onclick="resetFilters()"
                                        data-bs-dismiss="modal">{{ __('Reset') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped table-hover mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Reference') }}</th>
                                <th>{{ __('Added by') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Warehouse/Outlet') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Grand Total') }}</th>
                                <th>{{ __('Payment Status') }}</th>
                                <th>{{ __('Shipping Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale as $item)
                                <tr>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->Ref }}</td>
                                    <td>{{ $item->user->firstname }} {{ $item->user->lastname }}</td>
                                    <td>{{ $item->client->name }}</td>
                                    <td>{{ $item->warehouse->name }}</td>
                                    <td>
                                        @if ($item->statut == 'completed')
                                            <span class="status-completed">{{ __('Completed') }}</span>
                                        @elseif($item->statut == 'pending')
                                            <span class="status-pending">{{ __('Pending') }}</span>
                                        @else
                                            <span class="status-ordered">{{ __('Ordered') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ 'Rp ' . number_format($item->GrandTotal, 2, ',', '.') }}</td>
                                    <td>
                                        @if ($item->payment_statut == 'paid')
                                            <span class="payment-paid">{{ __('Paid') }}</span>
                                        @else
                                            <span class="payment-unpaid">{{ __('Unpaid') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->shipping_status == 'shipped' && $item->shipping != null)
                                            <span class="btn btn-outline-success btn-sm">{{ __('Shipped') }}</span>
                                        @elseif ($item->shipping_status == 'delivered' && $item->shipping != null)
                                            <span class="btn btn-outline-primary btn-sm">{{ __('Delivered') }}</span>
                                        @elseif ($item->shipping_status == 'cancelled' && $item->shipping != null)
                                            <span class="btn btn-outline-warning btn-sm">{{ __('Cancelled') }}</span>
                                        @elseif ($item->shipping_status == null && $item->shipping != null)
                                            <span class="btn btn-outline-info btn-sm">{{ __('Packed') }}</span>
                                        @else
                                            <span
                                                class="btn btn-outline-danger btn-sm">{{ __('Without Shipment') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="4em" height="4em"
                                            viewBox="0 0 24 24" class="dropdown-toggle"
                                            id="dropdownMenuButton{{ $item->id }}" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <circle cx="5.5" cy="7.5" r="1.5" fill="#546DEB" />
                                            <path fill="#546DEB" fill-rule="evenodd"
                                                d="M8 6.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5"
                                                clip-rule="evenodd" />
                                            <circle cx="5.5" cy="12" r="1.5" fill="#546DEB" />
                                            <path fill="#546DEB" fill-rule="evenodd"
                                                d="M8 11a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 0 1h-8A.5.5 0 0 1 8 11m0 2a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 8 13"
                                                clip-rule="evenodd" />
                                            <circle cx="5.5" cy="16.5" r="1.5" fill="#546DEB" />
                                            <path fill="#546DEB" fill-rule="evenodd"
                                                d="M8 15.5a.5.5 0 0 1 .5-.5H18a.5.5 0 0 1 0 1H8.5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div class="modal fade modal-lg" id="staticBackdrop{{ $item->id }}"
                                            data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                            aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="staticBackdropLabel">
                                                            {{ $item->Ref }} {{ __('Payment') }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    {{-- body --}}
                                                    <div class="modal-body">
                                                        <div class="card-body p-0">
                                                            <div class="table-responsive mt-4">
                                                                <table class="table table-striped mb-0" role="grid">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>{{ __('Date') }}</th>
                                                                            <th>{{ __('Reference') }}</th>
                                                                            <th>{{ __('Montant') }}</th>
                                                                            <th>{{ __('Change Return') }}</th>
                                                                            <th>{{ __('Payment Status') }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>{{ $item->paymentSales->date }}</td>
                                                                            <td>{{ $item->paymentSales->Ref }}</td>
                                                                            <td>{{ 'Rp ' . number_format($item->paymentSales->montant, 2, ',', '.') }}
                                                                            </td>
                                                                            <td>{{ 'Rp ' . number_format($item->paymentSales->change, 2, ',', '.') }}
                                                                            </td>
                                                                            <td>{{ $item->paymentSales->status }}</td>
                                                                            <td></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="shippingmodal{{ $item->id }}" tabindex="-1"
                                            aria-labelledby="shippingmodalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="shippingmodalLabel">
                                                            {{ __('Shipping') }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('sale.shipment.store') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="sale_id"
                                                                value="{{ $item->id }}">
                                                            <input type="hidden" name="Ref"
                                                                value="SM-{{ $item->Ref }}">
                                                            <div class="col mb-3">
                                                                <label class="form-label"
                                                                    for="status">{{ __('Status *') }}</label>
                                                                <select class="form-select" id="status"
                                                                    name="status">
                                                                    <option selected disabled value="">
                                                                        {{ __('Choose...') }}
                                                                    </option>
                                                                    <option value="shipped"
                                                                        {{ $item->shipment && $item->shipment->status == 'shipped' ? 'selected' : '' }}>
                                                                        {{ __('Shipped') }}</option>
                                                                    <option value="delivered"
                                                                        {{ $item->shipment && $item->shipment->status == 'delivered' ? 'selected' : '' }}>
                                                                        {{ __('Delivered') }}</option>
                                                                    <option value="cancelled"
                                                                        {{ $item->shipment && $item->shipment->status == 'cancelled' ? 'selected' : '' }}>
                                                                        {{ __('Cancelled') }}</option>
                                                                </select>
                                                            </div>
                                                            <div class="col mb-3">
                                                                <label class="form-label"
                                                                    for="delivered_to">{{ __('Delivered To *') }}</label>
                                                                <input type="text" class="form-control"
                                                                    id="delivered_to" required name="delivered_to"
                                                                    value="{{ $item->shipment ? $item->shipment->delivered_to : '' }}"
                                                                    placeholder="{{ __('Input...') }}">
                                                            </div>
                                                            <div class="col mb-3">
                                                                <label class="form-label"
                                                                    for="shipping_address">{{ __('Address *') }}</label>
                                                                <input type="text" class="form-control"
                                                                    id="shipping_address" required name="shipping_address"
                                                                    value="{{ $item->shipment ? $item->shipment->shipping_address : '' }}"
                                                                    placeholder="{{ __('Input...') }}">
                                                            </div>
                                                            <div class="col mb-3">
                                                                <label class="form-label"
                                                                    for="shipping_details">{{ __('Details Note') }}</label>
                                                                <input type="text" class="form-control"
                                                                    id="shipping_details" required name="shipping_details"
                                                                    value="{{ $item->shipment ? $item->shipment->shipping_details : '' }}"
                                                                    placeholder="{{ __('Input...') }}">
                                                            </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ __('Save changes') }}</button>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="invoiceModal{{ $item->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="invoiceModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="invoiceModalLabel">Invoice POS</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="invoice-content"
                                                            style="box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); padding: 15px;">
                                                            <p class="mb-0" style="margin-bottom: 5px;">Date:
                                                                {{ $item['date'] }}</p>
                                                            <p class="mb-0" style="margin-bottom: 5px;">Warehouse:
                                                                {{ $item['warehouse']['name'] }}</p>
                                                            <p class="mb-0" style="margin-bottom: 5px;">Client:
                                                                {{ $item['client']['name'] }}</p>
                                                            <p class="mb-0" style="margin-bottom: 10px;">Reference:
                                                                {{ $item['Ref'] }}</p>
                                                        </div>
                                                        <div class="invoice-content" style="padding: 15px;">
                                                            <ul style="list-style-type: none; padding: 0;">
                                                                @foreach ($details as $detail)
                                                                    @if ($detail['sale_id'] == $item->id)
                                                                        <table id="basic-table"
                                                                            class="table table-hover table-bordered table-sm"
                                                                            role="grid">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>Product Information</td>
                                                                                    <th> <strong>{{ $detail['name'] }}</strong>
                                                                                        ({{ $detail['code'] }})
                                                                                    </th>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Quantity</td>
                                                                                    <th> {{ $detail['quantity'] }}
                                                                                        {{ $detail['unit_sale'] }} x
                                                                                        {{ $detail['Unit_price'] }}</th>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Discount</td>
                                                                                    <th>{{ 'Rp ' . number_format($detail['discount'], 2, ',', '.') }}
                                                                                    </th>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Tax</td>
                                                                                    <th>{{ 'Rp ' . number_format($detail['taxe_total'], 2, ',', '.') }}
                                                                                    </th>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Total</td>
                                                                                    <th>{{ $detail['total'] }}</th>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                        <div class="invoice-content"
                                                            style="box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); padding: 15px;">
                                                            <p class="mb-0" style="margin-bottom: 5px;">Discount:
                                                                {{ 'Rp ' . number_format($item['discount'], 2, ',', '.') }}
                                                            </p>
                                                            <p class="mb-0" style="margin-bottom: 5px;">Shipping:
                                                                {{ 'Rp ' . number_format($item['shipping'], 2, ',', '.') }}
                                                            </p>
                                                            <p class="mb-0" style="margin-bottom: 5px;">Tax:
                                                                {{ 'Rp ' . number_format($item['TaxNet'], 2, ',', '.') }}
                                                                ~
                                                                {{ $item['tax_rate'] }} %</p>
                                                            <p class="mb-0" style="margin-bottom: 10px;">Grand Total:
                                                                {{ 'Rp ' . number_format($item['GrandTotal'], 2, ',', '.') }}
                                                            </p>
                                                        </div>
                                                        <div class="invoice-content" style="padding: 15px;">
                                                            <table id="product-table" class="table table-striped mb-0"
                                                                role="grid">
                                                                <thead>
                                                                    <tr>

                                                                        <th>{{ __('Reference') }}</th>
                                                                        <th>{{ __('Montant') }}</th>
                                                                        <th>{{ __('Change Return') }}</th>

                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <th>{{ $item['paymentSales']['Ref'] }}</th>
                                                                        <th> {{ 'Rp ' . number_format($item['paymentSales']['montant'], 2, ',', '.') }}
                                                                        </th>
                                                                        <th> {{ 'Rp ' . number_format($item['paymentSales']['change'], 2, ',', '.') }}
                                                                        </th>
                                                                        <th></th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="invoice-content" style="padding: 15px;">
                                                            {{-- <td style="text-align: center; vertical-align: middle;"> --}}
                                                            @php
                                                                $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
                                                                $barcodeData = base64_encode(
                                                                    $generatorPNG->getBarcode(
                                                                        $item['Ref'],
                                                                        $generatorPNG::TYPE_CODE_128,
                                                                    ),
                                                                );
                                                                $barcodeUrl = 'data:image/png;base64,' . $barcodeData;
                                                            @endphp
                                                            {{-- <div style="display: flex; flex-direction: column; align-items: center;"> --}}
                                                            <img src="{{ $barcodeUrl }}" alt="Barcode"
                                                                style="margin-bottom: 5px;">
                                                            {{-- <span>{{ $item['Ref'] }}</span> --}}
                                                            {{-- </div> --}}
                                                            {{-- </td> --}}
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        {{-- <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Close</button> --}}
                                                        <button type="button"
                                                            class="btn btn-primary btn-print">Print</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="p-0 sub-drop dropdown-menu dropdown-menu-end"
                                            aria-labelledby="dropdownMenuButton{{ $item->id }}"
                                            style="  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);">
                                            <div class="m-0 border-0 shadow-none card">
                                                <div class="p-0 ">
                                                    <ul class="p-0 list-group list-group-flush">
                                                        <li class="iq-sub-card list-group-item">
                                                            @if ($item->payment_statut == 'paid')
                                                                <div class="pay-button"
                                                                    style="color: red; cursor: not-allowed;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                        height="1.5em" viewBox="0 0 48 48">
                                                                        <g fill="currentColor" fill-rule="evenodd"
                                                                            clip-rule="evenodd">
                                                                            <path
                                                                                d="M28.772 24.667A4 4 0 0 0 25 22v-1h-2v1a4 4 0 1 0 0 8v4c-.87 0-1.611-.555-1.887-1.333a1 1 0 1 0-1.885.666A4 4 0 0 0 23 36v1h2v-1a4 4 0 0 0 0-8v-4a2 2 0 0 1 1.886 1.333a1 1 0 1 0 1.886-.666M23 24a2 2 0 1 0 0 4zm2 10a2 2 0 1 0 0-4z" />
                                                                            <path
                                                                                d="M13.153 8.621C15.607 7.42 19.633 6 24.039 6c4.314 0 8.234 1.361 10.675 2.546l.138.067c.736.364 1.33.708 1.748.987L32.906 15C41.422 23.706 48 41.997 24.039 41.997S6.479 24.038 15.069 15l-3.67-5.4c.283-.185.642-.4 1.07-.628q.318-.171.684-.35m17.379 6.307l2.957-4.323c-2.75.198-6.022.844-9.172 1.756c-2.25.65-4.75.551-7.065.124a25 25 0 0 1-1.737-.386l1.92 2.827c4.115 1.465 8.981 1.465 13.097.002M16.28 16.63c4.815 1.86 10.602 1.86 15.417-.002a29.3 29.3 0 0 1 4.988 7.143c1.352 2.758 2.088 5.515 1.968 7.891c-.116 2.293-1.018 4.252-3.078 5.708c-2.147 1.517-5.758 2.627-11.537 2.627c-5.785 0-9.413-1.091-11.58-2.591c-2.075-1.437-2.986-3.37-3.115-5.632c-.135-2.35.585-5.093 1.932-7.87c1.285-2.648 3.078-5.197 5.005-7.274m-1.15-6.714c.8.238 1.636.445 2.484.602c2.15.396 4.306.454 6.146-.079a54 54 0 0 1 6.53-1.471C28.45 8.414 26.298 8 24.038 8c-3.445 0-6.658.961-8.908 1.916" />
                                                                        </g>
                                                                    </svg> {{ __('Paid') }}
                                                                </div>
                                                            @elseif ($item->statut == 'pending')
                                                                <div class="pay-button"
                                                                    style="color: red; cursor: not-allowed;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                        height="1.5em" viewBox="0 0 48 48">
                                                                        <g fill="currentColor" fill-rule="evenodd"
                                                                            clip-rule="evenodd">
                                                                            <path
                                                                                d="M28.772 24.667A4 4 0 0 0 25 22v-1h-2v1a4 4 0 1 0 0 8v4c-.87 0-1.611-.555-1.887-1.333a1 1 0 1 0-1.885.666A4 4 0 0 0 23 36v1h2v-1a4 4 0 0 0 0-8v-4a2 2 0 0 1 1.886 1.333a1 1 0 1 0 1.886-.666M23 24a2 2 0 1 0 0 4zm2 10a2 2 0 1 0 0-4z" />
                                                                            <path
                                                                                d="M13.153 8.621C15.607 7.42 19.633 6 24.039 6c4.314 0 8.234 1.361 10.675 2.546l.138.067c.736.364 1.33.708 1.748.987L32.906 15C41.422 23.706 48 41.997 24.039 41.997S6.479 24.038 15.069 15l-3.67-5.4c.283-.185.642-.4 1.07-.628q.318-.171.684-.35m17.379 6.307l2.957-4.323c-2.75.198-6.022.844-9.172 1.756c-2.25.65-4.75.551-7.065.124a25 25 0 0 1-1.737-.386l1.92 2.827c4.115 1.465 8.981 1.465 13.097.002M16.28 16.63c4.815 1.86 10.602 1.86 15.417-.002a29.3 29.3 0 0 1 4.988 7.143c1.352 2.758 2.088 5.515 1.968 7.891c-.116 2.293-1.018 4.252-3.078 5.708c-2.147 1.517-5.758 2.627-11.537 2.627c-5.785 0-9.413-1.091-11.58-2.591c-2.075-1.437-2.986-3.37-3.115-5.632c-.135-2.35.585-5.093 1.932-7.87c1.285-2.648 3.078-5.197 5.005-7.274m-1.15-6.714c.8.238 1.636.445 2.484.602c2.15.396 4.306.454 6.146-.079a54 54 0 0 1 6.53-1.471C28.45 8.414 26.298 8 24.038 8c-3.445 0-6.658.961-8.908 1.916" />
                                                                        </g>
                                                                    </svg> {{ __('Pay !') }}
                                                                </div>
                                                            @else
                                                                <div class="pay-button"
                                                                    data-snap-token="{{ $item->paymentSales->Reglement }}"
                                                                    data-payment-id="{{ $item->paymentSales->id }}"
                                                                    style="color: #546DEB">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                        height="1.5em" viewBox="0 0 48 48">
                                                                        <g fill="currentColor" fill-rule="evenodd"
                                                                            clip-rule="evenodd">
                                                                            <path
                                                                                d="M28.772 24.667A4 4 0 0 0 25 22v-1h-2v1a4 4 0 1 0 0 8v4c-.87 0-1.611-.555-1.887-1.333a1 1 0 1 0-1.885.666A4 4 0 0 0 23 36v1h2v-1a4 4 0 0 0 0-8v-4a2 2 0 0 1 1.886 1.333a1 1 0 1 0 1.886-.666M23 24a2 2 0 1 0 0 4zm2 10a2 2 0 1 0 0-4z" />
                                                                            <path
                                                                                d="M13.153 8.621C15.607 7.42 19.633 6 24.039 6c4.314 0 8.234 1.361 10.675 2.546l.138.067c.736.364 1.33.708 1.748.987L32.906 15C41.422 23.706 48 41.997 24.039 41.997S6.479 24.038 15.069 15l-3.67-5.4c.283-.185.642-.4 1.07-.628q.318-.171.684-.35m17.379 6.307l2.957-4.323c-2.75.198-6.022.844-9.172 1.756c-2.25.65-4.75.551-7.065.124a25 25 0 0 1-1.737-.386l1.92 2.827c4.115 1.465 8.981 1.465 13.097.002M16.28 16.63c4.815 1.86 10.602 1.86 15.417-.002a29.3 29.3 0 0 1 4.988 7.143c1.352 2.758 2.088 5.515 1.968 7.891c-.116 2.293-1.018 4.252-3.078 5.708c-2.147 1.517-5.758 2.627-11.537 2.627c-5.785 0-9.413-1.091-11.58-2.591c-2.075-1.437-2.986-3.37-3.115-5.632c-.135-2.35.585-5.093 1.932-7.87c1.285-2.648 3.078-5.197 5.005-7.274m-1.15-6.714c.8.238 1.636.445 2.484.602c2.15.396 4.306.454 6.146-.079a54 54 0 0 1 6.53-1.471C28.45 8.414 26.298 8 24.038 8c-3.445 0-6.658.961-8.908 1.916" />
                                                                        </g>
                                                                    </svg> {{ __('Pay !') }}
                                                                </div>
                                                            @endif
                                                        </li>
                                                        {{-- <li class="iq-sub-card list-group-item" data-bs-toggle="modal"
                                                            data-bs-target="#invoiceModal{{ $item->id }}"
                                                            style="color: #546DEB;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                height="1.5em" viewBox="0 0 24 24">
                                                                <g fill="none" stroke="currentColor"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="1.5" color="currentColor">
                                                                    <path
                                                                        d="M20.016 2C18.903 2 18 4.686 18 8h2.016c.972 0 1.457 0 1.758-.335c.3-.336.248-.778.144-1.661C21.64 3.67 20.894 2 20.016 2" />
                                                                    <path
                                                                        d="M18 8.054v10.592c0 1.511 0 2.267-.462 2.565c-.755.486-1.922-.534-2.509-.904c-.485-.306-.727-.458-.996-.467c-.291-.01-.538.137-1.062.467l-1.911 1.205c-.516.325-.773.488-1.06.488s-.545-.163-1.06-.488l-1.91-1.205c-.486-.306-.728-.458-.997-.467c-.291-.01-.538.137-1.062.467c-.587.37-1.754 1.39-2.51.904C2 20.913 2 20.158 2 18.646V8.054c0-2.854 0-4.28.879-5.167C3.757 2 5.172 2 8 2h12" />
                                                                    <path
                                                                        d="M10 8c-1.105 0-2 .672-2 1.5s.895 1.5 2 1.5s2 .672 2 1.5s-.895 1.5-2 1.5m0-6c.87 0 1.612.417 1.886 1M10 8V7m0 7c-.87 0-1.612-.417-1.886-1M10 14v1" />
                                                                </g>
                                                            </svg> {{ __('Invoice POS') }}
                                                            </a>
                                                        </li> --}}
                                                        @if ($item->payment_statut == 'paid' && $item->statut == 'completed')
                                                            <li class="iq-sub-card list-group-item"><a class="p-0"
                                                                    href="{{ route('sale.print-invoice', $item->id) }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                        height="1.5em" viewBox="0 0 24 24">
                                                                        <path fill="currentColor"
                                                                            d="M7 7h10v2H7zm0 4h7v2H7z" />
                                                                        <path fill="currentColor"
                                                                            d="M20 2H4c-1.103 0-2 .897-2 2v18l5.333-4H20c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2m0 14H6.667L4 18V4h16z" />
                                                                    </svg> {{ __('Invoice POS') }} </a>
                                                            </li>
                                                        @else
                                                            <li class="iq-sub-card list-group-item">
                                                                <a class="p-0 text-danger" href="javascript:void(0)"
                                                                    style="pointer-events: none;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                        height="1.5em" viewBox="0 0 24 24">
                                                                        <path fill="currentColor"
                                                                            d="M7 7h10v2H7zm0 4h7v2H7z" />
                                                                        <path fill="currentColor"
                                                                            d="M20 2H4c-1.103 0-2 .897-2 2v18l5.333-4H20c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2m0 14H6.667L4 18V4h16z" />
                                                                    </svg> {{ __('Invoice POS') }} </a>
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @role('underdev')
                                                            <li class="iq-sub-card list-group-item"><a class="p-0"
                                                                    href="#">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                        height="1.5em" viewBox="0 0 24 24">
                                                                        <path fill="currentColor"
                                                                            d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2zm-2 0l-8 5l-8-5zm0 12H4V8l8 5l8-5z" />
                                                                    </svg> {{ __('Email Notifications') }} </a>
                                                            </li>
                                                        @endrole
                                                        @if ($item->payment_statut == 'unpaid')
                                                            <li class="iq-sub-card list-group-item">
                                                                <a class="p-0 text-danger" href="javascript:void(0)"
                                                                    style="pointer-events: none;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                        height="1.5em" viewBox="0 0 24 24">
                                                                        <g fill="none">
                                                                            <path
                                                                                d="m12.594 23.258l-.012.002l-.071.035l-.02.004l-.014-.004l-.071-.036c-.01-.003-.019 0-.024.006l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.016-.018m.264-.113l-.014.002l-.184.093l-.01.01l-.003.011l.018.43l.005.012l.008.008l.201.092c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.003-.011l.018-.43l-.003-.012l-.01-.01z" />
                                                                            <path fill="currentColor"
                                                                                d="M21.66 9.412c1.428 5.334-1.737 10.818-7.072 12.247c-4.598 1.232-9.304-.95-11.433-4.99a1 1 0 0 1 1.77-.932a8 8 0 1 0-.452-6.449l1.057-.235c1.186-.265 1.862 1.306.854 1.985L3.711 12.84c-.718.483-1.72-.016-1.713-.918a10.003 10.003 0 0 1 7.414-9.58C14.746.91 20.23 4.076 21.659 9.41M12 6a1 1 0 0 1 1 1v1h2a1 1 0 1 1 0 2h-5a.5.5 0 0 0 0 1h4a2.5 2.5 0 0 1 0 5h-1v1a1 1 0 1 1-2 0v-1H9a1 1 0 1 1 0-2h5a.5.5 0 0 0 0-1h-4a2.5 2.5 0 0 1 0-5h1V7a1 1 0 0 1 1-1" />
                                                                        </g>
                                                                    </svg> {{ __('Sale Return') }}
                                                                </a>
                                                            </li>
                                                        @else
                                                            <li class="iq-sub-card list-group-item"><a class="p-0"
                                                                    href="{{ route('sale.return.create', $item->id) }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                        height="1.5em" viewBox="0 0 24 24">
                                                                        <g fill="none">
                                                                            <path
                                                                                d="m12.594 23.258l-.012.002l-.071.035l-.02.004l-.014-.004l-.071-.036c-.01-.003-.019 0-.024.006l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.016-.018m.264-.113l-.014.002l-.184.093l-.01.01l-.003.011l.018.43l.005.012l.008.008l.201.092c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.003-.011l.018-.43l-.003-.012l-.01-.01z" />
                                                                            <path fill="currentColor"
                                                                                d="M21.66 9.412c1.428 5.334-1.737 10.818-7.072 12.247c-4.598 1.232-9.304-.95-11.433-4.99a1 1 0 0 1 1.77-.932a8 8 0 1 0-.452-6.449l1.057-.235c1.186-.265 1.862 1.306.854 1.985L3.711 12.84c-.718.483-1.72-.016-1.713-.918a10.003 10.003 0 0 1 7.414-9.58C14.746.91 20.23 4.076 21.659 9.41M12 6a1 1 0 0 1 1 1v1h2a1 1 0 1 1 0 2h-5a.5.5 0 0 0 0 1h4a2.5 2.5 0 0 1 0 5h-1v1a1 1 0 1 1-2 0v-1H9a1 1 0 1 1 0-2h5a.5.5 0 0 0 0-1h-4a2.5 2.5 0 0 1 0-5h1V7a1 1 0 0 1 1-1" />
                                                                        </g>
                                                                    </svg> {{ __('Sale Return') }} </a>
                                                            </li>
                                                        @endif
                                                        <li class="iq-sub-card list-group-item"><a class="p-0"
                                                                href="{{ route('sale.show', $item->id) }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                    height="1.5em" viewBox="0 0 24 24">
                                                                    <path fill="currentColor"
                                                                        d="M7 7h10v2H7zm0 4h7v2H7z" />
                                                                    <path fill="currentColor"
                                                                        d="M20 2H4c-1.103 0-2 .897-2 2v18l5.333-4H20c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2m0 14H6.667L4 18V4h16z" />
                                                                </svg> {{ __('Sale Detail') }} </a>
                                                        </li>
                                                        @if ($item->payment_statut == 'paid' && $item->statut == 'completed')
                                                            <li class="iq-sub-card list-group-item">
                                                                <a class="p-0 text-danger" href="javascript:void(0)"
                                                                    style="pointer-events: none;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                        height="1.5em" viewBox="0 0 24 24"
                                                                        fill="red">
                                                                        <path fill="currentColor"
                                                                            d="M21 12a1 1 0 0 0-1 1v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h6a1 1 0 0 0 0-2H5a3 3 0 0 0-3 3v14a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-6a1 1 0 0 0-1-1m-15 .76V17a1 1 0 0 0 1 1h4.24a1 1 0 0 0 .71-.29l6.92-6.93L21.71 8a1 1 0 0 0 0-1.42l-4.24-4.29a1 1 0 0 0-1.42 0l-2.82 2.83l-6.94 6.93a1 1 0 0 0-.29.71m10.76-8.35l2.83 2.83l-1.42 1.42l-2.83-2.83ZM8 13.17l5.93-5.93l2.83 2.83L10.83 16H8Z" />
                                                                    </svg> {{ __('Edit Sales') }}
                                                                </a>
                                                            </li>
                                                        @else
                                                            <li class="iq-sub-card list-group-item">
                                                                <a class="p-0"
                                                                    href="{{ route('sale.edit', $item->id) }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                        height="1.5em" viewBox="0 0 24 24">
                                                                        <path fill="currentColor"
                                                                            d="M21 12a1 1 0 0 0-1 1v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h6a1 1 0 0 0 0-2H5a3 3 0 0 0-3 3v14a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-6a1 1 0 0 0-1-1m-15 .76V17a1 1 0 0 0 1 1h4.24a1 1 0 0 0 .71-.29l6.92-6.93L21.71 8a1 1 0 0 0 0-1.42l-4.24-4.29a1 1 0 0 0-1.42 0l-2.82 2.83l-6.94 6.93a1 1 0 0 0-.29.71m10.76-8.35l2.83 2.83l-1.42 1.42l-2.83-2.83ZM8 13.17l5.93-5.93l2.83 2.83L10.83 16H8Z" />
                                                                    </svg> {{ __('Edit Sales') }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li class="iq-sub-card list-group-item" data-bs-toggle="modal"
                                                            data-bs-target="#staticBackdrop{{ $item->id }}"
                                                            style="color: #546DEB;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                height="1.5em" viewBox="0 0 256 256">
                                                                <path fill="#546DEB"
                                                                    d="M128 88a40 40 0 1 0 40 40a40 40 0 0 0-40-40m0 64a24 24 0 1 1 24-24a24 24 0 0 1-24 24m112-96H16a8 8 0 0 0-8 8v128a8 8 0 0 0 8 8h224a8 8 0 0 0 8-8V64a8 8 0 0 0-8-8m-46.35 128H62.35A56.78 56.78 0 0 0 24 145.65v-35.3A56.78 56.78 0 0 0 62.35 72h131.3A56.78 56.78 0 0 0 232 110.35v35.3A56.78 56.78 0 0 0 193.65 184M232 93.37A40.8 40.8 0 0 1 210.63 72H232ZM45.37 72A40.8 40.8 0 0 1 24 93.37V72ZM24 162.63A40.8 40.8 0 0 1 45.37 184H24ZM210.63 184A40.8 40.8 0 0 1 232 162.63V184Z" />
                                                            </svg> {{ __('Show Payment') }}
                                                        </li>
                                                        <li class="iq-sub-card list-group-item {{ $item->shipping ? '' : 'text-danger text-decoration-line-through' }}"
                                                            {{ $item->shipping ? 'data-bs-toggle=modal data-bs-target=#shippingmodal' . $item->id : '' }}
                                                            style="color: #546DEB;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                height="1.5em" viewBox="0 0 32 32">
                                                                <path fill="currentColor"
                                                                    d="M0 6v2h19v15h-6.156c-.446-1.719-1.992-3-3.844-3c-1.852 0-3.398 1.281-3.844 3H4v-5H2v7h3.156c.446 1.719 1.992 3 3.844 3c1.852 0 3.398-1.281 3.844-3h8.312c.446 1.719 1.992 3 3.844 3c1.852 0 3.398-1.281 3.844-3H32v-8.156l-.063-.157l-2-6L29.72 10H21V6zm1 4v2h9v-2zm20 2h7.281L30 17.125V23h-1.156c-.446-1.719-1.992-3-3.844-3c-1.852 0-3.398 1.281-3.844 3H21zM2 14v2h6v-2zm7 8c1.117 0 2 .883 2 2s-.883 2-2 2s-2-.883-2-2s.883-2 2-2m16 0c1.117 0 2 .883 2 2s-.883 2-2 2s-2-.883-2-2s.883-2 2-2" />
                                                            </svg>
                                                            {{ __('Shipping') }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{ $sale->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
    </script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const payButtons = document.querySelectorAll('.pay-button');

            payButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const snapToken = button.getAttribute('data-snap-token');

                    snap.pay(snapToken, {
                        onSuccess: function(result) {
                            const paymentId = button.getAttribute('data-payment-id');
                            window.location.href = `/sale/payment/success/${paymentId}`;
                        },
                        onPending: function(result) {
                            document.getElementById('result-json').innerHTML += JSON
                                .stringify(result, null, 2);
                        },
                        onError: function(result) {
                            document.getElementById('result-json').innerHTML += JSON
                                .stringify(result, null, 2);
                        }
                    });
                });
            });
        });
    </script>
    <script>
        function resetFilters() {
            // Reset nilai-nilai input dari formulir
            if (document.getElementById('date')) {
                document.getElementById('date').value = '';
            }
            if (document.getElementById('Ref')) {
                document.getElementById('Ref').value = '';
            }
            if (document.getElementById('statut')) {
                document.getElementById('statut').value = '';
            }
            if (document.getElementById('payment_statut')) {
                document.getElementById('payment_statut').value = '';
            }
            if (document.getElementById('shipping_status')) {
                document.getElementById('shipping_status').value = '';
            }
            if (document.getElementById('warehouse_id')) {
                document.getElementById('warehouse_id').value = '';
            }
            if (document.getElementById('client_id')) {
                document.getElementById('client_id').value = '';
            }

            // Submit formulir secara otomatis untuk menghapus filter
            document.getElementById('filterForm').submit();
        }
    </script>
@endpush
