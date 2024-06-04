<style>
    .alert1 {
        display: inline-flex;
        align-items: center;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .alert1 svg {
        width: 32px;
        height: 32px;
        margin-right: 10px;
    }

    .alert1-primary {
        background-color: #cce5ff;
        color: #007bff;
    }

    .alert1-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>

<div style="text-align: center;" class="m-2">
    @if (\Session::has('success'))
        <div class="alert1 alert1-primary" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
                <path fill="currentColor" style="margin-right: -20px"
                    d="M16 2a14 14 0 1 0 14 14A14 14 0 0 0 16 2Zm0 26a12 12 0 1 1 12-12a12 12 0 0 1-12 12Z" />
                <path fill="currentColor" d="M15 8h2v11h-2zm1 14a1.5 1.5 0 1 0 1.5 1.5A1.5 1.5 0 0 0 16 22z" />
            </svg>
            <div>
                {!! \Session::get('success') !!}
            </div>
        </div>
    @endif
    @if (\Session::has('errorzz'))
        <div class="alert1 alert1-danger" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
                <path fill="currentColor" style="margin-right: -20px"
                    d="M16 2a14 14 0 1 0 14 14A14 14 0 0 0 16 2Zm0 26a12 12 0 1 1 12-12a12 12 0 0 1-12 12Z" />
                <path fill="currentColor" d="M15 8h2v11h-2zm1 14a1.5 1.5 0 1 0 1.5 1.5A1.5 1.5 0 0 0 16 22z" />
            </svg>
            <div>
                {!! \Session::get('errorzz') !!}
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert1 alert1-danger" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
                <path fill="currentColor" style="margin-right: -20px"
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

