@extends ('admin.layouts.master')

@section('title', 'Config Auto')

@section('head.scripts')
<link rel="stylesheet" href="{{ asset('assets/admin/css/auto_config.css') }}">
@endsection

@section('content')
<div class="content-wrapper " style="background: #191c24;">
    <div class="container-fluid">
        <div class="row">
            <!-- Bảng danh sách quyền -->
            <div class="col-md-4">
                <div class="card wrapper">
                    <div class="card-body">
                        <form action="{{ route('facebook.config.save') }}" method="post">
                            @csrf
                            <h4 class="card-title title">Cấu hình tự động cho Facebook</h4>
                            <!-- Auto -->
                            <span>
                                <!-- Input ẩn để gửi giá trị false nếu checkbox không được chọn -->
                                <input type="hidden" name="auto" value="0">
                                <input type="checkbox" class="switch" id="facebook-main-toggle" name="auto" value="1"
                                    {{ $configFacebook['auto'] ? 'checked' : '' }} style="margin-left: 44px; margin-top: 4px;">
                            </span>
                            <hr>
                            <div id="config-options" class="platform-options">
                                <div class="platform">
                                    <h6>Windows</h6>
                                    <input type="radio" class="switch" id="facebook-windows-toggle" name="session" value="windows"
                                        {{ $configFacebook['session'] === 'windows' ? 'checked' : '' }}>
                                </div>
                                <div class="platform">
                                    <h6>Android APK</h6>
                                    <input type="radio" class="switch" id="facebook-android-toggle" name="session" value="android"
                                        {{ $configFacebook['session'] === 'android' ? 'checked' : '' }}>
                                </div>
                                <div class="platform">
                                    <h6>Mobile Browser</h6>
                                    <input type="radio" class="switch" id="facebook-mobile-toggle" name="session" value="mobile"
                                        {{ $configFacebook['session'] === 'mobile' ? 'checked' : '' }}>
                                </div>
                            </div>
                            <hr>
                            <div class="configurations">
                                <!-- Repeat this structure for each configuration -->
                                @foreach($configFacebook['configurations'] as $key => $config)
                                <div class="form-group config-details">
                                    <div class="feature">
                                        <h6>{{ ucwords(str_replace('_', ' ', $key)) }}</h6>
                                        <!-- Input ẩn để gửi giá trị false nếu checkbox không được chọn -->
                                        <input type="hidden" name="configurations[{{ $key }}][auto]" value="0">
                                        <input type="checkbox" class="switch" name="configurations[{{ $key }}][auto]" value="1"
                                            {{ $config['auto'] ? 'checked' : '' }}>
                                    </div>
                                    <div class="inputs">
                                        <div class="input-group">
                                            <label for="{{ $key }}-time">Thời gian min</label>
                                            <input type="number" id="{{ $key }}-time" class="edit-form-control"
                                                name="configurations[{{ $key }}][min_time]"
                                                value="{{ $config['min_time'] }}">
                                        </div>
                                        <div class="input-group">
                                            <label for="{{ $key }}-action">Action limit</label>
                                            <input type="number" id="{{ $key }}-action" class="edit-form-control"
                                                name="configurations[{{ $key }}][action_limit]"
                                                value="{{ $config['action_limit'] }}">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-primary">Lưu cấu hình</button>
                        </form>

                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card wrapper">
                    <div class="card-body">
                        <form action="{{ route('fanpage.config.save') }}" method="post">
                            @csrf
                            <h4 class="card-title title">Cấu hình tự động cho Fanpage</h4>
                            <!-- Auto -->
                            <span>
                                <!-- Input ẩn để gửi giá trị false nếu checkbox không được chọn -->
                                <input type="hidden" name="auto" value="0">
                                <input type="checkbox" class="switch" id="fanpage-main-toggle" name="auto" value="1"
                                    {{ $configFanpage['auto'] ? 'checked' : '' }} style="margin-left: 44px; margin-top: 4px;">
                            </span>
                            <hr>
                            <div id="config-options" class="platform-options">
                                <div class="platform">
                                    <h6>Windows</h6>
                                    <input type="radio" class="switch" id="fanpage-windows-toggle" name="session" value="windows"
                                        {{ $configFanpage['session'] === 'windows' ? 'checked' : '' }}>
                                </div>
                                <div class="platform">
                                    <h6>Android APK</h6>
                                    <input type="radio" class="switch" id="fanpage-android-toggle" name="session" value="android"
                                        {{ $configFanpage['session'] === 'android' ? 'checked' : '' }}>
                                </div>
                                <div class="platform">
                                    <h6>Mobile Browser</h6>
                                    <input type="radio" class="switch" id="fanpage-mobile-toggle" name="session" value="mobile"
                                        {{ $configFanpage['session'] === 'mobile' ? 'checked' : '' }}>
                                </div>
                            </div>
                            <hr>
                            <div class="configurations">
                                <!-- Repeat this structure for each configuration -->
                                @foreach($configFanpage['configurations'] as $key => $config)
                                <div class="form-group config-details">
                                    <div class="feature">
                                        <h6>{{ ucwords(str_replace('_', ' ', $key)) }}</h6>
                                        <!-- Input ẩn để gửi giá trị false nếu checkbox không được chọn -->
                                        <input type="hidden" name="configurations[{{ $key }}][auto]" value="0">
                                        <input type="checkbox" class="switch" name="configurations[{{ $key }}][auto]" value="1"
                                            {{ $config['auto'] ? 'checked' : '' }}>
                                    </div>
                                    <div class="inputs">
                                        <div class="input-group">
                                            <label for="{{ $key }}-time">Thời gian min</label>
                                            <input type="number" id="{{ $key }}-time" class="edit-form-control"
                                                name="configurations[{{ $key }}][min_time]"
                                                value="{{ $config['min_time'] }}">
                                        </div>
                                        <div class="input-group">
                                            <label for="{{ $key }}-action">Action limit</label>
                                            <input type="number" id="{{ $key }}-action" class="edit-form-control"
                                                name="configurations[{{ $key }}][action_limit]"
                                                value="{{ $config['action_limit'] }}">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-primary">Lưu cấu hình</button>
                        </form>

                    </div>
                </div>
            </div>

            <!-- Form thêm/chỉnh sửa quyền -->
            <div class="col-md-4">
                <div class="card wrapper">
                    <div class="card-body">
                        <form action="{{ route('zalo.config.save') }}" method="post">
                            @csrf
                            <h4 class="card-title title">Cấu hình tự động cho Zalo</h4>
                            <!-- Auto -->
                            <span>
                                <!-- Input ẩn để gửi false nếu checkbox không được chọn -->
                                <input type="hidden" name="auto" value="0">
                                <input type="checkbox" class="switch" id="main-toggle" name="auto" value="1"
                                    {{ $configZalo['auto'] ? 'checked' : '' }} style="margin-left: 30px; margin-top: 4px;">
                            </span>
                            <hr>
                            <div id="config-options" class="platform-options">
                                <div class="platform">
                                    <h6>Windows</h6>
                                    <input type="radio" class="switch" id="windows-toggle" name="session" value="windows"
                                        {{ $configZalo['session'] === 'windows' ? 'checked' : '' }}>
                                </div>
                                <div class="platform">
                                    <h6>Android APK</h6>
                                    <input type="radio" class="switch" id="android-toggle" name="session" value="android"
                                        {{ $configZalo['session'] === 'android' ? 'checked' : '' }}>
                                </div>
                                <div class="platform">
                                    <h6>Mobile Browser</h6>
                                    <input type="radio" class="switch" id="mobile-toggle" name="session" value="mobile"
                                        {{ $configZalo['session'] === 'mobile' ? 'checked' : '' }}>
                                </div>
                            </div>
                            <hr>
                            <div class="configurations">
                                <!-- Repeat cấu hình cho từng module -->
                                @foreach($configZalo['configurations'] as $key => $config)
                                <div class="form-group config-details">
                                    <div class="feature">
                                        <h6>{{ ucwords(str_replace('_', ' ', $key)) }}</h6>
                                        <!-- Input ẩn để gửi false nếu checkbox không được chọn -->
                                        <input type="hidden" name="configurations[{{ $key }}][auto]" value="0">
                                        <input type="checkbox" class="switch" name="configurations[{{ $key }}][auto]" value="1"
                                            {{ $config['auto'] ? 'checked' : '' }}>
                                    </div>
                                    <div class="inputs">
                                        <div class="input-group">
                                            <label for="{{ $key }}-time">Thời gian min</label>
                                            <input type="number" id="{{ $key }}-time" class="edit-form-control"
                                                name="configurations[{{ $key }}][min_time]"
                                                value="{{ $config['min_time'] }}">
                                        </div>
                                        <div class="input-group">
                                            <label for="{{ $key }}-action">Action limit</label>
                                            <input type="number" id="{{ $key }}-action" class="edit-form-control"
                                                name="configurations[{{ $key }}][action_limit]"
                                                value="{{ $config['action_limit'] }}">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-primary">Lưu cấu hình</button>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection