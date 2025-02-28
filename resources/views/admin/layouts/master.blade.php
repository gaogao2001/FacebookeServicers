<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery and Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
        integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
    <!-- Leaflet CSS -->
    <!-- seting_site -->
    <script src="{{ asset('assets/admin/js/setting_site.js') }}" defer></script>
    <link rel="stylesheet" href="{{ asset('assets/admin/css/setting_site.css') }}">

    <link rel="stylesheet" href="{{ asset('modules/document/css/document.css') }}">
    <script src="{{ asset('modules/document/js/document.js') }}"></script>
    <!-- Font Awesome -->
    <meta name="title" content="{{ $siteManager->meta_title ?? '' }}">
    <meta name="description" content="{{ $siteManager->meta_description ?? '' }}">
    <meta name="keywords" content="{{ $siteManager->meta_keywords ?? '' }}">
    <meta property="og:site_name" content="{{ $siteManager->og_site_name ?? '' }}">
    <meta property="og:type" content="{{ $siteManager->og_type ?? '' }}">
    <meta property="og:locale" content="{{ $siteManager->og_locale ?? '' }}">
    <meta property="og:locale:alternate" content="{{ $siteManager->og_locale_alternate ?? '' }}">
    <meta name="robots" content="{{ $siteManager->robots ?? '' }}">

    @include('admin.partial.css')
    <title>{{ $siteManager->name ?? '' }} - @yield('title')</title>
    <link rel="shortcut icon" href="{{ $siteManager->favicon ?? ''}}" />
    @yield('head.scripts')
    <!-- Meta Tags -->

    
</head>

<body>
    <div class="container-scroller">
        @include('admin.partial.sidebar')
        <div class="main-panel" style="border-left: 1px solid #212529;">
            @include('admin.partial.header')
            @yield('content')
        </div>

        <!-- Thay thế phần nút toggle và extraControls bằng dropdown -->
        <div class="dropdown" style="position: fixed; bottom: 20px; right: 20px; z-index: 1200;">
            <button id="extraControlsToggle" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-headset"></i> Hỗ trợ
            </button>
            <div class="dropdown-menu dropdown-menu-right" style="margin-bottom: 10px;">
                <a class="dropdown-item" href="#" id="settingLink">Cài đặt giao diện</a>
                <a class="dropdown-item" href="#" id="todoLink">Note</a>
                <a class="dropdown-item" href="#" id="docLink">Tài liệu</a>
            </div>
        </div>

        <!-- Giữ nguyên các include nhưng ẩn các nút toggle gốc -->
        <div>@include('admin.partial.todolist')</div>
        <div>@include('admin.partial.setting_site')</div>
        <div>@include('Document::documentation')</div>


    </div>
    @include('admin.partial.script')
    @yield('footer.scripts')



   
</body>

<footer class="footer" style="padding: 0px !important;">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
        <span class="text-muted d-block text-center text-sm-left d-sm-inline-block"></span>
        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"><a href="https://www.bootstrapdash.com/bootstrap-admin-template/" target="_blank"></a></span>
    </div>
</footer>


</html>