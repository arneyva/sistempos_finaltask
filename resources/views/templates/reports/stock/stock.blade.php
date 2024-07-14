@extends('templates.main')
@section('pages_title')
    <h1>{{ __('Stock') }} ~ {{ __('Reports') }}</h1>
    <p>{{ __('look up your daily report') }}</p>
@endsection

<style>
    .warehousedeleted {
        padding: 7px;
        border-radius: 7px;
        background-color: #eff8ff;
        color: #377b9d;
    }

    .pdfstyle {
        padding: 7px;
        border-radius: 7px;
        background-color: #ffeff1;
        color: #9d3798;
    }
</style>
<style>
    .warehousedeleted {
        padding: 7px;
        border-radius: 7px;
        background-color: #eff8ff;
        color: #377b9d;
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
                    <h4 class="card-title">{{ __('Stock') }} {{ __('Reports') }}
                    </h4>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
                @role('superadmin|inventaris')
                    <div class="col-md-4 mb-3">
                        <form action="{{ route('reports.stock.index') }}" method="GET">
                            <select class="form-select" id="selectWarehouse" name="warehouse_id">
                                <option value="">{{ __('All Warehouse/Outlet') }}</option>
                                @foreach ($warehouses as $wh)
                                    <option value="{{ $wh->id }}"
                                        {{ request()->input('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->name }}
                                    </option>
                                @endforeach
                            </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="input-group search-input">
                            <span class="input-group-text d-inline" id="search-input">
                                <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"></circle>
                                    <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </span>
                            <input type="search" class="form-control" name="search" value="{{ request()->input('search') }}"
                                placeholder="{{ __('Search...') }}">
                        </div>
                    </div>
                    </form>
                @endrole
                @role('staff')
                    <div class="col-md-4 mb-3">
                        <form action="{{ route('reports.stock.index') }}" method="GET">
                            <div class="input-group search-input">
                                <span class="input-group-text d-inline" id="search-input">
                                    <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>
                                        <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </span>
                                <input type="search" class="form-control" name="search"
                                    value="{{ request()->input('search') }}" placeholder="{{ __('Search...') }}">
                            </div>
                        </form>
                    </div>
                @endrole
                <div class="header-title">
                    {{-- <button type="button" class="btn btn-soft-success">PDF</button> --}}
                    <button type="button" class="btn btn-soft-danger">Excel</button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Product Name') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Current Stock') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($report as $item)
                                <tr>
                                    <td>{{ $item['code'] }}</td>
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ $item['category'] }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td>
                                        <a href="{{ route('reports.stock.sales', $item['id']) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em"
                                                viewBox="0 0 32 32">
                                                <path fill="#546DEB" d="M15 20h2v4h-2zm5-2h2v6h-2zm-10-4h2v10h-2z" />
                                                <path fill="#546DEB"
                                                    d="M25 5h-3V4a2 2 0 0 0-2-2h-8a2 2 0 0 0-2 2v1H7a2 2 0 0 0-2 2v21a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2M12 4h8v4h-8Zm13 24H7V7h3v3h12V7h3Z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-right: 10px; margin-top:10px">
                        {{ $products->links() }}
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
