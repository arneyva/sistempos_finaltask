@extends('templates.main')

@section('pages_title')
<h1>Edit Request</h1>
<p>Do Something with attendance data</p>
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

    .select2-selection__rendered .caption-sub-title {
        display:none;
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
    <form action="{{ route('hrm.request.update', $reqattd['id']) }}" method="POST" class="row" enctype="multipart/form-data">
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
                    <h4 class="card-title">Edit Request</h4>
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
                        <label class="form-label" for="email">Attendance:</label>
                        <select id="staffDropdown" class="form-control" name="attendance_id" style="width: 100%;" required>
                                        <option value=""></option>
                                        @foreach($attd as $data)
                                            <option 
                                                value="{{$data->id}}" 
                                                data-latein="{{$data->late_in}}"
                                                data-lateout="{{$data->late_out}}"
                                                data-status="{{$data->status}}"
                                                {{ $reqattd->attendance_id == $data->id ? 'selected' : '' }}>
                                                {{ $data->date->translatedFormat('d, F Y') }}
                                            </option>
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
                            @if($reqattd->user_id == Auth::id())
                            <div class="form-group col-md-12">
                                <label for="customFile1" class="form-label custom-file-input">Choose file</label>
                                <input class="form-control mb-3" type="file" onchange="checkFileSize(this)" name="file_pendukung">
                                <small class=" text-danger font-italic">
                                    @error('file_pendukung')
                                        {{ $message }}
                                    @enderror
                                </small>
                                @endif
                                <p><strong>Download File: </strong><a href="{{ route('hrm.request.file', $reqattd['id']) }}">Download</a></p>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="form-label" for="fname">Attendance Detail:</label>
                                <textarea class="form-control" name="details" placeholder="Expense Detail" required>{{ $reqattd->details}}</textarea>
                                <small class=" text-danger font-italic">
                                    @error('details')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>
                            
                        </div>
                        <div class="row">
                        </div>
                        <br>
                        <div class="" style="float: right;">
                            @if ($reqattd->status ==0 || $reqattd->status ==2)
                            <button type="submit" name="action" value="{{$reqattd->status}}" class="btn btn-primary" >Submit</button>
                            @endif
                            @role('superadmin')
                            @if ($reqattd->user_id !== Auth::user()->id && $reqattd->status ==0)
                            <button type="submit" name="action" value="1" class="btn btn-success" >Agreed</button>
                            <button type="submit" name="action" value="2" class="btn btn-danger" >Refused</button>
                            @endif
                            @endrole
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
        $('#staffDropdown').select2({
            placeholder: "Pick Date...",
            templateResult: formatUser,
            templateSelection: formatUser,
            matcher: customMatcher
        });

        $('#staffDropdown').on('select2:open', function() {
            $('.select2-search__field').select(); // Mengatur fokus ke kotak pencarian
        });

        function formatUser (user) {
            if (!user.id) {
                return user.text;
            }
            var status = $(user.element).data('status');
            var latein = $(user.element).data('latein');
            var lateout = $(user.element).data('latein');

            if (status == 'present') {
                var statusfinal = ''
            } else {
                var statusfinal = 'absent'
                
            }
            if (latein == 'yes') {
                var lateinstatus = 'late in'
            } else {
                var lateinstatus = ''
                
            }
            if (lateout == 'yes') {
                var lateoutstatus = 'late out'
                
            } else {
                var lateoutstatus = ''

            }

            var $user = $(
                '<div class="d-flex align-items-center">'+ 
                    '<a style="margin-right:10px;">'+'<a/>'+
                    '<div>'+
                        '<h6 class="mb-0 caption-title">'+ user.text + '</h6>'+
                        '<p class="mb-0 caption-sub-title">' +  statusfinal +
                         ' ' +  lateinstatus +
                          ' ' +  lateoutstatus +
                           '</p>'+
                    '</div>'+
                '</div>'
            );
            return $user;
        };

        function customMatcher(params, data) {
            // If there are no search terms, return all data
            if ($.trim(params.term) === '') {
                return data;
            }

            // `params.term` should be the term that is used for searching
            // `data.text` is the text that is displayed for the data object
            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                return data;
            }

            var status = $(data.element).data('status');
            var latein = $(data.element).data('latein');
            var lateout = $(data.element).data('latein');

            if (latein == 'yes') {
                var lateinstatus = 'late in'
            } else {
                var lateinstatus = ''
                
            }
            if (lateout == 'yes') {
                var lateoutstatus = 'late out'
                
            } else {
                var lateoutstatus = ''

            }
            if (status && status.toString().toLowerCase().indexOf(params.term.toLowerCase()) >= 0) {
                return data;
                }
            if (lateinstatus && lateinstatus.toString().toLowerCase().indexOf(params.term.toLowerCase()) >= 0) {
                return data;
                }
            if (lateoutstatus && lateoutstatus.toString().toLowerCase().indexOf(params.term.toLowerCase()) >= 0) {
                return data;
                }
                
            // Return `null` if the term should not be displayed
            return null;
        }
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
