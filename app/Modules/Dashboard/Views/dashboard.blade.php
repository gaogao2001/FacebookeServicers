@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
<link rel="stylesheet" href="{{ asset('modules/dashboard/css/dashboard.css') }}">

<script src="{{ asset('modules/dashboard/js/dashboard.js') }}" defer></script>

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
        padding: 1px 1px;
    }

    .card {
        position: relative;
        /* Đảm bảo card là relative */
    }

    .btn-icon {
        position: absolute;
        top: 8px;
        /* Căn chỉnh khoảng cách từ trên */
        right: 8px;
        /* Căn chỉnh khoảng cách từ phải */
        background: transparent;
        /* Nền trong suốt */
        border: none;
        /* Không có viền */
        color: #ffffff;
        /* Màu trắng */
        font-size: 1.2rem;
        /* Kích thước icon */
        z-index: 10;
        /* Đảm bảo luôn nằm trên */
    }

    .btn-icon:hover {
        color: #ddd;
        /* Thêm hiệu ứng hover */
    }
</style>

@endsection

@section('content')

<div class="content-wrapper " style="background: #191c24;">
    <div class="container " style="padding-top:30px;">

        <div class="row">
            <div class="col-md-12">
                <div class="card wrapper">
                    <div class="card-body text-center">
                        <h4 class="card-title mb-0" id="linkCountTitle" style="color: #bfbfbf; font-weight: bold;">
                            <i class="fas fa-tachometer-alt"></i> 1: Báo cáo tình trạng CPU
                        </h4>
                    </div>
                </div>
            </div>
            <div class="row mt-4 justify-content-center">
                <!-- CPU Card -->
                <div class="col-xl-3 col-lg-3 mb-4">
                    <div class="card l-bg-red">
                        <div class="card-statistic-3 p-4 text-center">
                            <div class="card-icon card-icon-large mb-3">
                                <i class="fas fa-cog spin-icon"></i>
                            </div>
                            <h5 class="card-title mb-2 text-white">
                                <i class="fas fa-microchip"></i> CPU
                            </h5>
                            <div class="display-4 text-white" id="currentCpu">
                                <i class="fas fa-percent"></i> 0%
                            </div>
                        </div>
                    </div>
                </div>
                <!-- RAM Card -->
                <div class="col-xl-3 col-lg-3 mb-4">
                    <div class="card l-bg-purple">
                        <div class="card-statistic-3 p-4 text-center">
                            <div class="card-icon card-icon-large mb-3">
                                <i class="fas fa-sync-alt spin-icon"></i>
                            </div>
                            <h5 class="card-title mb-2 text-white">
                                <i class="fas fa-memory"></i> RAM
                            </h5>
                            <div class="display-4 text-white" id="ramUsage">
                                <i class="fas fa-percent"></i> 0%
                            </div>
                        </div>
                    </div>
                </div>
                <!-- SSD Card -->
                <div class="col-xl-3 col-lg-3 mb-4">
                    <div class="card l-bg-black">
                        <div class="card-statistic-3 p-4 text-center">
                            <div class="card-icon card-icon-large mb-3">
                                <i class="fas fa-hdd spin-icon"></i>
                            </div>
                            <h5 class="card-title mb-2 text-white">
                                <i class="fas fa-loading"></i> SSD
                            </h5>
                            <div class="display-4 text-white" id="diskUsage">
                                <i class="fas fa-loading"></i> 0%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="container " style="padding-top: 30px;">

            <div class="row">
                <div class="col-md-12  ">
                    <div class="card wrapper ">
                        <div class="card-body">
                            <h4 class="card-title mb-0" id="linkCountTitle" style="  color: #bfbfbf"> 2: Tương tác hệ thống</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3" style="padding-right: 20px;">
                    <div class="card l-bg-cherry">
						<!-- Nút icon dấu ba chấm -->
                        <button class="btn btn-light btn-icon position-absolute top-0 end-0 m-2">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="card-statistic-3 p-4 text-center">
                            <!-- Phần icon và tiêu đề -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <i class="fab fa-facebook-square text-white display-4 me-3"></i>
                                <h5 class="card-title mb-0 text-white fw-bold">Facebook Services</h5>
                            </div>

                            <!-- Phần trạng thái -->
                            <div class="mb-4">
                                <span class="badge status-badge {{ $FbService === 'running' ? 'badge-running' : 'badge-stopped' }}">
                                    <i class="fas {{ $FbService === 'running' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $FbService === 'running' ? 'Đang chạy' : 'Dừng' }}
                                </span>
                            </div>

                            <!-- Phần nút điều khiển -->
                            <div class="template-demo mt-4">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="ON" data-name="smbd" {{ $serviceStatus === 'running' ? 'disabled' : '' }}>
                                        <i class="fas fa-play-circle"></i> Start
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="OFF" data-name="smbd" {{ $serviceStatus === 'stopped' ? 'disabled' : '' }}>
                                        <i class="fas fa-stop-circle"></i> Stop
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="RESTART" data-name="smbd">
                                        <i class="fas fa-sync"></i> Restart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-xl-3 col-lg-3" style="padding-right: 20px;">
                    <div class="card l-bg-green-dark">
						<!-- Nút icon dấu ba chấm -->
                        <button class="btn btn-light btn-icon position-absolute top-0 end-0 m-2">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="card-statistic-3 p-4 text-center">
                            <!-- Phần icon và tiêu đề -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-comments text-white display-4 me-3"></i>
                                <h5 class="card-title mb-0 text-white fw-bold">Zalo Service</h5>
                            </div>

                            <!-- Phần trạng thái -->
                            <div class="mb-4">
                                <span class="badge status-badge {{ $ZaloService === 'running' ? 'badge-running' : 'badge-stopped' }}">
                                    <i class="fas {{ $ZaloService === 'running' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $ZaloService === 'running' ? 'Đang chạy' : 'Dừng' }}
                                </span>
                            </div>

                            <!-- Phần nút điều khiển -->
                            <div class="template-demo mt-4">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="ON" data-name="smbd" {{ $serviceStatus === 'running' ? 'disabled' : '' }}>
                                        <i class="fas fa-play-circle"></i> Start
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="OFF" data-name="smbd" {{ $serviceStatus === 'stopped' ? 'disabled' : '' }}>
                                        <i class="fas fa-stop-circle"></i> Stop
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="RESTART" data-name="smbd">
                                        <i class="fas fa-sync"></i> Restart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-xl-3 col-lg-3" style="padding-right: 20px;">
                    <div class="card l-bg-blue-dark">
						<!-- Nút icon dấu ba chấm -->
                        <button class="btn btn-light btn-icon position-absolute top-0 end-0 m-2">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="card-statistic-3 p-4 text-center">
                            <!-- Phần icon và tiêu đề -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-network-wired text-white display-4 me-3"></i>
                                <h5 class="card-title mb-0 text-white fw-bold">Proxy System</h5>
                            </div>

                            <!-- Phần trạng thái -->
                            <div class="mb-4">
                                <span class="badge status-badge {{ $ProxyService === 'running' ? 'badge-running' : 'badge-stopped' }}">
                                    <i class="fas {{ $ProxyService === 'running' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $ProxyService === 'running' ? 'Đang chạy' : 'Dừng' }}
                                </span>
                            </div>

                            <!-- Phần nút điều khiển -->
                            <div class="template-demo mt-4">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="ON" data-name="smbd" {{ $serviceStatus === 'running' ? 'disabled' : '' }}>
                                        <i class="fas fa-play-circle"></i> Start
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="OFF" data-name="smbd" {{ $serviceStatus === 'stopped' ? 'disabled' : '' }}>
                                        <i class="fas fa-stop-circle"></i> Stop
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="RESTART" data-name="smbd">
                                        <i class="fas fa-sync"></i> Restart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3">
                    <div class="card l-bg-orange-dark">
						<!-- Nút icon dấu ba chấm -->
                        <button class="btn btn-light btn-icon position-absolute top-0 end-0 m-2">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="card-statistic-3 p-4 text-center">
                            <!-- Phần icon và tiêu đề -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-database text-white display-4 me-3"></i>
                                <h5 class="card-title mb-0 text-white fw-bold">NAS Data</h5>
                            </div>

                            <!-- Phần trạng thái -->
                            <div class="mb-4">
                                <span class="badge status-badge {{ $serviceStatus === 'running' ? 'badge-running' : 'badge-stopped' }}">
                                    <i class="fas {{ $serviceStatus === 'running' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $serviceStatus === 'running' ? 'Đang chạy' : 'Dừng' }}
                                </span>
                            </div>

                            <!-- Phần nút điều khiển -->
                            <div class="template-demo mt-4">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="ON" data-name="smbd" {{ $serviceStatus === 'running' ? 'disabled' : '' }}>
                                        <i class="fas fa-play-circle"></i> Start
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="OFF" data-name="smbd" {{ $serviceStatus === 'stopped' ? 'disabled' : '' }}>
                                        <i class="fas fa-stop-circle"></i> Stop
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="RESTART" data-name="smbd">
                                        <i class="fas fa-sync"></i> Restart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-xl-3 col-lg-3" style="padding-right: 20px;">
                    <div class="card l-bg-orange-dark">
						<!-- Nút icon dấu ba chấm -->
                        <button class="btn btn-light btn-icon position-absolute top-0 end-0 m-2">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="card-statistic-3 p-4 text-center">
                            <!-- Phần icon và tiêu đề -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-cloud-upload-alt text-white display-4 me-3"></i>
                                <h5 class="card-title mb-0 text-white fw-bold">Backup Dữ Liệu</h5>
                            </div>
                            <!-- Phần trạng thái -->
                            <div class="mb-4">
                                <span class="badge status-badge {{ $BackupService === 'running' ? 'badge-running' : 'badge-stopped' }}">
                                    <i class="fas {{ $BackupService === 'running' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $BackupService === 'running' ? 'Đang chạy' : 'Dừng' }}
                                </span>
                            </div>
                            <!-- Phần nút điều khiển -->
                            <div class="template-demo mt-4">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="ON" data-name="mongodb-backup.timer" {{ $BackupService === 'running' ? 'disabled' : '' }}>
                                        <i class="fas fa-play-circle"></i> Start
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="OFF" data-name="mongodb-backup.timer" {{ $BackupService === 'stopped' ? 'disabled' : '' }}>
                                        <i class="fas fa-stop-circle"></i> Stop
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="RESTART" data-name="mongodb-backup.timer">
                                        <i class="fas fa-sync"></i> Restart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3" style="padding-right: 20px;">
                    <div class="card l-bg-orange-dark">
						<!-- Nút icon dấu ba chấm -->
                        <button class="btn btn-light btn-icon position-absolute top-0 end-0 m-2">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="card-statistic-3 p-4 text-center">
                            <!-- Phần icon và tiêu đề -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-network-wired text-white display-4 me-3"></i>
                                <h5 class="card-title mb-0 text-white fw-bold">Network History</h5>
                            </div>
                            <!-- Phần trạng thái -->
                            <div class="mb-4">
                                <span class="badge status-badge {{ $NetworkMonitor === 'running' ? 'badge-running' : 'badge-stopped' }}">
                                    <i class="fas {{ $NetworkMonitor === 'running' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $NetworkMonitor === 'running' ? 'Đang chạy' : 'Dừng' }}
                                </span>
                            </div>
                            <!-- Phần nút điều khiển -->
                            <div class="template-demo mt-4">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="ON" data-name="network_monitor.service" {{ $NetworkMonitor === 'running' ? 'disabled' : '' }}>
                                        <i class="fas fa-play-circle"></i> Start
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="OFF" data-name="network_monitor.service" {{ $NetworkMonitor === 'stopped' ? 'disabled' : '' }}>
                                        <i class="fas fa-stop-circle"></i> Stop
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="RESTART" data-name="network_monitor.service">
                                        <i class="fas fa-sync"></i> Restart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-xl-3 col-lg-3" style="padding-right: 20px;">
                    <div class="card l-bg-orange-dark">
						<!-- Nút icon dấu ba chấm -->
                        <button class="btn btn-light btn-icon position-absolute top-0 end-0 m-2">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="card-statistic-3 p-4 text-center">
                            <!-- Phần icon và tiêu đề -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-reply text-white display-4 me-3"></i>
                                <h5 class="card-title mb-0 text-white fw-bold">System History</h5>
                            </div>
                            <!-- Phần trạng thái -->
                            <div class="mb-4">
                                <span class="badge status-badge {{ $SystemMonitor === 'running' ? 'badge-running' : 'badge-stopped' }}">
                                    <i class="fas {{ $SystemMonitor === 'running' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $SystemMonitor === 'running' ? 'Đang chạy' : 'Dừng' }}
                                </span>
                            </div>
                            <!-- Phần nút điều khiển -->
                            <div class="template-demo mt-4">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="ON" data-name="system_monitor" {{ $SystemMonitor === 'running' ? 'disabled' : '' }}>
                                        <i class="fas fa-play-circle"></i> Start
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="OFF" data-name="system_monitor" {{ $SystemMonitor === 'stopped' ? 'disabled' : '' }}>
                                        <i class="fas fa-stop-circle"></i> Stop
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="RESTART" data-name="system_monitor">
                                        <i class="fas fa-sync"></i> Restart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3" style="padding-right: 20px;">
                    <div class="card l-bg-orange-dark position-relative">
                        <!-- Nút icon dấu ba chấm -->
                        <button class="btn btn-light btn-icon position-absolute top-0 end-0 m-2">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>

                        <div class="card-statistic-3 p-4 text-center">
                            <!-- Phần icon và tiêu đề -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-reply text-white display-4 me-3"></i>
                                <h5 class="card-title mb-0 text-white fw-bold">Clear Log</h5>
                            </div>
                            <!-- Phần trạng thái -->
                            <div class="mb-4">
                                <span class="badge status-badge {{ $clearLog === 'running' ? 'badge-running' : 'badge-stopped' }}">
                                    <i class="fas {{ $clearLog === 'running' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $clearLog === 'running' ? 'Đang chạy' : 'Dừng' }}
                                </span>
                            </div>
                            <!-- Phần nút điều khiển -->
                            <div class="template-demo mt-4">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="ON" data-name="clearLog.timer" {{ $clearLog === 'running' ? 'disabled' : '' }}>
                                        <i class="fas fa-play-circle"></i> Start
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="OFF" data-name="clearLog.timer" {{ $clearLog === 'stopped' ? 'disabled' : '' }}>
                                        <i class="fas fa-stop-circle"></i> Stop
                                    </button>
                                    <button type="button" class="btn btn-outline-light SeviceControler" data-action="/SeviceControler" data-option="RESTART" data-name="clearLog.timer">
                                        <i class="fas fa-sync"></i> Restart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    @endsection