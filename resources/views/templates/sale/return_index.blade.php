@extends('templates.main')

@section('pages_title')
    <h1>
        {{ __('All Sales Return') }}</h1>
    <p>{{ __('Look All your sales return') }}</p>
@endsection

<style>
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
                    <h4 class="card-title">{{ __('All Sales Return') }}
                    </h4>
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary" data-bs-toggle="modal"
                        data-bs-target="#createModal">{{ __('Filter') }}</button>
                    @role('superadmin|inventaris')
                        <a href="{{ route('sale.return.export', request()->query()) }}" class="btn btn-soft-danger">Excel</a>
                    @endrole
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
                                    <form action="{{ route('sale.return.index') }}" method="GET" id="filterForm">
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
                                                    *</label>
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
                                            <label class="form-label" for="client_id">
                                                {{ __('Customer') }}</label>
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
                                                <option value="received"
                                                    {{ request()->input('statut') == 'received' ? 'selected' : '' }}>
                                                    {{ __('Received') }}</option>
                                                <option value="pending"
                                                    {{ request()->input('statut') == 'pending' ? 'selected' : '' }}>
                                                    {{ __('Pending') }}
                                                </option>
                                            </select>
                                        </div>
                                        @role('dev')
                                            <div class="col mb-3">
                                                <label class="form-label" for="payment_statut">Payment Status *</label>
                                                <select class="form-select" id="payment_statut" name="payment_statut">
                                                    <option selected disabled value="">Choose...</option>
                                                    <option value="paid"
                                                        {{ request()->input('payment_statut') == 'paid' ? 'selected' : '' }}>
                                                        Paid</option>
                                                    <option value="unpaid"
                                                        {{ request()->input('payment_statut') == 'unpaid' ? 'selected' : '' }}>
                                                        Unpaid
                                                    </option>
                                                </select>
                                            </div>
                                        @endrole
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
                                <th>{{ __('Sale Reference') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Warehouse/Outlet') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Grand Total') }}</th>
                                <th>{{ __('Payment Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($salereturn as $item)
                                <tr>
                                    <td>{{ $item->date }}</td>
                                    <td>{{ $item->Ref }}</td>
                                    <td>{{ $item->sale->Ref ?? '?' }}</td>
                                    <td>{{ $item->client->name }}</td>
                                    <td>{{ $item->warehouse->name }}</td>
                                    <td>
                                        @if ($item->statut == 'received')
                                            <span class="status-completed">{{ __('Received') }}</span>
                                        @else
                                            <span class="status-ordered">{{ __('Pending') }}</span>
                                        @endif
                                    </td>
                                    <td>Rp. {{ number_format($item->GrandTotal, 2, ',', '.') }}</td>
                                    <td>
                                        @if ($item->payment_statut == 'paid')
                                            <span class="payment-paid">{{ __('Paid') }}</span>
                                        @else
                                            <span class="payment-unpaid">{{ __('Unpaid') }}</span>
                                        @endif
                                    </td>
                                    <td> <svg xmlns="http://www.w3.org/2000/svg" width="4em" height="4em"
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
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>{{ $item->facture->date }}</td>
                                                                            <td>{{ $item->facture->Ref }}</td>
                                                                            <td>{{ 'Rp ' . number_format($item->facture->montant, 2, ',', '.') }}
                                                                            </td>
                                                                            <td>{{ 'Rp ' . number_format($item->facture->change, 2, ',', '.') }}
                                                                            </td>
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
                                        <div class="p-0 sub-drop dropdown-menu dropdown-menu-end"
                                            aria-labelledby="dropdownMenuButton{{ $item->id }}"
                                            style="  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);">
                                            <div class="m-0 border-0 shadow-none card">
                                                <div class="p-0 ">
                                                    <ul class="p-0 list-group list-group-flush">
                                                        @role('dev')
                                                            <li class="iq-sub-card list-group-item"><a class="p-0"
                                                                    href="#">
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
                                                                    </svg> Dwonload Pdf </a>
                                                            </li>

                                                            <li class="iq-sub-card list-group-item"><a class="p-0"
                                                                    href="#">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                        height="1.5em" viewBox="0 0 24 24">
                                                                        <path fill="currentColor"
                                                                            d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2zm-2 0l-8 5l-8-5zm0 12H4V8l8 5l8-5z" />
                                                                    </svg> {{ __('Email Notifications') }} </a>
                                                            </li>
                                                        @endrole
                                                        <li class="iq-sub-card list-group-item"><a class="p-0"
                                                                href="{{ route('sale.return.show', $item->id) }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                    height="1.5em" viewBox="0 0 24 24">
                                                                    <path fill="currentColor"
                                                                        d="M7 7h10v2H7zm0 4h7v2H7z" />
                                                                    <path fill="currentColor"
                                                                        d="M20 2H4c-1.103 0-2 .897-2 2v18l5.333-4H20c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2m0 14H6.667L4 18V4h16z" />
                                                                </svg> {{ __('Return Detail') }} </a>
                                                        </li>
                                                        @role('underdev')
                                                            @if ($item->payment_statut == 'paid' && $item->statut == 'received')
                                                                <li class="iq-sub-card list-group-item">
                                                                    <a class="p-0 text-danger" href="javascript:void(0)"
                                                                        style="pointer-events: none;">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                            height="1.5em" viewBox="0 0 24 24"
                                                                            fill="red">
                                                                            <path fill="currentColor"
                                                                                d="M21 12a1 1 0 0 0-1 1v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h6a1 1 0 0 0 0-2H5a3 3 0 0 0-3 3v14a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-6a1 1 0 0 0-1-1m-15 .76V17a1 1 0 0 0 1 1h4.24a1 1 0 0 0 .71-.29l6.92-6.93L21.71 8a1 1 0 0 0 0-1.42l-4.24-4.29a1 1 0 0 0-1.42 0l-2.82 2.83l-6.94 6.93a1 1 0 0 0-.29.71m10.76-8.35l2.83 2.83l-1.42 1.42l-2.83-2.83ZM8 13.17l5.93-5.93l2.83 2.83L10.83 16H8Z" />
                                                                        </svg> {{ __('Edit Return') }}
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li class="iq-sub-card list-group-item">
                                                                    <a class="p-0" href="#">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                            height="1.5em" viewBox="0 0 24 24">
                                                                            <path fill="currentColor"
                                                                                d="M21 12a1 1 0 0 0-1 1v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h6a1 1 0 0 0 0-2H5a3 3 0 0 0-3 3v14a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-6a1 1 0 0 0-1-1m-15 .76V17a1 1 0 0 0 1 1h4.24a1 1 0 0 0 .71-.29l6.92-6.93L21.71 8a1 1 0 0 0 0-1.42l-4.24-4.29a1 1 0 0 0-1.42 0l-2.82 2.83l-6.94 6.93a1 1 0 0 0-.29.71m10.76-8.35l2.83 2.83l-1.42 1.42l-2.83-2.83ZM8 13.17l5.93-5.93l2.83 2.83L10.83 16H8Z" />
                                                                        </svg> {{ __('Edit Return') }}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endrole
                                                        <li class="iq-sub-card list-group-item" data-bs-toggle="modal"
                                                            data-bs-target="#staticBackdrop{{ $item->id }}"
                                                            style="color: #546DEB;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                height="1.5em" viewBox="0 0 256 256">
                                                                <path fill="currentColor"
                                                                    d="M128 88a40 40 0 1 0 40 40a40 40 0 0 0-40-40m0 64a24 24 0 1 1 24-24a24 24 0 0 1-24 24m112-96H16a8 8 0 0 0-8 8v128a8 8 0 0 0 8 8h224a8 8 0 0 0 8-8V64a8 8 0 0 0-8-8m-46.35 128H62.35A56.78 56.78 0 0 0 24 145.65v-35.3A56.78 56.78 0 0 0 62.35 72h131.3A56.78 56.78 0 0 0 232 110.35v35.3A56.78 56.78 0 0 0 193.65 184M232 93.37A40.8 40.8 0 0 1 210.63 72H232ZM45.37 72A40.8 40.8 0 0 1 24 93.37V72ZM24 162.63A40.8 40.8 0 0 1 45.37 184H24ZM210.63 184A40.8 40.8 0 0 1 232 162.63V184Z" />
                                                            </svg> {{ __('Show Payment') }}
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
                        {{ $salereturn->links() }}
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
