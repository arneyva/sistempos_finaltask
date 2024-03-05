@extends('templates.main')
<style>
    .upload-logo {
        padding: 20px 8px;
        border: 1px dashed #D5DBE1;
    }

    .upload-logo:hover {
        cursor: pointer;
        border-color: #D25555;
    }

    .after-upload-logo {
        padding: 20px 8px;
        border: 1px dashed #D25555;
    }

    .logo-action {
        display: none;
        position: absolute;
    }

    .logo-wrapper:hover {
        cursor: pointer;
    }

    .logo-wrapper:hover #previewLogo {
        position: relative;
        filter: brightness(50%)
    }

    .logo-wrapper:hover .logo-action {
        display: flex;
        gap: 5px
    }
</style>
@section('content')
    {{-- part 1 --}}
    <div class="col-md-12 col-lg-12">

    </div>
    {{-- part 2  sisi kiri --}}
    <div class="col-md-12 col-lg-8">
        <div class="row">
            {{-- part --}}
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title">Create Product</h4>
                        </div>
                    </div>
                    {{--  --}}
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="validationDefault01">Create Product *</label>
                                    <input type="text" class="form-control" id="validationDefault01" required
                                        placeholder="input name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="validationDefault03">Barcode Symbology *</label>
                                    <select class="form-select" id="validationDefault04" required>
                                        <option selected disabled value="">Choose...</option>
                                        <option>...</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="validationDefault02">Code Product *</label>
                                    <input type="text" class="form-control" id="validationDefault02" required
                                        placeholder="input code">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="validationDefault02">Brand</label>
                                    <select class="form-select" id="validationDefault04" required>
                                        <option selected disabled value="">Choose...</option>
                                        @foreach ($brand as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="validationDefault03">Category *</label>
                                    <select class="form-select" id="validationDefault04" required>
                                        <option selected disabled value="">Choose...</option>
                                        @foreach ($category as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="validationDefault04">Tax Type</label>
                                    <select class="form-select" id="validationDefault04" required>
                                        <option selected disabled value="">Choose...</option>
                                        <option>...</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="validationDefault05">Tax</label>
                                    <div class="form-group input-group">
                                        <span class="input-group-text" id="basic-addon1">%</span>
                                        <input type="text" class="form-control" id="validationCustomUsername"
                                            aria-label="Username" aria-describedby="basic-addon1" required
                                            placeholder="input tax">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="validationDefault05">Description</label>
                                    <input type="text" class="form-control" id="validationDefault05" required
                                        placeholder="a few words...">
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    {{--  --}}
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="validationDefault02">Type</label>
                                    <select class="form-select" id="validationDefault04" required>
                                        <option selected disabled value="">Choose...</option>
                                        <option>...</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="validationDefault01">Product Cost *</label>
                                    <input type="text" class="form-control" id="validationDefault01" required
                                        placeholder="input product cost">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="validationDefault03">Product Price *</label>
                                    <input type="text" class="form-control" id="validationDefault03" required
                                        placeholder="input product price">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="validationCustomUsername" class="form-label">Product Unit</label>
                                    <select class="form-select" id="validationDefault04" required>
                                        <option selected disabled value="">Choose...</option>
                                        <option>...</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="validationCustomUsername" class="form-label">Sale Unit</label>
                                    <select class="form-select" id="validationDefault04" required>
                                        <option selected disabled value="">Choose...</option>
                                        <option>...</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="validationCustomUsername" class="form-label">Purchase Unit</label>
                                    <select class="form-select" id="validationDefault04" required>
                                        <option selected disabled value="">Choose...</option>
                                        <option>...</option>
                                    </select>
                                </div>

                            </div>

                            <div class="form-group mt-2">
                                <button class="btn btn-primary" type="submit">Submit form</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- part 2 --}}
            {{-- <div class="col-md-12 col-xl-6">
                <div class="card" data-aos="fade-up" data-aos-delay="900">
                    <div class="flex-wrap card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Earnings</h4>
                        </div>
                        <div class="dropdown">
                            <a href="#" class="text-gray dropdown-toggle" id="dropdownMenuButton1"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                This Week
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item" href="#">This Week</a></li>
                                <li><a class="dropdown-item" href="#">This Month</a></li>
                                <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex-wrap d-flex align-items-center justify-content-between">
                            <div id="myChart" class="col-md-8 col-lg-8 myChart"></div>
                            <div class="d-grid gap col-md-4 col-lg-4">
                                <div class="d-flex align-items-start">
                                    <svg class="mt-2 icon-14" xmlns="http://www.w3.org/2000/svg" width="14"
                                        viewBox="0 0 24 24" fill="#3a57e8">
                                        <g>
                                            <circle cx="12" cy="12" r="8" fill="#3a57e8"></circle>
                                        </g>
                                    </svg>
                                    <div class="ms-3">
                                        <span class="text-gray">Fashion</span>
                                        <h6>251K</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <svg class="mt-2 icon-14" xmlns="http://www.w3.org/2000/svg" width="14"
                                        viewBox="0 0 24 24" fill="#4bc7d2">
                                        <g>
                                            <circle cx="12" cy="12" r="8" fill="#4bc7d2"></circle>
                                        </g>
                                    </svg>
                                    <div class="ms-3">
                                        <span class="text-gray">Accessories</span>
                                        <h6>176K</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xl-6">
                <div class="card" data-aos="fade-up" data-aos-delay="1000">
                    <div class="flex-wrap card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Conversions</h4>
                        </div>
                        <div class="dropdown">
                            <a href="#" class="text-gray dropdown-toggle" id="dropdownMenuButton3"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                This Week
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton3">
                                <li><a class="dropdown-item" href="#">This Week</a></li>
                                <li><a class="dropdown-item" href="#">This Month</a></li>
                                <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="d-activity" class="d-activity"></div>
                    </div>
                </div>
            </div> --}}
            {{-- <div class="col-md-12 col-lg-12">
                <div class="overflow-hidden card" data-aos="fade-up" data-aos-delay="600">
                    <div class="flex-wrap card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="mb-2 card-title">Enterprise Clients</h4>
                            <p class="mb-0">
                                <svg class ="me-2 text-primary icon-24" width="24" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" />
                                </svg>
                                15 new acquired this month
                            </p>
                        </div>
                    </div>
                    <div class="p-0 card-body">
                        <div class="mt-4 table-responsive">
                            <table id="basic-table" class="table mb-0 table-striped" role="grid">
                                <thead>
                                    <tr>
                                        <th>COMPANIES</th>
                                        <th>CONTACTS</th>
                                        <th>ORDER</th>
                                        <th>COMPLETION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img class="rounded bg-soft-primary img-fluid avatar-40 me-3"
                                                    src="{{ asset('hopeui/html/assets/images/shapes/01.png') }}"
                                                    alt="profile">
                                                <h6>Addidis Sportwear</h6>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="iq-media-group iq-media-group-1">
                                                <a href="#" class="iq-media-1">
                                                    <div class="icon iq-icon-box-3 rounded-pill">SP</div>
                                                </a>
                                                <a href="#" class="iq-media-1">
                                                    <div class="icon iq-icon-box-3 rounded-pill">PP</div>
                                                </a>
                                                <a href="#" class="iq-media-1">
                                                    <div class="icon iq-icon-box-3 rounded-pill">MM</div>
                                                </a>
                                            </div>
                                        </td>
                                        <td>$14,000</td>
                                        <td>
                                            <div class="mb-2 d-flex align-items-center">
                                                <h6>60%</h6>
                                            </div>
                                            <div class="shadow-none progress bg-soft-primary w-100" style="height: 4px">
                                                <div class="progress-bar bg-primary" data-toggle="progress-bar"
                                                    role="progressbar" aria-valuenow="60" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img class="rounded bg-soft-primary img-fluid avatar-40 me-3"
                                                    src="{{ asset('hopeui/html/assets/images/shapes/05.png') }}"
                                                    alt="profile">
                                                <h6>Netflixer Platforms</h6>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="iq-media-group iq-media-group-1">
                                                <a href="#" class="iq-media-1">
                                                    <div class="icon iq-icon-box-3 rounded-pill">SP</div>
                                                </a>
                                                <a href="#" class="iq-media-1">
                                                    <div class="icon iq-icon-box-3 rounded-pill">PP</div>
                                                </a>
                                            </div>
                                        </td>
                                        <td>$30,000</td>
                                        <td>
                                            <div class="mb-2 d-flex align-items-center">
                                                <h6>25%</h6>
                                            </div>
                                            <div class="shadow-none progress bg-soft-primary w-100" style="height: 4px">
                                                <div class="progress-bar bg-primary" data-toggle="progress-bar"
                                                    role="progressbar" aria-valuenow="25" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img class="rounded bg-soft-primary img-fluid avatar-40 me-3"
                                                    src="{{ asset('hopeui/html/assets/images/shapes/02.png') }}"
                                                    alt="profile">
                                                <h6>Shopifi Stores</h6>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="iq-media-group iq-media-group-1">
                                                <a href="#" class="iq-media-1">
                                                    <div class="icon iq-icon-box-3 rounded-pill">PP</div>
                                                </a>
                                                <a href="#" class="iq-media-1">
                                                    <div class="icon iq-icon-box-3 rounded-pill">TP</div>
                                                </a>
                                            </div>
                                        </td>
                                        <td>$8,500</td>
                                        <td>
                                            <div class="mb-2 d-flex align-items-center">
                                                <h6>100%</h6>
                                            </div>
                                            <div class="shadow-none progress bg-soft-success w-100" style="height: 4px">
                                                <div class="progress-bar bg-success" data-toggle="progress-bar"
                                                    role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img class="rounded bg-soft-primary img-fluid avatar-40 me-3"
                                                    src="{{ asset('hopeui/html/assets/images/shapes/03.png') }}"
                                                    alt="profile">
                                                <h6>Bootstrap Technologies</h6>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="iq-media-group iq-media-group-1">
                                                <a href="#" class="iq-media-1">
                                                    <div class="icon iq-icon-box-3 rounded-pill">SP</div>
                                                </a>
                                                <a href="#" class="iq-media-1">
                                                    <div class="icon iq-icon-box-3 rounded-pill">PP</div>
                                                </a>
                                                <a href="#" class="iq-media-1">
                                                    <div class="icon iq-icon-box-3 rounded-pill">MM</div>
                                                </a>
                                                <a href="#" class="iq-media-1">
                                                    <div class="icon iq-icon-box-3 rounded-pill">TP</div>
                                                </a>
                                            </div>
                                        </td>
                                        <td>$20,500</td>
                                        <td>
                                            <div class="mb-2 d-flex align-items-center">
                                                <h6>100%</h6>
                                            </div>
                                            <div class="shadow-none progress bg-soft-success w-100" style="height: 4px">
                                                <div class="progress-bar bg-success" data-toggle="progress-bar"
                                                    role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img class="rounded bg-soft-primary img-fluid avatar-40 me-3"
                                                    src="{{ asset('hopeui/html/assets/images/shapes/04.png') }}"
                                                    alt="profile">
                                                <h6>Community First</h6>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="iq-media-group iq-media-group-1">
                                                <a href="#" class="iq-media-1">
                                                    <div class="icon iq-icon-box-3 rounded-pill">MM</div>
                                                </a>
                                            </div>
                                        </td>
                                        <td>$9,800</td>
                                        <td>
                                            <div class="mb-2 d-flex align-items-center">
                                                <h6>75%</h6>
                                            </div>
                                            <div class="shadow-none progress bg-soft-primary w-100" style="height: 4px">
                                                <div class="progress-bar bg-primary" data-toggle="progress-bar"
                                                    role="progressbar" aria-valuenow="75" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> --}}


        </div>
    </div>
    {{-- part 3 sisi kanan --}}
    <div class="col-md-12 col-lg-4">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card credit-card-widget" data-aos="fade-up" data-aos-delay="900">
                    <div class="pb-4 border-0 card-header">
                        <div class="p-4 border border-white rounded primary-gradient-card">
                            {{-- <div class="d-flex justify-content-between align-items-center">

                                <input type="file" accept="image/*" style="display: none" name="logo"
                                    id="logo">
                                <div id="openLogoUpload"
                                    class="d-flex flex-column justify-content-center align-items-center upload-logo">
                                    <span style="font-size: 18px; color:#D25555">+</span>
                                    <span style="font-size: 10px; color:#687385">Upload Image</span>
                                    <span style="font-size: 10px; color:#A3ACBA; margin-top: 10px;">Max. File Size
                                        15MB</span>
                                </div>
                                <div id="afterLogoUpload" style="height: 104px"
                                    class="d-none justify-content-center align-items-center after-upload-logo">
                                    <div class="logo-wrapper d-flex justify-content-center align-items-center">
                                        <img style="height: 86px; object-fit: contain; object-position: center;"
                                            id="previewLogo">
                                        <div class="logo-action">
                                            <img id="viewLogoIcon" src="{{ asset('template/assets/img/icon-view.svg') }}"
                                                alt="" srcset="" data-bs-toggle="modal"
                                                data-bs-target="#viewLogoModal">
                                            <img id="deleteLogoIcon"
                                                src="{{ asset('template/assets/img/icon-delete.svg') }}" alt=""
                                                srcset="">
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="d-flex justify-content-center align-items-center">
                                <input type="file" accept="image/*" style="display: none" name="logo"
                                    id="logo">
                                <div id="openLogoUpload"
                                    class="d-flex flex-column justify-content-center align-items-center upload-logo">
                                    <span style="font-size: 24px; color:#D25555">+</span>
                                    <span style="font-size: 20px; color:#ffffff">Upload Image</span>
                                    <span style="font-size: 20px; color:#ffffff; margin-top: 10px;">Max. File Size
                                        15MB</span>
                                </div>
                                <div id="afterLogoUpload" style="max-height: 100%;max-width: 100%;"
                                    class="d-none justify-content-center align-items-center after-upload-logo">
                                    <div class="logo-wrapper d-flex justify-content-center align-items-center">
                                        <img style="max-height: 100%;max-width: 100%;; object-fit: contain; object-position: center;"
                                            id="previewLogo">
                                        <div class="logo-action">
                                            <img id="viewLogoIcon" src="{{ asset('template/assets/img/icon-view.svg') }}"
                                                alt="" srcset="" data-bs-toggle="modal"
                                                data-bs-target="#viewLogoModal">
                                            <img id="deleteLogoIcon"
                                                src="{{ asset('template/assets/img/icon-delete.svg') }}" alt=""
                                                srcset="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="viewLogoModal" tabindex="-1" aria-labelledby="logoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <img id="logoExtend" alt="">
            </div>
        </div>
    </div>
    {{-- end --}}
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            var openLogoUpload = $('#openLogoUpload');
            var afterLogoUpload = $('#afterLogoUpload');
            var logoUpload = $('#logo');
            var preview = $('#previewLogo');
            var previewExtend = $('#logoExtend');
            var deleteLogoIcon = $('#deleteLogoIcon');


            openLogoUpload.click(function() {
                logoUpload.click();
            });

            logoUpload.change(function() {
                var file = logoUpload[0].files[0];
                if (file) {
                    // Check file size
                    if (file.size > 15728640) { // 1 MB in bytes
                        $('#error-message').text('File size exceeds 15 MB.');
                        logoUpload.value = ''; // Clear the file input
                        return;
                    }

                    // Create an image element to check the aspect ratio
                    var img = new Image();
                    img.src = URL.createObjectURL(file);

                    img.onload = function() {
                        // var aspectRatio = img.width / img.height;

                        // if (Math.abs(aspectRatio - 1) > 0.01) { // Allowing a small margin of error
                        //     $('#error-message').text('Aspect ratio must be 1:1.');
                        //     logoUpload.value = ''; // Clear the file input
                        // } 
                        // else {
                        $('#error-message').text(''); // Clear any error messages
                        openLogoUpload.removeClass('d-flex').addClass('d-none');
                        afterLogoUpload.removeClass('d-none').addClass('d-flex');
                        preview.attr('src', URL.createObjectURL(file));
                        previewExtend.attr('src', URL.createObjectURL(file));
                        // }
                    };
                }
            });

            deleteLogoIcon.click(function() {
                afterLogoUpload.removeClass('d-flex').addClass('d-none');
                openLogoUpload.removeClass('d-none').addClass('d-flex');
                logoUpload.value = '';
            })


        });
    </script>
@endpush
