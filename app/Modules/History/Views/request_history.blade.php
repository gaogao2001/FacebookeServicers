<!-- filepath: /var/www/FacebookService/app/Modules/History/Views/request_history.blade.php -->
@extends('admin.layouts.master')

@section('title', 'Request History')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/pagination.css') }}">
<script src="{{ asset('assets/admin/js/pagination.js') }}" defer></script>
<script src="{{ asset('modules/History/Request/js/request_history.js') }}" defer></script>
<style>
    .wrapper {
        background: transparent;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 30px 40px;
    }

    .search-container {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
    }

    .search-container input {
        padding: 5px;
        width: 200px;
    }

    /* Định dạng bảng có thanh cuộn nội bộ */
    .table-responsive {
        max-height: 500px;
        /* Điều chỉnh chiều cao theo nhu cầu */
        overflow-y: auto;
    }

    table th,
    table td {
        white-space: nowrap;
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
<div class="content-wrapper" style="background: #191c24;">
    <div id="loading" style="display: none; color: #FFFFFF; text-align: center;"></div>
    <div class="row" style="padding-top:20px;">
        <div class="col-md-12 grid-margin">
            <div class="card wrapper">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0" id="requestCountTitle" style="color: #bfbfbf">Request History</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card wrapper">
                <div class="card-body">
                    <!-- Search Container -->
                    <div class="search-container">
                        <input type="text" id="searchInput" class="face-edit-form-control" placeholder="Tìm kiếm...">
                    </div>

                    <button id="deleteAllButton" class="btn btn-danger">Xóa tất cả</button>

                    <div class="table-responsive" style="padding-top: 20px;">
                        <table id="requestTable" class="table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>UID</th>
                                    <th>Http Status Code</th>
                                    <th>ErrorCode</th>
                                    <th>ErrorMessage</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="requestList">
                                <!-- Dữ liệu sẽ được tải tại đây bằng AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <ul id="pagination" class="page-numbers pagination"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection