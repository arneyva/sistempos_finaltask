<div style="text-align: center;" class="m-2">
    @if (\Session::has('success'))
        {{-- <div class="row justify-content-center" id="success-notification">
            <div class="col-md-4 d-flex justify-content-center">
                <div class="alert alert-success d-flex align-items-center gap-2" role="alert"
                    style="color: #049F67; background-color: #D7F1EB; border-color: #ABDFCC">
                    <div>
                        {!! \Session::get('success') !!}
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="alert alert-primary d-flex align-items-center" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                <use xlink:href="#info-fill" />
            </svg>
            <div>
                An example alert with an icon
            </div>
        </div>
    @endif
    @if ($errors->any())
        {{-- <div class="row justify-content-center" id="error-notification">
            <div class="col-auto d-flex justify-content-center">
                <div class="alert alert-danger d-flex align-items-center gap-2 px-3" role="alert"
                    style="color: #EC6564; background-color: #FDF0F0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 256 256">
                        <path fill="currentColor"
                            d="M142 176a6 6 0 0 1-6 6a14 14 0 0 1-14-14v-40a2 2 0 0 0-2-2a6 6 0 0 1 0-12a14 14 0 0 1 14 14v40a2 2 0 0 0 2 2a6 6 0 0 1 6 6Zm-18-82a10 10 0 1 0-10-10a10 10 0 0 0 10 10Zm106 34A102 102 0 1 1 128 26a102.12 102.12 0 0 1 102 102Zm-12 0a90 90 0 1 0-90 90a90.1 90.1 0 0 0 90-90Z" />
                    </svg>

                    <ul class="my-0 " style="list-style-type: none;">
                        @foreach ($errors->all() as $error)
                            <li style="text-align: start">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div> --}}
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            {{-- <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                <use xlink:href="#exclamation-triangle-fill" />
            </svg> --}}
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
                <path fill="currentColor"
                    d="M16 2a14 14 0 1 0 14 14A14 14 0 0 0 16 2Zm0 26a12 12 0 1 1 12-12a12 12 0 0 1-12 12Z" />
                <path fill="currentColor" d="M15 8h2v11h-2zm1 14a1.5 1.5 0 1 0 1.5 1.5A1.5 1.5 0 0 0 16 22z" />
            </svg>
            <div>
                <ul class="my-0 " style="list-style-type: none;">
                    @foreach ($errors->all() as $error)
                        <li style="text-align: start">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif


</div>
