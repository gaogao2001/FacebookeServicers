@php
    $showTopBar = true;
    $showLeftSidebar = true;
    $showRightSidebar = true;
    $showSidePanel = true;
    $showPopupWraper = true;
    $showFooter = true;
@endphp

@extends ('user.layouts.master')

@section('title', 'admin')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"> -->
<!-- jQuery, Popper.js, và Bootstrap JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="{{ asset('user/js/PhuongNhatDien.js') }}"></script>



<!-- Toastr JS -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection

@section('content')
@if(session('success'))
<script>
    toastr.success("{{ session('success') }}", "Thành công", {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-left",
        "timeOut": "3000"
    });
</script>
@endif

@if(session('error'))
<script>
    toastr.error("{{ session('error') }}", "Lỗi", {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-left",
        "timeOut": "3000"
    });
</script>
@endif
<div class="gap2 gray-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="row merged20" id="page-contents">
                    <div class="col-lg-3">
                        @include('Android::Content.Sidebar_static_left.aside')
                        
                    </div><!-- sidebar -->
                    <div class="col-lg-6">
                        <!-- create post -->
                        @include('Android::Content.Create_post.create_post')
                        <!-- STORY -->
                        @include('Android::Content.Stories.stories')

                        <!-- Post content -->
                        @include('Android::Content.Post.post')
                      
                    </div><!-- centerl meta -->
                    <div class="col-lg-3">
                        @include('Android::Content.Sidebar_static_right.aside')
                    </div><!-- sidebar -->
                    <div style="color: #fff;">
                        @include('admin.partial.todolist')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('admin.pages.map_modal', [
'modalId' =>$data['account']->_id,
'formAction' => route('facebook.updateCoordinates', $data['account']->_id)
])

<script src="{{ asset('assets/admin/js/map.js') }}"></script>


@endsection

@section('footer.scripts')

@endsection