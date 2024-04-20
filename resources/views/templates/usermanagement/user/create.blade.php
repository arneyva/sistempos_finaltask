@extends('templates.main')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css"  />

<style type="text/css">
    img {
        display: block;
        max-width: 100%;
    }

    .preview {
        overflow: hidden;
        width: 160px;
        height: 160px;
        margin: 10px;
        border: 1px solid red;
    }
</style>

@section('content')
<div class="mt-3" style="justify-content-center">
    @include('templates.alert')
</div>

<div class="col-xl-3 col-lg-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="header-title">
                <h4 class="card-title">Add New User</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="profile-img-edit position-relative">
                    <img src="/hopeui/html/assets/images/avatars/no_avatar.png" alt="profile-pic" class="theme-color-default-img profile-pic rounded avatar-100">
                    <button type="button" class="upload-icone bg-primary" id="chooseImageButton">
                        <svg class="upload-button icon-14" width="14" viewBox="0 0 24 24">
                            <path fill="#ffffff" d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z" />
                        </svg>
                        <input type="file" name="image" class="image" id="imageInput" style="display: none;">
                    </button>
                </div>
                <input type="hidden" id="croppedImageData" name="avatar">
                <div class="img-extension mt-3">
                    <div class="d-inline-block align-items-center">
                        <span>Only</span>
                        <a href="">.jpg</a>
                        <a href="">.png</a>
                        <a href="">.jpeg</a>
                        <span>allowed</span>
                    </div>
                </div>
                <div id="mainPage" style="display: none;">
                    <img id="croppedImage" src="" alt="Cropped Image" class="theme-color-default-img profile-pic rounded avatar-100">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">User Role:</label>
                <select name="type" class="selectpicker form-control" data-style="py-0">
                    <option>Select</option>
                    <option>Web Designer</option>
                    <option>Web Developer</option>
                    <option>Tester</option>
                    <option>Php Developer</option>
                    <option>Ios Developer</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="furl">Facebook Url:</label>
                <input type="text" class="form-control" id="furl" placeholder="Facebook Url">
            </div>
            <div class="form-group">
                <label class="form-label" for="turl">Twitter Url:</label>
                <input type="text" class="form-control" id="turl" placeholder="Twitter Url">
            </div>
            <div class="form-group">
                <label class="form-label" for="instaurl">Instagram Url:</label>
                <input type="text" class="form-control" id="instaurl" placeholder="Instagram Url">
            </div>
            <div class="form-group mb-0">
                <label class="form-label" for="lurl">Linkedin Url:</label>
                <input type="text" class="form-control" id="lurl" placeholder="Linkedin Url">
            </div>
        </div>
    </div>
</div>

<div class="col-xl-9 col-lg-8">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="header-title">
                <h4 class="card-title">New User Information</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="new-user-info">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label" for="fname">First Name:</label>
                        <input type="text" class="form-control" id="fname" placeholder="First Name">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label" for="lname">Last Name:</label>
                        <input type="text" class="form-control" id="lname" placeholder="Last Name">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label" for="add1">Street Address 1:</label>
                        <input type="text" class="form-control" id="add1" placeholder="Street Address 1">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label" for="add2">Street Address 2:</label>
                        <input type="text" class="form-control" id="add2" placeholder="Street Address 2">
                    </div>
                    <div class="form-group col-md-12">
                        <label class="form-label" for="cname">Company Name:</label>
                        <input type="text" class="form-control" id="cname" placeholder="Company Name">
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label">Country:</label>
                        <select name="type" class="selectpicker form-control" data-style="py-0">
                            <option>Select Country</option>
                            <option>Caneda</option>
                            <option>Noida</option>
                            <option>USA</option>
                            <option>India</option>
                            <option>Africa</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label" for="mobno">Mobile Number:</label>
                        <input type="text" class="form-control" id="mobno" placeholder="Mobile Number">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label" for="altconno">Alternate Contact:</label>
                        <input type="text" class="form-control" id="altconno" placeholder="Alternate Contact">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label" for="email">Email:</label>
                        <input type="email" class="form-control" id="email" placeholder="Email">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label" for="pno">Pin Code:</label>
                        <input type="text" class="form-control" id="pno" placeholder="Pin Code">
                    </div>
                    <div class="form-group col-md-12">
                        <label class="form-label" for="city">Town/City:</label>
                        <input type="text" class="form-control" id="city" placeholder="Town/City">
                    </div>
                </div>
                <hr>
                <h5 class="mb-3">Security</h5>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="form-label" for="uname">User Name:</label>
                        <input type="text" class="form-control" id="uname" placeholder="User Name">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label" for="pass">Password:</label>
                        <input type="password" class="form-control" id="pass" placeholder="Password">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label" for="rpass">Repeat Password:</label>
                        <input type="password" class="form-control" id="rpass" placeholder="Repeat Password ">
                    </div>
                </div>
                <div class="checkbox">
                    <label class="form-label"><input class="form-check-input me-2" type="checkbox" value="" id="flexCheckChecked">Enable Two-Factor-Authentication</label>
                </div>
                <button type="submit" class="btn btn-primary">Add New User</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal for image preview and cropping -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalLabel">Crop Image</h5>
        </div>
        <div class="modal-body">
            <div class="img-container">
                <div class="row" style="height: 300px;">
                    <div class="col-md-8">  
                        <!-- Default image where we will set the src via jQuery -->
                        <img id="image">
                    </div>
                    <div class="col-md-4">
                        <div class="preview"></div>
                    </div>
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
@endsection
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
    var bs_modal = $('#modal');
    var image = document.getElementById('image');
    var cropper, reader, file;

    $("body").on("change", ".image", function(e) {
    var files = e.target.files;
    var done = function(url) {
        image.src = url;
        bs_modal.modal('show');
    };

    if (files && files.length > 0) {
        file = files[0];

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
            preview: '.preview'
        });
    }).on('hidden.bs.modal', function() {
        cropper.destroy();
        cropper = null;
    });

    $("#crop").click(function() {
        canvas = cropper.getCroppedCanvas({
            width: 160,
            height: 160,
        });
        var croppedImage = canvas.toDataURL(); // Get the cropped image as base64 data URL
        $("#croppedImage").attr("src", croppedImage); // Set the src attribute of the image element on the main page
        $("#croppedImageData").val(croppedImage); // Set the cropped image data to a hidden input field in the form
        bs_modal.modal('hide'); // Close the modal
        $("#mainPage").show(); // Show the submit button on the main page
    });

    document.getElementById('chooseImageButton').addEventListener('click', function () {
        document.getElementById('imageInput').click();
    })

</script>
@endpush