@extends('templates.main')

@section('pages_title')
<h1>Edit {{ $user->firstname}} {{ $user->lastname}}</h1>
<p>Do Something with {{ $user->firstname}} {{ $user->lastname}} data</p>
@endsection

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" />
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
<form action="{{ route('people.users.update', $user['id']) }}" method="POST" class="row" enctype="multipart/form-data">
    @csrf
    @method('patch')

    <div class="col-xl-3 col-lg-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="header-title">
                <h4 class="card-title">User Profile</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="profile-img-edit position-relative">
                    <img src="/hopeui/html/assets/images/avatars/{{ $user->avatar }}" id="firstImage" alt="profile-pic" class="theme-color-default-img profile-pic rounded avatar-100">
                    <button type="button" class="upload-icone bg-primary" id="chooseImageButton">
                        <svg class="upload-button icon-14" width="14" viewBox="0 0 24 24">
                            <path fill="#ffffff" d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z" />
                        </svg>
                        <input type="file" name="image" class="image" id="imageInput" style="display: none;">
                    </button>
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
                <label class="form-label" for="email">PIN:</label>
                <input type="tel" class="form-control" value="{{ htmlspecialchars($user->pin) }}" placeholder="PIN" disabled>
            </div>
            <div class="form-group">
                <label class="form-label" for="email">Email:</label>
                <input type="email" class="form-control bg-transparent @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Email" required>
                <small class=" text-danger font-italic">
                    @error('email')
                        {{ $message }}
                    @enderror
                </small>
            </div>
<div class="form-group">
    <label class="form-label" for="password">Password:</label>
    <input type="password" class="form-control bg-transparent @error('password') is-invalid @enderror" id="password" name="NewPassword"  placeholder="New Password" >
</div>
        </div>
    </div>
</div>

<div class="col-xl-9 col-lg-8">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="header-title">
                <h4 class="card-title">User Information</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="new-user-info">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label" for="fname">First Name:</label>
                        <input type="text" name="firstname" class="form-control bg-transparent @error('firstname') is-invalid @enderror"  id="fname" value="{{ old('firstname', $user->firstname) }}" placeholder="First Name" required>
                        <small class=" text-danger font-italic">
                            @error('firstname')
                                {{ $message }}
                            @enderror
                        </small>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label" for="lname">Last Name:</label>
                        <input type="text" name="lastname" class="form-control bg-transparent @error('lastname') is-invalid @enderror"  value="{{ old('lastname', $user->lastname) }}" id="lname" placeholder="Last Name" required>
                        <small class=" text-danger font-italic">
                            @error('lastname')
                                {{ $message }}
                            @enderror
                        </small>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="form-label" for="cname">Phone:</label>
                        <input type="tel" name="phone" class="form-control bg-transparent @error('phone') is-invalid @enderror"  value="{{ old('phone', $user->phone) }}" id="cname" placeholder="Phone" required>
                        <small class=" text-danger font-italic">
                            @error('phone')
                                {{ $message }}
                            @enderror
                        </small>
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label">Gender:</label>
                        <select name="gender" class="selectpicker form-control"  data-style="py-0" >
                            <option selected disabled hidden value="">Gender</option>    
                            <option value="Laki-Laki" {{ $user->gender === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $user->gender === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        <small class=" text-danger font-italic">
                            @error('gender')
                                {{ $message }}
                            @enderror
                    </small>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label class="form-label">Role:</label>
                        <select name="role" class="selectpicker form-control"  data-style="py-0" id="role" >
                            <option selected disabled hidden value="">Role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" data-old-role="{{ $user->roles->first()->id }}" {{ $user->roles->isNotEmpty() && $user->roles->first()->id == $role->id ? 'selected' : '' }} >{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <small class=" text-danger font-italic">
                            @error('role')
                                {{ $message }}
                            @enderror
                    </small>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="form-label">Work Location:</label>
                        <select name="workLocation" class="selectpicker form-control"  data-style="py-0" id="workLocation" disabled>
                            <option selected disabled style="display:none !important" value="">Work Location</option>    
                            @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ $user->warehouses->isNotEmpty() && $user->warehouses->first()->id == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                        <small class=" text-danger font-italic">
                            @error('workLocation')
                            {{ $message }}
                            @enderror
                        </small>
                    </div>
                    <div class="form-group col-sm-6" id="outletAccess1">
                        <label class="form-label">Outlet Access:</label>
            <select class="selectpicker form-control" data-style="py-0" disabled >
                <option value="">Outlet Access</option>
            </select>
        </div>
                    <div class="form-group col-sm-6" style="display: none;" id="outletAccess2">
                        <label class="form-label">Outlet Access:</label>
            <select name="outletAccess" class="selectpicker form-control" data-style="py-0" id="outletAccess" multiple multiselect-select-all="true" multiselect-search="true" multiselect-max-items=3>
            @foreach($warehouses as $warehouse)
            @continue($warehouse->id == 1)
                            <option value="{{ $warehouse->id }}"{{ $user->warehouses->contains('id', $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach
            </select>
        </div>
                </div>
                <br>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for image preview and cropping -->
        <div class="modal fade" id="modal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
            aria-hidden="true">
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
<script type="text/javascript" src="{{asset('hopeui/html/assets/js/multiselect-dropdown.js')}}"></script>

<script>
$(document).ready(function () {
    var oldRoleId = $('#role option:selected').attr('data-old-role');
    var workLocation = $('#workLocation');
        var outletAccess1 = $('#outletAccess1');
        var outletAccess2 = $('#outletAccess2');
        var outletAccess = $('#outletAccess');
        var options = workLocation.find('option');
        var warehouseOption = [1]; // Assuming warehouse options have value 1
        
        workLocation.removeAttr("disabled"); // Use removeAttr to remove the disabled attribute
        options.prop('disabled', false); // Enable all options first

    if (oldRoleId === "1") {
        workLocation.attr("disabled", "disabled");
        outletAccess2.attr("style", "display:none");
            outletAccess1.removeAttr("style");
        }
    else if (oldRoleId === "2") {
        options.each(function() {
                var option = $(this);
                if (warehouseOption.includes(parseInt(option.val()))) {
                    option.prop('hidden', false);
                }
                if (!warehouseOption.includes(parseInt(option.val()))) {
                    option.prop('hidden', true);
                }
            });
        outletAccess1.attr("style", "display:none");
            outletAccess2.removeAttr("style");
    }
    else if (oldRoleId === "3") {
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

    $('#role').on('change', function () {
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
        $("#firstImage").attr("src", croppedImage); // Set the src attribute of the image element on the main page
        $("#croppedImageData").val(croppedImage); // Set the cropped image data to a hidden input field in the form
        bs_modal.modal('hide'); // Close the modal
        // $("#mainPage").show(); // Show the submit button on the main page
    });

    document.getElementById('chooseImageButton').addEventListener('click', function () {
        document.getElementById('imageInput').click();
    })

</script>
@endpush
