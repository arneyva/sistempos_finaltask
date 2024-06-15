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

    .select2-container .select2-selection--single {
    height: 54px; /* Atur tinggi sesuai kebutuhan */
    display: flex;
    align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 54px; /* Sesuaikan dengan tinggi yang diatur */
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 52px; /* Sesuaikan dengan tinggi yang diatur - 2px untuk padding */
    }

    .select2-container .select2-dropdown .select2-results__options {
    max-height: 220px; /* Atur tinggi maksimum sesuai kebutuhan */
    }
</style>
<div class="col-lg-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="header-title">
                <h4 class="card-title">General Information</h4>
            </div>
        </div>
        <form action="{{ route('hrm.shifts.update', $shift['id']) }}" method="POST">
        @csrf
        @method('patch')
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <label for="name">Nama Shift</label>
                        <input type="text" class="form-control" value="{{$shift->name}}" id="name" name="name" required>
                    </div>
                    <div class="col">
                        <label for="location">Lokasi</label>
                        <select class="form-control" id="location" name="location[]" multiple>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ $shift->warehouses->contains('id', $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <br>
            <div class="card-body p-0">
                <div class="row">
                    <div class="d-flex col justify-content-center">
                        <ul class=" nav nav-pills mb-0 text-center profile-tab " data-toggle="slider-tab" id="profile-pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active show" data-bs-toggle="tab" href="#days" role="tab" aria-selected="false">Days</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#users" role="tab" aria-selected="false">Users</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <div class="card-body py-4 tab-pane fade active show" id="days">
                    @php
                        $days = [
                            'monday' => 'monday_in',
                            'tuesday' => 'tuesday_in',
                            'wednesday' => 'wednesday_in',
                            'thursday' => 'thursday_in',
                            'friday' => 'friday_in',
                            'saturday' => 'saturday_in',
                            'sunday' => 'sunday_in'
                        ];
                    @endphp
                    <div class="row">
                        @foreach($days as $day => $dayName)
                        <div class="col-md-12 mb-2">
                            @if (!is_null($shift->$dayName))
                            <div class="checkbox-wrapper-46 mt-3" id="{{ $day }}-wrapper">
                                <input type="checkbox" id="{{ $day }}" name="{{ $day }}" value="1" class="inp-cbx" checked=""/>
                                @else
                                <div class="checkbox-wrapper-46" id="{{ $day }}-wrapper">
                                <input type="checkbox" id="{{ $day }}" name="{{ $day }}" value="1" class="inp-cbx" />
                                @endif
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
                                <div class="row ">
                                    <div class="col-md-6">
                                        <label for="{{ $day }}_in">In-Time</label>
                                        <input type="time" class="form-control" id="{{ $day }}_in" name="{{ $day }}_in" value="{{$shift->$dayName}}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="{{ $day }}_out">Out-Time</label>
                                        <p style="display:none;">{{$day_out=substr_replace($dayName, 'out', -2)}}</p>
                                        <input type="time" class="form-control" id="{{ $day }}_out" name="{{ $day }}_out" value="{{$shift->$day_out}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-body py-5 tab-pane fade" id="users">
                    <div class="row">
                        <div class="col">
                            <select id="itemDropdown" name="items[]" style="width: 100%;">
                                <option value=""></option>
                                @foreach($users as $item)
                                    <option value="{{ $item->id }}" data-avatar="{{ $item->avatar }}" data-pin="{{ $item->pin }}" data-role="{{substr($item->getRoleNames(), 2, -2)}}" data-email="{{ $item->email }}" data-gender="{{ $item->gender }}">
                                        {{ $item->firstname }} {{ $item->lastname }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" id="selectedItems" name="users">
                            <input type="hidden" id="selectedItemsValue" name="delete_users">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <table id="selectedItemsTable" class="table table-borderless mt-3">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Position</th>
                                        <th>Gender</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users_office_shift as $data)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="/hopeui/html/assets/images/avatars/{{ $data->avatar }}" class="rounded img-fluid avatar-40 me-3 bg-soft-primary" alt="profile">
                                                <div class="d-flex flex-column"> 
                                                    <div style="margin-bottom: 5px;">
                                                        <h6> {{ ucfirst($data->firstname) }} {{ ucfirst($data->lastname) }} </h6>
                                                    </div> 
                                                    <div>{{ $data->pin }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $data->email }}</td>
                                        <td>{{ ucfirst(substr($data->getRoleNames(), 2, -2)) }}</td>
                                        <td>{{ $data->gender }}</td>
                                        <td> 
                                            <div class="flex align-items-center list-user-action"> 
                                                <a class="btn btn-sm btn-icon btn-danger" data-value="{{$data->id}}">
                                                    <span class="btn-inner">
                                                        <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor">
                                                            <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
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
    $(document).ready(function() {
        $('#itemDropdown').select2({
            placeholder: "Add Employee...",
            templateResult: formatUser,
            templateSelected: formatUser,
            matcher: customMatcher
        });

        function formatUser (user) {
            if (!user.id) {
                return user.text;
            }
            var $user = $(
                '<div class="d-flex align-items-center"> <img class="img-fluid avatar avatar-50 avatar-rounded" src="/hopeui/html/assets/images/avatars/'+ $(user.element).data('avatar') + '"alt="profile"><a style="margin-right:10px;"><a/><div><h6 class="mb-0 caption-title">'+ user.text + '</h6><p class="mb-0 caption-sub-title">' +  $(user.element).data('pin') + '</p></div></div>'
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

            var pin = $(data.element).data('pin');
            if (pin && pin.toString().toLowerCase().indexOf(params.term.toLowerCase()) >= 0) {
                return data;
                }
                
            // Return `null` if the term should not be displayed
            return null;
        }
        });
</script>

<script>
    $(document).ready(function() {
        var selectedItems = [];
        var selectedItemsValue = [];
        $('#itemDropdown').change(function() {
            var selectedValue = $(this).val();
            if (selectedValue) {
                var name = $('#itemDropdown option:selected').text();
                var avatar = $('#itemDropdown option:selected').data('avatar');
                var pin = $('#itemDropdown option:selected').data('pin');
                var role = $('#itemDropdown option:selected').data('role');
                var email = $('#itemDropdown option:selected').data('email');
                var gender = $('#itemDropdown option:selected').data('gender');

                var newRow = '<tr>' +
                '<td><div class="d-flex align-items-center">' +
                '<img src="/hopeui/html/assets/images/avatars/' + avatar + '" class="rounded img-fluid avatar-40 me-3 bg-soft-primary" alt="profile">' +
                '<div class="d-flex flex-column">'+ 
                '<div style="margin-bottom: 5px;">'+
                '<h6>' + name + '</h6>'+
                '</div> <div>' + pin +
                '</div></div></div></td>' +
                '<td>' + email + '</td>' +
                '<td>' + role + '</td>' +
                '<td>' + gender + '</td>' +
                '<td> <div class="flex align-items-center list-user-action"> <a class="btn btn-sm btn-icon btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" data-value="' + selectedValue + '"><span class="btn-inner"><svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor"><path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></span></a></div></td>' +
                '</tr>';

                $('#selectedItemsTable tbody').append(newRow);
                selectedItems.push(selectedValue);
                $('#selectedItems').val(JSON.stringify(selectedItems));
                $('#itemDropdown option:selected').remove();
                $('#itemDropdown').val(null).trigger('change');
            }
        });

        $('#selectedItemsTable').on('click', '.btn-danger', function() {
            var row = $(this).closest('tr');
            var value = $(this).data('value');
            var name = row.find('h6').text();
            var avatar = row.find('img').attr('src').split('/').pop();
            var pin = row.find('.d-flex.flex-column > div').eq(1).text().trim();
            var role = row.find('td').eq(2).text();
            var email = row.find('td').eq(1).text();
            var gender = row.find('td').eq(3).text();

            selectedItemsValue.push(value);
            $('#selectedItemsValue').val(JSON.stringify(selectedItemsValue));

            // Filter and remove value from selectedItems
            selectedItems = selectedItems.filter(function(item) {
            return item != value;
            });

            // Set updated selectedItems to hidden input
            $('#selectedItems').val(JSON.stringify(selectedItems));

            $('#itemDropdown').append('<option value="'+value+'" data-avatar="'+avatar+'" data-pin="'+pin+'" data-role="'+role+'" data-email="'+email+'" data-gender="'+gender+'">'+name+'</option>');
            $('#itemDropdown').val(null).trigger('change');
            row.remove();

        });
    });
</script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        days.forEach(day => {
            const checkbox = document.getElementById(day);
            const times = document.getElementById(day + '-times');
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

            if (checkbox.checked) {
                times.style.display = 'block';
            } else {
                times.style.display = 'none';
            }
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
