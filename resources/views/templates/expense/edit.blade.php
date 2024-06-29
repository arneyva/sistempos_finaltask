@extends('templates.main')

@section('pages_title')
<h1>Edit Expense {{ $expense->Ref}}</h1>
<p>Do Something with {{ $expense->Ref}} data</p>
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
    <form action="{{ route('expenses.update', $expense['id']) }}" method="POST" class="row" enctype="multipart/form-data">
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
                    <h4 class="card-title">Edit Expense</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="name">Date:</label>
                        <input type="date" value="{{ $dateValue }}" class="form-control bg-transparent @error('date') is-invalid @enderror"
                            id="name" name="date" required>
                        <small class=" text-danger font-italic">
                            @error('date')
                                {{ $message }}
                            @enderror
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Category:</label>
                        <select name="category_id" class="form-select" data-style="py-0" id="workLocation">
                            <option selected disabled style="display:none !important" value="">Expense Category</option>
                            @foreach ($expense_category as $expencat)
                                <option value="{{ $expencat->id }}" {{  $expense->expense_category_id == $expencat->id ? 'selected' : '' }}>{{ $expencat->name }}</option>
                            @endforeach
                        </select>
                        <small class=" text-danger font-italic">
                            @error('category_id')
                                {{ $message }}
                            @enderror
                        </small>
                    </div>
                    <div class="new-user-info">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="form-label" for="cname">Warehouse:</label>
                                <select name="warehouse_id" class="form-select" data-style="py-0" id="workLocation">
                                    <option selected disabled style="display:none !important" value="">Expense Category</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ $expense->warehouse_id == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                <small class=" text-danger font-italic">
                                    @error('warehouse_id')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="customFile1" class="form-label custom-file-input">Choose file</label>
                                <input class="form-control mb-3" type="file" onchange="checkFileSize(this)" name="file_pendukung">
                                <small class=" text-danger font-italic">
                                    @error('file_pendukung')
                                        {{ $message }}
                                    @enderror
                                </small>
                                <p><strong>Download Old File: </strong><a href="{{ route('expenses.file', $expense['id']) }}">Download</a></p>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label" for="fname">Expense Detail:</label>
                                <textarea class="form-control" name="details" placeholder="Expense Detail" required>{{ $expense->details}}</textarea>
                                <small class=" text-danger font-italic">
                                    @error('details')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label" for="fname">Amount :</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">Rp</span>
                                    <input type="tel" id="amount" aria-label="Username" name="amount" class="form-control bg-transparent @error('amount') is-invalid @enderror"  value="{{ $expense->amount }}" aria-describedby="basic-addon1" required>
                                    <small class=" text-danger font-italic">
                                        @error('amount')
                                            {{ $message }}
                                        @enderror
                                    </small>
                                </div>
                            </div>
                            <div class="form-group col-md-6 mt-4">
                                <label class="form-label" for="lname">Status :</label>
                                <select name="status" class="form-select"  data-style="py-0" >
                                    <option selected disabled hidden value="">Status</option>
                                    @role('superadmin')  
                                    <option value="0" {{ old('status', $expense->status) == 0 ? 'selected' : '' }}>Pending</option>
                                    <option value="1" {{ old('status', $expense->status) == 1 ? 'selected' : '' }}>Agreed</option>
                                    <option value="2" {{ old('status', $expense->status) == 2 ? 'selected' : '' }}>Canceled</option>
                                    @endrole
                                    @hasanyrole('staff|inventaris')
                                    @if($expense->status == 0)
                                    <option value="0" {{ old('status', $expense->status) == 0 ? 'selected' : '' }}>Pending</option>
                                    <option value="2" {{ old('status', $expense->status) == 2 ? 'selected' : '' }}>Canceled</option>
                                    @elseif($expense->status == 2)
                                    <option value="2" {{ old('status', $expense->status) == 2 ? 'selected' : '' }}>Canceled</option>
                                    @elseif($expense->status == 1)
                                    <option value="1" {{ old('status', $expense->status) == 1 ? 'selected' : '' }}>Agreed</option>
                                    @endif
                                    @endhasanyrole
                                </select>
                                <small class=" text-danger font-italic">
                                    @error('status')
                                        {{ $message }}
                                    @enderror
                                </small>
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
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@latest"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        new AutoNumeric('#amount', 'commaDecimalCharDotSeparator');
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
    function checkFileSize(input) {
        const maxFileSize = 10 * 1024 * 1024; // 2MB
        if (input.files[0].size > maxFileSize) {
            alert("Max File Size is 10 MB");
            input.value = ""; // 입력을 초기화합니다.
        }
    }
</script>

    <script>
        var bs_modal = $('#modal');
        var image = document.getElementById('image');
        var cropper, reader, file;


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
