@extends ('admin.layouts.master')

@section('title', 'Admin Info')

@section('head.scripts')
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
<script src="{{ asset('modules/dashboard/js/user.js') }}"></script>
<script src="{{ asset('assets/admin/js/alert.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .form-control {
        color: white;
        /* Màu chữ mặc định */
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
        /* Màu chữ placeholder */
    }

    .form-control:focus {
        color: white;
        /* Màu chữ khi input được focus */
    }

    .wrapper {
        background: transparent;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 30px 40px;
    }
</style>
@endsection

@section('content')

<div class="content-wrapper " style="background: #191c24;">


    <div class="container-fluid" style="padding-top:20px;">
        <div class="row" style="padding-top:20px;">
            <div class="col-md-12 grid-margin">
                <div class="card wrapper">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0" >Admin Info</h4>
                        <div class="button-container" style="display: flex; justify-content: flex-end; margin-right:50px;">
                            <a href="#" class="btn btn-outline-primary btn-fw" id="addUser">Thêm User</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card wrapper">
                    <div class="card-body">
                        <div class="table-responsive" style="padding-top: 20px; max-height: 600px; overflow-y: auto;">
                            <table id="accountTable" class="table ">
                                <thead style="color: black; font-weight: bold !important;">
                                    <tr>
                                <thead>
                                    <tr>
                                        <th> Name </th>
                                        <th> Email </th>
                                        <th> Role </th>
                                        <th> Action </th>
                                    </tr>
                                </thead>
                                <tbody id="adminAcountList"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl " style="padding-top: 129px;" role="document">
            <div class="modal-content" style="background-color: #191c24;">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        @csrf
                        <div class="form-group">
                            <label for="editUsername">Họ và tên</label>
                            <input type="text" class="form-control" id="editUsername" name="name" placeholder="Username">
                        </div>
                        <div class="form-group">
                            <label for="editEmail">Email </label>
                            <input type="email" class="form-control" id="editEmail" name="email" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label for="editPassword">Mật khẩu</label>
                            <input type="password" class="form-control" id="editPassword" name="password" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <label for="editRole">Chọn quyền</label>
                            <select class="form-control" id="editRole" name="role">

                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection