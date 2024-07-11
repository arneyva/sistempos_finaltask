@extends('templates.main')

@section('pages_title')
    <h1>{{ __('All Product Brand') }}</h1>
    <p>{{ __('Do Something with all your product brands') }}</p>
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
</style>
@section('content')
    <div class="col-sm-12">
        <div class="mt-3" style="justify-content-center">
            <!-- @include('templates.alert') -->
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">{{ __('All Brand') }}</h4>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
                <form action="{{ route('product.brand.index') }}" method="GET">
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
                        <input type="search" class="form-control" name="search" value="{{ request()->input('search') }}"
                            placeholder="{{ __('Search...') }}">
                    </div>
                </form>
                <div class="header-title">
                    @role('superadmin|inventaris')
                        <button type="button" class="btn btn-soft-primary" data-bs-toggle="modal"
                            data-bs-target="#createModal">
                            {{ __('Create +') }}
                        </button>
                        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">{{ __('Create') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('product.brand.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="col mb-3">
                                                <label class="form-label" for="name">{{ __('Brand Name*') }}</label>
                                                <input type="text" class="form-control" id="name" required
                                                    placeholder="{{ __('input brands') }}" name="name">
                                            </div>
                                            <div class="col mb-3">
                                                <label class="form-label" for="description">{{ __('Description*') }}</label>
                                                <input type="text" class="form-control" id="description" required
                                                    placeholder="{{ __('input description') }}" name="description">
                                            </div>
                                            <div class="col mb-3">
                                                <label class="form-label" for="image">{{ __('Image') }}</label>
                                                <div class="form-group">
                                                    <div class="card-header pb-4 border-dashed rounded"
                                                        style="border: 1px dashed rgb(94, 87, 87);">
                                                        <div
                                                            class="profile-img-edit position-relative d-flex justify-content-center align-items-center">
                                                            <img src="/hopeui/html/assets/images/brands/image.png"
                                                                id="firstImage" alt="profile-pic"
                                                                class="theme-color-default-img profile-pic rounded avatar-100">
                                                            <button type="button" class="upload-icone bg-primary"
                                                                id="chooseImageButton">
                                                                <svg class="upload-button icon-14" width="14"
                                                                    viewBox="0 0 24 24">
                                                                    <path fill="#ffffff"
                                                                        d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z" />
                                                                </svg>
                                                                <input type="file" name="image" class="image"
                                                                    id="imageInput" style="display: none;">
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
                                            <!-- Modal for image preview and cropping -->
                                            <div class="modal fade" id="modal" data-bs-backdrop="static" tabindex="-1"
                                                role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="background">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg overlay"
                                                        role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="modalLabel">Crop Image</h5>
                                                            </div>
                                                            <div class="modal-body">
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
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary"
                                                                    id="crop">Save changes</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                                        <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endrole
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>{{ __('Brand Name') }}</th>
                                <th>{{ __('Description') }}</th>
                                @role('superadmin|inventaris')
                                    <th>{{ __('Actions') }}</th>
                                @endrole
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($brands as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img class="rounded img-fluid avatar-40 me-3 bg-soft-primary"
                                                src="{{ asset('hopeui/html/assets/images/brands/' . $item->image) }}"
                                                alt="profile">
                                            <h6>{{ $item->name }}</h6>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $item->description }}
                                    </td>
                                    @role('superadmin|inventaris')
                                        <td>
                                            <div class="inline">
                                                <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $item->id }}">
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
                                                <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1"
                                                    aria-labelledby="exampleModalLabel{{ $item->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="exampleModalLabel{{ $item->id }}">
                                                                    {{ __('Update Brand') }}</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="{{ route('product.brand.update', $item->id) }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="col mb-3">
                                                                        <label class="form-label"
                                                                            for="editname{{ $item->id }}">{{ __('Brand Name*') }}</label>
                                                                        <input type="text" class="form-control"
                                                                            id="editname{{ $item->id }}" required
                                                                            placeholder="{{ __('input brands') }}"
                                                                            name="name" value="{{ $item->name }}">
                                                                    </div>
                                                                    <div class="col mb-3">
                                                                        <label class="form-label"
                                                                            for="editdescription{{ $item->id }}">{{ __('Description*') }}
                                                                        </label>
                                                                        <input type="text" class="form-control"
                                                                            id="editdescription{{ $item->id }}" required
                                                                            placeholder="{{ __('input description') }}"
                                                                            name="description"
                                                                            value="{{ $item->description }}">
                                                                    </div>
                                                                    <div class="col mb-3">
                                                                        <label class="form-label"
                                                                            for="editimage{{ $item->id }}">{{ __('Image') }}</label>
                                                                        <div class="form-group">
                                                                            <div class="card-header pb-4 border-dashed rounded"
                                                                                style="border: 1px dashed rgb(94, 87, 87);">
                                                                                <div
                                                                                    class="profile-img-edit position-relative d-flex justify-content-center align-items-center">
                                                                                    <img src="{{ asset('hopeui/html/assets/images/brands/' . $item->image) }}"
                                                                                        id="firstImageUpdate_{{ $item->id }}"
                                                                                        alt="profile-pic"
                                                                                        class="theme-color-default-img profile-pic rounded avatar-100">
                                                                                    <button type="button"
                                                                                        class="upload-icone bg-primary"
                                                                                        id="chooseImageButtonUpdate_{{ $item->id }}">
                                                                                        <svg class="upload-button icon-14"
                                                                                            width="14"
                                                                                            viewBox="0 0 24 24">
                                                                                            <path fill="#ffffff"
                                                                                                d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z" />
                                                                                        </svg>
                                                                                        <input type="file" name="image"
                                                                                            class="imageUpdate"
                                                                                            id="imageInputUpdate_{{ $item->id }}"
                                                                                            style="display: none;">
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden"
                                                                                id="croppedImageDataUpdate_{{ $item->id }}"
                                                                                name="avatar">
                                                                            <div class="img-extension mt-3">
                                                                                <div
                                                                                    class="d-inline-block align-items-center py-1">
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

                                                                    <!-- Modal for image preview and cropping -->
                                                                    <div class="modal fade"
                                                                        id="modalUpdate_{{ $item->id }}"
                                                                        data-bs-backdrop="static" tabindex="-1"
                                                                        role="dialog"
                                                                        aria-labelledby="modalLabelUpdate_{{ $item->id }}"
                                                                        aria-hidden="true">
                                                                        <div class="background">
                                                                            <div class="modal-dialog modal-dialog-centered modal-lg overlay"
                                                                                role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title"
                                                                                            id="modalLabelUpdate_{{ $item->id }}">
                                                                                            Crop Image</h5>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <div class="row">
                                                                                            <div class="col-md-8">
                                                                                                <!-- Default image where we will set the src via jQuery -->
                                                                                                <div class="docs-demo">
                                                                                                    <div
                                                                                                        class="image-container">
                                                                                                        <img
                                                                                                            id="imageUpdate_{{ $item->id }}">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-4 px-0">
                                                                                                <div
                                                                                                    class="preview previewUpdate_{{ $item->id }}">
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button"
                                                                                            class="btn btn-secondary"
                                                                                            data-bs-dismiss="modal">Close</button>
                                                                                        <button type="button"
                                                                                            class="btn btn-primary"
                                                                                            id="cropUpdate_{{ $item->id }}">Save
                                                                                            changes</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>




                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">{{ __('Save changes') }}</button>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- button delete --}}
                                                <button type="button" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $item->id }}"
                                                    style="border: none; background: none; padding: 0; margin: 0;">
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
                                                </button>
                                                {{-- modal delete --}}
                                                <div class="modal fade" id="deleteModal{{ $item->id }}"
                                                    data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                                    aria-labelledby="deleteModalLabel{{ $item->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteModalLabel{{ $item->id }}">
                                                                    {{ $item->name }}
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>{{ __('Are you sure you want to delete this data?') }}</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                                <form action="{{ route('product.brand.destroy', $item->id) }}"
                                                                    method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-primary">{{ __('Delete') }}</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    @endrole
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-right: 10px; margin-top:10px">
                        {{ $brands->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
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
        $(document).ready(function() {
            @foreach ($brands as $item)
                (function(itemId) {
                    var bs_modal_update = $('#modalUpdate_' + itemId);
                    var image_update = document.getElementById('imageUpdate_' + itemId);
                    var cropper_update, reader_update, file_update;

                    $("body").on("change", "#imageInputUpdate_" + itemId, function(e) {
                        var files_update = e.target.files;
                        var maxFileSizeInBytes_update = 10 * 1024 * 1024;
                        var allowedExtensions_update = ['jpg', 'jpeg', 'png'];

                        if (files_update && files_update.length > 0) {
                            file_update = files_update[0];

                            var fileExtension_update = file_update.name.split('.').pop().toLowerCase();

                            if (!allowedExtensions_update.includes(fileExtension_update)) {
                                // Display an error message
                                alert("Only .jpg, .jpeg, and .png files are allowed.");

                                // Optionally, clear the file input
                                $(this).val('');
                                return; // Exit the function early
                            }

                            if (file_update.size > maxFileSizeInBytes_update) {
                                // Display an error message
                                alert("File size exceeds the maximum allowed size.");

                                // Optionally, clear the file input
                                $(this).val('');
                                return; // Exit the function early
                            }

                            var done_update = function(url_update) {
                                image_update.src = url_update;
                                bs_modal_update.modal('show');
                            };

                            if (URL) {
                                done_update(URL.createObjectURL(file_update));
                            } else if (FileReader) {
                                reader_update = new FileReader();
                                reader_update.onload = function(e) {
                                    done_update(reader_update.result);
                                };
                                reader_update.readAsDataURL(file_update);
                            }
                        }
                        // Reset the value of the file input to trigger change event even if the same file is selected again
                        $(this).val('');
                    });

                    bs_modal_update.on('shown.bs.modal', function() {
                        cropper_update = new Cropper(image_update, {
                            aspectRatio: 1,
                            viewMode: 1,
                            autoCropArea: 1,
                            dragMode: 'move',
                            preview: '.previewUpdate_' + itemId
                        });
                    }).on('hidden.bs.modal', function() {
                        cropper_update.destroy();
                        cropper_update = null;
                    });

                    $("#cropUpdate_" + itemId).click(function() {
                        var canvas_update = cropper_update.getCroppedCanvas();
                        var croppedImage_update = canvas_update
                            .toDataURL(); // Get the cropped image as base64 data URL
                        $("#firstImageUpdate_" + itemId).attr("src",
                            croppedImage_update
                        ); // Set the src attribute of the image element on the main page
                        $("#croppedImageDataUpdate_" + itemId).val(
                            croppedImage_update
                        ); // Set the cropped image data to a hidden input field in the form
                        bs_modal_update.modal('hide'); // Close the modal
                    });

                    document.getElementById('chooseImageButtonUpdate_' + itemId).addEventListener('click',
                        function() {
                            document.getElementById('imageInputUpdate_' + itemId).click();
                        });
                })({{ $item->id }});
            @endforeach
        });
    </script>
@endpush
