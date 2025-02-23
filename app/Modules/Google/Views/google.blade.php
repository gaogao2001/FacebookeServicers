@extends('admin.layouts.master')

@section('title', 'Quản lí Google')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="{{ asset('modules/google/js/google.js') }}" defer></script>


<style>
    .wrapper {
        background: transparent;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 30px 40px;
    }

    #deleteAccountModal .modal-content {
        background-color: #ffffff;
        /* Màu nền trắng */
        border-radius: 8px;
        /* Bo góc */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        /* Đổ bóng */
        color: #333333;
        /* Màu chữ chính */
    }

    /* Header của modal */
    #deleteAccountModal .modal-header {
        border-bottom: 1px solid #e9ecef;
        padding: 15px 20px;
        background-color: #f8f9fa;
        /* Màu nền header */
        color: #495057;
        /* Màu chữ header */
        font-size: 18px;
        font-weight: 500;
    }

    /* Body của modal */
    #deleteAccountModal .modal-body {
        padding: 20px;
    }

    /* Form input */
    #deleteAccountModal .form-control {
        background-color: #ffffff;
        color: #333333;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 10px;
        font-size: 14px;
    }

    #deleteAccountModal .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    /* Danh sách các tài khoản đã chọn */
    #deleteAccountModal .list-group-item {
        background-color: #f8f9fa;
        color: #333333;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 5px;
        padding: 10px;
    }

    /* Footer của modal */
    #deleteAccountModal .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
    }

    /* Nút */
    #deleteAccountModal .btn {
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 14px;
    }

    #deleteAccountModal .btn-secondary {
        background-color: #6c757d;
        color: #ffffff;
        border: none;
    }

    #deleteAccountModal .btn-secondary:hover {
        background-color: #5a6268;
    }

    #deleteAccountModal .btn-primary {
        background-color: #007bff;
        color: #ffffff;
        border: none;
    }

    #deleteAccountModal .btn-primary:hover {
        background-color: #0056b3;
    }

    /* Style cho select nhóm */
    #deleteAccountModal .form-group.group-select {
        display: none;
        /* Ẩn mặc định */
        margin-top: 10px;
    }

    .input-group-text {
        background-color: #0056b3;
        /* Màu nền xanh nhạt hơn */
        color: #ffffff;
        /* Màu chữ trắng */
        border: none;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .input-group-text:hover {
        background-color: #ffffff;
        /* Màu nền trắng */
        color: #0056b3;
        /* Màu chữ xanh đậm */
    }

    .uid-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .uid-select {
        width: 30%;
    }

    /* Tạo lưới 3 cột, mỗi cột chiếm 1/3 chiều rộng, khoảng cách giữa các mục là 10px */
    .uid-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        /* Hoặc margin nếu thích */
    }

    .uid-item {
        position: relative;
        /* Cho phép đặt nút xóa chồng lên nếu muốn */
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 10px;
        color: #333333;
    }

    /* Nút xóa nhỏ nằm ở góc, nếu muốn */
    .uid-remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgb(15, 15, 15);
        color: rgb(238, 233, 233);
        border: none;
        border-radius: 50%;
        font-size: 11px;
        padding: 3px 6px;
        cursor: pointer;
    }

    .uid-remove-btn:hover {
        background: #c82333;
    }

    #deleteAccountModal .modal-dialog {
        max-width: 40%;
        /* to rộng bớt cũng được */
    }

    #deleteAccountModal .modal-body {
        max-height: 60vh;

        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper " style="background: #191c24;">

    <div class="container-fluid " style="padding-top:20px;">
        <div class="row " style="padding-top:20px;">
            <div class="col-md-12 grid-margin">
                <div class="card wrapper">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Quản lí tài khoản Google</h4>
                        <div class="button-container" style="display: flex; justify-content: flex-end; margin-right:50px;">
                            <a href="#" class="btn btn-outline-primary btn-fw" id="addAccount">Thêm Account</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card wrapper ">
                    <div class="card-body ">
                        <button class="btn btn-danger" id="deleteSelected">Xóa Accounts <span style="font-size: 12px;">(chexbox/input)</button>
                        <button id="deleteAllGoogleButton" class="btn btn-danger">Xóa tất cả</button>
                        <div class="table-responsive " style="padding-top: 20px; max-height: 600px; overflow-y: auto;">
                            <table id="accountTable" class="table ">
                                <thead style="color: black; font-weight: bold !important;">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>STT</th>
                                        <th>Tên đăng nhập</th>
                                        <th>Site Domain</th>
                                        <th>Key Code</th>
                                        <th>Mã Auth</th>
                                        <th>Mật khẩu</th>
                                        <th>Ghi chú</th>
                                        <th>Ngày tạo</th>
                                        <th>Ngày cập nhật</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="accountList">
                                    @foreach($accountsArray as $index => $account)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $account['username'] }}</td>
                                        <td>{{ $account['siteDomain'] }}</td>
                                        <td >{{ $account['keyCode'] }}</td>
                                        <td>{{ $account['password'] }}</td>
                                        <td>{{ $account['notes'] }}</td>
                                        <td>{{ $account['created_at'] }}</td>
                                        <td>{{ $account['updated_at'] }}</td>
                                        <td>
                                            <button class="btn btn-inverse-light edit-account" data-id="{{ $account['_id'] }}">Sửa</button>
                                            <button class="btn btn-inverse-danger delete-account" data-id="{{ $account['_id'] }}">Xóa</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Thêm/Chỉnh sửa -->
    <div class="modal fade" id="editAccountModal" tabindex="-1" role="dialog" aria-labelledby="editAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl " style="padding-top: 129px;" role="document">
            <div class="modal-content" style="background-color: #191c24;">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAccountModalLabel">Chỉnh sửa tài khoản</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editAccountForm">
                        @csrf
                        <div class="form-group">
                            <label for="editUsername">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="editUsername" name="username" placeholder="Tên đăng nhập">
                        </div>
                        <div class="form-group">
                            <label for="editSiteDomain">Site Domain</label>
                            <input type="text" class="form-control" id="editSiteDomain" name="siteDomain" placeholder="Site Domain">
                        </div>
                        <div class="form-group">
                            <label for="editKeyCode">Key Code</label>
                            <input type="text" class="form-control" id="editKeyCode" name="keyCode" placeholder="Key Code">
                        </div>
                        <div class="form-group">
                            <label for="editPassword">Mật khẩu</label>
                            <input type="text" class="form-control" id="editPassword" name="password" placeholder="Mật khẩu">
                        </div>
                        <div class="form-group">
                            <label for="editNotes">Ghi chú</label>
                            <textarea class="form-control" id="editNotes" name="notes" placeholder="Ghi chú"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Gửi</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="deleteAccountForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xóa tài khoản</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn xóa các tài khoản đã chọn?</p>
                        <textarea id="emails" name="emails" class="form-control" rows="5" ></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Xóa</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>


@endsection