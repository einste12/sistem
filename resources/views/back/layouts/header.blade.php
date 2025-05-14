<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/prism.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/rangeslider.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/quill.snow.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/google-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fullcalendar.main.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css"
        integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
    <style>
        table.dataTable>thead .sorting:before,
        table.dataTable>thead .sorting:after,
        table.dataTable>thead .sorting_asc:before,
        table.dataTable>thead .sorting_asc:after,
        table.dataTable>thead .sorting_desc:before,
        table.dataTable>thead .sorting_desc:after,
        table.dataTable>thead .sorting_asc_disabled:before,
        table.dataTable>thead .sorting_asc_disabled:after,
        table.dataTable>thead .sorting_desc_disabled:before,
        table.dataTable>thead .sorting_desc_disabled:after {
            bottom: 1em !important;
        }

        .select2-container--default .select2-selection--single {
            height: 54px;
            padding: .7rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 10px;
            right: 10px;
        }

        .select2-search--dropdown .select2-search__field {
            padding: 8px;
        }

        .select2.select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background: #524FD9;
        }

        .select2-selection--multiple {
            padding: 14px 16px;

        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #D5D9E2;
        }

        .select2-container--default .select2-selection--multiple {
            border-color: #D5D9E2;
        }

        .select2-container--default .select2-selection--multiple {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: #524FD9;
            color: #fff;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
        }
    </style>
    @yield('styles')
    <title>Sistem Panel | @yield('title', 'Anasayfa')</title>
</head>

<body class="boxed-size">
    <div class="preloader" id="preloader">
        <div class="preloader">
            <div class="waviy position-relative">
                <span class="d-inline-block">S</span>
                <span class="d-inline-block">İ</span>
                <span class="d-inline-block">S</span>
                <span class="d-inline-block">T</span>
                <span class="d-inline-block">E</span>
                <span class="d-inline-block">M</span>

            </div>
        </div>
    </div>
    <div class="sidebar-area" id="sidebar-area">
        <div class="logo position-relative">
            <a href="{{ route('dashboard') }}" class="d-block text-decoration-none position-relative">
                <img src="{{ asset('assets/images/logo-icon.png') }}" alt="logo-icon">
                <span class="logo-text fw-bold text-dark">SİSTEM</span>
            </a>
            <button
                class="sidebar-burger-menu bg-transparent p-0 border-0 opacity-0 z-n1 position-absolute top-50 end-0 translate-middle-y"
                id="sidebar-burger-menu">
                <i data-feather="x"></i>
            </button>
        </div>

        <aside id="layout-menu" class="layout-menu menu-vertical menu active" data-simplebar>
            <ul class="menu-inner">
                <li class="menu-item  {{request()->segment(2) == 'account' ? 'open' : ''}}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">note_stack</span>
                        <span class="title">CARİ KARTLAR</span>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="" class="menu-link">
                                Cari
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="" class="menu-link ">
                                Cari Kategori
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="" class="menu-link">
                                Cari Vade Takip
                            </a>
                        </li>
                    </ul>
                <li class="menu-item">
                    <a href="{{ route('admin.users.index') }}" class="menu-link">
                        Kullanıcılar
                    </a>
                </li>
{{--                <li class="menu-item">--}}
{{--                    <a href="{{ route('lorawan.devices.index') }}" class="menu-link">--}}
{{--                        Cihazlar--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                <li class="menu-item">--}}
{{--                    <a href="{{ route('admin.lora.devices.index') }}" class="menu-link">--}}
{{--                        Alt Cihazlar--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                <li class="menu-item">--}}
{{--                    <a href="{{ route('admin.device-assignments.index') }}" class="menu-link">--}}
{{--                        Cihaz Atama Yap--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                <li class="menu-item">--}}
{{--                    <a href="{{ route('user.my-devices') }}" class="menu-link">--}}
{{--                        Bana Atanan Cihazlar--}}
{{--                    </a>--}}
{{--                </li>--}}

                <li class="menu-item">
                    <a href="{{ route('admin.chirpstack.index') }}" class="menu-link">
                        <i class="fas fa-broadcast-tower"></i>
                        ChirpStack Entegrasyonu
                    </a>
                </li>

            </ul>
        </aside>
    </div>

    <div class="container-fluid">
        <div class="main-content d-flex flex-column">
            <header class="header-area bg-white mb-4 rounded-bottom-15" id="header-area">
                <div class="row align-items-center">
                    <div class="col-lg-4 col-sm-6">
                        <div class="left-header-content">
                            <ul
                                class="d-flex align-items-center ps-0 mb-0 list-unstyled justify-content-center justify-content-sm-start">
                                <li>
                                    <button class="header-burger-menu bg-transparent p-0 border-0"
                                        id="header-burger-menu">
                                        <span class="material-symbols-outlined">menu</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-8 col-sm-6">
                        <div class="right-header-content mt-2 mt-sm-0">
                            <ul
                                class="d-flex align-items-center justify-content-center justify-content-sm-end ps-0 mb-0 list-unstyled">
                                <li class="header-right-item">
                                    <div class="light-dark">
                                        <button class="switch-toggle settings-btn dark-btn p-0 bg-transparent"
                                            id="switch-toggle">
                                            <span class="dark"><i
                                                    class="material-symbols-outlined">light_mode</i></span>
                                            <span class="light"><i
                                                    class="material-symbols-outlined">dark_mode</i></span>
                                        </button>
                                    </div>
                                </li>
                                <li class="header-right-item">
                                    <div class="dropdown admin-profile">
                                        <div class="d-xxl-flex align-items-center bg-transparent border-0 text-start p-0 cursor dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                            <div class="flex-shrink-0">
                                                <img class="rounded-circle wh-40 administrator"
                                                    src="{{ auth()->user()->profile_img }}" alt="admin">
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-none d-xxl-block">
                                                        <div class="d-flex align-content-center">
                                                            <h3>{{ auth()->user()->name }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="dropdown-menu border-0 bg-white dropdown-menu-end">
                                            <div class="d-flex align-items-center info">
                                                <div class="flex-shrink-0">
                                                    <img class="rounded-circle wh-30 administrator"
                                                        src="{{ auth()->user()->profile_img }}" alt="admin">
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h3 class="fw-medium">{{ auth()->user()->name }}</h3>
                                                </div>
                                            </div>
                                            <ul class="admin-link ps-0 mb-0 list-unstyled">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center text-body"
                                                        href="{{ route('profile') }}">
                                                        <i class="material-symbols-outlined">account_circle</i>
                                                        <span class="ms-2">Profilim</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center text-body"
                                                        href="{{ route('logout') }}">
                                                        <i class="material-symbols-outlined">logout</i>
                                                        <span class="ms-2">Çıkış Yap</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
