@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Edit Product') }}</h1>
    <p>{{ __('Do Something with product data') }}</p>
@endsection

<style>
    .upload-logo {
        padding: 20px 8px;
        border: 1px dashed #D5DBE1;
    }

    .upload-logo:hover {
        cursor: pointer;
        border-color: #D25555;
    }

    .after-upload-logo {
        padding: 20px 8px;
        border: 1px dashed #D25555;
    }

    .logo-action {
        display: none;
        position: absolute;
    }

    .logo-wrapper:hover {
        cursor: pointer;
    }

    .logo-wrapper:hover #previewLogo {
        position: relative;
        filter: brightness(50%)
    }

    .logo-wrapper:hover .logo-action {
        display: flex;
        gap: 5px
    }
</style>
@section('content')
    <div class="col-md-12 col-lg-12">
        <div class="mt-3" style="justify-content-center">
            <!-- @include('templates.alert') -->
        </div>
    </div>
    <div class="col-md-12 col-lg-10">
        <div class="row">
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title">{{ __('Update Product') }}</h4>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('product.update', $product['id']) }}"
                        enctype="multipart/form-data" onsubmit="saveVariantData()">
                        @method('PATCH')
                        @csrf
                        <div class="card-body">
                            <input type="hidden" id="variantData" name="new_variants">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="name">{{ __('Product Name *') }}</label>
                                    <input type="text" class="form-control" id="name" required
                                        placeholder="input name" name="name" value="{{ $product['name'] }}">
                                    @error('name')
                                        <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                            role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                            <span style="margin-left: 3px"> {{ $message }}</span>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                                aria-label="Close"
                                                style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="codebaseproduct">{{ __('Product Code *') }}</label>
                                    <input type="text" class="form-control" id="codebaseproduct" required
                                        placeholder="input code" name="code" value="{{ $product['code'] }}">
                                    @error('code')
                                        <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                            role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                            <span style="margin-left: 3px"> {{ $message }}</span>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                                aria-label="Close"
                                                style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="brand">{{ __('Brand') }}</label>
                                    <select class="form-select select2" id="brand" required name="brand_id"
                                        data-placeholder="Select a Brand ">
                                        <option selected disabled value="">{{ __('Choose...') }}</option>
                                        @foreach ($brand as $item)
                                            <option value="{{ $item->id }}"
                                                {{ (old('brand_id') ?? $product['brand_id']) == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('brand_id')
                                        <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                            role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                            <span style="margin-left: 3px"> {{ $message }}</span>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                                aria-label="Close"
                                                style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="category">{{ __('Category')  }}</label>
                                    <select class="form-select select2" id="category" required name="category_id"
                                        data-placeholder="Select a Category">>
                                        <option selected disabled value="">{{ __('Choose...') }}</option>
                                        @foreach ($category as $item)
                                            <option value="{{ $item->id }}"
                                                {{ (old('category_id') ?? $product['category_id']) == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                            role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                            <span style="margin-left: 3px"> {{ $message }}</span>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                                aria-label="Close"
                                                style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="tax">{{ __('Tax') }}</label>
                                    <div class="form-group input-group">
                                        <span class="input-group-text" id="basic-addon1">%</span>
                                        <input type="text" class="form-control" id="tax" aria-label="Username"
                                            aria-describedby="basic-addon1" required placeholder="input tax"
                                            name="TaxNet" value="{{ $product['TaxNet'] }}">
                                    </div>
                                    @error('TaxNet')
                                        <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                            role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                            <span style="margin-left: 3px"> {{ $message }}</span>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                                aria-label="Close"
                                                style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="description">{{ __('Note') }}</label>
                                    <input type="text" class="form-control" id="description"
                                        placeholder="a few words..." name="note" value="{{ $product['note'] }}">
                                    @error('note')
                                        <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                            role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                            <span style="margin-left: 3px"> {{ $message }}</span>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                                aria-label="Close"
                                                style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="type">{{ __('Type') }}</label>
                                <input type="text" class="form-select" id="type" required name="type"
                                    value="{{ $product['type'] }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="productcost">{{ __('Cost') }}</label>
                                @if ($product['type'] == 'is_variant')
                                    <input type="text" class="form-control" id="productcost" required
                                        placeholder="input product cost" name="cost" value="{{ $product['cost'] }}"
                                        disabled>
                                @else
                                    <input type="text" class="form-control" id="productcost" required
                                        placeholder="input product cost" name="cost" value="{{ $product['cost'] }}">
                                @endif
                                @error('cost')
                                    <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                        role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                        <span style="margin-left: 3px"> {{ $message }}</span>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                            aria-label="Close"
                                            style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="productprice">{{ __('Price') }}</label>
                                @if ($product['type'] == 'is_variant')
                                    <input type="text" class="form-control" id="productprice" required
                                        placeholder="input product price" name="price"
                                        value=" {{ $product['price'] }}" disabled>
                                @else
                                    <input type="text" class="form-control" id="productprice" required
                                        placeholder="input product price" name="price"
                                        value="{{ $product['price'] }}">
                                @endif
                                @error('price')
                                    <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                        role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                        <span style="margin-left: 3px"> {{ $message }}</span>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                            aria-label="Close"
                                            style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="productunit" class="form-label">{{ __('Product Unit & Sale Unit') }}</label>
                                <select class="form-select select2" id="productunit" required name="unit_id"
                                    data-placeholder="Select a Product Unit">
                                    <option selected disabled value="">{{ __('Choose...') }}</option>
                                    @foreach ($unit as $item)
                                        <option value="{{ $item->id }}"
                                            {{ (old('unit_id') ?? $product['unit_id']) == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="col-md-6 mb-3">
                                <label for="saleunit" class="form-label">Sale Unit</label>
                                <select class="form-select select2" id="saleunit" required name="unit_sale_id"
                                    data-placeholder="Select a Sale Unit">
                                    @foreach ($units_sub as $item)
                                        <option value="{{ $item->id }}"
                                            {{ (old('unit_sale_id') ?? $product['unit_sale_id']) == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="col-md-6 mb-3">
                                <label for="purchaseunit" class="form-label">{{ __('Purchase Unit') }}</label>
                                <select class="form-select select2" id="purchaseunit" required name="unit_purchase_id"
                                    data-placeholder="Select a Purchase Unit">
                                    @foreach ($units_sub as $item)
                                        <option value="{{ $item->id }}"
                                            {{ (old('unit_purchase_id') ?? $product['unit_purchase_id']) == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- handel produk variant lama --}}
                            @if ($product['type'] == 'is_variant')
                                <div class="col-md-12 mb-3" id="createvariant">
                                    <div class="row">
                                        <div class="col-md-9 mb-3">
                                            <input type="text" class="form-control" id="variantNameInput"
                                                placeholder="{{ __('Input Variant Name') }}">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <button class="btn btn-soft-primary" id="createVariantBtn" type="button">{{ __('Add +') }}</button>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="table-responsive">
                                                <table id="variantTable" class="table table-striped mb-0" role="grid">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Variant Name') }}</th>
                                                            <th>{{ __('Variant code') }}</th>
                                                            <th>{{ __('Variant cost') }}</th>
                                                            <th>{{ __('Variant price') }}</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($product['ProductVariant'] as $variant)
                                                            <tr data-id="{{ $variant['var_id'] }}">
                                                                <td><input required class="form-control" type="text"
                                                                        value="{{ $variant['name'] }}"
                                                                        name="variants[{{ $variant['id'] }}][name]"></td>
                                                                <td><input required class="form-control" type="text"
                                                                        value="{{ $variant['code'] }}"
                                                                        name="variants[{{ $variant['id'] }}][code]"></td>
                                                                <td><input required class="form-control" type="text"
                                                                        value="{{ $variant['cost'] }}"
                                                                        name="variants[{{ $variant['id'] }}][cost]"></td>
                                                                <td><input required class="form-control" type="text"
                                                                        value="{{ $variant['price'] }}"
                                                                        name="variants[{{ $variant['id'] }}][price]"></td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-soft-warning delete-variant">Delete</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tbody id="variantTableBody">
                                                        {{-- variant baru --}}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                            @endif
                            {{-- <div class="col-md-6 mb-3">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="is_imei" name="is_imei">
                                    <label class="form-check-label" for="is_imei">Product has Imei/Serial
                                        Number</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="not_selling" name="not_selling">
                                    <label class="form-check-label" for="not_selling">This Product Not For Selling
                                        Number</label>
                                </div>

                            </div> --}}
                        </div>
                        <div class="form-group mt-2">
                            <button class="btn btn-primary" type="submit">Update</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Handle Image --}}
    <div class="col-md-12 col-lg-4">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card credit-card-widget" data-aos="fade-up" data-aos-delay="900">
                    <div class="pb-4 border-0 card-header">
                        <div class="p-4 border border-white rounded primary-gradient-card">
                            <div class="d-flex justify-content-center align-items-center">
                                <input type="file" accept="image/*" style="display: none" name="logo"
                                    id="logo">
                                <div id="openLogoUpload"
                                    class="d-flex flex-column justify-content-center align-items-center upload-logo">
                                    <span style="font-size: 24px; color:#D25555">+</span>
                                    <span style="font-size: 20px; color:#ffffff">Upload Image</span>
                                    <span style="font-size: 20px; color:#ffffff; margin-top: 10px;">Max. File Size
                                        15MB</span>
                                </div>
                                <div id="afterLogoUpload" style="max-height: 100%;max-width: 100%;"
                                    class="d-none justify-content-center align-items-center after-upload-logo">
                                    <div class="logo-wrapper d-flex justify-content-center align-items-center">
                                        <img style="max-height: 100%;max-width: 100%; object-fit: contain; object-position: center;"
                                            id="previewLogo">
                                        <div class="logo-action">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em"
                                                viewBox="0 0 24 24" id="viewLogoIcon" alt="View"
                                                data-bs-toggle="modal" data-bs-target="#viewLogoModal">
                                                <g fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="1.5" color="currentColor">
                                                    <path
                                                        d="M21.544 11.045c.304.426.456.64.456.955c0 .316-.152.529-.456.955C20.178 14.871 16.689 19 12 19c-4.69 0-8.178-4.13-9.544-6.045C2.152 12.529 2 12.315 2 12c0-.316.152-.529.456-.955C3.822 9.129 7.311 5 12 5c4.69 0 8.178 4.13 9.544 6.045" />
                                                    <path d="M15 12a3 3 0 1 0-6 0a3 3 0 0 0 6 0" />
                                                </g>
                                            </svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em"
                                                viewBox="0 0 24 24" id="deleteLogoIcon" alt="Delete">
                                                <path fill="currentColor"
                                                    d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6zM19 4h-3.5l-1-1h-5l-1 1H5v2h14z" />
                                            </svg>
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
    <!-- Error message container -->
    <div id="error-message" class="text-danger mt-2"></div>
    <!-- Modal for viewing logo -->
    <div class="modal fade" id="viewLogoModal" tabindex="-1" aria-labelledby="viewLogoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewLogoModalLabel">Logo Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="logoExtend" src="" alt="Logo Preview" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>
    </form>
    {{-- end --}}
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            var openLogoUpload = $('#openLogoUpload');
            var afterLogoUpload = $('#afterLogoUpload');
            var logoUpload = $('#logo');
            var preview = $('#previewLogo');
            var previewExtend = $('#logoExtend');
            var deleteLogoIcon = $('#deleteLogoIcon');
            var viewLogoIcon = $('#viewLogoIcon');
            var errorMessage = $('#error-message');

            openLogoUpload.click(function() {
                logoUpload.click();
            });

            logoUpload.change(function() {
                var file = this.files[0];
                if (file) {
                    // Check file size
                    if (file.size > 15728640) { // 15 MB in bytes
                        errorMessage.text('File size exceeds 15 MB.');
                        this.value = ''; // Clear the file input
                        return;
                    }

                    var img = new Image();
                    img.src = URL.createObjectURL(file);

                    img.onload = function() {
                        errorMessage.text(''); // Clear any error messages
                        openLogoUpload.removeClass('d-flex').addClass('d-none');
                        afterLogoUpload.removeClass('d-none').addClass('d-flex');
                        preview.attr('src', URL.createObjectURL(file));
                        previewExtend.attr('src', URL.createObjectURL(file));
                    };

                    img.onerror = function() {
                        errorMessage.text('Error loading image. Please try again.');
                        logoUpload.val('');
                    };
                }
            });

            deleteLogoIcon.click(function() {
                afterLogoUpload.removeClass('d-flex').addClass('d-none');
                openLogoUpload.removeClass('d-none').addClass('d-flex');
                logoUpload.val('');
                preview.attr('src', '');
                previewExtend.attr('src', '');
                errorMessage.text('');
            });

            viewLogoIcon.click(function() {
                $('#viewLogoModal').modal('show');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            // Event listener for product unit change
            $('#productunit').change(function() {
                var productId = $(this).val();

                // Fetch related units via AJAX
                $.ajax({
                    url: '/product/get-units/' + productId,
                    type: 'GET',
                    success: function(response) {
                        // Clear previous options
                        $('#saleunit').empty();
                        $('#purchaseunit').empty();

                        // Append new options
                        $.each(response.related_units, function(key, value) {
                            $('#saleunit').append('<option value="' + value.id + '">' +
                                value.name + '</option>');
                            $('#purchaseunit').append('<option value="' + value.id +
                                '">' + value.name + '</option>');
                        });

                        // Refresh Select2
                        $('#saleunit').select2();
                        $('#purchaseunit').select2();
                    }
                });
            });
        });
    </script>
    <script>
        var deleteButtons = document.querySelectorAll(".delete-variant");
        deleteButtons.forEach(function(button) {
            button.addEventListener("click", function() {
                button.closest("tr").remove();
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var typeSelect = document.getElementById("type");
            var productCostField = document.getElementById("productcost");
            var productPriceField = document.getElementById("productprice");
            var productVariantField = document.getElementById("createvariant");

            typeSelect.addEventListener("change", function() {
                var selectedType = this.value;
                if (selectedType === "is_variant") {
                    productCostField.value = ""; // Kosongkan nilai input biaya produk
                    productPriceField.value = ""; // Kosongkan nilai input harga produk
                    productCostField.disabled = true;
                    productPriceField.disabled = true;
                    productVariantField.style.display = "block";
                } else {
                    productCostField.disabled = false;
                    productPriceField.disabled = false;
                    productVariantField.style.display = "none";
                }
            });
            // Sembunyikan area pembuatan varian produk secara default
            productVariantField.style.display = typeSelect.value === "is_variant" ? "block" : "none";
        });
        document.addEventListener("DOMContentLoaded", function() {
            var createVariantBtn = document.getElementById("createVariantBtn");
            var variantNameInput = document.getElementById("variantNameInput");
            var variantTableBody = document.getElementById("variantTableBody");

            createVariantBtn.addEventListener("click", function() {
                var variantName = variantNameInput.value.trim();

                if (variantName === "") {
                    alert("Please enter a variant name.");
                    return;
                }

                // Memeriksa apakah nama variant sudah ada
                var isDuplicate = false;
                var rows = variantTableBody.querySelectorAll("tr");
                rows.forEach(function(row) {
                    var existingName = row.cells[0].querySelector('input').value.trim();
                    if (existingName === variantName) {
                        isDuplicate = true;
                    }
                });

                if (isDuplicate) {
                    alert("Variant name already exists.");
                    return;
                }

                addVariantRow(variantName);
                variantNameInput.value = ""; // Reset the input field after adding the variant
            });

            function addVariantRow(variantName) {
                var newRow = document.createElement("tr");
                newRow.innerHTML = `
    <td><input required class="form-control" type="text" style="border-color: #DF4141;" value="${variantName}" name="new_variants[name]"></td>
    <td contenteditable="true" class="variant-code"><input required class="form-control" type="text" style="border-color: #DF4141;" name="new_variants[code]"></td>
    <td contenteditable="true" class="variant-cost"><input required class="form-control" type="text" style="border-color: #DF4141;"  name="new_variants[cost]"></td>
    <td contenteditable="true" class="variant-price"><input required class="form-control" type="text" style="border-color: #DF4141;" name="new_variants[price]"></td>
    <td>
        <button type="button" class="btn btn-soft-warning delete-variant">Delete</button>
    </td>
`;
                variantTableBody.appendChild(newRow);

                // Add event listener for delete button
                newRow.querySelector(".delete-variant").addEventListener("click", function() {
                    newRow.remove(); // Remove the row when delete button is clicked
                });
            }
        });

        // Menangani penyimpanan data produk varian sebelum formulir disubmit
        function saveVariantData() {
            var variantsData = [];
            var rows = document.getElementById("variantTableBody").querySelectorAll("tr");

            // Menyimpan semua kode dalam array untuk memeriksanya
            var codes = [];
            rows.forEach(function(row) {
                var variantName = row.cells[0].querySelector('input').value;
                var variantCode = row.cells[1].querySelector('input').value;
                var variantCost = row.cells[2].querySelector('input').value;
                var variantPrice = row.cells[3].querySelector('input').value;

                // Menambahkan kode ke dalam array
                codes.push(variantCode)

                // Memeriksa apakah input cost dan price numerik
                if (isNaN(variantCost) || isNaN(variantPrice)) {
                    alert("Cost and price must be numeric.");
                    event.preventDefault();
                    return;
                }
                // memriksa apakah cost dan price tidak kosong
                if (variantCost == '' || variantPrice == '') {
                    alert("Cost and price cannot be empty.");
                    event.preventDefault();
                    return;
                }
                if (variantCode == '') {
                    alert("Code cannot be empty.");
                    event.preventDefault();
                    return;
                }


                variantsData.push({
                    name: variantName,
                    code: variantCode,
                    cost: variantCost,
                    price: variantPrice
                });
            });

            // Jika jenis produk adalah varian, maka lakukan validasi
            if (document.getElementById("type").value === "is_variant") {
                // Memeriksa duplikat kode
                if (checkDuplicateCodes(codes)) {
                    alert("Duplicate code found.");
                    event.preventDefault();
                    return;
                }
            }

            // Simpan data produk varian ke dalam input tersembunyi sebelum formulir disubmit
            document.getElementById("variantData").value = JSON.stringify(variantsData);
        }

        // Fungsi untuk memeriksa duplikat kode
        function checkDuplicateCodes(codes) {
            var uniqueCodes = new Set(codes); // Membuat set untuk mendapatkan nilai unik
            return uniqueCodes.size !== codes.length; // Jika panjang set kurang dari panjang array, berarti ada duplikat
        }
    </script>
@endpush
