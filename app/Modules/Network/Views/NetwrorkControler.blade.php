@extends('admin.layouts.master')

@section('title', 'Quản Lý Mạng')

@section('head.scripts')
<script src="{{ asset('modules/network/js/networkcontroler.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .wrapper {
        background: #191c24;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 30px 40px;
        border-radius: 10px;
    }

    .form-control {
        background-color: transparent;
        color: #FFFFFF;
        border: 2px solid rgba(255, 255, 255, .2);
        font-size: 16px;
        padding: 10px;
        border-radius: 5px;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
        border-radius: 5px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 30px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #28a745;
    }

    input:checked+.slider:before {
        transform: translateX(30px);
    }

    .group-container {
        display: flex;
        flex-direction: column;
        margin-bottom: 20px;
    }

    .group-box {
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 5px;
        padding: 15px;
        position: relative;
        margin-bottom: 20px;
    }

    .group-title {
        position: absolute;
        top: -12px;
        left: 15px;
        background-color: #191c24;
        padding: 0 10px;
        font-size: 16px;
        font-weight: bold;
    }

    .network-container {
        display: flex;
        justify-content: space-between;
    }

    .port-label {
        font-size: 16px;
        font-weight: 500;
    }

    .port-status {
        font-size: 14px;
        color: #aaa;
    }

    .pppoe-options {
        display: none;
        margin-top: 10px;
    }
	
	table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
</style>
@endsection

@section('content')
<div class="content-wrapper wrapper">
    <div class="page-header text-center mb-4">
        <h3 class="page-title">Quản Lý Mạng</h3>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h2>Thống kê cổng mạng</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Cổng mạng</th>
                                <th>Số lượng IPv4</th>
                                <th>Số lượng IPv6</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($NetworkInfo as $info)
                            <tr>
                                <td>{{ $info->iface }}</td>
                                <td>{{ $info->ipv4_count }}</td>
                                <td>{{ $info->ipv6_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">Cài Đặt Thông Tin Mạng</h4>
                    <form action="{{ route('UpdateNetworkConfig.index') }}" method="POST">
                        @csrf
                        <div class="group-container">
                            @foreach ($interface as $portName => $details)
                            @php
                            $settingsData = collect($settings)->first(function ($setting) use ($portName) {
                            return isset($setting[$portName]);
                            });

                            $portSettings = $settingsData[$portName] ?? [];
                            @endphp
                            <div class="group-box">
                                <div class="group-title">{{ $portName }}</div>
                                <div class="network-switch">
                                    <span class="port-label">{{ $portName }}</span>
                                    <label class="switch">
                                        <input type="checkbox" data-action="/InterfaceControler" name="{{ $portName }}[status]" id="{{ $portName }}" {{ $details->status ?? false ? 'checked' : '' }} {{ $details->isDefault ? 'disabled' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    <span class="port-status">{{ $details->status ? 'Đã kết nối' : 'Chưa kết nối' }}</span>
                                </div>

                                @if ($details->isDefault)
                                <div class="alert alert-danger mt-3" style="font-size: 14px; color: red;">
                                    Đây là cổng mặc định nên hệ thống không cho phép cấu hình PPPoE. Cổng phục vụ kết nối LAN Remote
                                </div>
                                @endif
                                <div class="pppoe-options" id="pppoe-options-{{ $portName }}" style="{{ $details->isDefault ? 'display: none;' : '' }}">
                                    <div class="form-group mb-3">
                                        <label for="{{ $portName }}-pppoeUsername">Username PPPoE</label>
                                        <input type="text" name="{{ $portName }}[pppoe_username]" class="form-control" id="{{ $portName }}-pppoeUsername" placeholder="Nhập Username PPPoE" value="{{ $portSettings['pppoe_username'] ?? '' }}" {{ $details->isDefault ? 'disabled' : '' }}>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="{{ $portName }}-pppoePassword">Password PPPoE</label>
                                        <input type="password" name="{{ $portName }}[pppoe_password]" class="form-control" id="{{ $portName }}-pppoePassword" placeholder="Nhập Password PPPoE" value="{{ $portSettings['pppoe_password'] ?? '' }}" {{ $details->isDefault ? 'disabled' : '' }}>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="{{ $portName }}-proxyPort1">Limit Render</label>
                                        <input type="number" name="{{ $portName }}[limit_render]" class="form-control" id="{{ $portName }}-limit_render" value="{{ $portSettings['limit_render'] ?? '1' }}" {{ $details->isDefault ? 'disabled' : '' }}>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label>Nhà Mạng</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="{{ $portName }}[network]" id="{{ $portName }}-networkFPT" value="FPT" {{ ($portSettings['network'] ?? '') == 'FPT' ? 'checked' : '' }} {{ $details->isDefault ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="{{ $portName }}-networkFPT">FPT</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="{{ $portName }}[network]" id="{{ $portName }}-networkViettel" value="Viettel" {{ ($portSettings['network'] ?? '') == 'Viettel' ? 'checked' : '' }} {{ $details->isDefault ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="{{ $portName }}-networkViettel">Viettel</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary px-4 btnLuuThongTin">Lưu Thông Tin</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const switches = document.querySelectorAll('.network-switch input[type="checkbox"]');
    switches.forEach(switchInput => {
        const statusLabel = switchInput.closest('.network-switch').querySelector('.port-status');
        const pppoeOptions = document.getElementById('pppoe-options-' + switchInput.name.match(/(\w+)\[status\]/)[1]);

        function updateStatus() {
            const isDefault = switchInput.hasAttribute('disabled');
            if (!isDefault) {
                statusLabel.textContent = switchInput.checked ? 'Đã kết nối' : 'Chưa kết nối';
                statusLabel.style.color = switchInput.checked ? '#28a745' : '#aaa';
                pppoeOptions.style.display = switchInput.checked ? 'block' : 'none';
            } else {
                pppoeOptions.style.display = 'none'; // Always hide if default
            }
        }

        updateStatus();
        switchInput.addEventListener('change', updateStatus);
    });
});


</script>