@extends('admin.layouts.master')
@section('title', 'Quản lí Facebook')
@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
    integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- FACEBOOK JS-->

<script src="{{ asset('modules/facebook/js/facebook.js') }}" defer></script>
<script src="{{ asset('modules/facebook/js/all_action_facebook.js') }}" defer></script>
<!-- Facebook css-->
<link rel="stylesheet" href="{{ asset('modules/dashboard/css/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('modules/facebook/css/facebook.css') }}">
<!-- Toast js/ss-->
<script src="{{ asset('assets/admin/js/toast.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('assets/admin/css/toast.css') }}">

<style>
   
</style>
@endsection

@php
use Carbon\Carbon;
@endphp

@section('content')

<div class="content-wrapper" style="background: #191c24;">
    @if(session('status'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: '{{ session('status')}}',
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: '{{ session('error') }}',
        });
    </script>
    @endif
    <div class="row">
        <div class="col-12" style="margin-bottom: -50px;">
            <div class="card wrapper" style="height: 66.67%; padding: 0;">
                <div class="card-body d-flex flex-wrap">
                    @php
                        $cardClasses = ['l-bg-red', 'l-bg-purple', 'l-bg-black', 'l-bg-green-dark'];
                    @endphp
                    @foreach($cardClasses as $class)
                        <div class="col-xl-3 col-lg-3" style="padding-right: 20px;">
                            <div class="card {{ $class }}">
                                <div class="card-statistic-3 p-4">
                                    <div class="row align-items-center">
                                        <div class="col-4 d-flex align-items-center justify-content-center">
                                            <button type="button" class="btn btn-outline-secondary" style="font-size: 2.5em; color: #000; width: 100%;">
                                                <i class="mdi mdi-server d-block mb-1"></i>
                                            </button>
                                        </div>
                                        <div class="col-8">
                                            <h5 class="card-title mb-0">
                                                @if($class === 'l-bg-red') Tổng có
                                                @elseif($class === 'l-bg-purple') Die
                                                @elseif($class === 'l-bg-black') Live
                                                @elseif($class === 'l-bg-green-dark') KXD
                                                @endif
                                            </h5>
                                            <p class="mb-0">
                                                @if($class === 'l-bg-red') {{ $totalAccounts }}
                                                @elseif($class === 'l-bg-purple') {{ $dieAccounts }}
                                                @elseif($class === 'l-bg-black') {{$liveAccounts, }}
                                                @elseif($class === 'l-bg-green-dark') {{ $kxdAccounts }}
                                                @endif
                                                Tài khoản
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- Table -->
    <div class="row">
        <div class="col-12">
            <div class="card wrapper">
                <div class="card-body">
                    <div class="card-body d-flex justify-content-between align-items-center" style="padding: 0.25rem 0.5rem;">
                        <h4 class="card-title mb-0" style="color: #000;">Quản lí tài khoản Facebook</h4>
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" id="searchInput" class="zalo-edit-form-control form-control" placeholder="Tìm kiếm..." style="border-radius: 20px;">
                            <div class="input-group-append">
                                <span class="input-group-text filter-icon" id="filterToolbarIcon" style="background: transparent; border: none; cursor: pointer;">
                                    <i class="fas fa-filter"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!--Xử lí tác vụ-->
                    <div class="d-flex">
                        <button class="btn btn-inverse-success btn-fw" href="#" data-toggle="modal" data-target="#modal-lg" style="margin-right: 10px;">Nạp thêm nick</button>
                        <button type="button" class="btn btn-inverse-success btn-fw BtnCheckLiveFacebookID" style="margin-right: 10px;">
                            Check live
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-inverse-success btn-fw dropdown-toggle" type="button" id="dropdownMenuButton5" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Tác vụ </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton5" style="">
                                <h6 class="dropdown-header">Action</h6>
                                <a class="dropdown-item" href="#" id="sendDataAccountButton">Send Data Account</a>
                                <a class="dropdown-item" href="#" id="changeAccountGroupButton">Đổi nhóm tài khoản</a>
                                <a class="dropdown-item" href="#" id="deleteAccounts">Xóa Accounts</a>
                                <a class="dropdown-item" href="#" id="exportAccount"> Export Account</a>
                                <a class="dropdown-item" href="#" id="importAccount"> Import Account</a>
                                <a class="dropdown-item" href="#" id="openNetworkOption">Open Network Options</a>
                                <a class="dropdown-item" href="#" id="deleteAllData">Xóa hết dữ liệu</a>
                                <a class="dropdown-item" href="#" id="openChangeStatusModal">Thay Đổi Trạng Thái</a>
                                <a class="dropdown-item" href="#" id="multiMessageComment">Multi Message && Comment</a>
                            </div>
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive" id="tableContainer" style="max-height: 600px; overflow-y: auto;">
                        <table id="faceAcountTable" class="table" style="table-layout: fixed; width: 100%;">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>STT</th>
                                    <th>Facebook UID</th>
                                    <th>Full name</th>
                                    <th>Sinh nhật</th>
                                    <th>Bạn bè</th>
                                    <th>Status</th>
                                    <th>Bài viết</th>
                                    <th>Time Create</th>
                                    <th>Tương tác cuối</th>
                                    <th>Nhóm tài khoản</th>
                                    <th>Tác vụ</th>
                                </tr>
                            </thead>
                            <tbody id="facebookAccountList">
                                @php
                                 $sttStart = ($currentPage - 1) * 1000 + 1; // Tính STT bắt đầu cho trang hiện tại
                                @endphp

                                @foreach($accounts as $index => $item)
                                    <tr>
                                        <td><input type="checkbox" class="checkItem" value="{{ $item->uid }}"></td>
                                        <td>{{ $sttStart + $index }}</td> <!-- Hiển thị STT -->
                                        <td><a href="https://facebook.com/{{ $item->uid }}" target="_blank">{{ $item->uid }}</a></td>
                                        <td>{{ $item->fullname ?? null }}</td>
                                        <td>{{ $item->birthday ?? null }}</td>
                                        <td>{{ $item->friends->count ?? 0 }}</td>
                                        <td>{{ $item->status ?? 'KXD'}}</td>
                                        <td>{{ $item->post_data->count ?? 0 }}</td>
                                        <td>{{ $item->created_time ?? 'KXD'}}</td>
                                        <td>
                                            @if (!empty($item->last_seeding) && Carbon::hasFormat($item->last_seeding, 'd/m/Y H:i:s'))
                                            {{ Carbon::createFromFormat('d/m/Y H:i:s', $item->last_seeding)->diffForHumans() }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>{{ $item->groups_account ?? 'KXD'}}</td>
                                        <td>
                                            <div class="dropdown mb-2">
                                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="mdi mdi-apps"></i> Action
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                    <a class="dropdown-item" href="#">Mở khóa (Android App)</a>
                                                    <a class="dropdown-item" href="#">Mở khóa (Android Browser)</a>
                                                    <a class="dropdown-item" href="#">Login app Android</a>
                                                    <a class="dropdown-item" href="#">Login Webrowser Adroid</a>
                                                    <a class="dropdown-item" href="#">Login Webrowser PC</a>
                                                    <hr>
                                                    <a class="dropdown-item" href="#">Up Web Bán</a>
                                                    <a class="dropdown-item" href="#">Up Spam Sell Clone</a>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <a target="_blank" href="{{route('facebook.showJson' , $item->_id)}}" class="btn btn-light btn-sm btn-edit mb-2" style="padding: 3px;width: 100%;">Xem Json</a>
                                            </div>
                                            <div class="d-flex justify-content-between edit-delete">
                                                <a href="{{ route('facebook.edit', $item->_id) }}" class="btn btn-info btn-sm btn-edit" style="padding: 3px; flex: 1; margin-right: 5px;">Edit</a>
                                                <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $item->_id }}" data-name="{{ $item->fullname ?? '' }}" style="padding: 3px;">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="loading" style="text-align: center; display: none;">Đang tải...</div>
                        <div id="endMessage" style="text-align: center; display: none;">Đã hết dữ liệu.</div>
                        <div class="pagination-controls" style="text-align: center; display: flex; justify-content: center; align-items: center;">
                            <button id="prevPage" class="btn btn-secondary" disabled>&lt;</button>
                            <div id="pageNumbers" style="display: flex; margin: 0 10px;"></div>
                            <button id="nextPage" class="btn btn-secondary">&gt;</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    window.routes = {
        importAccounts: '{{ route("facebook.import_accounts") }}',
        updateNetworkUse: '{{ route("facebook.update_network_use") }}',
        proxySplit: '{{ route("facebook.proxySplit") }}',
        updateNetworkUseList: '{{ route("facebook.update_networkuse_by_proxy_list") }}',
        loadMore: '{{route("facebook.load_more")}}',
        deleteAllAccounts: '{{ route("facebook.delete_all_accounts") }}',
        multi_message_comment_page: '{{ route("facebook.multi_message_comment_page") }}'
    };
</script>


<!-- Modal send data-->
@include('Facebook::Modal.send_data_account_modal')

@include('Facebook::Modal.change_account_group_modal')

@include('Facebook::Modal.add_account')

@include('Facebook::Modal.filter_account')

@include('Facebook::Modal.delete_account')

@include('Facebook::Modal.export_account')

@include('Facebook::Modal.import_account')

@include('Facebook::Modal.network_option_modal')

@include('Facebook::Modal.change_accounts_status')

@include('Facebook::Modal.multi_message_comment_modal')