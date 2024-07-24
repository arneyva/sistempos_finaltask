@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Company') }} {{ __('Profile') }}</h1>
    <p>{{ __('Configure your company profile') }} </p>
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
                            <h4 class="card-title">{{ __('Update Company Profile') }}</h4>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('settings.company.update', $company->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="name">{{ __('Company') }} {{ __('Name') }}
                                        *</label>
                                    <input type="text" class="form-control" id="name" required
                                        placeholder="input name" name="CompanyName" value="{{ $company->CompanyName }}">
                                    @error('CompanyName')
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
                                    <label class="form-label" for="tax">{{ __('Company') }}
                                        {{ __('Phone') }}</label>
                                    <div class="form-group input-group">
                                        <input type="text" class="form-control" id="tax" aria-label="Username"
                                            aria-describedby="basic-addon1" required placeholder="input tax"
                                            name="CompanyPhone" value="{{ $company->CompanyPhone }}">
                                    </div>
                                    @error('CompanyPhone')
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
                                    <label class="form-label" for="codebaseproduct">{{ __('Company') }}
                                        {{ __('Email') }}
                                        *</label>
                                    <input type="text" class="form-control" id="codebaseproduct" required
                                        placeholder="input code" name="email" value="{{ $company->email }}">
                                    @error('email')
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
                                    <label class="form-label" for="codebaseproduct">Email App Password (for server)
                                        *</label>
                                    <input type="password" class="form-control" id="codebaseproduct"
                                        placeholder="{{ empty($company->server_password) ? "App Password doesn't exist" : 'change app password' }}"
                                        name="server_password" value="{{ $company->server_password }}">
                                    @error('server_password')
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
                                    <label class="form-label" for="description">{{ __('Company') }}
                                        {{ __('Address') }}</label>
                                    <input type="text" class="form-control" id="description"
                                        placeholder="a few words..." name="CompanyAdress"
                                        value="{{ $company->CompanyAdress }}">
                                    @error('CompanyAdress')
                                        <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                            role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                            <span style="margin-left: 3px"> {{ $message }}</span>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                                aria-label="Close"
                                                style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                        </div>
                                    @enderror
                                </div>
                                {{-- <div class="col-md-6 mb-3">
                                    <label class="form-label" for="type">{{ __('Default Language') }}</label>
                                    <select class="form-select" id="type" name="type">
                                        <option selected disabled value="">{{ __('Choose...') }}</option>
                                        <option value="is_single" {{ old('type') == 'is_single' ? 'selected' : '' }}>
                                            {{ __('English Language') }}</option>
                                        <option value="is_variant" {{ old('type') == 'is_variant' ? 'selected' : '' }}>
                                            {{ __('Indonesian Language') }}</option>
                                    </select>
                                </div> --}}
                                <div class="col-md-6 mb-3">
                                    {{-- <div class="form-group mt-2" style="margin-top: 10px"> --}}
                                    <button class="btn btn-primary" type="submit"
                                        style="margin-top: 30px;">{{ __('Save changes') }}</button>
                                    {{-- </div> --}}
                                </div>
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
                    <h4 class="card-title">{{ __('Update') }} {{ __('Logo') }}</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="card-header pb-4 border-dashed rounded" style="border: 1px dashed rgb(94, 87, 87);">
                        <div class="profile-img-edit position-relative d-flex justify-content-center align-items-center">
                            <img src="{{ asset('hopeui/html/assets/images/avatars/' . $company['logo']) }}"
                                id="firstImage" alt="profile-pic"
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
                        <!-- <div class="img-container"> -->
                        <!-- <div class="row" style="height: 300px;"> -->
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Default image where we will set the src via jQuery -->
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
                        <!-- </div> -->
                        <!-- </div> -->
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
@endpush
