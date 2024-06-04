@extends('templates.main')
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
                    <h4 class="card-title">Stock Report
                    </h4>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
                <div class="col-md-4 mb-3">
                    {{-- <label class="form-label" for="selectWarehouse">Warehouse/Outlet *</label> --}}
                    <select class="form-select" id="selectWarehouse" name="warehouse_id" required>
                        <option selected disabled value="">Warehouse/Outlet</option>
                        <option value="">Warehouse 1</option>
                        {{-- @foreach ($warehouse as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach --}}
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
                        <input type="search" class="form-control" placeholder="Search...">
                    </div>
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
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>70171027</td>
                                <td>Banana</td>
                                <td>Warehouse 1</td>
                                <td>19</td>
                                <td>
                                    <a href="#" class="warehousedeleted">Report
                                        {{-- <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M22.4541 11.3918C22.7819 11.7385 22.7819 12.2615 22.4541 12.6082C21.0124 14.1335 16.8768 18 12 18C7.12317 18 2.98759 14.1335 1.54586 12.6082C1.21811 12.2615 1.21811 11.7385 1.54586 11.3918C2.98759 9.86647 7.12317 6 12 6C16.8768 6 21.0124 9.86647 22.4541 11.3918Z"
                                                stroke="#130F26"></path>
                                            <circle cx="12" cy="12" r="5" stroke="#130F26"></circle>
                                            <circle cx="12" cy="12" r="3" fill="#130F26"></circle>
                                            <mask mask-type="alpha" maskUnits="userSpaceOnUse" x="9" y="9" width="6"
                                                height="6">
                                                <circle cx="12" cy="12" r="3" fill="#130F26"></circle>
                                            </mask>
                                            <circle opacity="0.89" cx="13.5" cy="10.5" r="1.5" fill="white">
                                            </circle>
                                        </svg> --}}
                                    </a>
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
