@extends('templates.main')
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
            @include('templates.alert')
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Product Quantity Alerts
                    </h4>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="selectWarehouse">Warehouse/Outlet *</label>
                    <select class="form-select" id="selectWarehouse" name="warehouse_id" required>
                        <option selected disabled value="">Choose...</option>
                        <option value="">Warehouse 1</option>
                        {{-- @foreach ($warehouse as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach --}}
                    </select>
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-success">PDF</button>
                    <button type="button" class="btn btn-soft-danger">Excel</button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Product</th>
                                <th>Warehouse</th>
                                <th>Quantity</th>
                                <th>Alert Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>70171027</td>
                                <td>Banana</td>
                                <td>Warehouse 1</td>
                                <td>19</td>
                                <td>
                                    <span class="warehousedeleted">10</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-right: 10px; margin-top:10px">
                        {{-- {{ $brands->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.querySelector('.search-input input');
            const rows = document.querySelectorAll('#basic-table tbody tr');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.trim().toLowerCase();

                rows.forEach(row => {
                    const nameColumn = row.querySelector('td:first-child').textContent.trim()
                        .toLowerCase();


                    if (nameColumn.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endpush
