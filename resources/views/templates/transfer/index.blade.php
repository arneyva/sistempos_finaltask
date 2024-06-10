@extends('templates.main')

@section('pages_title')
    <h1>All Transfer</h1>
    <p>Look All your transfer</p>
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
                    <h4 class="card-title">All Transfers
                    </h4>
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary" data-bs-toggle="modal"
                        data-bs-target="#createModal">Filter</button>
                    <button type="button" class="btn btn-soft-success">PDF</button>
                    <button type="button" class="btn btn-soft-danger">Excel</button>
                    <a href="{{ route('transfer.create') }}"><button type="button" class="btn btn-soft-primary">Create
                            +</button></a>
                    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createModalLabel">Filter</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('transfer.index') }}" method="GET" id="filterForm">
                                        <div class="col mb-3">
                                            <label class="form-label" for="date">Date *</label>
                                            <input type="date" class="form-control" id="date" name="date"
                                                value="{{ request()->input('date') }}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="Ref">Reference*</label>
                                            <input type="text" class="form-control" id="Ref" name="Ref"
                                                value="{{ request()->input('Ref') }}" placeholder="Input Ref ...">
                                        </div>
                                        @role('superadmin|inventaris')
                                        <div class="col mb-3">
                                            <label class="form-label" for="from_warehouse_id">From Warehouse/Outlet
                                                *</label>
                                            <select class="form-select" id="from_warehouse_id" name="from_warehouse_id">
                                                <option selected disabled value="">Choose...</option>
                                                @foreach ($warehouse as $wh)
                                                    <option value="{{ $wh->id }}"
                                                        {{ request()->input('from_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                                        {{ $wh->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="to_warehouse_id">To Warehouse/Outlet *</label>
                                            <select class="form-select" id="to_warehouse_id" name="to_warehouse_id">
                                                <option selected disabled value="">Choose...</option>
                                                @foreach ($warehouse as $wh)
                                                    <option value="{{ $wh->id }}"
                                                        {{ request()->input('to_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                                        {{ $wh->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endrole
                                        <div class="col mb-3">
                                            <label class="form-label" for="statut">Status *</label>
                                            <select class="form-select" id="statut" name="statut">
                                                <option selected disabled value="">Choose...</option>
                                                <option value="completed"
                                                    {{ request()->input('statut') == 'completed' ? 'selected' : '' }}>
                                                    Completed</option>
                                                <option value="sent"
                                                    {{ request()->input('statut') == 'sent' ? 'selected' : '' }}>Sent
                                                </option>
                                            </select>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" onclick="resetFilters()"
                                        data-bs-dismiss="modal">Reset</button>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>From Warehouse/Outlet</th>
                                <th>To Warehouse/Outlet</th>
                                <th>Total Products</th>
                                <th>Grand Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transfer as $item)
                                <tr>
                                    <td>{{ $item['date'] }}</td>
                                    <td>{{ $item['Ref'] }}</td>
                                    <td>{{ $item['from_warehouse']['name'] }}</td>
                                    <td>{{ $item['to_warehouse']['name'] }}</td>
                                    <td>{{ $item['items'] }} Items</td>
                                    <td>Rp {{ $item['GrandTotal'] }}</td>
                                    <td>{{ $item['statut'] }}</td>
                                    <td>
                                        <a href="{{ route('transfer.edit', $item['id']) }}"><button type="button"
                                                class="btn btn-soft-primary">Edit</button></a>
                                        <a href="{{ route('transfer.destroy', $item['id']) }}"><button type="button"
                                                class="btn btn-soft-danger">Delete</button></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{ $transfer->links() }}
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
            document.getElementById('date').value = '';
            document.getElementById('Ref').value = '';
            document.getElementById('statut').value = '';
            document.getElementById('from_warehouse_id').value = '';
            document.getElementById('to_warehouse_id').value = '';

            // Submit formulir secara otomatis untuk menghapus filter
            document.getElementById('filterForm').submit();
        }
    </script>
@endpush
