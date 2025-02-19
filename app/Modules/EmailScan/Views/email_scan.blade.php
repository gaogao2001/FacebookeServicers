@extends('admin.layouts.master')

@section('title', 'Quản lí email')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.7/dayjs.min.js"></script>

<script src="{{ asset('modules/email_scan/js/email_scan.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('modules/email_scan/css/email_scan.css') }}">

<style>
    .wrapper {
        background: transparent;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 30px 40px;
    }

    .zalo-edit-form-control {
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

    .zalo-edit-form-control:hover,
    .zalo-edit-form-control:focus {
        background-color: #FFFFFF;
        color: black;
    }

   

   
</style>
@endsection

@section('content')
<div class="content-wrapper " style="background: #191c24;">
    <div class="container-fluid" style="padding-top:20px;">
        <div class="row" style="padding-top:20px;">
            <div class="col-md-12 grid-margin">
                <div class="card wrapper ">
                    <div class=" card-body  ">
                        <div>
                            <h4 class="card-title">Email Scan <p>Số lượng: <span id="emailCount">0</span></p></h4>
                            <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                                <div class="input-group" style="max-width: 300px;">
                                    <input type="text" id="searchInput" class="zalo-edit-form-control form-control" placeholder="Tìm kiếm..." style="border-radius: 20px; ">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="background: transparent; border: none;"><i class="fas fa-search"></i></span>
                                    </div>
                                </div>
                                <button href="#" class="btn btn-outline-primary btn-fw" id="addEmailScan">Thêm Tài khoản</button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card wrapper">
                    <div class="card-body ">
                        <div class="table-responsive" style="padding-top: 20px; max-height: 600px; overflow-y: auto;">
                            <table id="accountTable" class="table table-lights">
                                <thead >
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>UID</th>
                                        <th>Email</th>
                                        <th>Domain</th>
                                        <th>Phone</th>
                                        <th>Full Name</th>
                                        <th>Quốc gia</th>
                                        <th>Quê quán</th>
                                        <th>Follow</th>
                                        <th>Friend</th>
                                        <th>Ngày sinh</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="emailScanList">
                                    <!-- Dữ liệu sẽ được thêm ở đây -->
                                </tbody>
                            </table>
                            <div id="loading" style="text-align: center; display: none;">Đang tải...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="editEmailScan" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" style="padding-top: 129px;" role="document">
            <div class="modal-content" style="background-color: #191c24;">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmailScanModalLabel">Edit Email Scan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        @csrf
                        <div class="form-group">
                            <label for="editUid">UID</label>
                            <input type="text" class="form-control" id="editUid" name="uid" placeholder="UID">
                        </div>
                        <div class="form-group">
                            <label for="editEmail">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label for="editDomain">Domain</label>
                            <input type="text" class="form-control" id="editDomain" name="domain" placeholder="Domain">
                        </div>
                        <div class="form-group">
                            <label for="editPhone">Phone</label>
                            <input type="text" class="form-control" id="editPhone" name="phone" placeholder="Phone">
                        </div>
                        <div class="form-group">
                            <label for="editFullname">Full Name</label>
                            <input type="text" class="form-control" id="editFullname" name="fullname" placeholder="Full Name">
                        </div>
                        <div class="form-group">
                            <label for="editQuocgia">Quốc gia</label>
                            <input type="text" class="form-control" id="editQuocgia" name="quocgia" placeholder="Quốc gia">
                        </div>
                        <div class="form-group">
                            <label for="editQuequan">Quê quán</label>
                            <input type="text" class="form-control" id="editQuequan" name="quequan" placeholder="Quê quán">
                        </div>
                        <div class="form-group">
                            <label for="editFollow">Follow</label>
                            <input type="number" class="form-control" id="editFollow" name="follow" placeholder="Follow">
                        </div>
                        <div class="form-group">
                            <label for="editFriend">Friend</label>
                            <input type="number" class="form-control" id="editFriend" name="friend" placeholder="Friend">
                        </div>
                        <div class="form-group">
                            <label for="editSinhnhat">Ngày sinh</label>
                            <input type="date" class="form-control" id="editSinhnhat" name="sinhnhat">
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