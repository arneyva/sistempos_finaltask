@extends('templates.main')
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
    {{-- part 1 --}}
    <div class="col-md-12 col-lg-12">
        <div class="mt-3" style="justify-content-center">
            @include('templates.alert')
        </div>
    </div>
    {{-- part 2  sisi kiri --}}
    <div class="col-md-12 col-lg-8">
        <div class="row">
            {{-- part --}}
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title">Create Product</h4>
                        </div>
                    </div>
                    {{--  --}}
                    <form method="POST" action="{{ route('product.update', $product['id']) }}"
                        enctype="multipart/form-data" onsubmit="saveVariantData()">
                        @method('PATCH')
                        @csrf
                        <div class="card-body">
                            <input type="hidden" id="variantData" name="variants">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="name">Name Product *</label>
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
                                    <label class="form-label" for="codebaseproduct">Code Product *</label>
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
                                    <label class="form-label" for="brand">Brand</label>
                                    <select class="form-select select2" id="brand" required name="brand_id"
                                        data-placeholder="Select a Brand ">
                                        <option selected disabled value="">Choose...</option>
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
                                    <label class="form-label" for="category">Category *</label>
                                    <select class="form-select select2" id="category" required name="category_id"
                                        data-placeholder="Select a Category">>
                                        <option selected disabled value="">Choose...</option>
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
                                    <label class="form-label" for="tax">Tax</label>
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
                                    <label class="form-label" for="description">Note</label>
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
                                <label class="form-label" for="type">Type</label>
                                <input type="text" class="form-select" id="type" required name="type"
                                    value="{{ $product['type'] }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="productcost">Product Cost *</label>
                                <input type="text" class="form-control" id="productcost" required
                                    placeholder="input product cost" name="cost" value="{{ $product['cost'] }}">
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
                                <label class="form-label" for="productprice">Product Price *</label>
                                <input type="text" class="form-control" id="productprice" required
                                    placeholder="input product price" name="price" value=" {{ $product['price'] }}">
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
                                <label for="productunit" class="form-label">Product Unit</label>
                                <select class="form-select select2" id="productunit" required name="unit_id"
                                    data-placeholder="Select a Product Unit">
                                    <option selected disabled value="">Choose...</option>
                                    @foreach ($unit as $item)
                                        {{-- <option value="{{ $item->id }}">{{ $item->name }}
                                        </option> --}}
                                        <option value="{{ $item->id }}"
                                            {{ (old('unit_id') ?? $product['unit_id']) == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="saleunit" class="form-label">Sale Unit</label>
                                <select class="form-select select2" id="saleunit" required name="unit_sale_id"
                                    data-placeholder="Select a Sale Unit">
                                    {{-- <option selected disabled value="">Choose...</option> --}}
                                    <option value="{{ $item->id }}"
                                        {{ (old('unit_sale_id') ?? $product['unit_sale_id']) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="purchaseunit" class="form-label">Purchase Unit</label>
                                <select class="form-select select2" id="purchaseunit" required name="unit_purchase_id"
                                    data-placeholder="Select a Purchase Unit">
                                    {{-- <option selected disabled value="">Choose...</option> --}}
                                    <option value="{{ $item->id }}"
                                        {{ (old('unit_purchase_id') ?? $product['unit_purchase_id']) == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                </select>
                            </div>
                            {{-- handel produk variant --}}
                            <div class="col-md-12 mb-3" id="createvariant">
                                <div class="row">
                                    <div class="col-md-9 mb-3">
                                        <input type="text" class="form-control" id="variantNameInput"
                                            placeholder="Enter Variant Name">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <button class="btn btn-soft-primary" id="createVariantBtn" type="button">Add
                                            +</button>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="table-responsive">
                                            <table id="variantTable" class="table table-striped mb-0" role="grid">
                                                <thead>
                                                    <tr>
                                                        <th>Variant Name</th>
                                                        <th>Variant code</th>
                                                        <th>Variant cost</th>
                                                        <th>Variant price</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="variantTableBody">
                                                    @foreach ($product['ProductVariant'] as $variant)
                                                        <tr>
                                                            <td><input required class="form-control" type="text"
                                                                    style="border-color: #DF4141;"
                                                                    value="{{ $variant['name'] }}"
                                                                    name="variants[{{ $variant['id'] }}][name]"></td>
                                                            <td><input required class="form-control" type="text"
                                                                    style="border-color: #DF4141;"
                                                                    value="{{ $variant['code'] }}"
                                                                    name="variants[{{ $variant['id'] }}][code]"></td>
                                                            <td><input required class="form-control" type="text"
                                                                    style="border-color: #DF4141;"
                                                                    value="{{ $variant['cost'] }}"
                                                                    name="variants[{{ $variant['id'] }}][cost]"></td>
                                                            <td><input required class="form-control" type="text"
                                                                    style="border-color: #DF4141;"
                                                                    value="{{ $variant['price'] }}"
                                                                    name="variants[{{ $variant['id'] }}][price]"></td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-soft-warning delete-variant">Delete</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    {{-- Table rows will be dynamically added here --}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Input hidden untuk menyimpan data varian sebelum form disubmit -->
                            <input type="hidden" id="variantData" name="variantData">
                            {{--  --}}
                            <div class="col-md-6 mb-3">
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

                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <button class="btn btn-primary" type="submit">Create</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- part 3 sisi kanan --}}
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
                                        <img style="max-height: 100%;max-width: 100%;; object-fit: contain; object-position: center;"
                                            id="previewLogo">
                                        <div class="logo-action">
                                            <img id="viewLogoIcon" src="{{ asset('template/assets/img/icon-view.svg') }}"
                                                alt="" srcset="" data-bs-toggle="modal"
                                                data-bs-target="#viewLogoModal">
                                            <img id="deleteLogoIcon"
                                                src="{{ asset('template/assets/img/icon-delete.svg') }}" alt=""
                                                srcset="">
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
    </div>
    <div class="modal fade" id="viewLogoModal" tabindex="-1" aria-labelledby="logoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <img id="logoExtend" alt="">
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


            openLogoUpload.click(function() {
                logoUpload.click();
            });

            logoUpload.change(function() {
                var file = logoUpload[0].files[0];
                if (file) {
                    // Check file size
                    if (file.size > 15728640) { // 1 MB in bytes
                        $('#error-message').text('File size exceeds 15 MB.');
                        logoUpload.value = ''; // Clear the file input
                        return;
                    }

                    // Create an image element to check the aspect ratio
                    var img = new Image();
                    img.src = URL.createObjectURL(file);

                    img.onload = function() {
                        $('#error-message').text(''); // Clear any error messages
                        openLogoUpload.removeClass('d-flex').addClass('d-none');
                        afterLogoUpload.removeClass('d-none').addClass('d-flex');
                        preview.attr('src', URL.createObjectURL(file));
                        previewExtend.attr('src', URL.createObjectURL(file));
                        // }
                    };
                }
            });
            deleteLogoIcon.click(function() {
                afterLogoUpload.removeClass('d-flex').addClass('d-none');
                openLogoUpload.removeClass('d-none').addClass('d-flex');
                logoUpload.value = '';
            })
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
    <td><input required class="form-control" type="text" style="border-color: #DF4141;" value="${variantName}" name="variants[name][]"></td>
    <td contenteditable="true" class="variant-code"><input required class="form-control" type="text" style="border-color: #DF4141;" name="variants[code][]"></td>
    <td contenteditable="true" class="variant-cost"><input required class="form-control" type="text" style="border-color: #DF4141;"  name="variants[cost][]"></td>
    <td contenteditable="true" class="variant-price"><input required class="form-control" type="text" style="border-color: #DF4141;" name="variants[price][]"></td>
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
            // Add delete functionality to existing rows
            var deleteButtons = document.querySelectorAll(".delete-variant");
            deleteButtons.forEach(function(button) {
                button.addEventListener("click", function() {
                    button.closest("tr").remove();
                });
            });
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
                if (variantsData.length === 0) {
                    alert("Please add at least one variant.");
                    event.preventDefault();
                    return;
                }

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

    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            var productUnitSelect = document.getElementById("productunit");
            var saleUnitSelect = document.getElementById("saleunit");
            var purchaseUnitSelect = document.getElementById("purchaseunit");

            productUnitSelect.addEventListener("change", function() {
                var selectedProductId = this.value;
                // Hapus opsi yang ada pada kedua select
                clearSelectOptions(saleUnitSelect);
                clearSelectOptions(purchaseUnitSelect);

                // Ambil data unit terkait berdasarkan product unit yang dipilih
                fetch(`/product/get-units/${selectedProductId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Isi opsi untuk sale unit
                        data.related_units.forEach(unit => {
                            var option = document.createElement("option");
                            option.value = unit.id;
                            option.textContent = unit.name;
                            saleUnitSelect.appendChild(option);
                        });

                        // Isi opsi untuk purchase unit
                        data.related_units.forEach(unit => {
                            var option = document.createElement("option");
                            option.value = unit.id;
                            option.textContent = unit.name;
                            purchaseUnitSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error:', error));
            });

            function clearSelectOptions(selectElement) {
                while (selectElement.options.length > 1) {
                    selectElement.remove(1);
                }
                selectElement.selectedIndex = 0;
            }
        });
    </script> --}}
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
@endpush
