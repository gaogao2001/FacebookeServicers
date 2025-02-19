@extends('admin.layouts.master')

@section('title', 'Quản lí zalo')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
@php
use Carbon\Carbon;
@endphp

<div class="content-wrapper " style="background: #191c24;">

    <div class="container-fluid" style="padding-top:20px;">
        <div class="row" style="padding-top:20px;">
            <div class="col-md-12 grid-margin">
                <div class="card wrapper">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0" style="color: #bfbfbf">Quản lí tài khoản zalo</h4>
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" id="searchInput" class="zalo-edit-form-control form-control" placeholder="Tìm kiếm..." style="border-radius: 20px; ">
                            <div class="input-group-append">
                                <span class="input-group-text" style="background: transparent; border: none;"><i class="fas fa-search" style="color: black;"></i></span>
                            </div>
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
                            <table id="accountTable" class="table table-lights" >
                                <thead style="color: black; font-weight: bold !important;">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Phone number</th>
                                        <th>Zalo ID</th>
                                        <th>Full Name</th>
                                        <th>Sinh nhật</th>
                                        <th>Bạn bè</th>
                                        <th>Nhóm</th>
                                        <th>Status</th>
                                        <th>Ngày nạp</th>
                                        <th>Lần cuối tương tác</th>
                                        <th>Last IP</th>
                                        <th>Use Account</th>
                                        <th>Tác vụ</th>
                                    </tr>
                                </thead>
                                <tbody id="zaloAccountList">

                                    @foreach($zalo as $item)
                                    <tr>
                                        <td><input type="checkbox" name="select[]" value="{{ (string) $item->_id }}"></td>
                                        <td>{{ $item->phone }}</td>
                                        <td>{{ $item->userId }}</td>
                                        <td>{{ $item->zaloName }}</td>
                                        <td>{{ $item->zaloBirthday ?? 'N/A' }}</td>
                                        <td>{{ count($item->friends) }}</td>
                                        <td>{{ count($item->groups)}}</td>
                                        <td>{{ $item->status ?? 'N/A' }}</td>
                                        <td>{{ isset($item->insertedAt) ? $item->insertedAt->toDateTime()->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                        <td>
                                            @if($item->last_seeding)
                                            {{ Carbon::createFromFormat('d/m/Y H:i:s', $item->last_seeding)->diffForHumans() }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>{{ $item->last_ip ?? 'N/A' }}</td>
                                        <td>{{ $item->useAccount ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('zalos.edit', ['id' => (string) $item->_id]) }}" class="btn btn-inverse-light btn-edit">Edit</a>
                                            <button class="btn btn-inverse-danger btn-delete" data-id="{{ (string) $item->_id }}">Delete</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div id="loading" style="text-align: center; display: none;">Đang tải...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection