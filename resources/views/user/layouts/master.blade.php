<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        .toast-success {
            background-color: #28a745 !important;
            color: #fff !important;
        }

        .toast-error {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .toast-info {
            background-color: #17a2b8 !important;
            color: #fff !important;
        }

        .toast-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
    </style>
    @include('user.partial.css')

    @yield('head.scripts')

</head>

<body>

    <div class="theme-layout">
        @include('user.partial.header')

        @if(!isset($showTopBar) || $showTopBar === true)
            @include('user.partial.topbar', ['uid' => $uid ?? null])
        @endif

        @if(!isset($showRightSidebar) || $showRightSidebar === true)
            @include('user.partial.right_sidebar')
        @endif

        <div style="color: #fff;">
            @include('admin.partial.todolist')
        </div>

        @if(!isset($showLeftSidebar) || $showLeftSidebar === true)
            @include('user.partial.left_sidebar')
        @endif

        <section>
            @yield('content')
        </section>

        @if( !isset($showSidePanel) || $showSidePanel === true)
            @include('user.partial.side-panel')
        @endif
       
        @if( !isset($showPopupWraper) || $showPopupWraper === true)
            @include('user.partial.popup-wraper')
        @endif
      
        @if( !isset($showFooter) || $showFooter === true)
            @include('user.partial.footer')
        @endif

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="{{ asset('user/js/script.js') }}"></script>

    @include('user.partial.script')

    @yield('footer.scripts')
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000",
            "preventDuplicates": true,
            "showDuration": "300",
            "hideDuration": "1000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>
</body>

</html>