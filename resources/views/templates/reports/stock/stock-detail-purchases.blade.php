@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Stock') }} {{ __('Purchases') }} ~ {{ __('Reports') }}</h1>
    <p>{{ __('look up your daily reports') }}</p>
@endsection

@section('content')
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <h4 class="card-title" style="align-self:center;margin-top:20px;">{{ $product->name }}</h4>
                    <div class="card-body">
                        <div class="col-md-6 col-lg-12" style="margin-bottom: 30px">
                            <table class="table table-striped mb-0" role="grid">
                                <thead>
                                    <tr>
                                        <th>{{ __('Warehouse/Outlet') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($b as $c)
                                        <tr>
                                            <td>{{ $c['warehouse'] }}</td>
                                            <td>{{ $c['qty'] }} {{ $c['unit'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div class="card-body" style="background-color:bisque">
                                <ul class="d-flex nav nav-pills mb-0 text-center profile-tab" data-toggle="slider-tab"
                                    id="profile-pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active show" data-bs-toggle="tab" href="#profile-feed"
                                            role="tab" aria-selected="false">{{ __('Purchases') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.stock.sales-returns', $product->id) }}"
                                            role="tab" aria-selected="false">{{ __('Sales') }}
                                            {{ __('Return') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.stock.sales', $product->id) }}"
                                            role="tab" aria-selected="false">{{ __('Sales') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link"
                                            href="{{ route('reports.stock.purchases-returns', $product->id) }}"
                                            role="tab" aria-selected="false">{{ __('Purchases') }}
                                            {{ __('Return') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.stock.adjustment', $product->id) }}"
                                            role="tab" aria-selected="false">{{ __('Adjustment') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reports.stock.transfer', $product->id) }}"
                                            role="tab" aria-selected="false">{{ __('Transfer') }}</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-12">
                                <div class="profile-content tab-content">
                                    <div id="profile-feed" class="tab-pane fade active show">
                                        <div class="card-header d-flex justify-content-between">
                                            <form action="{{ route('reports.stock.purchases', $product->id) }}"
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
                                                <button type="button" class="btn btn-soft-danger">Excel</button>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive mt-4">
                                                <table id="basic-table" class="table table-striped mb-0" role="grid">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Date') }}</th>
                                                            <th>{{ __('Reference') }}</th>
                                                            <th>{{ __('Product Name') }}</th>
                                                            <th>{{ __('Supplier') }}</th>
                                                            <th>{{ __('Warehouse/Outlet') }}</th>
                                                            <th>{{ __('Quantity') }}</th>
                                                            <th>{{ __('SubTotal') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($purchases as $item)
                                                            <tr>
                                                                <td>{{ $item['date'] }}</td>
                                                                <td>{{ $item['Ref'] }}</td>
                                                                <td>{{ $item['product_name'] }}</td>
                                                                <td>{{ $item['provider_name'] }}</td>
                                                                <td>{{ $item['warehouse_name'] }}</td>
                                                                <td>{{ $item['quantity'] }}</td>
                                                                <td>{{ 'Rp ' . number_format($item['total'], 2, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <div class="bd-example"
                                                    style="margin-left: 10px; margin-top:10px; margin-right:10px">
                                                    {{ $purchase_details->links() }}
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
