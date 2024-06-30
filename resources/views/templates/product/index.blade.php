@extends('templates.main')

@section('pages_title')
    <h1>{{ __('All Products') }}</h1>
    <p>{{ __('Do Something with all your stores products') }}</p>
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="mt-3" style="justify-content-center">
            <!-- @include('templates.alert') -->
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">{{ __('All Products') }}</h4>
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary" data-bs-toggle="modal"
                        data-bs-target="#createModal">{{ __('Filter') }}</button>
                    <a href="{{ route('product.pdf', request()->query()) }}" class="btn btn-soft-success">PDF</a>
                    <a href="{{ route('product.export', request()->query()) }}" class="btn btn-soft-danger">Excel</a>
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
                                    <form action="{{ route('product.index') }}" method="GET" id="filterForm">
                                        <div class="col mb-3">
                                            <label class="form-label" for="code">{{ __('Search By Code') }}</label>
                                                <input type="text" class="form-control" id="code" name="code"
                                                    value="{{ request()->input('code') }}" placeholder="{{ __('Input Code ...') }}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="name">{{ __('Search By Name') }}</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ request()->input('name') }}" placeholder="{{ __('Input Name ...') }}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="category_id">{{ __('Choose Category') }}</label>
                                            <select class="form-select" id="category_id" name="category_id">
                                                <option selected disabled value="">{{ __('Choose...') }}</option>
                                                @foreach ($categories as $wh)
                                                    <option value="{{ $wh->id }}"
                                                        {{ request()->input('category_id') == $wh->id ? 'selected' : '' }}>
                                                        {{ $wh->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="brand_id">{{ __('Choose Brand') }}</label>
                                            <select class="form-select" id="brand_id" name="brand_id">
                                                <option selected disabled value="">{{ __('Choose...') }}</option>
                                                @foreach ($brands as $wh)
                                                    <option value="{{ $wh->id }}"
                                                        {{ request()->input('brand_id') == $wh->id ? 'selected' : '' }}>
                                                        {{ $wh->name }}
                                                    </option>
                                                @endforeach
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
                    @role('superadmin|inventaris')
                        <button type="button" class="btn btn-soft-gray" data-bs-toggle="modal"
                            data-bs-target="#ImportProduct">{{ __('Import Product') }}</button>
                        <div class="modal fade" id="ImportProduct" tabindex="-1" aria-labelledby="ImportProduct"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">{{ __('Import Product') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-header">
                                        <h6 class="modal-title" id="createModalLabel" style="color:#d06565">
                                            {{ __('*Only Single Product Type') }}</h6>
                                    </div>
                                    <form action="{{ route('product.import') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="fileInput" class="form-label">{{ __('Choose File') }}</label>
                                                <input class="form-control" type="file" name="products" id="fileInput"
                                                    accept=".csv">
                                                <p style="color: #d06565">{{ __('File must be in CSV format') }}</p>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label"
                                                    for="name">{{ __('Name*') }}</label>
                                                <div class="col-sm-8">
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        disabled>{{ __('This Field is required') }}</button>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label"
                                                    for="codeProduct">{{ __('Code Product*') }}</label>
                                                <div class="col-sm-8">
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        disabled>{{ __('This Field is required') }}</button>
                                                    <button style="margin-top: 5px" type="button"
                                                        class="btn btn-outline-danger btn-sm"
                                                        disabled>{{ __('Code must not exist already') }}</button>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label"
                                                    for="category">{{ __('Category') }}</label>
                                                <div class="col-sm-8">
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        disabled>{{ __('This Field is required') }}</button>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label"
                                                    for="productCost">{{ __('Product Cost') }}</label>
                                                <div class="col-sm-8">
                                                    <button type="button" class="btn btn-outline-danger btn-sm" disabled>{{ __('This Field is required') }}</button>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label" for="productUnit">{{ __('Product Unit') }}</label>
                                                <div class="col-sm-8">
                                                    <button type="button" class="btn btn-outline-danger btn-sm" disabled>{{ __('This Field is required')  }}</button>
                                                    <button style="margin-top: 5px" type="button"
                                                        class="btn btn-outline-danger btn-sm" disabled>{{ __('This Unit must already be created') }}</button>
                                                    <button style="margin-top: 5px" type="button"
                                                        class="btn btn-outline-danger btn-sm" disabled>
                                                        {{ __('Please use short name of unit')  }}</button>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label" for="brand">{{ __('Brand') }}</label>
                                                <div class="col-sm-8">
                                                    <button type="button"
                                                        class="btn btn-outline-primary btn-sm"disabled>{{ __('Field optional') }}</button>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label class="col-sm-4 col-form-label" for="note">{{ __('Note') }}</label>
                                                <div class="col-sm-8">
                                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                                        disabled>{{ __('Field optional') }}</button>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="/import/import_products.csv" class="btn btn-soft-success"
                                                    download>{{ __('Download Example') }}</a>
                                                <button type="submit" class="btn btn-soft-primary">{{ __('Submit form') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('product.create') }}"><button type="button" class="btn btn-soft-primary">{{ __('Create +') }}</button></a>
                    @endrole
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>{{ __('Product Name') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Brand') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Cost') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Unit') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                @role('superadmin|inventaris')
                                    <th>{{ __('Actions') }}</th>
                                @endrole
                                @role('staff')
                                    <th>{{ __('Stock Alert') }}</th>
                                    <th>{{ __('Tax') }}</th>
                                @endrole
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img class="rounded img-fluid avatar-40 me-3 bg-soft-primary"
                                                src="{{ asset('hopeui/html/assets/images/shapes/01.png') }}"
                                                alt="profile">
                                            <div class="d-flex flex-column">
                                                @if ($item['type'] === 'Variant Product')
                                                    @foreach ($item['name'] as $name)
                                                        <h6 style="margin-top:10px"> * {{ $name }}</h6>
                                                    @endforeach
                                                @else
                                                    <h6>{{ $item['name'] }}</h6>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item['type'] }}</td>
                                    <td>{{ $item['code'] }}</td>
                                    <td>{{ $item['brand'] }}</td>
                                    <td>{{ $item['category'] }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            @if ($item['type'] === 'Variant Product')
                                                @foreach ($item['cost'] as $cost)
                                                    <h6 style="margin-top:10px">
                                                        {{ 'Rp ' . number_format($cost, 2, ',', '.') }} </h6>
                                                @endforeach
                                            @else
                                                {{ $item['cost'] }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            @if ($item['type'] === 'Variant Product')
                                                @foreach ($item['price'] as $price)
                                                    <h6 style="margin-top:10px">
                                                        {{ 'Rp ' . number_format($price, 2, ',', '.') }} </h6>
                                                @endforeach
                                            @else
                                                {{ $item['price'] }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $item['unit'] }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    @role('staff')
                                        <td style="color:#d06565">{{ $item['stock_alert'] }}</td>
                                    @endrole
                                    @role('superadmin|inventaris')
                                        <td>
                                            <div class="inline">
                                                <a href="{{ route('product.show', $item['id']) }}">
                                                    <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
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
                                                        <circle opacity="0.89" cx="13.5" cy="10.5" r="1.5"
                                                            fill="white">
                                                        </circle>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('product.edit', $item['id']) }}">
                                                    <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.7476 20.4428H21.0002" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M12.78 3.79479C13.5557 2.86779 14.95 2.73186 15.8962 3.49173C15.9485 3.53296 17.6295 4.83879 17.6295 4.83879C18.669 5.46719 18.992 6.80311 18.3494 7.82259C18.3153 7.87718 8.81195 19.7645 8.81195 19.7645C8.49578 20.1589 8.01583 20.3918 7.50291 20.3973L3.86353 20.443L3.04353 16.9723C2.92866 16.4843 3.04353 15.9718 3.3597 15.5773L12.78 3.79479Z"
                                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path d="M11.021 6.00098L16.4732 10.1881" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <form id="delete-form-{{ $item['id'] }}"
                                                    action="{{ route('product.destroy', $item['id']) }}" method="POST"
                                                    style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <svg class="icon-32 delete-icon" width="32" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg"
                                                        data-id="{{ $item['id'] }}">
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
                                                </form>
                                            </div>
                                        </td>
                                    @endrole
                                    @role('staff')
                                        <td>{{ $item['TaxNet'] }} %</td>
                                    @endrole
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-icon').forEach(function(element) {
            element.addEventListener('click', function() {
                var formId = 'delete-form-' + this.getAttribute('data-id');
                document.getElementById(formId).submit();
            });
        });
    });
</script>
<script>
    function resetFilters() {
        // Reset nilai-nilai input dari formulir
        document.getElementById('code').value = '';
        document.getElementById('name').value = '';
        document.getElementById('category_id').value = '';
        document.getElementById('brand_id').value = '';

        // Submit formulir secara otomatis untuk menghapus filter
        document.getElementById('filterForm').submit();
    }
</script>
