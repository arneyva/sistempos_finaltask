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
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">All Adjustments
                    </h4>
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary">Filter</button>
                    <button type="button" class="btn btn-soft-success">PDF</button>
                    <button type="button" class="btn btn-soft-danger">Excel</button>
                    <button type="button" class="btn btn-soft-gray">Import Product</button>
                    {{-- <button type="button" class="btn btn-soft-primary" data-bs-toggle="modal"
                        data-bs-target="#exampleModal">
                        Create+
                    </button> --}}
                    <a href="{{ route('adjustment.create') }}"><button type="button" class="btn btn-soft-primary">Create
                            +</button></a>
                    {{-- <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Creataae</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
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
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                        aria-expanded="true" aria-controls="collapseOne">
                                                        Base Unit
                                                    </button>
                                                </h4>
                                                <div id="collapseOne" class="accordion-collapse collapse show"
                                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <label for="validationCustomUsername" class="form-label">Product
                                                            Unit</label>
                                                        <select class="form-select" id="validationDefault04" required>
                                                            <option selected disabled value="">Choose...</option>
                                                            <option>Gram</option>
                                                            <option>Liter</option>
                                                            <option>Meter</option>
                                                            <option>Gram</option>
                                                        </select>
                                                    </div>
                                                    <div class="accordion-body">
                                                        <label for="validationCustomUsername"
                                                            class="form-label">Operator</label>
                                                        <select class="form-select" id="validationDefault04" required>
                                                            <option selected disabled value="">Choose...</option>
                                                            <option>Multiply (*)</option>
                                                            <option>Devide (/)</option>
                                                        </select>
                                                    </div>
                                                    <div class="accordion-body">
                                                        <label class="form-label" for="validationDefault01">Operation value
                                                            *</label>
                                                        <input type="text" class="form-control" id="validationDefault01"
                                                            required placeholder="input product cost">
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
                    </div> --}}
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Warehouse/Outlet</th>
                                <th>Total Products</th>

                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>
                                        {{ $item['date'] }}
                                    </td>
                                    <td>
                                        {{ $item['Ref'] }}
                                    </td>
                                    <td>
                                        {{-- {{ $item['warehouse'] }} --}}
                                        @if ($item['warehouse'] === 'deleted')
                                            <span class="warehousedeleted">warehouse deleted</span>
                                        @else
                                            {{ $item['warehouse'] }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item['items'] }} Items
                                    </td>
                                    <td>
                                        <div class="inline">
                                            <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg" data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $item['id'] }}">
                                                <path
                                                    d="M22.4541 11.3918C22.7819 11.7385 22.7819 12.2615 22.4541 12.6082C21.0124 14.1335 16.8768 18 12 18C7.12317 18 2.98759 14.1335 1.54586 12.6082C1.21811 12.2615 1.21811 11.7385 1.54586 11.3918C2.98759 9.86647 7.12317 6 12 6C16.8768 6 21.0124 9.86647 22.4541 11.3918Z"
                                                    stroke="#130F26"></path>
                                                <circle cx="12" cy="12" r="5" stroke="#130F26">
                                                </circle>
                                                <circle cx="12" cy="12" r="3" fill="#130F26"></circle>
                                                <mask mask-type="alpha" maskUnits="userSpaceOnUse" x="9" y="9"
                                                    width="6" height="6">
                                                    <circle cx="12" cy="12" r="3" fill="#130F26">
                                                    </circle>
                                                </mask>
                                                <circle opacity="0.89" cx="13.5" cy="10.5" r="1.5" fill="white">
                                                </circle>
                                            </svg>
                                            <div class="modal fade" id="detailModal{{ $item['id'] }}" tabindex="-1"
                                                role="dialog" aria-labelledby="showDetailsLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="showDetailsLabel">
                                                                Adjustment Detail</h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-lg-5 col-md-12 col-sm-12 mt-3">
                                                                    <table
                                                                        class="table table-hover table-bordered table-sm">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>{{ __('Date') }}</td>
                                                                                <th>{{ $item['date'] }}</th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>{{ __('Reference') }}</td>
                                                                                <th>{{ $item['Ref'] }}</th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>{{ __('Warehouse') }}</td>
                                                                                <th>{{ $item['warehouse'] }}</th>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="col-lg-7 col-md-12 col-sm-12 mt-3">
                                                                    <div class="table-responsive">
                                                                        <table
                                                                            class="table table-hover table-bordered table-sm">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th scope="col">
                                                                                        {{ __('ProductName') }}</th>
                                                                                    <th scope="col">
                                                                                        {{ __('CodeProduct') }}</th>
                                                                                    <th scope="col">
                                                                                        {{ __('Quantity') }}</th>
                                                                                    <th scope="col">{{ __('Type') }}
                                                                                    </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach ($item['details_product'] as $key => $product)
                                                                                    <tr>
                                                                                        {{-- <td>{{ $product }}</td> --}}
                                                                                        <td>
                                                                                            @if ($item['details_product_variant'][$key])
                                                                                                {{ $item['details_product'][$key] . ' - ' . $item['details_product_variant'][$key] }}
                                                                                            @else
                                                                                                {{ $item['details_product'][$key] }}
                                                                                            @endif
                                                                                        </td>
                                                                                        <td>
                                                                                            @if ($item['details_code_variant'][$key])
                                                                                                {{ $item['details_code'][$key] . ' - ' . $item['details_code_variant'][$key] }}
                                                                                            @else
                                                                                                {{ $item['details_code'][$key] }}
                                                                                            @endif
                                                                                        </td>
                                                                                        <td>{{ $item['details_quantity'][$key] }}
                                                                                        </td>
                                                                                        <td>
                                                                                            @if ($item['details_type'][$key] == 'add')
                                                                                                {{ __('Addition') }}
                                                                                            @elseif($item['details_type'][$key] == 'sub')
                                                                                                {{ __('Subtraction') }}
                                                                                            @endif
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            {{-- @if ($adjustment->note)
                                                                <div class="row mt-4">
                                                                    <div class="col-md-12">
                                                                        <p>{{ $adjustment->note }}</p>
                                                                    </div>
                                                                </div>
                                                            @endif --}}
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            {{--  --}}
                                            <a href="edit.html">
                                                <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M13.7476 20.4428H21.0002" stroke="currentColor"
                                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    </path>
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M12.78 3.79479C13.5557 2.86779 14.95 2.73186 15.8962 3.49173C15.9485 3.53296 17.6295 4.83879 17.6295 4.83879C18.669 5.46719 18.992 6.80311 18.3494 7.82259C18.3153 7.87718 8.81195 19.7645 8.81195 19.7645C8.49578 20.1589 8.01583 20.3918 7.50291 20.3973L3.86353 20.443L3.04353 16.9723C2.92866 16.4843 3.04353 15.9718 3.3597 15.5773L12.78 3.79479Z"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path d="M11.021 6.00098L16.4732 10.1881" stroke="currentColor"
                                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    </path>
                                                </svg>
                                            </a>
                                            <a href="hapus.html">
                                                <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M14.737 2.76196H7.979C5.919 2.76196 4.25 4.43196 4.25 6.49096V17.34C4.262 19.439 5.973 21.13 8.072 21.117C8.112 21.117 8.151 21.116 8.19 21.115H16.073C18.141 21.094 19.806 19.409 19.802 17.34V8.03996L14.737 2.76196Z"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path
                                                        d="M14.4736 2.75024V5.65924C14.4736 7.07924 15.6216 8.23024 17.0416 8.23424H19.7966"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path d="M13.5759 14.6481L10.1099 11.1821" stroke="currentColor"
                                                        stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                    </path>
                                                    <path d="M10.1108 14.6481L13.5768 11.1821" stroke="currentColor"
                                                        stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                    </path>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{ $adjustment->links() }}
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
