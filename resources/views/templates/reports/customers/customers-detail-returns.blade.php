@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Customer') }} ~ {{ __('Sales') }} {{ __('Return') }} {{ __('Reports') }}</h1>
    <p>{{ __('look up your daily reports') }}</p>
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
                    <h4 class="card-title" style="align-self:center;margin-top:20px;">{{ $client->name }}</h4>
                    <div class="card-body">
                        <div class="row">
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
                                                <p class="mb-2">{{ __('Sales') }}</p>
                                                <h7 class="counter">{{ $client_data['total_sales'] }}</h7>
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
                                                <p class="mb-2">{{ __('Total') }} {{ __('Amount') }}</p>
                                                <h7 class="counter">
                                                    {{ 'Rp ' . number_format($client_data['total_amount'], 2, ',', '.') }}
                                                </h7>
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
                                                <p class="mb-2">{{ __('Total') }} {{ __('Paid') }}</p>
                                                <h7 class="counter">
                                                    {{ 'Rp ' . number_format($client_data['total_paid'], 2, ',', '.') }}
                                                </h7>
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
                                                <p class="mb-2">{{ __('Total') }} {{ __('Due') }}</p>
                                                <h7 class="counter">
                                                    {{ 'Rp ' . number_format($client_data['due'], 2, ',', '.') }}</h7>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div class="card-body" style="background-color:bisque">
                                <ul class="d-flex nav nav-pills mb-0 text-center profile-tab" data-toggle="slider-tab"
                                    id="profile-pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active show" role="tab"
                                            aria-selected="false">{{ __('Sales') }}
                                            {{ __('Return') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.customers.sales', $client->id) }}"
                                            role="tab" aria-selected="false">{{ __('Sales') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.customers.payments', $client->id) }}"
                                            role="tab" aria-selected="false">{{ __('Payments') }}</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-12">
                                <div class="profile-content tab-content">
                                    <div id="profile-friends" class="tab-pane fade active show">
                                        <div class="card-header d-flex justify-content-between">
                                            <form action="{{ route('reports.customers.returns', $client->id) }}"
                                                method="GET">
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
                                                        value="{{ request()->input('search') }}"
                                                        placeholder="{{ __('Search...') }}">
                                                </div>
                                            </form>
                                            <div class="header-title">
                                                <a href="{{ route('reports.customers.returns-export', ['search' => request('search'), 'id' => $client->id]) }}"
                                                    class="btn btn-soft-danger">Excel</a>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive mt-4">
                                                <table id="basic-table" class="table table-striped mb-0" role="grid">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Reference') }}</th>
                                                            <th>{{ __('Customer') }}</th>
                                                            <th>{{ __('Sales') }} {{ __('Reference') }}</th>
                                                            <th>{{ __('Warehouse/Outlet') }}</th>
                                                            <th>{{ __('Grand Total') }}</th>
                                                            <th>{{ __('Paid') }}</th>
                                                            <th>{{ __('Due') }}</th>
                                                            <th>{{ __('Status') }}</th>
                                                            <th>{{ __('Payment') }} {{ __('Status') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($returns_customer as $item)
                                                            <tr>
                                                                <td>{{ $item['Ref'] }}</td>
                                                                <td>{{ $item['client_name'] }}</td>
                                                                <td>{{ $item['sale_ref'] }}</td>
                                                                <td>{{ $item['warehouse_name'] }}</td>
                                                                <td>{{ 'Rp ' . number_format($item['GrandTotal'], 2, ',', '.') }}
                                                                </td>
                                                                <td>{{ 'Rp ' . number_format($item['paid_amount'], 2, ',', '.') }}
                                                                </td>
                                                                <td>{{ 'Rp ' . number_format($item['due'], 2, ',', '.') }}
                                                                </td>
                                                                <td>
                                                                    @if ($item['statut'] == 'received')
                                                                        <span
                                                                            class="status-completed">{{ __('Received') }}</span>
                                                                    @else
                                                                        <span
                                                                            class="status-pending">{{ __('Pending') }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($item['payment_status'] == 'paid')
                                                                        <span
                                                                            class="payment-paid">{{ __('Paid') }}</span>
                                                                    @else
                                                                        <span
                                                                            class="payment-unpaid">{{ __('Unpaid') }}</span>
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
@endpush
