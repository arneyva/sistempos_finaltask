@extends('templates.main')

@section('pages_title')
<h1>Edit Supplier {{ $provider->name}}</h1>
<p>Do Something with {{ $provider->name}} data</p>
@endsection

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
    <form action="{{ route('people.suppliers.update', $provider['id']) }}" method="POST" class="row" enctype="multipart/form-data">
        @csrf
        @method('patch')
        <!-- <div class="mt-3" style="justify-content-center">
            @include('templates.alert')
        </div> -->

        <div class="col-sm-12">
            <div class="mt-3">
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                    <h4 class="card-title">New Supplier Information</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="name">Company Name:</label>
                        <input value="{{ old('name', $provider->name) }}" type="text" class="form-control bg-transparent @error('name') is-invalid @enderror"
                            id="name" name="name" placeholder="Company Name" required>
                        <small class=" text-danger font-italic">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Email:</label>
                        <input value="{{ old('email', $provider->email) }}" type="email" class="form-control bg-transparent @error('email') is-invalid @enderror"
                            id="email" name="email" placeholder="Email" required>
                        <small class=" text-danger font-italic">
                            @error('email')
                                {{ $message }}
                            @enderror
                        </small>
                    </div>
                    <div class="new-user-info">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label" for="cname">Phone:</label>
                                <input value="{{ old('phone', $provider->phone) }}" type="tel" name="phone"
                                    class="form-control bg-transparent @error('phone') is-invalid @enderror"
                                    id="cname" placeholder="Phone" required>
                                <small class=" text-danger font-italic">
                                    @error('phone')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="fname">Contact Person:</label>
                                <input value="{{ old('nama_kontak_person', $provider->nama_kontak_person) }}" type="text" name="nama_kontak_person"
                                    class="form-control bg-transparent @error('firstname') is-invalid @enderror"
                                    id="fname" placeholder="Contact Person" required>
                                <small class=" text-danger font-italic">
                                    @error('firstname')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="lname">CP Phone:</label>
                                <input value="{{ old('nomor_kontak_person', $provider->nomor_kontak_person) }}" type="text" name="nomor_kontak_person"
                                    class="form-control bg-transparent @error('lastname') is-invalid @enderror"
                                    id="lname" placeholder="CP Phone" required>
                                <small class=" text-danger font-italic">
                                    @error('lastname')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="fname">City :</label>
                                <input value="{{ old('city', $provider->city) }}" type="text" name="city"
                                    class="form-control bg-transparent @error('firstname') is-invalid @enderror"
                                    id="fname" placeholder="City" required>
                                <small class=" text-danger font-italic">
                                    @error('firstname')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="lname">Country :</label>
                                <input value="{{ old('country', $provider->country) }}" type="text" name="country"
                                    class="form-control bg-transparent @error('lastname') is-invalid @enderror"
                                    id="lname" placeholder="Country" required>
                                <small class=" text-danger font-italic">
                                    @error('lastname')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label" for="cname">Website:</label>
                                <input value="{{ old('alamat_website', $provider->alamat_website) }}" type="text" name="alamat_website"
                                    class="form-control bg-transparent @error('phone') is-invalid @enderror"
                                    id="cname" placeholder="Website Address" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="cname">Lead Time:</label>
                                <div class="form-group input-group">
                                    <input value="{{ old('lead_time', $provider->lead_time) }}" type="tel"  id="score_to_email" aria-label="Username" name="lead_time" class="form-control bg-transparent @error('score_to_email') is-invalid @enderror" aria-describedby="basic-addon1" required>
                                    <span class="input-group-text" id="basic-addon1">Days</span>
                                    <small class=" text-danger font-italic">
                                        @error('score_to_email')
                                            {{ $message }}
                                        @enderror
                                    </small>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label" for="lname">Payment Method :</label>
                                <select name="payment_method" id="payment_method" class="form-control">
                                    <option value="" selected disabled hidden>Payment Method</option>
                                    <option value="bni" {{ old('payment_method', $provider->payment_method) == 'bni' ? 'selected' : '' }}>BNI</option>
                                    <option value="bri" {{ old('payment_method', $provider->payment_method) == 'bri' ? 'selected' : '' }}>BRI</option>
                                    <option value="mandiri" {{ old('payment_method', $provider->payment_method) == 'mandiri' ? 'selected' : '' }}>Mandiri</option>
                                    <option value="permata" {{ old('payment_method', $provider->payment_method) == 'permata' ? 'selected' : '' }}>Permata</option>
                                    <option value="bca" {{ old('payment_method', $provider->payment_method) == 'bca' ? 'selected' : '' }}>BCA</option>
                                    <option value="gopay" {{ old('payment_method', $provider->payment_method) == 'gopay' ? 'selected' : '' }}>Gopay</option>
                                    <option value="ovo" {{ old('payment_method', $provider->payment_method) == 'ovo' ? 'selected' : '' }}>OVO</option>
                                    <option value="cash" {{ old('payment_method', $provider->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label" for="lname">Payment Term :</label>
                                <select name="payment_term" id="payment_term" class="form-control">
                                    <option value="" selected disabled hidden>Payment Term</option>
                                    <option value="on_invoice" {{ old('payment_term', $provider->payment_term) == 'on_invoice' ? 'selected' : '' }}>Due on invoice</option>
                                    <option value="7_invoice" {{ old('payment_term', $provider->payment_term) == '7_invoice' ? 'selected' : '' }}>7 days after invoice</option>
                                    <option value="14_invoice" {{ old('payment_term', $provider->payment_term) == '14_invoice' ? 'selected' : '' }}>14 Days after Invoice</option>
                                    <option value="on_arrive" {{ old('payment_term', $provider->payment_term) == 'on_arrive' ? 'selected' : '' }}>Due on arrive</option>
                                    <option value="7_arrive" {{ old('payment_term', $provider->payment_term) == '7_arrive' ? 'selected' : '' }}>7 days after arrive</option>
                                    <option value="14_arrive" {{ old('payment_term', $provider->payment_term) == '14_arrive' ? 'selected' : '' }}>14 Days after arrive</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label" for="lname">Down Payment :</label>
                                <div class="input-group">
                                    <input type="tel" value="{{ old('down_payment', $provider->down_payment) }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="down_payment" name="down_payment" aria-describedby="basic-addon2">
                                    <span class="input-group-text" id="basic-addon2">%</span>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label" for="cname">Address:</label>
                                <textarea class="form-control" name="adresse" placeholder="Address" required>{{ $provider->adresse}}</textarea>
                            </div>
                        </div>
                        <div class="row">
                        </div>
                        <br>
                        <div class="" style="float: right;">
                            <button type="submit" class="btn btn-primary" >Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script type="text/javascript" src="{{ asset('hopeui/html/assets/js/multiselect-dropdown.js') }}"></script>

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
