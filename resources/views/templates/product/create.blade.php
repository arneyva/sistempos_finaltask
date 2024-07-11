@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Add New Product') }}</h1>
    <p>{{ __('Add new product to your store') }} </p>
@endsection
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" />
<style>
    .background {
        position: fixed;
        /* atau 'absolute', tergantung kebutuhan */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* Warna gelap dengan transparansi */
        z-index: 1;
        /* Pastikan lebih tinggi dari elemen lain kecuali modal */
    }

    .overlay {
        z-index: 2;
        /* Pastikan lebih tinggi dari elemen lain kecuali modal */
    }

    img {
        display: block;
        max-width: 100%;
    }

    .image-container {
        overflow: hidden;
        max-width: 510px !important;
        max-height: 370px !important;
    }

    .preview {
        display: none;
    }

    @media (min-width: 768px) {

        /* Adjust the large (lg) screen breakpoint */
        .modal-lg {
            --bs-modal-width: 700px;
            /* Set your desired minimum width for large screens (lg) */
        }

        .preview {
            display: block;
            overflow: hidden;
            width: 210px;
            height: 210px;
            border: 1px solid red;
        }
    }

    .select2-container .select2-selection--single {
        height: 38px !important;
        /* Atur tinggi sesuai kebutuhan */
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        /* Sesuaikan dengan tinggi yang diatur */
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        /* Sesuaikan dengan tinggi yang diatur - 2px untuk padding */
    }

    .select2-container .select2-dropdown .select2-results__options {
        max-height: 220px;
        /* Atur tinggi maksimum sesuai kebutuhan */
    }
</style>
@section('content')
    <div class="col-md-12 col-lg-12">
        <div class="mt-3" style="justify-content-center">
            <!-- @include('templates.alert') -->
        </div>
    </div>
    <div class="col-md-12 col-lg-8">
        <div class="row">
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title">{{ __('Product Detail') }}</h4>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data" id="form"
                        onsubmit="saveVariantData()">
                        @csrf
                        <div class="card-body">
                            <input type="hidden" id="variantData" name="variants">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="brand">{{ __('Brand') }}</label>
                                    <select class="form-select select2" id="brand" required name="brand_id"
                                        data-placeholder="{{ __('Select a Brand') }}">
                                        <option selected disabled value="">{{ __('Choose...') }}</option>
                                        @foreach ($brand as $item)
                                            <option value="{{ $item->id }}"
                                                {{ old('brand_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}
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
                                    <label class="form-label" for="category">{{ __('Category') }}</label>
                                    <select class="form-select select2" id="category" required name="category_id"
                                        data-placeholder="{{ __('Select a Category') }}">
                                        <option selected disabled value="">{{ __('Choose...') }}</option>
                                        @foreach ($category as $item)
                                            <option value="{{ $item->id }}" data-code="{{ $item->code }}"
                                                {{ old('category_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
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
                                            aria-describedby="basic-addon1" required placeholder="{{ __('input tax') }}"
                                            name="TaxNet" value="{{ Session::get('TaxNet') }}">
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
                                        placeholder="{{ __('a few words...') }}" name="note"
                                        value="{{ Session::get('note') }}">
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
                                <select class="form-select" id="type" required name="type">
                                    <option selected disabled value="">{{ __('Choose...') }}</option>
                                    <option value="is_single" {{ old('type') == 'is_single' ? 'selected' : '' }}>
                                        {{ __('Standard Product') }}</option>
                                    <option value="is_variant" {{ old('type') == 'is_variant' ? 'selected' : '' }}>
                                        {{ __('Varied Product') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="productcost">{{ __('Cost') }}</label>
                                <input type="text" class="form-control" id="productcost" required
                                    placeholder="{{ __('Rp ') }}" name="cost"
                                    value="{{ Session::get('cost') }}">
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
                                <input type="text" class="form-control" id="productprice" required
                                    placeholder="{{ __('Rp ') }}" name="price"
                                    value="{{ Session::get('price') }}">
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
                                    data-placeholder="{{ __('Select a Product & Sale Unit') }}">
                                    <option selected disabled value="">{{ __('Choose...') }}</option>
                                    @foreach ($unit as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="purchaseunit" class="form-label">{{ __('Purchase Unit') }}</label>
                                <select class="form-select select2" id="purchaseunit" required name="unit_purchase_id"
                                    data-placeholder="{{ __('Select a Purchase Unit') }}">
                                    <option selected disabled value="">{{ __('Choose...') }}</option>
                                    <option>...</option>
                                </select>
                            </div>
                            {{-- handel produk variant --}}
                            <div class="col-md-12 mb-3" id="createvariant">
                                <div class="row">
                                    <div class="col-md-9 mb-3">
                                        <input type="text" class="form-control" id="variantNameInput"
                                            placeholder="{{ __('Input Variant Name') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <button class="btn btn-soft-primary" id="createVariantBtn"
                                            type="button">{{ __('Add +') }}</button>
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
                                                <tbody id="variantTableBody">
                                                    {{-- Data Variant Tertampil Disini --}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <button class="btn btn-primary" type="submit">{{ __('Create') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">{{ __('Create Product') }}</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="card-header pb-4 border-dashed rounded" style="border: 1px dashed rgb(94, 87, 87);">
                        <div class="profile-img-edit position-relative d-flex justify-content-center align-items-center">
                            <img src="/hopeui/html/assets/images/products/no-image.png" id="firstImage" alt="profile-pic"
                                class="theme-color-default-img profile-pic rounded avatar-100">
                            <button type="button" class="upload-icone bg-primary" id="chooseImageButton">
                                <svg class="upload-button icon-14" width="14" viewBox="0 0 24 24">
                                    <path fill="#ffffff"
                                        d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z" />
                                </svg>
                                <input type="file" name="image" class="image" id="imageInput"
                                    style="display: none;">
                            </button>
                        </div>
                    </div>
                    <input type="hidden" id="croppedImageData" name="avatar">
                    <div class="img-extension mt-3">
                        <div class="d-inline-block align-items-center py-1">
                            <span>Only</span>
                            <a href="#">.jpg</a>
                            <a href="#">.png</a>
                            <a href="#">.jpeg</a>
                            <span>allowed</span>
                        </div>
                        <div class="d-inline-block align-items-center">
                            <span>Max. File size</span>
                            <a href="#">10 MB</a>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="name">{{ __('Product Name *') }}</label>
                    <input type="text" class="form-control" id="name" required
                        placeholder="{{ __('Input Name ...') }}" name="name" value="{{ Session::get('name') }}">
                    @error('name')
                        <div class="alert alert-right alert-warning alert-dismissible fade show mb-3" role="alert"
                            style="padding: 1px 1px 1px 1px; margin-top: 3px">
                            <span style="margin-left: 3px"> {{ $message }}</span>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                aria-label="Close"
                                style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="codebaseproduct">{{ __('Product Code *') }}</label>
                    <input type="text" class="form-control" id="codebaseproduct" required
                        placeholder="{{ __('Input Code ...') }}" name="code" value="{{ Session::get('code') }}">
                    @error('code')
                        <div class="alert alert-right alert-warning alert-dismissible fade show mb-3" role="alert"
                            style="padding: 1px 1px 1px 1px; margin-top: 3px">
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
    <!-- Modal for image preview and cropping -->
    <div class="modal fade" id="modal" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="modalLabel" aria-hidden="true">
        <div class="background">
            <div class="modal-dialog modal-dialog-centered modal-lg overlay" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Crop Image</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="docs-demo">
                                    <div class="image-container">
                                        <img id="image">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 px-0">
                                <div class="preview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="crop">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script>
        var bs_modal = $('#modal');
        var image = document.getElementById('image');
        var cropper, reader, file;

        $("body").on("change", ".image", function(e) {
            var files = e.target.files;
            var maxFileSizeInBytes = 10 * 1024 * 1024;
            var allowedExtensions = ['jpg', 'jpeg', 'png'];

            if (files && files.length > 0) {
                file = files[0];

                var fileExtension = file.name.split('.').pop().toLowerCase();

                if (!allowedExtensions.includes(fileExtension)) {
                    // Display an error message
                    alert("Only .jpg, .jpeg, and .png files are allowed.");

                    // Optionally, clear the file input
                    $(this).val('');
                    return; // Exit the function early
                }

                if (file.size > maxFileSizeInBytes) {
                    // Display an error message
                    alert("File size exceeds the maximum allowed size.");

                    // Optionally, clear the file input
                    $(this).val('');
                    return; // Exit the function early
                }


                var done = function(url) {
                    image.src = url;
                    bs_modal.modal('show');
                };


                if (URL) {
                    done(URL.createObjectURL(file));
                } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = function(e) {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
            // Reset the value of the file input to trigger change event even if the same file is selected again
            $(this).val('');
        });

        bs_modal.on('shown.bs.modal', function() {
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
                dragMode: 'move',
                preview: '.preview'
            });
        }).on('hidden.bs.modal', function() {
            cropper.destroy();
            cropper = null;
        });

        $("#crop").click(function() {
            canvas = cropper.getCroppedCanvas();
            var croppedImage = canvas.toDataURL(); // Get the cropped image as base64 data URL
            $("#firstImage").attr("src",
                croppedImage); // Set the src attribute of the image element on the main page
            $("#croppedImageData").val(
                croppedImage); // Set the cropped image data to a hidden input field in the form
            bs_modal.modal('hide'); // Close the modal
            // $("#mainPage").show(); // Show the submit button on the main page
        });

        document.getElementById('chooseImageButton').addEventListener('click', function() {
            document.getElementById('imageInput').click();
        })
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Cleave.js untuk input biaya
            new Cleave('#productcost', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                delimiter: '.', // Pemisah ribuan
                numeralDecimalMark: ',', // Pemisah desimal
                numeralDecimalScale: 2, // Dua digit desimal
                prefix: 'Rp ', // Prefix mata uang
                rawValueTrimPrefix: true // Hapus prefix saat mendapatkan nilai mentah
            });

            // Inisialisasi Cleave.js untuk input harga
            new Cleave('#productprice', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                delimiter: '.', // Pemisah ribuan
                numeralDecimalMark: ',', // Pemisah desimal
                numeralDecimalScale: 2, // Dua digit desimal
                prefix: 'Rp ', // Prefix mata uang
                rawValueTrimPrefix: true // Hapus prefix saat mendapatkan nilai mentah
            });
        });
        document.querySelector('#form').addEventListener('submit', function(event) {
            var costInput = document.getElementById('productcost');
            // Menghapus format dari input biaya
            var cleanedCostValue = costInput.value.replace(/[Rp\s.]/g, '').replace(',', '.');
            costInput.value = cleanedCostValue;

            var priceInput = document.getElementById('productprice');
            // Menghapus format dari input harga
            var cleanedPriceValue = priceInput.value.replace(/[Rp\s.]/g, '').replace(',', '.');
            priceInput.value = cleanedPriceValue;
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

            // Inisialisasi Cleave.js untuk input biaya dan harga
            function initializeCleaveForVariant(row) {
                new Cleave(row.querySelector('.variant-cost input'), {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    delimiter: '.',
                    numeralDecimalMark: ',',
                    numeralDecimalScale: 2, // Dua digit desimal
                    prefix: 'Rp ', // Prefix mata uang
                    rawValueTrimPrefix: true

                });

                new Cleave(row.querySelector('.variant-price input'), {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    delimiter: '.',
                    numeralDecimalMark: ',',
                    numeralDecimalScale: 2, // Dua digit desimal
                    prefix: 'Rp ',
                    rawValueTrimPrefix: true
                });
            }

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
    <td><input required class="form-control" type="text" style="border-color: #DF4141;" value="${variantName}" name="variants[name]"></td>
    <td contenteditable="true" class="variant-code"><input required class="form-control" type="text" style="border-color: #DF4141;" name="variants[code]"></td>
    <td contenteditable="true" class="variant-cost"><input required class="form-control" type="text" style="border-color: #DF4141;"  name="variants[cost]"></td>
    <td contenteditable="true" class="variant-price"><input required class="form-control" type="text" style="border-color: #DF4141;" name="variants[price]"></td>
    <td>
        <button type="button" class="btn btn-soft-warning delete-variant">Delete</button>
    </td>
`;
                variantTableBody.appendChild(newRow);

                // Inisialisasi Cleave.js untuk input biaya dan harga pada baris baru
                initializeCleaveForVariant(newRow);

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

            function cleanNumericValue(value) {
                return value.replace(/[^\d,]/g, '').replace(',',
                    '.'); // Menghapus karakter selain angka dan koma, ganti koma dengan titik
            }
            // Menyimpan semua kode dalam array untuk memeriksanya
            var codes = [];
            rows.forEach(function(row) {
                var variantName = row.cells[0].querySelector('input').value;
                var variantCode = row.cells[1].querySelector('input').value;
                var variantCost = cleanNumericValue(row.cells[2].querySelector('input').value);
                var variantPrice = cleanNumericValue(row.cells[3].querySelector('input').value);

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
        $(document).ready(function() {
            // Inisialisasi Select2 dengan custom matcher
            $('#category').select2({
                placeholder: $(this).data('placeholder'),
                allowClear: true,
                matcher: function(params, data) {
                    // If there are no search terms, return all data
                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    // If there is no 'text' or 'data-code' attribute, return null
                    if (typeof data.text === 'undefined' || typeof $(data.element).data('code') ===
                        'undefined') {
                        return null;
                    }

                    // Custom search logic: search in both text and data-code
                    var term = params.term.toLowerCase();
                    var text = data.text.toLowerCase();
                    var code = $(data.element).data('code').toString().toLowerCase();

                    if (text.indexOf(term) > -1 || code.indexOf(term) > -1) {
                        return data;
                    }

                    // Return null if the term should not be displayed
                    return null;
                }
            });

            // Tambahkan event listener untuk fokus pada input pencarian saat dropdown dibuka
            $('#category').on('select2:open', function() {
                setTimeout(function() {
                    document.querySelector('.select2-search__field').focus();
                }, 100); // Penundaan 100ms sebelum fokus pada input pencarian
            });
        });
    </script>
@endpush
