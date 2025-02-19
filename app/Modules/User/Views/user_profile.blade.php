@extends('admin.layouts.master')

@section('title', 'User Profile')

@section('head.scripts')

<meta name="csrf-token" content="{{ csrf_token() }}">
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
        font-size: 18px;
        padding: 8px;
        box-shadow: #000000 0 0 10px;
    }

    .edit-form-control:hover,
    .edit-form-control:focus {
        background-color: #FFFFFF;
        color: black;
    }
</style>

@endsection

@section('content')

<div class="content-wrapper " style="background: #191c24;">

    <div class="container-xl px-4 mt-4">
        <div class="row">
            <div class="col-xl-4">
                <div class="card mb-4 mb-xl-0 wrapper" >
                    <div class="card-header">Hình ảnh hồ sơ</div>
                    <div class="card-body text-center">
                        <img class="img-account-profile rounded-circle mb-2" src="http://bootdey.com/img/Content/avatar/avatar1.png" alt="">
                        <div class="small font-italic text-muted mb-4">JPG hoặc PNG không lớn hơn 5 MB</div>
                        <button class="btn btn-primary" type="button">Tải lên hình ảnh mới</button>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="card mb-4 wrapper">
                    <div class="card-header">Chi tiết tài khoản</div>
                    <div class="card-body ">
                        @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                        @endif
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                            @endforeach
                        </div>
                        @endif
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            <div class="row gx-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputFirstName">Tên</label>
                                    <input class="edit-form-control" id="inputFirstName" name="name" type="text" value="{{ old('name', $user['name']) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="small mb-1">Quyền hạn</label>
                                    <input class="edit-form-control"ol type="text" value="{{ $user['role_name'] }}" readonly style="background-color: lightblue; color: black;">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="small mb-1" for="inputEmailAddress">Địa chỉ email</label>
                                <input class="edit-form-control" id="inputEmailAddress" type="email" value="{{ $user['email'] }}" readonly style="background-color: lightblue; color: black;">
                            </div>
                            <div class="mb-3">
                                <label class="small mb-1" for="inputToken">Token</label>
                                <input class="edit-form-control" id="inputToken" name="token" type="text" value="{{ old('token', $user['token']) }}">
                            </div>
                            <div class="mb-3">
                                <label class="small mb-1" for="password">Mật khẩu</label>
                                <input class="edit-form-control" id="password" name="password" type="password" placeholder="Để trống nếu không muốn đổi">
                            </div>
                            <div class="mb-3">
                                <label class="small mb-1" for="password_confirmation">Xác nhận mật khẩu</label>
                                <input class="edit-form-control" id="password_confirmation" name="password_confirmation" type="password">
                            </div>
                            <button class="btn btn-primary" type="submit">Lưu thay đổi</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection