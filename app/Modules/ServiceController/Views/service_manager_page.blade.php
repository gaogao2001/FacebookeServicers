@extends('admin.layouts.master')

@section('title', 'Service Controller')

@section('head.scripts')

<style>
    .Hqitbackground {
        background-color: #f7f7f7;
        /* Nền sáng */
        color: #343a40 !important;
        /* Bắt buộc áp dụng màu */
        /* Màu chữ đậm dễ đọc */
        padding: 20px;
        /* Khoảng cách bao quanh */
    }

    /* --- Card --- */
    .card {
        background-color: #ffffff;
        /* Nền trắng cho card */
        border-radius: 8px;
        /* Bo góc card */
        box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.15);
        /* Hiệu ứng shadow nhẹ */
        padding: 20px;
        /* Khoảng cách nội dung trong card */
        margin-bottom: 20px;
        /* Khoảng cách giữa các card */
        display: flex;
        flex-direction: column;
        /* Sắp xếp nội dung theo cột */
        justify-content: space-between;
        /* Cân đối nội dung trong card */
        color: #333333 !important;
        /* Bắt buộc áp dụng màu */
    }

    .table-hover>tbody>tr:hover>* {
        --bs-table-accent-bg: #f5f5f5;
        /* Nền trắng sữa hơi xám */
        background-color: var(--bs-table-accent-bg);
        /* Áp dụng nền */
        color: #6b6b6b;
        /* Màu chữ xám đậm hơn */
    }

    .btn-update-version:hover {
        background-color: #007bff;
        /* Làm sáng màu xanh */
        box-shadow: 0 6px 15px rgba(0, 123, 255, 0.4);
        transform: translateY(-3px);
        /* Đẩy nút lên nhẹ */
    }





    /* Bố cục chung của bảng */
    .custom-table {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Header */
    .custom-table thead th {
        background: linear-gradient(135deg, #2196F3, #21CBF3);
        color: white;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 16px;
        padding: 15px;
    }

    /* Hover hàng */
    .custom-table tbody tr:hover {
        background-color: #f9f9f9;
        cursor: pointer;
        transform: scale(1.01);
        transition: all 0.2s ease;
    }

    /* Icon và văn bản căn giữa */
    .custom-table td,
    .custom-table th {
        vertical-align: middle;
        text-align: center;
    }

    /* Bo góc trạng thái */
    .status-line {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 5px 10px;
        border-radius: 25px;
        font-weight: bold;
        font-size: 14px;
    }

    .status-line i {
        font-size: 18px;
    }

    /* Màu sắc trạng thái */
    .status-running {
        background: #E8F5E9;
        color: #4CAF50;
    }

    .status-stopped {
        background: #FFF9E5;
        color: #FFC107;
    }

    .status-error {
        background: #FDECEA;
        color: #F44336;
    }

    /* Hiệu ứng nút hành động */
    .btn-xs {
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 8px;
        margin: 2px;
        transition: all 0.2s ease;
    }

    .btn-xs:hover {
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-outline-success:hover {
        background-color: #4CAF50;
        color: white;
    }

    .btn-outline-danger:hover {
        background-color: #F44336;
        color: white;
    }

    .btn-outline-primary:hover {
        background-color: #2196F3;
        color: white;
    }

    /* Hiệu ứng phóng to cho popup */
    .modal-zoom {
        transform: scale(0.7);
        transition: transform 0.3s ease-in-out;
    }

    .modal-zoom.show {
        transform: scale(1);
    }

    /* Form ẩn với hiệu ứng phóng to */
    .hidden-form {
        display: none;
        position: fixed;
        top: 45%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.7);
        opacity: 0;
        transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
        z-index: 1050;
        background: #575656;
        padding: 28px; /* Giảm kích thước padding */
        width: 56%; /* Giảm kích thước chiều rộng */
        max-width: 700px; /* Giảm giới hạn chiều rộng tối đa */
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        color: white; /* Đổi màu chữ thành trắng */
    }

    .hidden-form.show {
        display: block;
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .hidden-form .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .hidden-form .form-body {
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-height: 400px; /* Set a max height */
        overflow-y: auto; /* Enable vertical scrolling */
    }

    .hidden-form .form-body {
        margin-bottom: 15px;
    }

    .hidden-form .form-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    /* Tăng kích thước form historyForm */
    #historyForm {
        padding: 36px; /* Tăng kích thước padding */
        width: 72.8%; /* Tăng kích thước chiều rộng */
        max-width: 910px; /* Tăng giới hạn chiều rộng tối đa */
    }
</style>

@endsection


@section('content')

<!-- Form ẩn Customize -->
<div id="customizeForm" class="hidden-form">
    <div class="form-header">
        <h5 id="customizeFormTitle">Tùy Chỉnh Dịch Vụ</h5>
        <button type="button" class="btn-close" onclick="toggleForm('#customizeForm')"></button>
    </div>
    <div class="form-body">
        <!-- Nội dung tùy chỉnh -->
        <p>Đây là nội dung tùy chỉnh cho dịch vụ.</p>
        <div class="form-group">
            <label for="quantity">Số Lượng:</label>
            <input type="number" id="quantity" class="form-control" placeholder="Nhập số lượng" value="1">
        </div>
    </div>
    <div class="form-footer">
        <button type="button" class="btn btn-secondary" onclick="toggleForm('#customizeForm')">Đóng</button>
        <button type="button" class="btn btn-primary">Lưu thay đổi</button>
    </div>
</div>

<!-- Form ẩn History -->
<div id="historyForm" class="hidden-form">
    <div class="form-header">
        <h5 id="historyFormTitle">Lịch Sử Dịch Vụ</h5>
        <button type="button" class="btn-close" onclick="toggleForm('#historyForm')"></button>
    </div>
    <div class="form-body" id="historyContent">
        <!-- Nội dung lịch sử -->
        <p>Đang tải...</p>
    </div>
    <div class="form-footer">
        <button type="button" class="btn btn-secondary" onclick="toggleForm('#historyForm')">Đóng</button>
    </div>
</div>

<!-- Order Status -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-3">
                <h6 class="card-title text-center mb-3">
                    <i class="mdi mdi-server me-1"></i> System Service Status
                </h6>
                <div class="table-responsive">

                    <table class="table custom-table table-hover table-bordered table-sm align-middle">
                        <thead>
                            <tr>
                                <th><i class="mdi mdi-apps"></i> Service</th>
                                <th><i class="mdi mdi-check"></i> Status</th>
                                <th><i class="mdi mdi-clock-outline"></i> Start</th>
                                <th><i class="mdi mdi-calendar"></i> Last Check</th>
                                <th><i class="mdi mdi-settings"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="mdi mdi-share-outline text-primary" style="font-size: 20px;"></i> Facebook Share</td>
                                <td>
                                    <div class="status-line status-running">
                                        <i class="mdi mdi-check-circle"></i> Running
                                    </div>
                                </td>
                                <td>07:15 AM</td>
                                <td>08 Dec 2024</td>
                                <td>
                                    <button class="btn btn-xs btn-outline-success" disabled>
                                        <i class="mdi mdi-play"></i> Start
                                    </button>
                                    <button class="btn btn-xs btn-outline-danger">
                                        <i class="mdi mdi-stop"></i> Stop
                                    </button>
                                    <button class="btn btn-xs btn-outline-primary">
                                        <i class="mdi mdi-refresh"></i> Restart
                                    </button>
                                    <button class="btn btn-xs btn-outline-secondary customize-btn">
                                        <i class="mdi mdi-settings"></i> Customize
                                    </button>
                                    <button class="btn btn-xs btn-outline-info history-btn">
                                        <i class="mdi mdi-history"></i> History
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-account-multiple-outline text-info" style="font-size: 20px;"></i> Facebook Follow</td>
                                <td>
                                    <div class="status-line status-stopped">
                                        <i class="mdi mdi-pause-circle"></i> Stopped
                                    </div>
                                </td>
                                <td>--</td>
                                <td>08 Dec 2024</td>
                                <td>
                                    <button class="btn btn-xs btn-outline-success">
                                        <i class="mdi mdi-play"></i> Start
                                    </button>
                                    <button class="btn btn-xs btn-outline-danger" disabled>
                                        <i class="mdi mdi-stop"></i> Stop
                                    </button>
                                    <button class="btn btn-xs btn-outline-primary" disabled>
                                        <i class="mdi mdi-refresh"></i> Restart
                                    </button>
                                    <button class="btn btn-xs btn-outline-secondary customize-btn">
                                        <i class="mdi mdi-settings"></i> Customize
                                    </button>
                                    <button class="btn btn-xs btn-outline-info">
                                        <i class="mdi mdi-history"></i> History
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-comment-outline text-success" style="font-size: 20px;"></i> Facebook Seedings</td>
                                <td>
                                    @if($FacebookSeedings['status'] == 'running')
                                    <div class="status-line status-running">
                                        <i class="mdi mdi-check-circle"></i> Running
                                    </div>
                                    @else
                                    <div class="status-line status-stopped">
                                        <i class="mdi mdi-pause-circle"></i> Stopped
                                    </div>
                                    @endif
                                </td>
                                <td>{{ $FacebookSeedings['startTime'] }}</td>
                                <td>08 Dec 2024</td>
                                <td>
                                    <button class="btn btn-xs btn-outline-success" disabled>
                                        <i class="mdi mdi-play"></i> Start
                                    </button>
                                    <button class="btn btn-xs btn-outline-danger">
                                        <i class="mdi mdi-stop"></i> Stop
                                    </button>
                                    <button class="btn btn-xs btn-outline-primary">
                                        <i class="mdi mdi-refresh"></i> Restart
                                    </button>
                                    <button class="btn btn-xs btn-outline-secondary customize-btn">
                                        <i class="mdi mdi-settings"></i> Customize
                                    </button>
                                    <button class="btn btn-xs btn-outline-info history-btn">
                                        <i class="mdi mdi-history"></i> History
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-account-outline text-warning" style="font-size: 20px;"></i> Facebook Profile Seedings</td>
                                <td>
                                    <div class="status-line status-error">
                                        <i class="mdi mdi-alert-circle"></i> Error
                                    </div>
                                </td>
                                <td>06:30 AM</td>
                                <td>08 Dec 2024</td>
                                <td>
                                    <button class="btn btn-xs btn-outline-success" disabled>
                                        <i class="mdi mdi-play"></i> Start
                                    </button>
                                    <button class="btn btn-xs btn-outline-danger">
                                        <i class="mdi mdi-stop"></i> Stop
                                    </button>
                                    <button class="btn btn-xs btn-outline-primary">
                                        <i class="mdi mdi-refresh"></i> Restart
                                    </button>
                                    <button class="btn btn-xs btn-outline-secondary customize-btn">
                                        <i class="mdi mdi-settings"></i> Customize
                                    </button>
                                    <button class="btn btn-xs btn-outline-info history-btn">
                                        <i class="mdi mdi-history"></i> History
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-bookmark-outline text-danger" style="font-size: 20px;"></i> Facebook Page Seedings</td>
                                <td>
                                    <div class="status-line status-error">
                                        <i class="mdi mdi-alert-circle"></i> Error
                                    </div>
                                </td>
                                <td>06:30 AM</td>
                                <td>08 Dec 2024</td>
                                <td>
                                    <button class="btn btn-xs btn-outline-success" disabled>
                                        <i class="mdi mdi-play"></i> Start
                                    </button>
                                    <button class="btn btn-xs btn-outline-danger">
                                        <i class="mdi mdi-stop"></i> Stop
                                    </button>
                                    <button class="btn btn-xs btn-outline-primary">
                                        <i class="mdi mdi-refresh"></i> Restart
                                    </button>
                                    <button class="btn btn-xs btn-outline-secondary customize-btn">
                                        <i class="mdi mdi-settings"></i> Customize
                                    </button>
                                    <button class="btn btn-xs btn-outline-info history-btn">
                                        <i class="mdi mdi-history"></i> History
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-account-multiple-plus-outline text-success" style="font-size: 20px;"></i> Facebook Page Add Friends</td>
                                <td>
                                    <div class="status-line status-error">
                                        <i class="mdi mdi-alert-circle"></i> Error
                                    </div>
                                </td>
                                <td>06:30 AM</td>
                                <td>08 Dec 2024</td>
                                <td>
                                    <button class="btn btn-xs btn-outline-success" disabled>
                                        <i class="mdi mdi-play"></i> Start
                                    </button>
                                    <button class="btn btn-xs btn-outline-danger">
                                        <i class="mdi mdi-stop"></i> Stop
                                    </button>
                                    <button class="btn btn-xs btn-outline-primary">
                                        <i class="mdi mdi-refresh"></i> Restart
                                    </button>
                                    <button class="btn btn-xs btn-outline-secondary customize-btn">
                                        <i class="mdi mdi-settings"></i> Customize
                                    </button>
                                    <button class="btn btn-xs btn-outline-info history-btn">
                                        <i class="mdi mdi-history"></i> History
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-account-circle-outline text-primary" style="font-size: 20px;"></i> Facebook Profile Add Friends</td>
                                <td>
                                    <div class="status-line status-error">
                                        <i class="mdi mdi-alert-circle"></i> Error
                                    </div>
                                </td>
                                <td>06:30 AM</td>
                                <td>08 Dec 2024</td>
                                <td>
                                    <button class="btn btn-xs btn-outline-success" disabled>
                                        <i class="mdi mdi-play"></i> Start
                                    </button>
                                    <button class="btn btn-xs btn-outline-danger">
                                        <i class="mdi mdi-stop"></i> Stop
                                    </button>
                                    <button class="btn btn-xs btn-outline-primary">
                                        <i class="mdi mdi-refresh"></i> Restart
                                    </button>
                                    <button class="btn btn-xs btn-outline-secondary customize-btn">
                                        <i class="mdi mdi-settings"></i> Customize
                                    </button>
                                    <button class="btn btn-xs btn-outline-info history-btn">
                                        <i class="mdi mdi-history"></i> History
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function toggleForm(formId) {
    const form = $(formId);
    if (!form.hasClass('show')) {
        form.css('display', 'block');
        setTimeout(() => {
            form.addClass('show');
        }, 10); // Đợi để kích hoạt hiệu ứng
    } else {
        form.removeClass('show');
        setTimeout(() => {
            form.css('display', 'none');
        }, 300); // Chờ hiệu ứng kết thúc
    }
}

$(document).ready(function() {
    $('.customize-btn').on('click', function() {
        const serviceName = $(this).closest('tr').find('td:first').text().trim();
        $('#customizeFormTitle').text(`Tùy Chỉnh Dịch Vụ: ${serviceName}`);
        toggleForm('#customizeForm');
    });

    $('.history-btn').on('click', function() {
        const serviceName = $(this).closest('tr').find('td:first').text().trim();
        $('#historyFormTitle').text(`Lịch Sử Dịch Vụ: ${serviceName}`);
        $('#historyContent').html('<p>Đang tải...</p>');
        $.get('/service_manager_page/history', function(data) {
            $('#historyContent').html(data.history);
        });
        toggleForm('#historyForm');
    });
});
</script>

@endsection