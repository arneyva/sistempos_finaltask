<nav class="nav navbar navbar-expand-lg navbar-light iq-navbar">
    <div class="container-fluid navbar-inner">
        <a href="{{ route('dashboard') }}" class="navbar-brand">

            <!--Logo start-->
            <div class="logo-main">
                <div class="logo-normal">
                    <img src="{{ asset('hopeui/html/assets/images/logota3.png') }}" class="text-primary icon-30"
                        alt="">
                </div>
                <div class="logo-mini">
                    <img src="{{ asset('hopeui/html/assets/images/logota3.png') }}" class="text-primary icon-30"
                        alt="">
                </div>
            </div>
            <!--logo End-->
            <h4 class="logo-title">Project TA</h4>
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon">
                <svg width="20px" class="icon-20" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
                </svg>
            </i>
        </div>
        {{-- <div class="input-group search-input">
            <span class="input-group-text" id="search-input">
                <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></circle>
                    <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </span>
            <input type="search" class="form-control" placeholder="Search...">
        </div> --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
                <span class="mt-2 navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
            </span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="mb-2 navbar-nav ms-auto align-items-center navbar-list mb-lg-0">
                <a href="{{ url('/webclock') }}" style="margin-right:4px;">
                    <button type="button" class="btn btn-sm btn-danger" style="margin-right:4px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"></path>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"></path>
                        </svg>
                    </button>
                </a>
                <a href="{{ url('/cashier') }}" style="margin-right:6px;">
                    <button type="button" class="btn btn-sm btn-danger" style="margin-right:6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-basket2" viewBox="0 0 16 16">
                        <path d="M4 10a1 1 0 0 1 2 0v2a1 1 0 0 1-2 0zm3 0a1 1 0 0 1 2 0v2a1 1 0 0 1-2 0zm3 0a1 1 0 1 1 2 0v2a1 1 0 0 1-2 0z"/>
                        <path d="M5.757 1.071a.5.5 0 0 1 .172.686L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15.5a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-.623l-1.844 6.456a.75.75 0 0 1-.722.544H3.69a.75.75 0 0 1-.722-.544L1.123 8H.5a.5.5 0 0 1-.5-.5v-1A.5.5 0 0 1 .5 6h1.717L5.07 1.243a.5.5 0 0 1 .686-.172zM2.163 8l1.714 6h8.246l1.714-6z"/>
                    </svg>
                    </button>
                </a>
                <li class="nav-item dropdown">
                    <div class="nav-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="nav1" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="text-secondary fas fa-globe"></i><span class="text-with-icon text-responsive text-uppercase">{{ env('APP_LOCALE', 'en') }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-start" style="list-style: none;">
                            <li><a href="{{ url('lang/en') }}" class="dropdown-item"><i class="fi fi-us mr-2"></i> English</a></li>
                            <li> <a href="{{ url('lang/id') }}" class="dropdown-item"><i class="fi fi-id mr-2"></i> Indonesia</a></li>
                        </ul>
                    </div>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="py-0 nav-link d-flex align-items-center" href="#" id="navbarDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('hopeui/html/assets/images/avatars/' . Auth::user()->avatar) }}" alt="User-Profile"
                            class="theme-color-default-img img-fluid avatar avatar-50 avatar-rounded">
                        <img src="{{ asset('hopeui/html/assets/images/avatars/avtar_1.png') }}" alt="User-Profile"
                            class="theme-color-purple-img img-fluid avatar avatar-50 avatar-rounded">
                        <img src="{{ asset('hopeui/html/assets/images/avatars/avtar_2.png') }}" alt="User-Profile"
                            class="theme-color-blue-img img-fluid avatar avatar-50 avatar-rounded">
                        <img src="{{ asset('hopeui/html/assets/images/avatars/avtar_4.png') }}" alt="User-Profile"
                            class="theme-color-green-img img-fluid avatar avatar-50 avatar-rounded">
                        <img src="{{ asset('hopeui/html/assets/images/avatars/avtar_5.png') }}" alt="User-Profile"
                            class="theme-color-yellow-img img-fluid avatar avatar-50 avatar-rounded">
                        <img src="{{ asset('hopeui/html/assets/images/avatars/avtar_3.png') }}" alt="User-Profile"
                            class="theme-color-pink-img img-fluid avatar avatar-50 avatar-rounded">
                        <div class="caption ms-3 d-none d-md-block ">
                            <h6 class="mb-0 caption-title">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</h6>
                            {{-- <h6 class="mb-0 caption-title">User</h6> --}}
                            @if (Auth::user()->hasRole(['superadmin', 'inventaris']))
                            <p class="mb-0 caption-sub-title">{{ Auth::user()->getRoleNames()->implode(', ') }}</p>
                            @else
                            <p class="mb-0 caption-sub-title">{{ Auth::user()->getRoleNames()->first() }} {{ Auth::user()->warehouses->pluck('name')->implode(', ') }}</p>
                            @endif
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ route('people.users.show', Auth::user()->id) }}">Profile</a></li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" method="POST" action="{{ route('logout') }}"
                                style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
