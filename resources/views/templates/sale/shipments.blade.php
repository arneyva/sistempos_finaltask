@extends('templates.main')

@section('pages_title')
    <h1>{{ __('All Shipments') }}</h1>
    <p>{{ __('Look All your shipments') }}</p>
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
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">{{ __('All Shipments') }}
                    </h4>
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary" data-bs-toggle="modal"
                        data-bs-target="#createModal">{{ __('Filter') }}</button>
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
                                    <form action="{{ route('sale.shipments') }}" method="GET" id="filterForm">
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
                                                <label class="form-label" for="warehouse_id">
                                                    {{ __('Warehouse/Outlet') }}</label>
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
                                            <label class="form-label" for="status">{{ __('Shipping Status') }}</label>
                                            <select class="form-select" id="status" name="status">
                                                <option selected disabled value="">{{ __('Choose...') }}</option>
                                                <option value="shipped"
                                                    {{ request()->input('status') == 'shipped' ? 'selected' : '' }}>
                                                    {{ __('Shipped') }}</option>
                                                <option value="delivered"
                                                    {{ request()->input('status') == 'delivered' ? 'selected' : '' }}>
                                                    {{ __('Delivered') }}
                                                </option>
                                                <option value="cancelled"
                                                    {{ request()->input('status') == 'cancelled' ? 'selected' : '' }}>
                                                    {{ __('Cancelled') }}
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
                    @role('dev')
                        <button type="button" class="btn btn-soft-success">PDF</button>
                    @endrole
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
                                <th>{{ __('Sale Reference') }}</th>
                                <th>{{ __('Delivered To') }}</th>
                                <th>{{ __('Warehouse/Outlet') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($shipments as $item)
                                <tr>
                                    <td>{{ $item->date }}</td>
                                    <td>{{ $item->Ref }}</td>
                                    <td>{{ $item->sale->Ref }}</td>
                                    <td>{{ $item->delivered_to }}</td>
                                    <td>{{ $item->sale->warehouse->name }}</td>
                                    <td>
                                        @if ($item->status == 'shipped')
                                            <span class="btn btn-outline-success btn-sm">{{ __('Shipped') }}</span>
                                        @elseif ($item->status == 'delivered')
                                            <span class="btn btn-outline-primary btn-sm">{{ __('Delivered') }}</span>
                                        @else
                                            <span class="btn btn-outline-danger btn-sm">{{ __('Cancelled') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{ $shipments->links() }}
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
            if (document.getElementById('date')) {
                document.getElementById('date').value = '';
            }
            if (document.getElementById('Ref')) {
                document.getElementById('Ref').value = '';
            }
            if (document.getElementById('status')) {
                document.getElementById('status').value = '';
            }
            if (document.getElementById('warehouse_id')) {
                document.getElementById('warehouse_id').value = '';
            }
            // Submit formulir secara otomatis untuk menghapus filter
            document.getElementById('filterForm').submit();
        }
    </script>
@endpush
