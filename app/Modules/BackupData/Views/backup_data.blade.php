@extends('admin.layouts.master')

@section('title', 'Backup Data')

@section('head.scripts')

<!-- Backup Data JS -->
<script src="{{ asset('modules/backup_data/js/backup_data.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('modules/backup_data/css/backup_data.css') }}">

<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    h1 {
        font-family: 'Arial', sans-serif;
        font-weight: 300;
        font-size: 2em;
        color: #bfbfbf"

    }

    .wrapper {
        background: transparent;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 30px 40px;
    }

    .face-edit-form-control {
        width: 100%;
        background-color: #FFFFFF;
        /* Thay đổi thành màu nền trắng */
        color: #000000;
        /* Thay đổi thành màu chữ đen */
        outline: none;
        font-size: 16px;
        padding: 10px;
        /* Giảm padding nếu cần */
        box-shadow: 0 0 5px #000000;
        /* Điều chỉnh box-shadow nếu cần */
    }

    .face-edit-form-control::placeholder {
        color: #888888;
        /* Màu placeholder xám nhạt */
    }

    .face-edit-form-control:hover,
    .face-edit-form-control:focus {
        background-color: #FFFFFF;
        color: #000000;
    }

   
</style>
@endsection

@section('content')
<div class="content-wrapper" style="background: #191c24;">

    <div class="container-fluid pt-4">
        <!-- Hiển thị thông báo lỗi nếu có -->
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="row" style="padding-top:20px;">
            <div class="col-md-12 grid-margin">
                <div class="card wrapper">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">{{ __('Backup Data') }}</h4>
                        <!-- Phần chọn cơ sở dữ liệu và nút sao lưu -->
                        <div class="d-flex gap-5 mb-3">
                            <select id="dataSelect" class="form-control w-75" style="color: white;">
                                <option value="">{{ __('Chọn database cần sao lưu') }}</option>
                                @foreach($databaseList as $db)
                                <option value="{{ $db['database_name'] }}">{{ $db['database_name'] }}</option>
                                @endforeach
                            </select>

                            <!-- Select collection -->
                            <select id="collectionSelect" class="form-control w-75" disabled>
                                <option value="">{{ __('Chọn collection cần sao lưu') }}</option>
                            </select>

                            <button id="backupButton" class="btn btn-primary" style="flex: none;" disabled>
                                <i class="fas fa-cloud-upload-alt"></i> {{ __('Sao lưu') }}
                            </button>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card wrapper">
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-3">
                            <input type="text" class=" face-edit-form-control form-control search-backup" placeholder="Tìm kiếm backup dữ liệu..." style="width:300px; border-radius: 20px;">
                        </div>
                        <button id="addFile" class="btn btn-info" style="flex: none;">
                            <i class="fas fa-cloud-upload-alt"></i> {{ __('Tải file lên từ máy ') }}
                        </button>

                        <input type="file" id="uploadInput" accept=".gz" style="display: none;">

                        <div class="table-responsive" style="padding-top: 20px; max-height: 600px; overflow-y: auto;">
                            <table id="accountTable" class="table">
                                <thead style="color: black; font-weight: bold !important;">
                                    <thead style="color: black; font-weight: bold !important;">
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                            <th>{{ __('File Name') }}</th>
                                            <th>{{ __('Thời gian') }}</th>
                                            <th>{{ __('Dung lượng') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                <tbody id="backupDataList">
                                    @foreach($backupFiles as $file)
                                    <tr class="backup-file" data-name="{{ $file['file_name'] }}">
                                        <td>
                                            <input type="checkbox" class="file-checkbox" value="{{ $file['full_file_path'] }}">
                                        </td>
                                        <td>{{ $file['file_name'] }}</td>
                                        <td>{{ $file['file_time'] }}</td>
                                        <td>{{ $file['file_size'] ?? 'N/A' }}</td> <!-- Hiển thị 'N/A' nếu file_size không tồn tại -->
                                        <td>
                                            <button class="btn btn-inverse-success btn-fw restore-button" data-file="{{ $file['full_file_path'] }}">
                                                <i class="fas fa-undo"></i> {{ __('Khôi phục') }}
                                            </button>
                                            <button class="btn btn-inverse-primary btn-fw download-button" data-file="{{ $file['full_file_path'] }}">
                                                <i class="fas fa-download"></i> {{ __('Tải về') }}
                                            </button>
                                            <button class="btn btn-inverse-danger btn-fw delete-button" data-file="{{ $file['full_file_path'] }}">
                                                <i class="fas fa-trash"></i> {{ __('Xóa') }}
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection