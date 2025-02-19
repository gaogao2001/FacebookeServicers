@extends('admin.layouts.master')

@section('title', 'Quản lý Ads')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="{{ asset('modules/ads/js/ads.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('modules/ads/css/ads_manager.css') }}">
<style>
    /* Toàn bộ modal */
    #deleteAdsModal .modal-content {
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
    #deleteAdsModal .modal-header {
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
    #deleteAdsModal .modal-body {
        padding: 20px;
    }

    /* Form input */
    #deleteAdsModal .form-control {
        background-color: #ffffff;
        color: #333333;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 10px;
        font-size: 14px;
    }

    #deleteAdsModal .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    /* Danh sách các tài khoản đã chọn */
    #deleteAdsModal .list-group-item {
        background-color: #f8f9fa;
        color: #333333;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 5px;
        padding: 10px;
    }

    /* Footer của modal */
    #deleteAdsModal .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
    }

    /* Nút */
    #deleteAdsModal .btn {
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 14px;
    }

    #deleteAdsModal .btn-secondary {
        background-color: #6c757d;
        color: #ffffff;
        border: none;
    }

    #deleteAdsModal .btn-secondary:hover {
        background-color: #5a6268;
    }

    #deleteAdsModal .btn-primary {
        background-color: #007bff;
        color: #ffffff;
        border: none;
    }

    #seleteAccountModal .btn-primary:hover {
        background-color: #0056b3;
    }

    /* Style cho select nhóm */
    #seleteAccountModal .form-group.group-select {
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

    #deleteAdsModal .modal-dialog {
        max-width: 40%;
        /* to rộng bớt cũng được */
    }

    #deleteAdsModal .modal-body {
        max-height: 60vh;

        overflow-y: auto;
    }

    /* Style for table rows */
    #adsTable tbody tr:hover {
        background-color: #f1f1f1;
        /* Màu nền khi hover */
    }

    /* Style for table cells with truncated content */
    #adsTable tbody tr:hover td {
        overflow: visible;
        white-space: normal;
        /* Hiển thị nội dung đầy đủ khi hover */
    }
</style>

@endsection

@section('content')
<div class="content-wrapper" style="background: #191c24;">
    <div id="loading" style="display: none; color: #FFFFFF; text-align: center;">Đang tải...</div>
    <div class="row" style="padding-top:20px;">
        <div class="col-md-12 grid-margin">
            <div class="card wrapper">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0" id="adsCountTitle" style="color: #bfbfbf">Quản lý Ads</h4>

                </div>
            </div>
        </div>
    </div>
    <!-- ... (phần còn lại của giao diện) -->
    <div class="row">
        <div class="col-12">
            <div class="card wrapper">
                <div class="card-body">
                    <div class="search-container">
                        <input type="text" id="searchInput" class="face-edit-form-control" placeholder="Tìm kiếm...">
                        <div class="input-group-append">
                            <span class="input-group-text filter-icon" id="filterToolbarIcon" style="background: transparent; border: none; cursor: pointer;">
                                <i class="fas fa-filter"></i>
                            </span>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-12">
                            <button id="deleteAllButton" class="btn btn-danger">Xóa tất cả</button>
                            <button id="deleteSelectedButton" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#deleteAdsModal">Xóa Ads <span style="font-size: 12px;">(chexbox/input)</button>
                        </div>
                    </div>
                    <div class="table-responsive" style="padding-top: 20px;">
                        <table id="adsTable" class="table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Insights</th>
                                    <th>Account Type</th>
                                    <th>Total Spending</th>
                                    <th>Act ID</th>
                                    <th>Name</th>
                                    <th>Currency</th>
                                    <th>Ngày thêm</th>
                                    <th>Ngày thanh toán kế tiếp</th>
                                    <th>Time Zone</th>
                                    <th>Timezone Name</th>
                                    <th>Account Status</th>
                                    <th>Admin Count</th>
                                    <th>Admin Hidden</th>
                                    <th>User Roles</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="adsList">
                                <!-- Dữ liệu sẽ được tải tại đây bằng AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <div id="paginationStatus">
                        Trang: <span id="currentPage">1</span> / <span id="lastPage">1</span>
                    </div>
                    <!-- Nút Previous và Next -->
                    <div class="pagination-buttons">
                        <button id="prevButton" class="btn btn-secondary">Previous</button>
                        <button id="nextButton" class="btn btn-secondary">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!--modal -->
<div class="modal fade" id="deleteAdsModal" tabindex="-1" role="dialog" aria-labelledby="deleteAdsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <form id="deleteAdsForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xóa Ads</h5>
                </div>
                <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                    <div class="form-group">
                        <label for="adsTextArea">Nhập danh sách Ads ID</label>
                        <textarea
                            class="form-control"
                            id="adsTextArea"
                            rows="6"
                            placeholder="Mỗi dòng 1 Ads ID..."
                            style="resize: both;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xóa các mục đã chọn</button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('Ads::filter_ads_modal')

@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var filterModal = new bootstrap.Modal(document.getElementById('filterModal'));
        document.getElementById('filterToolbarIcon').addEventListener('click', function(e) {
            e.preventDefault();
            filterModal.show();
        });
    });
</script>