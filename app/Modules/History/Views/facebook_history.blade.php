@extends('admin.layouts.master')

@section('title', 'History')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('modules/History/FacebookHistory/js/facebook_history.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('assets/admin/css/pagination.css') }}">
<script src="{{ asset('assets/admin/js/pagination.js') }}" defer></script>

<style>
    .wrapper {
        background: transparent;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 30px 40px;
    }

    .truncate-message {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
    }

    .truncate-message:hover {
        white-space: normal;
        overflow: visible;
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
                    <h4 class="card-title mb-0" id="facebookHistoryCountTitle" style="  color: #bfbfbf">Quản lí History</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card wrapper">
                <div class="card-body">
                    <button id="deleteAllButton" class="btn btn-danger" style="display: none;">Xóa các mục đã chọn</button>
                    <button id="deleteAllHistory" class="btn btn-danger" >Xóa tất cả</button>

                    <div class="table-responsive" style="padding-top: 20px;">
                        <table id="historyTable" class="table ">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Facebook UID</th>
                                    <th>Object ID</th>
                                    <th>Hành động</th>
                                    <th>Status</th>
                                    <th>Response</th>
                                    <th>Thời điểm</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="historyList"></tbody>

                        </table>
                    </div>

                    <div id="pagination" class="pagination"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.routes = {
        deleteAllFacebookHistory: '{{ route("history.delete_all_history") }}',
    };
</script>

@endsection