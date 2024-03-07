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
                    <div class="card-body">
                        <form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="name">Name Product *</label>
                                    <input type="text" class="form-control" id="name" required
                                        placeholder="input name" name="name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="codebaseproduct">Code Product *</label>
                                    <input type="text" class="form-control" id="codebaseproduct" required
                                        placeholder="input code" name="code">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="brand">Brand</label>
                                    <select class="form-select" id="brand" required name="brand_id">
                                        <option selected disabled value="">Choose...</option>
                                        @foreach ($brand as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="category">Category *</label>
                                    <select class="form-select" id="category" required name="category_id">
                                        <option selected disabled value="">Choose...</option>
                                        @foreach ($category as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="tax">Tax</label>
                                    <div class="form-group input-group">
                                        <span class="input-group-text" id="basic-addon1">%</span>
                                        <input type="text" class="form-control" id="tax" aria-label="Username"
                                            aria-describedby="basic-addon1" required placeholder="input tax" name="TaxNet">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="description">Description</label>
                                    <input type="text" class="form-control" id="description" required
                                        placeholder="a few words..." name="note">
                                </div>
                            </div>

                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    {{--  --}}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="type">Type</label>
                                <select class="form-select" id="type" required name="type">
                                    <option selected disabled value="">Choose...</option>
                                    <option value="is_single">Standart Product</option>
                                    <option value="is_variant">Varied Product</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="productcost">Product Cost *</label>
                                <input type="text" class="form-control" id="productcost" required
                                    placeholder="input product cost" name="cost">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="productprice">Product Price *</label>
                                <input type="text" class="form-control" id="productprice" required
                                    placeholder="input product price" name="price">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="productunit" class="form-label">Product Unit</label>
                                <select class="form-select" id="productunit" required name="unit_id">
                                    <option selected disabled value="">Choose...</option>
                                    @foreach ($unit as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="saleunit" class="form-label">Sale Unit</label>
                                <select class="form-select" id="saleunit" required name="unit_sale_id">
                                    <option selected disabled value="">Choose...</option>
                                    <option>...</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="purchaseunit" class="form-label">Purchase Unit</label>
                                <select class="form-select" id="purchaseunit" required name="unit_purchase_id">
                                    <option selected disabled value="">Choose...</option>
                                    <option>...</option>
                                </select>
                            </div>
                            {{-- handel produk variant --}}
                            <div class="col-md-12 mb-3" id="createvariant">
                                <div class="row">
                                    <div class="col-md-9 mb-3">
                                        <input type="text" class="form-control" id="variantNameInput" required
                                            placeholder="Enter Variant Name">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <button class="btn btn-soft-primary" id="createVariantBtn"
                                            type="button">Create</button>
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
                                                    {{-- Table rows will be dynamically added here --}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

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

        // handle is_variant dan is_single
        // document.addEventListener("DOMContentLoaded", function() {
        //     var typeSelect = document.getElementById("type");
        //     var productCostField = document.getElementById("productcost");
        //     var productPriceField = document.getElementById("productprice");

        //     typeSelect.addEventListener("change", function() {
        //         var selectedType = this.value;
        //         if (selectedType === "is_variant") {
        //             productCostField.disabled = true;
        //             // productCostField.placeholder = 'Choose Module Category';
        //             productPriceField.disabled = "true";
        //         } else {
        //             productCostField.style.display = "block";
        //             productPriceField.style.display = "block";
        //         }
        //     });
        // });
        document.addEventListener("DOMContentLoaded", function() {
            var typeSelect = document.getElementById("type");
            var productCostField = document.getElementById("productcost");
            var productPriceField = document.getElementById("productprice");
            var productVariantField = document.getElementById("createvariant");

            typeSelect.addEventListener("change", function() {
                var selectedType = this.value;
                if (selectedType === "is_variant") {
                    productCostField.disabled = true;
                    productPriceField.disabled = true;
                    productVariantField.style.display = "block";
                } else {
                    productCostField.disabled = false;
                    productPriceField.disabled = false;
                    productVariantField.style.display = "none";
                }
            });

            // Set status awal
            var initialType = typeSelect.value;
            if (initialType === "is_variant") {
                productCostField.disabled = true;
                productPriceField.disabled = true;
                productVariantField.style.display = "block";
            }
        });
        document.addEventListener("DOMContentLoaded", function() {
            var createVariantBtn = document.getElementById("createVariantBtn");
            var variantNameInput = document.getElementById("variantNameInput");
            var variantTableBody = document.getElementById("variantTableBody");

            createVariantBtn.addEventListener("click", function() {
                var variantName = variantNameInput.value;

                if (variantName.trim() === "") {
                    alert("Please enter a variant name.");
                    return;
                }

                addVariantRow(variantName);
                variantNameInput.value = ""; // Reset the input field after adding the variant
            });

            function addVariantRow(variantName) {
                var newRow = document.createElement("tr");
                newRow.innerHTML = `
        <td>${variantName}</td>
        <td contenteditable="true" class="variant-code"></td>
        <td contenteditable="true" class="variant-cost"></td>
        <td contenteditable="true" class="variant-price"></td>
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
        var variantTable = document.getElementById("variantTable");

        function saveVariantData() {
            var rows = variantTable.querySelectorAll("tbody tr");

            rows.forEach(function(row) {
                var variantName = row.cells[0].textContent; // Ambil nama varian dari kolom pertama
                var variantCode = row.cells[1].textContent; // Ambil kode varian dari kolom kedua
                var variantCost = row.cells[2].textContent; // Ambil biaya varian dari kolom ketiga
                var variantPrice = row.cells[3].textContent; // Ambil harga varian dari kolom keempat

                // Lakukan sesuatu dengan data varian yang sudah diambil, misalnya simpan ke dalam array atau kirim ke server
            });
        }
    </script>
    <script>
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
    </script>
@endpush
