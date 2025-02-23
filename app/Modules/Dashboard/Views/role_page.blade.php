@extends ('admin.layouts.master')

@section('title', 'Quản lí quyền')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('modules/dashboard/js/role.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('modules/dashboard/css/role.css') }}">
<div id="menu-mapping" data-mapping='@json($menus)'></div>

<style>
    .wrapper {
        background: transparent;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 30px 40px;
    }

    .edit-form-control {
        width: 100%;
        background-color: transparent;
        color: #FFFFFF;
        border: none;
        outline: none;
        border: 2px solid rgba(255, 255, 255, .2);
        font-size: 16px;
        padding: 20px;
        box-shadow: #000000 0 0 10px;
    }

    .edit-form-control:hover,
    .edit-form-control:focus {
        background-color: #FFFFFF;
        color: #000000;
    }

    .edit-form-control::placeholder {
        color: #FFFFFF;
    }

    .edit-form-control:hover::placeholder,
    .edit-form-control:focus::placeholder {
        color: #000000;
    }

    
</style>
@endsection

@section('content')
<div class="content-wrapper " style="background: #191c24;">

    <div class="row" style="padding-top:20px; padding-left:13px;">
        <div class="col-md-12 grid-margin">
            <div class="card wrapper">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h3>Chỉnh sửa phân quyền</h3>
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <button type="button" class="btn btn-outline-primary btn-fw" id="saveRoleBtn">Lưu</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <!-- Bảng danh sách quyền -->
            <div class="col-md-8">
                <div class="card wrapper">
                    <div class="card-body">
                        <h4 class="card-title">Danh sách quyền hiện có</h4>
                        <hr>
                        <div class="table-responsive" style="padding-top: 20px;">
                            <table id="roleTable" class="table table">
                                <thead>
                                    <tr>
                                        <th>Tên quyền</th>
                                        <th>Mô tả</th>
                                        <th>Menu</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="roleList">
                                    <!-- Danh sách các quyền sẽ được tải ở đây -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Form thêm/chỉnh sửa quyền -->
            <div class="col-md-4">
                <div class="card wrapper">
                    <div class="card-body">
                        <h4 class="card-title">Thông tin cơ bản</h4>
                        <hr>
                        <form class="forms-sample">
                            @csrf
                            <div class="form-group">
                                <label for="role_name">Tên</label>
                                <input type="text" class="edit-form-control" id="role_name" name="name" placeholder="Nhập tên quyền">
                            </div>
                            <div class="form-group">
                                <label for="role_description">Mô tả</label>
                                <textarea class="edit-form-control" id="role_description" name="description" placeholder="Nhập mô tả" style="resize: vertical;"></textarea>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Danh sách các menu -->
                <div class="card wrapper">
                    <div class="card-body" style="overflow-y: auto;">
                        <h4 class="card-title">Các quyền được thực hiện</h4>
                        <hr>
                        <div class="form-check" style="margin-bottom: 10px;">
                            <label class="form-check-label">
                                <input type="checkbox" id="selectAllMenu" class="form-check-input">
                                Chọn tất cả quyền hạng
                            </label>
                        </div>
                        <hr>
                        <div class="row">
                            @foreach(array_chunk($menus, ceil(count($menus) / 2), true) as $menuChunk)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        @foreach($menuChunk as $menu => $route)
                                            @if(isset($route['url']))
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input menu-checkbox"
                                                            name="menu[]"
                                                            value="{{ '/' . ltrim($route['url'], '/') }}"
                                                            class="menu-checkbox">
                                                        {{ $menu }}
                                                    </label>
                                                </div>
                                            @endif
                                            @if(isset($route['children']))
                                                @foreach($route['children'] as $childName => $childRoute)
                                                    @if(isset($childRoute['url']))
                                                        <div class="form-check" >
                                                            <label class="form-check-label">
                                                                <input type="checkbox"class="form-check-input menu-checkbox"
                                                                    name="menu[]"
                                                                    value="{{ '/' . ltrim($childRoute['url'], '/') }}"
                                                                    class="menu-checkbox">
                                                                {{ $childName }}
                                                            </label>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection