@extends('admin.layouts.master')

@section('title', 'History')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="{{ asset('modules/History/ZaloHistory/js/zalo_history.js') }}" defer></script>
<style>
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
    <div id="loading" style="display: none;">Đang tải...</div>
    <div class="row" style="padding-top:20px;">
        <div class="col-md-12 grid-margin">
            <div class="card wrapper">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0" id="linkCountTitle" style="  color: #bfbfbf">Quản lí Zalo History</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card wrapper">
                <div class="card-body">
                    <button id="deleteAllButton" class="btn btn-danger" style="display: none;">Xóa tất cả</button>

                    <div class="table-responsive" style="padding-top: 20px;">
                        <table id="zaloHistoryTable" class="table ">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Zalo UID</th>
                                    <th>Object ID</th>
                                    <th>Hành động</th>
                                    <th>Status</th>
                                    <th>Response</th>
                                    <th>Thời điểm</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="zaloHistoryList"></tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection