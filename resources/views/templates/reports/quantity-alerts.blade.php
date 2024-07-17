@extends('templates.main')
<style>
    .warehousedeleted {
        padding: 7px;
        border-radius: 7px;
        background-color: #ffefef;
        color: #F24D4D;
    }
</style>
@section('pages_title')
    <h1>
        {{ __('Product Quantity Alerts') }}</h1>
    <p>{{ __('look up your daily reports') }}</p>
@endsection
@section('content')
    <div class="col-sm-12">
        <div class="mt-3" style="justify-content-center">
            <!-- @include('templates.alert') -->
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">{{ __('Product Quantity Alerts') }}
                    </h4>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
                @role('superadmin|inventaris')
                    <div class="col-md-6 mb-3">
                        <form action="{{ route('reports.quantity-alerts') }}" method="GET">
                            <select class="form-select" id="selectWarehouse" name="warehouse_id">
                                <option value="">{{ __('All Warehouse/Outlet') }}</option>
                                @foreach ($warehouses as $wh)
                                    <option value="{{ $wh->id }}"
                                        {{ request()->input('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div class="header-title">
                        <a href="{{ route('reports.export-quantity-alerts', request()->query()) }}"
                            class="btn btn-soft-danger">Excel</a>
                    </div>
                @endrole
            </div>
            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Product Name') }}</th>
                                <th>{{ __('Warehouse/Outlet') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Stock Alert') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stockalert as $item)
                                <tr>
                                    @if ($item->product_variant_id == null)
                                        <td>{{ $item->code }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->warehouse->name }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>
                                            <span class="warehousedeleted">{{ $item->stock_alert }}</span>
                                        </td>
                                    @else
                                        <td>{{ $item->productVariant->code }}</td>
                                        <td>{{ $item->name }} ~ {{ $item->productVariant->name }} </td>
                                        <td>{{ $item->warehouse->name }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>
                                            <span class="warehousedeleted">{{ $item->stock_alert }}</span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-right: 10px; margin-top:10px">
                        {{ $stockalert->links() }}
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
