@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Company') }} {{ __('Profile') }}</h1>
    <p>{{ __('Configure your company profile') }} </p>
@endsection

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
                                    <label class="form-label" for="codebaseproduct">{{ __('Company') }} {{ __('Email') }}
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
                                    <label class="form-label" for="codebaseproduct">Email App Password (for server) *</label>
                                    <input type="password" class="form-control" id="codebaseproduct" required
                                        placeholder="{{empty($company->server_password) ? "App Password doesn't exist" : "change app password" }}" name="server_password">
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
                                    <input type="text" class="form-control" id="description" placeholder="a few words..."
                                        name="CompanyAdress" value="{{ $company->CompanyAdress }}">
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
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="type">{{ __('Default Language') }}</label>
                                    <select class="form-select" id="type" name="type">
                                        <option selected disabled value="">{{ __('Choose...') }}</option>
                                        <option value="is_single" {{ old('type') == 'is_single' ? 'selected' : '' }}>
                                            {{ __('English Language') }}</option>
                                        <option value="is_variant" {{ old('type') == 'is_variant' ? 'selected' : '' }}>
                                            {{ __('Indonesian Language') }}</option>
                                    </select>
                                </div>
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
    {{-- Handle Image --}}
    <div class="col-md-12 col-lg-4">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card credit-card-widget" data-aos="fade-up" data-aos-delay="900">
                    <div class="pb-4 border-0 card-header">
                        <div class="p-4 border border-white rounded primary-gradient-card">
                            <div class="d-flex justify-content-center align-items-center">
                                <input type="file" accept="image/*" style="display: none" name="logo"
                                    id="logo">
                                <div id="openLogoUpload"
                                    class="d-flex flex-column justify-content-center align-items-center upload-logo">
                                    <span style="font-size: 24px; color:#D25555">+</span>
                                    <span style="font-size: 20px; color:#ffffff">{{ __('Upload Image') }}</span>
                                    <span
                                        style="font-size: 20px; color:#ffffff; margin-top: 10px;">{{ __('Max. File Size 15MB') }}</span>
                                </div>
                                <div id="afterLogoUpload" style="max-height: 100%;max-width: 100%;"
                                    class="d-none justify-content-center align-items-center after-upload-logo">
                                    <div class="logo-wrapper d-flex justify-content-center align-items-center">
                                        <img style="max-height: 100%;max-width: 100%; object-fit: contain; object-position: center;"
                                            id="previewLogo">
                                        <div class="logo-action">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em"
                                                viewBox="0 0 24 24" id="viewLogoIcon" alt="View"
                                                data-bs-toggle="modal" data-bs-target="#viewLogoModal">
                                                <g fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="1.5" color="currentColor">
                                                    <path
                                                        d="M21.544 11.045c.304.426.456.64.456.955c0 .316-.152.529-.456.955C20.178 14.871 16.689 19 12 19c-4.69 0-8.178-4.13-9.544-6.045C2.152 12.529 2 12.315 2 12c0-.316.152-.529.456-.955C3.822 9.129 7.311 5 12 5c4.69 0 8.178 4.13 9.544 6.045" />
                                                    <path d="M15 12a3 3 0 1 0-6 0a3 3 0 0 0 6 0" />
                                                </g>
                                            </svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em"
                                                viewBox="0 0 24 24" id="deleteLogoIcon" alt="Delete">
                                                <path fill="currentColor"
                                                    d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6zM19 4h-3.5l-1-1h-5l-1 1H5v2h14z" />
                                            </svg>
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
    <!-- Error message container -->
    <div id="error-message" class="text-danger mt-2"></div>
    <!-- Modal for viewing logo -->
    <div class="modal fade" id="viewLogoModal" tabindex="-1" aria-labelledby="viewLogoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewLogoModalLabel">{{ __('Logo Preview') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="logoExtend" src="" alt="Logo Preview" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>
    </form>
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
            var viewLogoIcon = $('#viewLogoIcon');
            var errorMessage = $('#error-message');

            openLogoUpload.click(function() {
                logoUpload.click();
            });

            logoUpload.change(function() {
                var file = this.files[0];
                if (file) {
                    // Check file size
                    if (file.size > 15728640) { // 15 MB in bytes
                        errorMessage.text('File size exceeds 15 MB.');
                        this.value = ''; // Clear the file input
                        return;
                    }

                    var img = new Image();
                    img.src = URL.createObjectURL(file);

                    img.onload = function() {
                        errorMessage.text(''); // Clear any error messages
                        openLogoUpload.removeClass('d-flex').addClass('d-none');
                        afterLogoUpload.removeClass('d-none').addClass('d-flex');
                        preview.attr('src', URL.createObjectURL(file));
                        previewExtend.attr('src', URL.createObjectURL(file));
                    };

                    img.onerror = function() {
                        errorMessage.text('Error loading image. Please try again.');
                        logoUpload.val('');
                    };
                }
            });

            deleteLogoIcon.click(function() {
                afterLogoUpload.removeClass('d-flex').addClass('d-none');
                openLogoUpload.removeClass('d-none').addClass('d-flex');
                logoUpload.val('');
                preview.attr('src', '');
                previewExtend.attr('src', '');
                errorMessage.text('');
            });

            viewLogoIcon.click(function() {
                $('#viewLogoModal').modal('show');
            });
        });
    </script>
@endpush
