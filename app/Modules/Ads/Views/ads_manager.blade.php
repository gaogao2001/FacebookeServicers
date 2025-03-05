@extends('admin.layouts.master')

@section('title', 'Quản lý Ads')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="{{ asset('modules/ads/js/ads.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('modules/ads/css/ads_manager.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/pagination.css') }}">
<script src="{{ asset('assets/admin/js/pagination.js') }}" defer></script>

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
                            <button type="button" id="syncAds" class="btn btn-inverse-success btn-fw sync-ads" style="margin-right: 10px;">
                                Đồng bộ ADS
                            </button>
                            <span class="input-group-text filter-icon" id="filterToolbarIcon" style="background: transparent; border: none; cursor: pointer;">
                                <i class="fas fa-filter"></i>
                            </span>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button id="deleteAllButton" class="btn btn-danger">Xóa tất cả</button>
                            <button id="deleteSelectedButton" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#deleteAdsModal">Xóa Ads <span style="font-size: 12px;">(chexbox/input)</button>
                            <button id="exportSelectedButton" class="btn btn-info" style="display: none; margin-left: 10px;">
                                Xuất account
                            </button>
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
                        <div id="pagination" class="pagination"></div>
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
    <script>
        window.syncAllAdsUrl = "{{ route('android.syncAllAds') }}";
    </script>

    @include('Ads::filter_ads_modal')

    @include('Ads::export_account_modal')

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