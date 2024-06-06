@extends('templates.main')

@section('pages_title')
    <h1>All Sales</h1>
    <p>Look All your sales</p>
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
                    <h4 class="card-title">All Sales
                    </h4>
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary">Filter</button>
                    <button type="button" class="btn btn-soft-success">PDF</button>
                    <button type="button" class="btn btn-soft-danger">Excel</button>
                    <a href="{{ route('sale.create') }}"><button type="button" class="btn btn-soft-primary">Create
                            +</button></a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped table-hover mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Added by</th>
                                <th>Customer</th>
                                <th>Warehouse/Outlet</th>
                                <th>Status</th>
                                <th>Grand Total</th>
                                <th>Payment Status</th>
                                {{-- <th>Shipping Status</th> --}}
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale as $item)
                                <tr>
                                    <th>{{ $item->created_at }}</th>
                                    <th>{{ $item->Ref }}</th>
                                    <th>{{ $item->user->username }}</th>
                                    <th>{{ $item->client->name }}</th>
                                    <th>{{ $item->warehouse->name }}</th>
                                    <th>{{ $item->statut }}</th>
                                    <th>{{ $item->GrandTotal }}</th>
                                    <th>{{ $item->payment_statut }}</th>
                                    <th>
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
                                        <div class="modal fade modal-lg" id="staticBackdrop{{ $item->id }}" data-bs-backdrop="static"
                                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="staticBackdropLabel">{{ $item->Ref }} Payment</h5>
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
                                                                            <th>Date</th>
                                                                            <th>Reference</th>
                                                                            <th>Montant</th>
                                                                            <th>Change</th>
                                                                            <th>Payment Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>{{ $item->paymentSales->date }}</td>
                                                                            <td>{{ $item->paymentSales->Ref }}</td>
                                                                            <td>{{ $item->paymentSales->montant }}</td>
                                                                            <td>{{ $item->paymentSales->change }}</td>
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
                                                            data-bs-dismiss="modal">Close</button>
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
                                                                    </svg> Paid
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
                                                                    </svg> Pay!
                                                                </div>
                                                            @endif
                                                        </li>
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
                                                                </svg> Invoice Pos </a>
                                                        </li>
                                                        <li class="iq-sub-card list-group-item"><a class="p-0"
                                                                href="#">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                    height="1.5em" viewBox="0 0 24 24">
                                                                    <path fill="currentColor"
                                                                        d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2zm-2 0l-8 5l-8-5zm0 12H4V8l8 5l8-5z" />
                                                                </svg> Email Notifications </a>
                                                        </li>
                                                        <li class="iq-sub-card list-group-item"><a class="p-0"
                                                                href="{{ route('sale.show', $item->id) }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                    height="1.5em" viewBox="0 0 24 24">
                                                                    <path fill="currentColor"
                                                                        d="M7 7h10v2H7zm0 4h7v2H7z" />
                                                                    <path fill="currentColor"
                                                                        d="M20 2H4c-1.103 0-2 .897-2 2v18l5.333-4H20c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2m0 14H6.667L4 18V4h16z" />
                                                                </svg> Sale Detail </a>
                                                        </li>
                                                        <li class="iq-sub-card list-group-item"><a class="p-0"
                                                                href="#">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                    height="1.5em" viewBox="0 0 24 24">
                                                                    <path fill="currentColor"
                                                                        d="M21 12a1 1 0 0 0-1 1v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h6a1 1 0 0 0 0-2H5a3 3 0 0 0-3 3v14a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-6a1 1 0 0 0-1-1m-15 .76V17a1 1 0 0 0 1 1h4.24a1 1 0 0 0 .71-.29l6.92-6.93L21.71 8a1 1 0 0 0 0-1.42l-4.24-4.29a1 1 0 0 0-1.42 0l-2.82 2.83l-6.94 6.93a1 1 0 0 0-.29.71m10.76-8.35l2.83 2.83l-1.42 1.42l-2.83-2.83ZM8 13.17l5.93-5.93l2.83 2.83L10.83 16H8Z" />
                                                                </svg> Edit Sale </a>
                                                        </li>
                                                        <li class="iq-sub-card list-group-item" data-bs-toggle="modal"
                                                            data-bs-target="#staticBackdrop{{ $item->id }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                height="1.5em" viewBox="0 0 256 256">
                                                                <path fill="#546DEB"
                                                                    d="M128 88a40 40 0 1 0 40 40a40 40 0 0 0-40-40m0 64a24 24 0 1 1 24-24a24 24 0 0 1-24 24m112-96H16a8 8 0 0 0-8 8v128a8 8 0 0 0 8 8h224a8 8 0 0 0 8-8V64a8 8 0 0 0-8-8m-46.35 128H62.35A56.78 56.78 0 0 0 24 145.65v-35.3A56.78 56.78 0 0 0 62.35 72h131.3A56.78 56.78 0 0 0 232 110.35v35.3A56.78 56.78 0 0 0 193.65 184M232 93.37A40.8 40.8 0 0 1 210.63 72H232ZM45.37 72A40.8 40.8 0 0 1 24 93.37V72ZM24 162.63A40.8 40.8 0 0 1 45.37 184H24ZM210.63 184A40.8 40.8 0 0 1 232 162.63V184Z" />
                                                            </svg> Show Payment
                                                        </li>
                                                        <li class="iq-sub-card list-group-item"><a class="p-0"
                                                                href="#">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                    height="1.5em" viewBox="0 0 32 32">
                                                                    <path fill="currentColor"
                                                                        d="M0 6v2h19v15h-6.156c-.446-1.719-1.992-3-3.844-3c-1.852 0-3.398 1.281-3.844 3H4v-5H2v7h3.156c.446 1.719 1.992 3 3.844 3c1.852 0 3.398-1.281 3.844-3h8.312c.446 1.719 1.992 3 3.844 3c1.852 0 3.398-1.281 3.844-3H32v-8.156l-.063-.157l-2-6L29.72 10H21V6zm1 4v2h9v-2zm20 2h7.281L30 17.125V23h-1.156c-.446-1.719-1.992-3-3.844-3c-1.852 0-3.398 1.281-3.844 3H21zM2 14v2h6v-2zm7 8c1.117 0 2 .883 2 2s-.883 2-2 2s-2-.883-2-2s.883-2 2-2m16 0c1.117 0 2 .883 2 2s-.883 2-2 2s-2-.883-2-2s.883-2 2-2" />
                                                                </svg> Shipping </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{-- {{ $sales->links() }} --}}
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
@endpush
