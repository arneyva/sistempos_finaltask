@extends('templates.main')

@section('pages_title')
<h1>Create Supplier</h1>
<p>Create new user supplier</p>
@endsection

@push('style')
<link rel="stylesheet" href="{{ asset('hopeui/html/assets/css/checkbox.css') }}">
@endpush

@section('content')
<style type="text/css">
    .background {
        position: fixed; /* atau 'absolute', tergantung kebutuhan */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Warna gelap dengan transparansi */
        z-index: 1; /* Pastikan lebih tinggi dari elemen lain kecuali modal */
    }

    .overlay {
        z-index: 2; /* Pastikan lebih tinggi dari elemen lain kecuali modal */
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

    /* Custom CSS to adjust the Bootstrap media query breakpoints */
    @media (min-width: 768px) {
        /* Adjust the large (lg) screen breakpoint */
        .modal-lg {
            --bs-modal-width: 700px; /* Set your desired minimum width for large screens (lg) */
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
<div class="col-lg-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="header-title">
                <h4 class="card-title">General Information</h4>
            </div>
        </div>
        <form action="{{ route('hrm.shifts.store') }}" method="POST">
        @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <label for="name">Nama Shift</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col">
                        <label for="location">Lokasi</label>
                        <select class="form-control" id="location" name="location[]" multiple>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                <h4 class="card-title">Days Shift</h4>
                </div>
            </div>
                <div class="card-body py-4 tab-pane fade active show" id="days">
                    <div class="row">
                        @foreach($days as $day)
                        <div class="col-md-12 mb-2">
                            <div class="checkbox-wrapper-46" id="{{ $day }}-wrapper">
                                <input type="checkbox" id="{{ $day }}" name="{{ $day }}" value="1" class="inp-cbx" />
                                <label for="{{ $day }}" class="cbx">
                                    <span>
                                        <svg viewBox="0 0 12 10" height="10px" width="12px">
                                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                        </svg>
                                    </span>
                                    <span>{{ $day }}</span>
                                </label>
                            </div>
                            <div id="{{ $day }}-times" class="mt-2 mb-3" style="display: none; ">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="{{ $day }}_in">In-Time</label>
                                        <input type="time" class="form-control" id="{{ $day }}_in" name="{{ $day }}_in" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="{{ $day }}_out">Out-Time</label>
                                        <input type="time" class="form-control" id="{{ $day }}_out" name="{{ $day }}_out" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            <div class="card-footer" style="float: right;">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script type="text/javascript" src="{{ asset('hopeui/html/assets/js/multiselect-dropdown.js') }}"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        days.forEach(day => {
            const checkbox = document.getElementById(day);
            const times = document.getElementById(day + '-times');
            const inn = document.getElementById(day + '_in');
            const out = document.getElementById(day + '_out');
            const wrapper = document.getElementById(day + '-wrapper');

            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    times.style.display = 'block';
                    wrapper.classList.add('mt-3');
                } else {
                    wrapper.classList.remove('mt-3');
                    times.style.display = 'none';
                }
            });
        });
    });
    </script>

    <script>
        $(document).ready(function() {
            $('#role').on('change', function() {
                var roleId = $(this).val(); // Get the selected role ID using jQuery
                var workLocation = $('#workLocation');
                var outletAccess1 = $('#outletAccess1');
                var outletAccess2 = $('#outletAccess2');
                var outletAccess = $('#outletAccess');
                var options = workLocation.find('option');
                var warehouseOption = [1]; // Assuming warehouse options have value 1

                workLocation.removeAttr("disabled"); // Use removeAttr to remove the disabled attribute
                options.prop('disabled', false); // Enable all options first

        if (roleId === "1") {
            workLocation.val(options.eq(0).val());
            outletAccess.val(null);
            workLocation.attr("disabled", "disabled");
            outletAccess2.attr("style", "display:none");
            outletAccess1.removeAttr("style");
        } else if (roleId === "2") {
            workLocation.val(options.eq(0).val());
            outletAccess.val(null);
            options.each(function() {
var option = $(this);
                if (!warehouseOption.includes(parseInt(option.val()))) {
                    option.prop('hidden', true);
                }
                if (warehouseOption.includes(parseInt(option.val()))) {
                    option.prop('hidden', false);
                }
            });
            outletAccess1.attr("style", "display:none");
            outletAccess2.removeAttr("style");
        } else if (roleId === "3") {
            workLocation.val(options.eq(0).val());
            outletAccess.val(null);
            options.each(function() {
                var option = $(this);
                if (warehouseOption.includes(parseInt(option.val()))) {
                    option.prop('hidden', true);
                }
                if (!warehouseOption.includes(parseInt(option.val()))) {
                    option.prop('hidden', false);
                        }
                    });
                    outletAccess2.attr("style", "display:none");
                    outletAccess1.removeAttr("style");
                }
            });
        });
    </script>
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
