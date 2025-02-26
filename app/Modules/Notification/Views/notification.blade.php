@extends('admin.layouts.master')

@section('title' , 'Notification')

@section('head.scripts')
<link rel="stylesheet" href="{{ asset('modules/notification/css/notification.css') }}">
<script src="{{ asset('modules/notification/js/notification.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/admin/css/pagination.css') }}">
<script src="{{ asset('assets/admin/js/pagination.js') }}" defer></script>
@endsection

@php
use Carbon\Carbon;
@endphp

@section('content')
<div class="content-wrapper " style="background: #191c24;">
    <div id="loading" style="display: none;">Đang tải...</div>
    <div class="row" style="padding-top:20px;">
        <div class="col-md-12 grid-margin">
            <div class="card wrapper">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0" id="notificationCountTitle" style="  color: #bfbfbf">Quản lí Thông báo</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card wrapper">
                <div class="card-body">
                    <div id="bulkActionButtons">
                        <button id="readAllBtn" class="btn btn-inverse-primary btn-fw">Đọc tất cả</button>
                        <button id="deleteAllBtn" class="btn btn-inverse-danger btn-fw">Xóa tất cả</button>
                    </div>
                    <!-- Container cho hành động khi chỉ chọn một vài mục -->
                    <div id="selectedActionButtons" style="display: none;">
                        <button id="readSelectedBtn" class="btn btn-inverse-primary btn-fw">Đọc các mục đã chọn</button>
                        <button id="deleteSelectedBtn" class="btn btn-inverse-danger btn-fw">Xóa các mục đã chọn</button>
                    </div>
                    <div class="table-responsive" style="padding-top: 20px;">
                        <table id="notificationTable" class="table ">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Stt</th>
                                    <th>Thời gian</th>
                                    <th>Kiểu thông báo</th>
                                    <th>Nội dung</th>
                                    <th>Trạng thái</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="notificationList"></tbody>

                        </table>
                    </div>

                    <div id="pagination" class="pagination"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer.script')


@endsection