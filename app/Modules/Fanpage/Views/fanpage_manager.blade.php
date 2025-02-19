@extends('admin.layouts.master')

@section('title', 'History')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('modules/fanpage/css/Fanpage_manager.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/pagination.css') }}">

<script src="{{ asset('assets/admin/js/pagination.js') }}" defer></script>
<script src="{{ asset('modules/fanpage/js/fanpage.js') }}" defer></script>
<style>
    table {
        table-layout: fixed;
        width: 100%;
    }

    /* Break text in specific columns, similar to link.blade.php */
    table td:nth-child(2),
    table td:nth-child(3),
    table td:nth-child(4) {
        width: 150px;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: break-word;
    }

    table td:nth-child(5),
    table td:nth-child(6),
    table td:nth-child(7),
    table td:nth-child(8),
    table td:nth-child(9) {
        width: 50px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Add CSS for the animation effect */
    .updated {
        animation: fadeIn 0.5s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }



    /* Toàn bộ modal */
    #deleteFanpageModal .modal-content {
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
    #deleteFanpageModal .modal-header {
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
    #deleteFanpageModal .modal-body {
        padding: 20px;
    }

    /* Form input */
    #deleteFanpageModal .form-control {
        background-color: #ffffff;
        color: #333333;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 10px;
        font-size: 14px;
    }

    #deleteFanpageModal .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    /* Danh sách các tài khoản đã chọn */
    #deleteFanpageModal .list-group-item {
        background-color: #f8f9fa;
        color: #333333;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 5px;
        padding: 10px;
    }

    /* Footer của modal */
    #deleteFanpageModal .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
    }

    /* Nút */
    #deleteFanpageModal .btn {
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 14px;
    }

    #deleteFanpageModal .btn-secondary {
        background-color: #6c757d;
        color: #ffffff;
        border: none;
    }

    #deleteFanpageModal .btn-secondary:hover {
        background-color: #5a6268;
    }

    #deleteFanpageModal .btn-primary {
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

    #deleteFanpageModal .modal-dialog {
        max-width: 40%;
        /* to rộng bớt cũng được */
    }

    #deleteFanpageModal .modal-body {
        max-height: 60vh;

        overflow-y: auto;
    }

    .loading-icon {
        display: none;
        margin-left: 5px;
    }

    /* Sử dụng style từ Search_result.blade.php */
    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 100000;
    }

    .loader {
        width: fit-content;
        font-size: 40px;
        font-family: system-ui, sans-serif;
        font-weight: bold;
        text-transform: uppercase;
        color: #0000;
        -webkit-text-stroke: 1px #fff;
        background: linear-gradient(-60deg, #0000 45%, rgb(247, 120, 46) 0 55%, #0000 0) 0 / 300% 100% no-repeat text;
        animation: l3 2s linear infinite;
    }

    .loader:before {
        content: "Loading";
    }

    @keyframes l3 {
        0% {
            background-position: 100%;
        }

        100% {
            background-position: 0;
        }
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
                    <h4 class="card-title mb-0" id="fanpageCountTitle" style="color: #bfbfbf">Quản lý Fanpage (Số Lượng : {{$countsFanpages}} )</h4>

                </div>
            </div>
        </div>
    </div>
    <!-- Rest of your content -->
    <div class="row">
        <div class="col-12">
            <div class="card wrapper">
                <div class="card-body">
                    <div class="search-container">
                        <input type="text" id="searchInput" class="face-edit-form-control" placeholder="Tìm kiếm...">
                        <button id="createPageButton" class="btn  btn-primary btn-fw" data-toggle="modal" data-target="#createPageModal">
                            <i class="fas fa-plus"></i> Tạo page
                        </button>
                        <button type="button" id="syncFanpageBtn" class="btn btn-inverse-success btn-fw" style="margin-right: 10px;">Đồng bộ Fanpage</button>
                        <div class="input-group-append">
                            <span class="input-group-text filter-icon" id="filterToolbarIcon" style="background: transparent; border: none; cursor: pointer;">
                                <i class="fas fa-filter"></i>
                            </span>
                        </div>
                    </div>
                    <button id="deleteAllFanpages" class="btn btn-danger">Xóa All Fanpages</button>
                    <button id="deleteFanpages" class="btn btn-danger">Xóa Fanpage <span style="font-size: 12px;">(chexbox/input)</span></button>
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto; overflow-x: hidden;">
                        <table id="fanpageTable" class="table" style="table-layout: fixed; width: 100%; white-space: normal; word-break: break-all;">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"><input type="checkbox" id="selectAll"></th>
                                    <th style="min-width: 150">UID Controller</th>
                                    <th style="min-width: 150px;">Page ID</th>
                                    <th style="min-width: 150px;">Page name</th>
                                    <th>Like</th>
                                    <th>Followers</th>
                                    <th>Post</th>
                                    <th>Admin</th>
                                    <th style="min-width: 100px;">Source Control</th>
                                    <th style="min-width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="fanpageList">
                                <!-- Data loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <ul id="pagination" class="page-numbers pagination"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Page Modal -->
<div class="modal fade" id="createPageModal" tabindex="-1" role="dialog" aria-labelledby="createPageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPageModalLabel">Tạo page mới</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createPageForm">
                    <div class="form-group">
                        <label for="searchKeyword">Tìm kiếm tài khoản Facebook Sẽ Tạo Page</label>
                        <input type="text" class="form-control" id="searchKeyword" name="searchKeyword" placeholder="Nhập từ khóa tìm kiếm">
                        <select id="searchResults" class="form-control mt-2" name="searchResults">
                            <option value="">Chọn tài khoản</option>
                            <!-- Options will be populated via AJAX -->
                        </select>
                    </div>
                    <div class="form-group" id="categoryNameGroup" style="display: none;">
                        <label for="categoryName">Tên danh mục</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="categoryName" name="categoryName" required>
                            <div class="input-group-append">
                                <button class="btn btn-secondary" id="checkCategoryNameButton" disabled>
                                    Kiểm tra
                                    <span class="loading-icon" id="categoryLoadingIcon"><i class="fas fa-spinner fa-spin"></i></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="pageNameGroup" style="display: none;">
                        <label for="pageName">Tên page</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="pageName" name="pageName" required>
                            <div class="input-group-append">
                                <button class="btn btn-secondary" id="checkPageNameButton" disabled>
                                    Kiểm tra
                                    <span class="loading-icon" id="pageLoadingIcon"><i class="fas fa-spinner fa-spin"></i></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Hidden inputs for name and username -->
                    <input type="hidden" id="hiddenName" name="name">
                    <input type="hidden" id="hiddenUsername" name="username">
                    <button class="btn btn-primary BtnCreateNewPage" style="display: none;">Tạo</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xóa Fanpage -->
<div class="modal fade" id="deleteFanpageModal" tabindex="-1" role="dialog" aria-labelledby="deleteFanpageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <!-- Form để submit xóa -->
        <form id="deleteFanpagesForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xóa Fanpage</h5>
                </div>

                <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                    <!-- Textarea để dán/thêm nhiều Fanpage ID -->
                    <div class="form-group">
                        <label for="fanpageTextArea">Nhập danh sách Fanpage ID</label>
                        <textarea
                            class="form-control"
                            id="fanpageTextArea"
                            rows="6"
                            placeholder="Mỗi dòng 1 Fanpage ID..."
                            style="resize: both;"></textarea>
                    </div>

                    <!-- Container ẩn để chứa các input hidden name="ids[]" -->
                    <div id="hiddenFanpageContainer"></div>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Đóng
                    </button>
                    <button
                        type="submit"
                        class="btn btn-danger">
                        Xóa các mục đã chọn
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>



@include('Fanpage::Form.filter_fanpage')
<script>
    window.syncAllFanpageRoute = "{{ route('android.syncAllFanpage') }}";
</script>

<script>
    let searchTimeout;
    $('#searchKeyword').on('input', function() {
        clearTimeout(searchTimeout);
        const keyword = $(this).val();
        searchTimeout = setTimeout(() => {
            if (keyword.length > 2) {
                $.getJSON(`/facebooks/search?search=${keyword}&per_page=50&page=1`, function(data) {
                    const $searchResults = $('#searchResults');
                    $searchResults.html('<option value="">Chọn tài khoản</option>');
                    $.each(data.data, function(index, account) {
                        $searchResults.append($('<option>', {
                            value: account.uid,
                            text: account.fullname
                        }));
                    });

                    // Add animation effect
                    $searchResults.addClass('updated');
                    setTimeout(() => {
                        $searchResults.removeClass('updated');
                    }, 500);

                    // Automatically select the first result
                    if (data.data.length > 0) {
                        $searchResults.val(data.data[0].uid);
                        $('#categoryNameGroup').show();
                    }
                });
            }
        }, 500); // Delay to wait for user to finish typing
    });

    $('#searchResults').on('change', function() {
        if ($(this).val()) {
            $('#categoryNameGroup').show();
        } else {
            $('#categoryNameGroup, #pageNameGroup').hide();
            $('.BtnCreateNewPage').hide();
        }
    });

    $('#categoryName').on('input', function() {
        const $checkCategoryNameButton = $('#checkCategoryNameButton');
        const $categorySelect = $('#categorySelect');
        if ($(this).val().trim() !== '') {
            $checkCategoryNameButton.prop('disabled', false);
            if ($categorySelect.length) {
                $categorySelect.hide();
            }
        } else {
            $checkCategoryNameButton.prop('disabled', true);
            if ($categorySelect.length) {
                $categorySelect.show();
            }
        }
    });

    $('#checkCategoryNameButton').on('click', function() {
        const formData = {
            uid: $('#searchResults').val(),
            categoryName: $('#categoryName').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Show loading effect
        $('#categoryLoadingIcon').show();

        $.post('/DeviceEmulator/CheckCategoryNameAvailability', formData, function(response) {
            if (response.status && response.category.length > 0) {
                // Handle success response
                $('#pageNameGroup').hide(); // Ensure pageNameGroup is hidden initially

                // Create and populate the new select element
                let $categorySelect = $('#categorySelect');
                if (!$categorySelect.length) {
                    $categorySelect = $('<select>', {
                        id: 'categorySelect',
                        class: 'form-control mt-2'
                    }).appendTo('#categoryNameGroup');
                }
                $categorySelect.html('<option value="">Chọn danh mục</option>');
                $.each(response.category, function(index, cat) {
                    $categorySelect.append($('<option>', {
                        value: cat.id,
                        text: cat.name
                    }));
                });

                // Add event listener for categorySelect change
                $categorySelect.on('change', function() {
                    if ($(this).val()) {
                        $('#categoryName, #checkCategoryNameButton').hide();
                        $('#pageNameGroup').show(); // Show pageNameGroup if a category is selected
                    } else {
                        $('#categoryName, #checkCategoryNameButton').show();
                        $categorySelect.hide();
                        $('#pageNameGroup').hide(); // Hide pageNameGroup if no category is selected
                    }
                });
            }
        }).fail(function(xhr, status, error) {
            // Handle error response
            alert('Error checking category name: ' + error);
        }).always(function() {
            // Hide loading effect
            $('#categoryLoadingIcon').hide();
        });
    });

    $('#pageName').on('input', function() {
        const $checkPageNameButton = $('#checkPageNameButton');
        if ($(this).val().trim() !== '') {
            $checkPageNameButton.prop('disabled', false);
        } else {
            $checkPageNameButton.prop('disabled', true);
        }
    });

    $('#checkPageNameButton').on('click', function(event) {
        event.preventDefault(); // Prevent the default form submission behavior
        const formData = {
            uid: $('#searchResults').val(),
            pageName: $('#pageName').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Show loading effect
        $('#pageLoadingIcon').show();

        $.post('/DeviceEmulator/CheckPageNameAvailability', formData, function(response) {
            if (response.status) {
                // Handle success response
                $('.BtnCreateNewPage').show();
                // Set hidden inputs with name and username
                $('#hiddenName').val(response.name);
                $('#hiddenUsername').val(response.username);
            }
        }).fail(function(xhr, status, error) {
            // Handle error response
            alert('Error checking page name: ' + error);
        }).always(function() {
            // Hide loading effect
            $('#pageLoadingIcon').hide();
        });
    });

    $(document).ready(function() {
        $('.BtnCreateNewPage').on('click', function(event) {
            event.preventDefault();

            const formData = {
                pageName: $('#pageName').val(),
                uid: $('#searchResults').val(),
                name: $('#hiddenName').val(),
                username: $('#hiddenUsername').val(),
                category: $('#categorySelect').val(), // Include selected category
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Show loading effect
            showLoading();

            $.post('/DeviceEmulator/CreateNewFanpage', formData, function(response) {
                $('#createPageModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: 'Tạo fanpage mới thành công!',
                });
            }).fail(function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Lỗi khi tạo fanpage: ' + error,
                });
            }).always(function() {
                hideLoading();
            });
        });
    });

    $('#filterToolbarIcon').on('click', function(e) {
        e.preventDefault();
        $('#filterModal').modal('show');
    });


    function showLoading() {
        console.log("Hiển thị loading...");
        if ($('#loadingOverlay').length === 0) {
            $('body').append(`
                    <div id="loadingOverlay">
                        <div class="loader"></div>
                    </div>
                `);
        }
    }

    function hideLoading() {
        console.log("Ẩn loading...");
        $('#loadingOverlay').remove();
    }
</script>
@endsection