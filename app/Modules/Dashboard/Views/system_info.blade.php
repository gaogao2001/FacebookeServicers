@extends('admin.layouts.master')

@section('title', 'Thông tin hệ thống')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
<link rel="stylesheet" href="{{ asset('modules/dashboard/css/dashboard.css') }}">
<style>
    /* --- Nền Tổng Thể (Hqitbackground) --- */
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

    /* --- Card Header --- */
    .card-header {
        background-color: #ffffff;
        /* Nền trắng */
        color: #1e293b;
        /* Màu xanh đậm dễ nhìn */
        font-weight: bold;
        /* Chữ đậm hơn */
        font-size: 1.2em;
        /* Tăng kích thước chữ */
        border-bottom: 1px solid #e0e0e0;
        /* Viền mảnh phân cách header và body */
        padding: 15px;
        /* Khoảng cách hợp lý */
        text-align: center;
        /* Căn giữa tiêu đề header */
    }

    /* --- Card Body --- */
    .card-body {
        background-color: #ffffff;
        /* Nền trắng */
        color: #2c3e50;
        /* Màu chữ xanh đậm hơn */
        padding: 20px;
        /* Khoảng cách nội dung */
        border-radius: 8px;
        /* Bo góc body để đồng bộ */
        display: flex;
        flex-direction: column;
        /* Nội dung sắp xếp theo cột */
        align-items: center;
        /* Căn giữa nội dung */
    }

    /* --- Tiêu Đề Card (card-title) --- */
    .card-title {
        color: #232121 !important;
        /* Bắt buộc áp dụng màu */
        /* Màu xanh đậm dễ nhìn */
        font-weight: bold;
        /* Chữ đậm để dễ đọc hơn */
        font-size: 1.3em;
        /* Tăng kích thước chữ */
        margin-bottom: 0.5em;
        /* Tạo khoảng cách bên dưới */
        text-align: center;
        /* Căn giữa tiêu đề */
    }

    /* --- Nội Dung Card (text, icon) --- */
    .card .card-body p {
        font-size: 1em;
        font-weight: 500;
        /* Chữ đậm vừa đủ */
        margin: 0.5em 0 0;
        /* Khoảng cách trên/dưới */
        text-align: center;
        /* Căn giữa nội dung */
    }

    .card .card-body i {
        font-size: 3em;
        /* Icon lớn hơn */
        margin-bottom: 0.5em;
        /* Khoảng cách giữa icon và text */
        color: #007bff;
        /* Màu xanh lam mặc định cho icon */
    }

    /* --- Badge Trạng Thái --- */
    .badge-success {
        background-color: #28a745;
        /* Màu xanh lá */
        color: #ffffff;
        /* Chữ trắng dễ nhìn */
        font-weight: bold;
        /* Chữ đậm hơn */
        padding: 0.25em 0.5em;
        /* Khoảng cách trong badge */
        border-radius: 12px;
        /* Bo tròn badge */
    }

    .badge-warning {
        background-color: #ffc107;
        /* Màu vàng */
        color: #212529;
        /* Chữ đen rõ ràng */
        font-weight: bold;
        padding: 0.25em 0.5em;
        border-radius: 12px;
    }

    .badge-danger {
        background-color: #dc3545;
        /* Màu đỏ */
        color: #ffffff;
        /* Chữ trắng rõ ràng */
        font-weight: bold;
        padding: 0.25em 0.5em;
        border-radius: 12px;
    }

    /* --- Các Card Trạng Thái --- */
    .card-status {
        text-align: center;
        /* Căn giữa toàn bộ nội dung */
        padding: 15px;
        background-color: #ffffff;
        /* Nền trắng */
        border-radius: 8px;
        /* Bo góc card */
        box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.15);
        /* Shadow nhẹ */
    }

    .card-status h5 {
        font-size: 1.1em;
        color: #2c3e50;
        /* Chữ xanh đậm */
        font-weight: bold;
        margin-top: 10px;
        /* Khoảng cách phía trên */
    }

    .card-status p {
        font-size: 0.9em;
        font-weight: 500;
        color: #343a40;
        /* Chữ xám đậm */
        margin: 5px 0;
    }

    .table-hover>tbody>tr:hover>* {
        --bs-table-accent-bg: #f5f5f5;
        /* Nền trắng sữa hơi xám */
        background-color: var(--bs-table-accent-bg);
        /* Áp dụng nền */
        color: #6b6b6b;
        /* Màu chữ xám đậm hơn */
    }



    .card-body {
        padding: 10px;
    }

    .card-title {
        font-size: 14px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .table {
        font-size: 11px;
    }

    .table td,
    .table th {
        padding: 5px;
    }

    /* Status line */
    .status-line {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 12px;
        font-weight: bold;
        color: #212529;
        text-align: center;
    }

    /* Màu sắc cho icon trạng thái */
    .text-success {
        color: #28a745;
    }

    .text-warning {
        color: #ffc107;
    }

    .text-danger {
        color: #dc3545;
    }

    /* Trạng thái text */
    .status-label {
        text-transform: uppercase;
        font-size: 11px;
        line-height: 1.5;
    }


    /* Nút cập nhật phiên bản mới */
    .btn-update-version {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 15px 30px;
        /* Tăng chiều dài nút */
        font-size: 16px;
        font-weight: bold;
        color: #fff;
        background-color: #0056b3;
        /* Xanh đậm */
        border: none;
        border-radius: 15px;
        /* Bo góc mềm mại */
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        min-width: 250px;
        /* Đảm bảo nút đủ dài */
    }

    /* Hiệu ứng hover */
    .btn-update-version:hover {
        background-color: #007bff;
        /* Làm sáng màu xanh */
        box-shadow: 0 6px 15px rgba(0, 123, 255, 0.4);
        transform: translateY(-3px);
        /* Đẩy nút lên nhẹ */
    }

    /* Icon trong nút */
    .btn-update-version i {
        font-size: 20px;
    }
</style>
@endsection

@section('content')
<div class="Hqitbackground">
    <!-- Nội dung chính -->
    <div class="container-fluid mt-4">


        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <!-- Tiêu đề -->
                        <h4 class="card-title text-center mb-4">
                            <i class="fas fa-sync-alt text-primary me-2"></i> Phiên bản Hệ Thống
                        </h4>
                        <!-- Nội dung -->
                        <div class="row align-items-center">
                            <!-- Khu vực thông tin phiên bản -->
                            <div class="col-lg-9">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td><strong>Phiên bản hiện tại:</strong></td>
                                            <td>8.3.0</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngày phát hành:</strong></td>
                                            <td>08 Dec 2024</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Trạng thái:</strong></td>
                                            <td><span class="badge badge-success">Hoạt động</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Nút cập nhật phiên bản -->
                            <div class="col-lg-3 text-center">
                                <button id="updateVersionButton" class="btn-update-version">
                                    <i class="fas fa-sync-alt"></i> Cập nhật phiên bản mới
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>










        <div class="row">

            <style>
                /* Card container */
                .card.wrapper {
                    background: linear-gradient(135deg, #ffffff, #f7f7f7);
                    border: 1px solid #e0e0e0;
                    border-radius: 15px;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
                    overflow: hidden;
                    transition: all 0.3s ease;
                }

                /* Card hover effect */
                .card.wrapper:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.25);
                }

                /* Card title */
                .card.wrapper .card-title {
                    color: #007bff;
                    font-size: 18px;
                    font-weight: bold;
                    text-transform: uppercase;
                    text-align: center;
                    margin-bottom: 20px;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #007bff;
                }

                /* Table inside card */
                .card.wrapper .table-hover {
                    font-size: 14px;
                    color: #343a40;
                }

                .card.wrapper .table-hover tbody tr td {
                    padding: 10px 15px;
                    border-bottom: 1px solid #e0e0e0;
                    font-weight: 500;
                }

                .card.wrapper .table-hover tbody tr td:first-child {
                    color: #495057;
                    font-weight: bold;
                }

                /* Last row without border */
                .card.wrapper .table-hover tbody tr:last-child td {
                    border-bottom: none;
                }

                /* Subtle row hover effect */
                .card.wrapper .table-hover tbody tr:hover {
                    background-color: #f8f9fa;
                    cursor: pointer;
                }

                /* Add padding for mobile view */
                @media (max-width: 768px) {
                    .card.wrapper {
                        padding: 15px;
                    }

                    .card.wrapper .table-hover tbody tr td {
                        padding: 8px 10px;
                    }

                    .card.wrapper .card-title {
                        font-size: 16px;
                    }
                }
            </style>
            <!-- Card Thông tin hệ thống -->
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card wrapper">
                    <div class="card-body">
                        <h4 class="card-title">Thông tin hệ thống</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <td>Phiên bản hệ thống:</td>
                                        <td>8.3.0</td>
                                    </tr>
                                    <tr>
                                        <td>Ngày cập nhật:</td>
                                        <td>08 Dec 2024</td>
                                    </tr>
                                    <tr>
                                        <td>Máy chủ:</td>
                                        <td>Server A</td>
                                    </tr>
                                    <tr>
                                        <td>PHP Version:</td>
                                        <td>8.3</td>
                                    </tr>
                                    <tr>
                                        <td>MongoDB Version:</td>
                                        <td>6.0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Thông tin bản quyền -->
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card wrapper">
                    <div class="card-body">
                        <h4 class="card-title">Thông tin bản quyền</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <td>Tên sản phẩm:</td>
                                        <td>Quản lý hệ thống</td>
                                    </tr>
                                    <tr>
                                        <td>Người sở hữu bản quyền:</td>
                                        <td>Hoàng Quý</td>
                                    </tr>
                                    <tr>
                                        <td>Giấy phép:</td>
                                        <td>GPL v3</td>
                                    </tr>
                                    <tr>
                                        <td>Hỗ trợ:</td>
                                        <td>24/7</td>
                                    </tr>
                                    <tr>
                                        <td>Thời gian hiệu lực:</td>
                                        <td>2024-2025</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Trạng thái hệ thống -->
        <!-- Trạng thái hệ thống -->
        <div class="container mt-4">
            <div class="row g-4">
                <!-- Card 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="card status-card shadow-sm">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-primary text-white mb-3">
                                <i class="fas fa-server" style="font-size: 2.5em;"></i>
                            </div>
                            <h5 class="card-title">Trạng thái máy chủ</h5>
                            <p class="status-text text-success">Hoạt động</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="card status-card shadow-sm">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-warning text-white mb-3">
                                <i class="fas fa-shield-alt" style="font-size: 2.5em;"></i>
                            </div>
                            <h5 class="card-title">Trạng thái bảo mật</h5>
                            <p class="status-text text-warning">Bảo mật tốt</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="card status-card shadow-sm">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-dark text-white mb-3">
                                <i class="fas fa-user-shield" style="font-size: 2.5em;"></i>
                            </div>
                            <h5 class="card-title">Quản trị viên</h5>
                            <p class="status-text text-dark">Hoàng Quý</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
<style>
    #overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    #overlay.show {
        display: block;
        opacity: 1;
    }

    #popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 10%;
        height: 10%;
        padding: 0;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        overflow: hidden;
    }

    #popup-content {
        padding: 20px;
        font-size: 16px;
        color: #333;
    }

    #close-popup {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #ff5e5e;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
    }
</style>


</style>

<div id="overlay"></div>
<div id="popup">
    <div id="popup-content"></div>
    <button id="close-popup">×</button>
</div>



<script>
    $(document).ready(function() {
        // Hiển thị popup với hiệu ứng mở rộng
        $(".table-hover tbody tr").on("dblclick", function() {
            // Tạo nội dung bảng mới
            var popupContent = '<h4>Thông tin chi tiết</h4>';
            popupContent += '<table class="table table-bordered">';
            popupContent += '<thead>';
            popupContent += '<tr>';
            popupContent += '<th>Cột 1</th>';
            popupContent += '<th>Cột 2</th>';
            popupContent += '<th>Cột 3</th>';
            popupContent += '</tr>';
            popupContent += '</thead>';
            popupContent += '<tbody>';
            popupContent += '<tr>';
            popupContent += '<td>Nội dung 1</td>';
            popupContent += '<td>Nội dung 2</td>';
            popupContent += '<td>Nội dung 3</td>';
            popupContent += '</tr>';
            popupContent += '<tr>';
            popupContent += '<td>Nội dung 4</td>';
            popupContent += '<td>Nội dung 5</td>';
            popupContent += '<td>Nội dung 6</td>';
            popupContent += '</tr>';
            popupContent += '</tbody>';
            popupContent += '</table>';

            // Chèn nội dung vào popup
            $("#popup-content").html(popupContent);

            // Hiển thị overlay
            $("#overlay").addClass("show");

            // Hiển thị popup nhỏ trước, sau đó phóng to dần
            $("#popup")
                .css({
                    display: "block",
                    width: "10%",
                    height: "10%"
                })
                .animate({
                        width: "80%",
                        height: "80%"
                    },
                    500 // Thời gian hiệu ứng (500ms)
                );
        });

        // Đóng popup khi nhấn vào overlay hoặc nút đóng
        $("#close-popup, #overlay").on("click", function() {
            // Thu nhỏ popup trước khi ẩn
            $("#popup").animate({
                    width: "10%",
                    height: "10%"
                },
                300,
                function() {
                    // Sau khi thu nhỏ, ẩn popup và overlay
                    $("#popup").css("display", "none");
                    $("#overlay").removeClass("show");
                }
            );
        });
    });
</script>


@endsection