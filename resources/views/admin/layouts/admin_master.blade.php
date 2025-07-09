@php
    App\Models\User::where('id', Auth::user()->id)->update(['last_active' =>  Carbon\Carbon::now() ]);
    $default_setting = App\Models\DefaultSetting::first();
@endphp
<!doctype html>
<html lang="en" class="light-theme">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('uploads/default_photo') }}/{{ $default_setting->favicon }}" type="image/png" />
    <!--plugins-->
    <link href="{{ asset('admin') }}/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="{{ asset('admin') }}/plugins/select2/css/select2.min.css" rel="stylesheet" />
	<link href="{{ asset('admin') }}/plugins/select2/css/select2-bootstrap4.css" rel="stylesheet" />
    <link href="{{ asset('admin') }}/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
    <link href="{{ asset('admin') }}/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <link href="{{ asset('admin') }}/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="{{ asset('admin') }}/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('admin') }}/css/bootstrap-extended.css" rel="stylesheet" />
    <link href="{{ asset('admin') }}/css/style.css" rel="stylesheet" />
    <link href="{{ asset('admin') }}/css/icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/@mapbox/mapbox-gl-geocoder@4.7.0/dist/mapbox-gl-geocoder.css" rel="stylesheet" />

   
 


    <!-- loader-->
    <link href="{{ asset('admin') }}/css/pace.min.css" rel="stylesheet" />

    <!--Theme Styles-->
    <link href="{{ asset('admin') }}/css/dark-theme.css" rel="stylesheet" />
    <link href="{{ asset('admin') }}/css/light-theme.css" rel="stylesheet" />
    <link href="{{ asset('admin') }}/css/semi-dark.css" rel="stylesheet" />
    <link href="{{ asset('admin') }}/css/header-colors.css" rel="stylesheet" />

    <link href="{{ asset('admin') }}/plugins/toastr/toastr.css" rel="stylesheet">
    <link href="{{ asset('admin') }}/plugins/summernote/summernote.min.css" rel="stylesheet">
    <link href="{{ asset('admin') }}/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet">
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.css' rel='stylesheet' />
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl-geocoder.css" rel="stylesheet" />

    <title>{{ env('APP_NAME') }} || @yield('title')</title>
    <style>
        .sidebar-wrapper .sidebar-header  .logo-text {
    font-size: 22px;
    margin-left: 10px;
    margin-bottom: 0;
    letter-spacing: 1px;
    color: #e8525c;
}

        </style>

</head>

<body>
    <!--start wrapper-->
    <div class="wrapper">
        <!--start top header-->
        <header class="top-header">
            <nav class="navbar navbar-expand">
                <div class="mobile-toggle-icon d-xl-none">
                    <i class="bi bi-list"></i>
                </div>
                <div class="top-navbar-right d-none d-xl-flex ms-auto ms-3">
                    <ul class="navbar-nav align-items-center">
                        @if (Auth::user()->role == "Super Admin" || Auth::user()->role == "Admin")
                        <li class="nav-item dropdown dropdown-large">
                            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                                <div class="messages">
                                    <span class="notify-badge">{{ App\Models\ContactMessage::where('status', 'Unread')->count() }}</span>
                                    <i class="bi bi-messenger"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-0">
                                <div class="p-2 border-bottom m-2">
                                    <h5 class="h5 mb-0">Messages</h5>
                                </div>
                                <div class="header-message-list p-2">
                                    <div class="dropdown-item bg-light radius-10 mb-1"></div>
                                    @foreach (App\Models\ContactMessage::where('status', 'Unread')->take(5)->get() as $message)
                                    <a class="dropdown-item" href="#">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('admin') }}/images/avatars/avatar-1.png" alt="" class="rounded-circle" width="52" height="52">
                                            <div class="ms-3 flex-grow-1">
                                            <h6 class="mb-0 dropdown-msg-user">{{ $message->name }}<span class="msg-time float-end text-secondary">{{ $message->created_at->format('D d-M,Y h:m A') }}</span></h6>
                                            <small class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">{{ $message->subject }}</small>
                                            </div>
                                        </div>
                                    </a>
                                    @endforeach
                                </div>
                                <div class="p-2">
                                    <div><hr class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('contact.message.index') }}">
                                        <div class="text-center">View All Messages</div>
                                    </a>
                                </div>
                            </div>
                        </li>
                        @endif
                        {{-- <li class="nav-item dropdown dropdown-large d-none d-sm-block">
                            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                                <div class="notifications">
                                <span class="notify-badge">8</span>
                                <i class="bi bi-bell-fill"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-0">
                                <div class="p-2 border-bottom m-2">
                                    <h5 class="h5 mb-0">Notifications</h5>
                                </div>
                                <div class="header-notifications-list p-2">
                                    <div class="dropdown-item bg-light radius-10 mb-1"></div>
                                    <a class="dropdown-item" href="#">
                                        <div class="d-flex align-items-center">
                                            <div class="notification-box"><i class="bi bi-basket2-fill"></i></div>
                                            <div class="ms-3 flex-grow-1">
                                                <h6 class="mb-0 dropdown-msg-user">New Orders <span class="msg-time float-end text-secondary">1 m</span></h6>
                                                <small class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">You have recived new orders</small>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <div class="d-flex align-items-center">
                                            <div class="notification-box"><i class="bi bi-people-fill"></i></div>
                                            <div class="ms-3 flex-grow-1">
                                                <h6 class="mb-0 dropdown-msg-user">New Customers <span class="msg-time float-end text-secondary">7 m</span></h6>
                                                <small class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">5 new user registered</small>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <div class="d-flex align-items-center">
                                        <div class="notification-box"><i class="bi bi-mic-fill"></i></div>
                                        <div class="ms-3 flex-grow-1">
                                            <h6 class="mb-0 dropdown-msg-user">Your item is shipped <span class="msg-time float-end text-secondary">7 m</span></h6>
                                            <small class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">Successfully shipped your item</small>
                                        </div>
                                        </div>
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <div class="d-flex align-items-center">
                                        <div class="notification-box"><i class="bi bi-lightbulb-fill"></i></div>
                                        <div class="ms-3 flex-grow-1">
                                            <h6 class="mb-0 dropdown-msg-user">Defense Alerts <span class="msg-time float-end text-secondary">2 h</span></h6>
                                            <small class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center">45% less alerts last 4 weeks</small>
                                        </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2">
                                    <div><hr class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">
                                        <div class="text-center">View All Notifications</div>
                                    </a>
                                </div>
                            </div>
                        </li> --}}
                        <li class="nav-item dropdown dropdown-large">
                            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                                <div class="user-setting d-flex align-items-center gap-1">
                                <img src="{{ asset('uploads/profile_photo') }}/{{ Auth::user()->profile_photo }}" class="user-img" alt="">
                                <div class="user-name d-none d-sm-block">{{ Auth::user()->name }}</div>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('uploads/profile_photo') }}/{{ Auth::user()->profile_photo }}" alt="" class="rounded-circle" width="60" height="60">
                                        <div class="ms-3">
                                        <h6 class="mb-0 dropdown-user-name">{{ Auth::user()->name }}</h6>
                                        <small class="mb-0 dropdown-user-designation text-secondary">{{ Auth::user()->role }}</small>
                                        </div>
                                    </div>
                                </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <div class="d-flex align-items-center">
                                        <div class="setting-icon"><i class="bi bi-person-fill"></i></div>
                                        <div class="setting-text ms-3"><span>Profile</span></div>
                                    </div>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                        <div class="d-flex align-items-center">
                                            <div class="setting-icon"><i class="bi bi-lock-fill"></i></div>
                                            <div class="setting-text ms-3"><span>Logout</span></div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!--end top header-->

        <!--start sidebar -->
        <aside class="sidebar-wrapper" data-simplebar="true">
            <div class="sidebar-header">
                <div>
                <a href="{{ route('dashboard') }}"> <img src="{{ asset('admin/images/firefighterlogo.png') }}" class="logo-icon" alt="logo icon"> </a>
                </div>
                 <div>
                    <a href="{{ route('dashboard') }}"><h4 class="logo-text">Fire Fighter</h4></a>
                </div> 
                <div class="toggle-icon ms-auto">
                    <i class="bi bi-chevron-double-left"></i>
                </div>
            </div>
            <!--navigation-->
            @include('admin.layouts.navigation')
            <!--end navigation-->
        </aside>
        <!--end sidebar -->

        <!--start content-->
        <main class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Dashboard</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            @yield('content')
        </main>
        <!--end page main-->

        <!--start overlay-->
        <div class="overlay nav-toggle-icon"></div>
        <!--end overlay-->

        <!--Start Back To Top Button-->
        <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->

        <!--start switcher-->
        
        <!--end switcher-->
    </div>
    <!--end wrapper-->

    <!-- Bootstrap bundle JS -->
    <script src="{{ asset('admin') }}/js/bootstrap.bundle.min.js"></script>
    <!--plugins-->
    <script src="{{ asset('admin') }}/js/jquery.min.js"></script>
    <script src="{{ asset('admin') }}/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="{{ asset('admin') }}/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
    <script src="{{ asset('admin') }}/js/pace.min.js"></script>

    <script src="{{ asset('admin') }}/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('admin') }}/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('admin') }}/js/table-datatable.js"></script>
    <script src="{{ asset('admin') }}/plugins/select2/js/select2.min.js"></script>
    <script src="{{ asset('admin') }}/js/form-select2.js"></script>

    <script src="{{ asset('admin') }}/plugins/apexcharts-bundle/js/apexcharts.min.js"></script>
    <script src="{{ asset('admin') }}/plugins/metismenu/js/metisMenu.min.js"></script>

    <script src="{{asset('admin')}}/plugins/printThis/printThis.js"></script>

    <!--app-->
    <script src="{{ asset('admin') }}/js/app.js"></script>

    <script src="{{ asset('admin') }}/plugins/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="{{ asset('admin') }}/plugins/summernote/summernote.min.js"></script>
    <script src="{{ asset('admin') }}/plugins/toastr/toastr.min.js"></script>
 
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.js'></script>
    
    <script src="https://cdn.jsdelivr.net/npm/@mapbox/mapbox-gl-geocoder@4.7.0/dist/mapbox-gl-geocoder.min.js"></script>





    <script>
        $(document).ready(function() {
            @if(Session::has('message'))
                var type = "{{ Session::get('alert-type', 'info') }}";
                switch(type){
                    case 'info':
                        toastr.info("{{ Session::get('message') }}");
                        break;

                    case 'warning':
                        toastr.warning("{{ Session::get('message') }}");
                        break;

                    case 'success':
                        toastr.success("{{ Session::get('message') }}");
                        break;

                    case 'error':
                        toastr.error("{{ Session::get('message') }}");
                        break;
                }
            @endif
        });
    </script>

    @yield('script')

</body>
</html>
