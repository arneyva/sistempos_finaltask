@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Profit & Loss') }} ~ {{ __('Reports') }}</h1>
    <p>{{ __('look up your daily reports') }}</p>
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
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">{{ __('Profit & Loss') }} {{ __('Reports') }}</h4>
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary" data-bs-toggle="modal"
                        data-bs-target="#createModal">{{ __('Filter') }}</button>
                    {{-- <a href="{{ route('sale.export', request()->query()) }}" class="btn btn-soft-danger">Excel</a> --}}
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
                                    <form action="{{ route('reports.profit-loss') }}" method="GET" id="filterForm">
                                        <div class="col mb-3">
                                            <label class="form-label" for="from_date">{{ __('From Date') }}</label>
                                            <input type="date" class="form-control" id="from_date" name="from"
                                                value="{{ request()->input('from', '2024-02-12') }}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="to_date">{{ __('To Date') }}</label>
                                            <input type="date" class="form-control" id="to_date" name="to"
                                                value="{{ request()->input('to', now()->format('Y-m-d')) }}">
                                        </div>
                                        @role('superadmin|inventaris')
                                            <div class="col mb-3">
                                                <label class="form-label"
                                                    for="warehouse_id">{{ __('Warehouse/Outlet') }}</label>
                                                <select class="form-select" id="warehouse_id" name="warehouse_id">
                                                    <option value="">{{ __('All Warehouse/Outlet') }}</option>
                                                    @foreach ($warehouses as $wh)
                                                        <option value="{{ $wh->id }}"
                                                            {{ request()->input('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                                            {{ $wh->name }}
                                                        </option>
                                                    @endforeach
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
                {{-- <script>
                    function resetFilters() {
                        document.getElementById('filterForm').reset();
                    }
                </script> --}}

            </div>
            <div class="card-header d-flex justify-content-between">
                <div class="card" style="background-color: #fbf9f6">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/sales.svg') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">{{ $data['sales_count'] }} {{ __('Sales') }}
                                                        </p>
                                                        <h4 class="counter">{{ $data['sales_sum'] }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @role('superadmin|inventaris')
                                        <div class="col-md-4 mb-3">
                                            <div class="swiper-slide card card-slide">
                                                <div class="card-body">
                                                    <div class="progress-widget">
                                                        <div
                                                            class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                            <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                                alt="purchase" style="max-height: 70px;max-width: 70px">
                                                        </div>
                                                        <div class="progress-detail">
                                                            <p class="mb-2">{{ $data['purchases_count'] }}
                                                                {{ __('Purchases') }}</p>
                                                            <h4 class="counter">{{ $data['purchases_sum'] }}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endrole
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/sales-return.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">{{ $data['returns_sales_count'] }}
                                                            {{ __('Sales') }} {{ __('Return') }}
                                                        </p>
                                                        <h4 class="counter">{{ $data['returns_sales_sum'] }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @role('superadmin|inventaris')
                                        <div class="col-md-4 mb-3">
                                            <div class="swiper-slide card card-slide">
                                                <div class="card-body">
                                                    <div class="progress-widget">
                                                        <div
                                                            class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                            <img src="{{ asset('hopeui/html/assets/images/purchase-return.png') }}"
                                                                alt="purchase" style="max-height: 70px;max-width: 70px">
                                                        </div>
                                                        <div class="progress-detail">
                                                            <p class="mb-2">{{ $data['returns_purchases_count'] }}
                                                                {{ __('Purchases') }} {{ __('Return') }}</p>
                                                            <h4 class="counter">{{ $data['returns_purchases_sum'] }}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endrole
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">{{ $data['expenses_count'] }}
                                                            {{ __('Expenses') }}</p>
                                                        <h4 class="counter">{{ $data['expenses_sum'] }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">{{ __('Total Revenue') }}</p>
                                                        <h4 class="counter">{{ $data['total_revenue'] }}</h4>
                                                        <p class="counter">{{ __('Sales') }} - {{ __('Sales') }}
                                                            {{ __('Return') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">{{ __('Payments') }} {{ __('Received') }}</p>
                                                        <h4 class="counter">{{ $data['payment_received'] }}</h4>
                                                        <p class="counter">{{ __('Sales') }} + {{ __('Purchases') }} {{ __('Return')  }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">{{ __('Payments') }} {{ __('Sent') }}</p>
                                                        <h4 class="counter">{{ $data['payment_sent'] }}</h4>
                                                        <p class="counter">{{ __('Expenses') }} + {{ __('Purchases') }} + {{ __('Sales') }} {{ __('Return')  }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">{{ __('Payments Net') }}</p>
                                                        <h4 class="counter">{{ $data['paiement_net'] }}</h4>
                                                        <hp class="counter">{{ __('Payments') }} {{ __('Received')  }} - {{ __('Payments') }} {{ __('Sent') }}</hp>
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
    </div>
@endsection
@push('script')
    <script>
        function resetFilters() {
            // Reset nilai-nilai input dari formulir
            document.getElementById('form_date').value = '';
            document.getElementById('to_date').value = '';
            document.getElementById('category_id').value = '';
            document.getElementById('warehouse_id').value = '';

            // Submit formulir secara otomatis untuk menghapus filter
            document.getElementById('filterForm').submit();
        }
    </script>
@endpush
