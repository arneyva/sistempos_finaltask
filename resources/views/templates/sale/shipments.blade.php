@extends('templates.main')

@section('pages_title')
    <h1>Shipments</h1>
    <p>Look All your shipments</p>
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
                    <h4 class="card-title">All Shipments
                    </h4>
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary" data-bs-toggle="modal"
                        data-bs-target="#createModal">Filter</button>
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
                                    <form action="{{ route('sale.shipments') }}" method="GET" id="filterForm">
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
                                                <label class="form-label" for="warehouse_id">Warehouse/Outlet
                                                    *</label>
                                                <select class="form-select" id="warehouse_id" name="warehouse_id">
                                                    <option selected disabled value="">Choose...</option>
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
                                            <label class="form-label" for="status">Shipping Status </label>
                                            <select class="form-select" id="status" name="status">
                                                <option selected disabled value="">Choose...</option>
                                                <option value="shipped"
                                                    {{ request()->input('status') == 'shipped' ? 'selected' : '' }}>
                                                    Shipped</option>
                                                <option value="delivered"
                                                    {{ request()->input('status') == 'delivered' ? 'selected' : '' }}>
                                                    Delivered
                                                </option>
                                                <option value="cancelled"
                                                    {{ request()->input('status') == 'cancelled' ? 'selected' : '' }}>
                                                    Cancelled
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
                    @role('dev')
                        <button type="button" class="btn btn-soft-success">PDF</button>
                        <button type="button" class="btn btn-soft-danger">Excel</button>
                    @endrole
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Shipment Ref</th>
                                <th>Sale Ref</th>
                                <th>Customer</th>
                                <th>Warehouse/Outlet</th>
                                <th>Status</th>
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
                                            <span class="btn btn-outline-success btn-sm">Shipped</span>
                                        @elseif ($item->status == 'delivered')
                                            <span class="btn btn-outline-primary btn-sm">Delivered</span>
                                        @else
                                            <span class="btn btn-outline-danger btn-sm">Cancelled</span>
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
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="">
                        <div class="col mb-3">
                            <label class="form-label" for="validationDefault01">Name *</label>
                            <input type="text" class="form-control" id="validationDefault01" required
                                placeholder="input product cost">
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="validationDefault01">Short Name*</label>
                            <input type="text" class="form-control" id="validationDefault01" required
                                placeholder="input product cost">
                        </div>
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h4 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Base Unit
                                    </button>
                                </h4>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <label for="validationCustomUsername" class="form-label">Product Unit</label>
                                        <select class="form-select" id="validationDefault04" required>
                                            <option selected disabled value="">Choose...</option>
                                            <option>Gram</option>
                                            <option>Liter</option>
                                            <option>Meter</option>
                                            <option>Gram</option>
                                        </select>
                                    </div>
                                    <div class="accordion-body">
                                        <label for="validationCustomUsername" class="form-label">Operator</label>
                                        <select class="form-select" id="validationDefault04" required>
                                            <option selected disabled value="">Choose...</option>
                                            <option>Multiply (*)</option>
                                            <option>Devide (/)</option>
                                        </select>
                                    </div>
                                    <div class="accordion-body">
                                        <label class="form-label" for="validationDefault01">Operation value *</label>
                                        <input type="text" class="form-control" id="validationDefault01" required
                                            placeholder="input product cost">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
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
