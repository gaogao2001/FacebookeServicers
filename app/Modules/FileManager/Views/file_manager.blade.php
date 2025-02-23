
@extends('admin.layouts.master')

@section('title', 'Quản lí File')

@section('head.scripts')
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ config('app.name', 'File Manager') }}</title>
<!-- Styles -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />
<link rel="stylesheet" href="{{ asset('modules/filemanager/css/folder.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    .modal-lg,
    .modal-xl {
        --bs-modal-width: 400px !important;
    }

    .file-item {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 10px;
        border-radius: 5px;
        background-color: #fff;
        margin-bottom: 15px;
    }

    .image-container img {
        width: 100%;
        height: auto;
        border-radius: 5px;
    }

    .video-container video {
        width: 100%;
        height: auto;
        border-radius: 5px;
    }
</style>
@endsection

@section('content')

<div class="container-fluid">
    <!-- Select Group -->
    <div class="row">
        <div class="col-4">
            <label for="groupsAccountSelect" class="form-label">Chọn nhóm tài khoản</label>
            <select id="groupsAccountSelect" name="group" class="form-select">
                <option value="">Chọn nhóm tài khoản</option>
                @foreach($groupAccounts as $group)
                <option value="{{ $group }}" {{ $selectedGroup == $group ? 'selected' : '' }}>{{ $group }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-4">
            <label for="selectedPath" class="form-label">Đường dẫn đã chọn</label>
            <input type="text" id="selectedPath" class="form-control" readonly placeholder="Đường dẫn sẽ được hiển thị ở đây" value="{{ config('file-path.base_path') }}">
        </div>

        <div class="col-4" style="padding-top: 30px;">
            <button class="btn btn-info btn-rounded btn-fw" id="openPopupButton" data-bs-toggle="modal" data-bs-target="#folderModal">Chọn đường dẫn</button>
        </div>

    </div>

</div>

<!-- File Manager Editor -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" id="fm-main-block">
            <div id="fm" style="width: 113%; height: 121%;"></div>
        </div>
    </div>
</div>

<!-- Loading Indicator -->
<div id="loading" class="text-center my-3" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- End Message -->
<div id="endMessage" class="text-center my-3" style="display: none;">
    <p>Đã tải hết các file.</p>
</div>

@include('FileManager::modal.folder_modal')


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
<script src="{{ asset('modules/filemanager/js/folder.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Đặt chiều cao cho fm
        document.getElementById('fm-main-block').setAttribute('style', 'height:600px; width:90%;');

        // Thêm callback cho File Manager
        if (typeof fm !== 'undefined') {
            fm.$store.commit('fm/setFileCallBack', function(fileUrl) {
                console.log('Đường dẫn tệp:', fileUrl);
            });
        }
    });

    $(document).ready(function() {
        $('#groupsAccountSelect').on('change', function() {
            var selectedGroup = $(this).val();
            if (selectedGroup) {
                $.ajax({
                    url: "{{ route('fileManager.getFiles') }}",
                    type: 'GET',
                    data: {
                        group: selectedGroup,
                        _token: '{{ csrf_token() }}' // Đảm bảo bảo mật cho yêu cầu
                    },
                    cache: false, // Vô hiệu hóa cache
                    beforeSend: function() {
                        $('#loading').show();
                        $('#endMessage').hide();

                        // Clear existing files in the editor by setting paths
                        if (fm && fm.instance) {
                            fm.instance.leftSetPath('/');
                            fm.instance.rightSetPath('/');
                        }
                    },
                    success: function(response) {
                        $('#loading').hide();

                        console.log('Response from server:', response); // Thêm dòng này

                        if (response && response.uids && Array.isArray(response.uids)) {
                            var uids = response.uids;
                            var uidsSet = new Set(uids);

                            console.log('UIDs nhận được từ máy chủ:', uids);

                            // Lặp qua tất cả các <li> trong .fm-tree-branch
                            $('.fm-tree-branch li').each(function() {
                                var $li = $(this);
                                var text = $li.find('p').text().trim();

                                // Giả sử text có dạng: " - 100087208561179" hoặc "100087208561179"
                                var parts = text.split(' ');
                                var uid = parts[parts.length - 1]; // Lấy phần tử cuối cùng

                                console.log('UID của mục đang kiểm tra:', uid);

                                // Kiểm tra nếu uid không nằm trong uidsSet
                                if (!uidsSet.has(uid)) {
                                    // Thay vì xóa, ta ẩn nó đi
                                    $li.hide();
                                } else {
                                    // Nếu UID nằm trong kết quả, đảm bảo nó được hiển thị
                                    $li.show();
                                }
                            });

                            // Hiển thị thông báo kết thúc nếu cần
                            $('#endMessage').show();
                        } else {
                            console.error('Invalid data received from server:', response); // Thêm dòng này
                            alert('Dữ liệu nhận được từ máy chủ không hợp lệ.');
                        }
                    },

                });
            } else {
                // Khi không chọn nhóm, hiển thị tất cả các tệp
                if (fm && fm.$store) {
                    fm.$store.commit('fm/setDisk', 'videos');
                    fm.$store.commit('fm/setPath', '/');

                    fm.$store.dispatch('fm/changePath', '/');
                }

                // Hiển thị thông báo kết thúc
                $('#endMessage').hide();
            }
        });
    });
</script>

@endsection

