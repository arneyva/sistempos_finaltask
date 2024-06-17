@extends('templates.main')

@section('pages_title')
    <h1>
        Warehouse ~ Sales Return Reports</h1>
    <p>look up your daily report</p>
@endsection

<style>
    .status-completed {
        padding: 7px;
        border-radius: 7px;
        background-color: #c9f1c4;
        color: #0f4c11;
    }

    .status-ordered {
        padding: 7px;
        border-radius: 7px;
        background-color: #eff1c4;
        color: #4c4b0f;
    }

    .status-pending {
        padding: 7px;
        border-radius: 7px;
        background-color: #f1c4c4;
        color: #4c0f0f;
    }

    .payment-paid {
        padding: 7px;
        border-radius: 7px;
        background-color: #c4d9f1;
        color: #105e7f;
    }

    .payment-unpaid {
        padding: 7px;
        border-radius: 7px;
        background-color: #f0c4f1;
        color: #7f107b;
    }

    .payment-partial {
        padding: 7px;
        border-radius: 7px;
        background-color: #f1dcc4;
        color: #7f6710;
    }

    .shipping-shipped {
        padding: 7px;
        border-radius: 7px;
        background-color: #c4c8f1;
        color: #33107f;
    }

    .shipping-delivered {
        padding: 7px;
        border-radius: 7px;
        background-color: #c4f1d1;
        color: #107f2c;
    }

    .shipping-packed {
        padding: 7px;
        border-radius: 7px;
        background-color: #b19785;
        color: #583606;
    }

    .shipping-cancelled {
        padding: 7px;
        border-radius: 7px;
        background-color: #f1c4e1;
        color: #7f104f;
    }
</style>
@section('content')
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <div style="align-self:center;margin-top:20px;">
                        <form action="{{ route('reports.warehouse.sales-returns') }}" method="GET">
                            <select class="form-select" id="selectWarehouse" name="warehouse_id">
                                <option value="">All Warehouse/Outlet</option>
                                @foreach ($warehouses as $wh)
                                    <option value="{{ $wh->id }}"
                                        {{ request()->input('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->name }}
                                    </option>
                                @endforeach
                            </select>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="swiper-slide card card-slide">
                                    <div class="card-body">
                                        <div class="progress-widget">
                                            <div
                                                class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                <img src="{{ asset('hopeui/html/assets/images/sales.svg') }}" alt="purchase"
                                                    style="max-height: 70px;max-width: 70px">
                                            </div>
                                            <div class="progress-detail">
                                                <p class="mb-2">Sales</p>
                                                <h7 class="counter">{{ $data['sales'] }}</h7>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="swiper-slide card card-slide">
                                    <div class="card-body">
                                        <div class="progress-widget">
                                            <div
                                                class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                    alt="purchase" style="max-height: 70px;max-width: 70px">
                                            </div>
                                            <div class="progress-detail">
                                                <p class="mb-2">Purchases</p>
                                                <h7 class="counter">{{ $data['purchases'] }}</h7>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="swiper-slide card card-slide">
                                    <div class="card-body">
                                        <div class="progress-widget">
                                            <div
                                                class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                <img src="{{ asset('hopeui/html/assets/images/purchase-return.png') }}"
                                                    alt="purchase" style="max-height: 70px;max-width: 70px">
                                            </div>
                                            <div class="progress-detail">
                                                <p class="mb-2">Purchases Return</p>
                                                <h4 class="counter">{{ $data['ReturnPurchase'] }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="swiper-slide card card-slide">
                                    <div class="card-body">
                                        <div class="progress-widget">
                                            <div
                                                class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                <img src="{{ asset('hopeui/html/assets/images/sales-return.png') }}"
                                                    alt="purchase" style="max-height: 70px;max-width: 70px">
                                            </div>
                                            <div class="progress-detail">
                                                <p class="mb-2">Sales Return</p>
                                                <h7 class="counter">{{ $data['ReturnSale'] }}</h7>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--  --}}
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div class="card-body" style="background-color:bisque">
                                <ul class="d-flex nav nav-pills mb-0 text-center profile-tab" data-toggle="slider-tab"
                                    id="profile-pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active show" role="tab" aria-selected="false">Sales
                                            Return</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.warehouse.sales') }}" role="tab"
                                            aria-selected="false">Sales</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.warehouse.purchase') }}" role="tab"
                                            aria-selected="false">Purchase</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.warehouse.purchase-returns') }}"
                                            role="tab" aria-selected="false">Purchase Returns</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.warehouse.expenses') }}" role="tab"
                                            aria-selected="false">Expenses</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-12">
                                <div class="profile-content tab-content">
                                    <div id="profile-feed" class="tab-pane fade active show">
                                        <div class="card-header d-flex justify-content-between">
                                            <div class="input-group search-input">
                                                <span class="input-group-text d-inline" id="search-input">
                                                    <svg class="icon-18" width="18" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="11.7669" cy="11.7666" r="8.98856"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round"></circle>
                                                        <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </svg>
                                                </span>
                                                <input type="search" class="form-control" name="search"
                                                    value="{{ request()->input('search') }}" placeholder="Search...">
                                            </div>
                                            </form>
                                            <div class="header-title">
                                                <button type="button" class="btn btn-soft-danger">Excel</button>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive mt-4">
                                                <table id="basic-table" class="table table-striped mb-0" role="grid">
                                                    <thead>
                                                        <tr>
                                                            <th>Reference</th>
                                                            <th>Customer Name</th>
                                                            <th>Sale Ref</th>
                                                            <th>Warehouse</th>
                                                            <th>Grand Total</th>
                                                            <th>Paid</th>
                                                            <th>Due</th>
                                                            <th>Status</th>
                                                            <th>Payment</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($saleReturns_data as $item)
                                                            <tr>
                                                                <td>{{ $item['Ref'] }}</td>
                                                                <td>{{ $item['client_name'] }}</td>
                                                                <td>{{ $item['sale_ref'] }}</td>
                                                                <td>{{ $item['warehouse_name'] }}</td>
                                                                <td> {{ 'Rp ' . number_format($item['GrandTotal'], 2, ',', '.') }}
                                                                </td>
                                                                <td>{{ 'Rp ' . number_format($item['paid_amount'], 2, ',', '.') }}
                                                                </td>
                                                                <td>{{ 'Rp ' . number_format($item['due'], 2, ',', '.') }}
                                                                </td>
                                                                <td>
                                                                    @if ($item['statut'] == 'received')
                                                                        <span class="status-completed">received</span>
                                                                    @elseif($item['statut'] == 'ordered')
                                                                        <span class="status-ordered">ordered</span>
                                                                    @else
                                                                        <span class="status-pending">pending</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($item['payment_status'] == 'paid')
                                                                        <span class="payment-paid">paid</span>
                                                                    @else
                                                                        <span class="payment-unpaid">unpaid</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <div class="bd-example"
                                                    style="margin-left: 10px; margin-top:10px; margin-right:10px">
                                                    {{ $saleReturns->links() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // Mendapatkan elemen dropdown
        const selectWarehouse = document.getElementById('selectWarehouse');

        // Menambahkan event listener untuk perubahan nilai dropdown
        selectWarehouse.addEventListener('change', function() {
            // Menyubmit formulir secara otomatis saat nilai dropdown berubah
            this.form.submit();
        });
    </script>
@endpush
