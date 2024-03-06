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
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="name">Name Product *</label>
                                    <input type="text" class="form-control" id="name" required
                                        placeholder="input name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="codebaseproduct">Code Product *</label>
                                    <input type="text" class="form-control" id="codebaseproduct" required
                                        placeholder="input code">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="brand">Brand</label>
                                    <select class="form-select" id="brand" required>
                                        <option selected disabled value="">Choose...</option>
                                        @foreach ($brand as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="category">Category *</label>
                                    <select class="form-select" id="category" required>
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
                                            aria-describedby="basic-addon1" required placeholder="input tax">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="description">Description</label>
                                    <input type="text" class="form-control" id="description" required
                                        placeholder="a few words...">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    {{--  --}}
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="type">Type</label>
                                    <select class="form-select" id="type" required>
                                        <option selected disabled value="">Choose...</option>
                                        <option value="is_single">Standart Product</option>
                                        <option value="is_variant">Varied Product</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="productcost">Product Cost *</label>
                                    <input type="text" class="form-control" id="productcost" required
                                        placeholder="input product cost">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="productprice">Product Price *</label>
                                    <input type="text" class="form-control" id="productprice" required
                                        placeholder="input product price">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productunit" class="form-label">Product Unit</label>
                                    <select class="form-select" id="productunit" required>
                                        <option selected disabled value="">Choose...</option>
                                        @foreach ($unit as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="saleunit" class="form-label">Sale Unit</label>
                                    <select class="form-select" id="saleunit" required>
                                        <option selected disabled value="">Choose...</option>
                                        <option>...</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="purchaseunit" class="form-label">Purchase Unit</label>
                                    <select class="form-select" id="purchaseunit" required>
                                        <option selected disabled value="">Choose...</option>
                                        <option>...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <button class="btn btn-primary" type="submit">Create</button>
                            </div>
                        </form>
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

            typeSelect.addEventListener("change", function() {
                var selectedType = this.value;
                if (selectedType === "is_variant") {
                    productCostField.disabled = true;
                    productPriceField.disabled = true;
                } else {
                    productCostField.disabled = false;
                    productPriceField.disabled = false;
                }
            });

            // Set status awal
            var initialType = typeSelect.value;
            if (initialType === "is_variant") {
                productCostField.disabled = true;
                productPriceField.disabled = true;
            }
        });
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
