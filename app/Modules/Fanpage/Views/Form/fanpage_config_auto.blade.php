<style>
    .wrapper {
        background: transparent;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 30px 40px;
    }

    .edit-form-control {
        width: 100%;
        background-color: transparent;
        color: #FFFFFF;
        border: none;
        outline: none;
        border: 2px solid rgba(255, 255, 255, .2);
        font-size: 16px;

        box-shadow: #000000 0 0 10px;
    }

    .edit-form-control:hover,
    .edit-form-control:focus {
        background-color: #FFFFFF;
        color: #000000;
    }

    .edit-form-control::placeholder {
        color: #FFFFFF;
    }

    .edit-form-control:hover::placeholder,
    .edit-form-control:focus::placeholder {
        color: #000000;
    }



    .switch[type="checkbox"] {
        appearance: none;
        position: relative;
        width: 42px;
        height: 20px;
        background: #f2f2f2;
        border-radius: 25px;
        border: 2px solid #d2d2d2;
        transition: 0.2s;

        &:focus {
            outline: none;
        }

        &:after {
            content: "";
            width: 16px;
            height: 16px;
            background: #fff;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            margin: auto;
            border-radius: 50%;
            transition: 0.2s;
            box-shadow: 1px 0 5px -1px rgba(0, 0, 0, 0.2);
        }

        &:checked {
            background: #5aacff;
            border-color: #95caff;

            &:after {
                left: 22px;
                box-shadow: -1px 0 5px -1px rgba(0, 0, 0, 0.2);
            }
        }
    }

    .switch {
        transform: scale(1);
        margin: auto;
    }

    span {
        display: inline-block;
        vertical-align: middle;

    }

    .title {
        display: inline-block;
    }

    /* Khu vực lựa chọn nền tảng */
    .platform-options {
        display: flex;
        justify-content: space-around;
        align-items: center;
        margin-bottom: 20px;
    }

    .platform {
        text-align: center;
    }

    .platform h6 {
        margin-bottom: 10px;
    }

    .config-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        /* Khoảng cách giữa các phần */
        margin-bottom: 20px;
    }

    .feature {
        flex-shrink: 0;
        text-align: center;
        width: 150px;
    }

    .inputs {
        display: flex;
        gap: 20px;
        flex: 1;
    }

    .input-group {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }

    .input-group label {
        margin-bottom: 5px;
        text-align: center;
        font-size: 12px;
    }
</style>

@php
$configFacebook = $configData;
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="card wrapper">
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @elseif(session('warning'))
                <div class="alert alert-warning">
                    {{ session('warning') }}
                </div>
                @endif
                <form action="{{ route('fanpage.config.update', ['id' => $id]) }}" method="post">
                    @csrf
                    <h4 class="card-title title">Cấu hình tự động cho Facebook</h4>
                    <!-- Auto -->
                    <span>
                        <input type="hidden" name="auto" value="0">
                        <input type="checkbox" class="switch" id="main-toggle" name="auto" value="1"
                            {{ $configFacebook['auto'] ? 'checked' : '' }} style="margin-left: 44px; margin-top: 4px;">
                    </span>
                    <hr>
                    <div id="config-options" class="platform-options">
                        <div class="platform">
                            <h6>Windows</h6>
                            <input type="radio" class="switch" id="windows-toggle" name="session" value="windows"
                                {{ $configFacebook['session'] === 'windows' ? 'checked' : '' }}>
                        </div>
                        <div class="platform">
                            <h6>Android APK</h6>
                            <input type="radio" class="switch" id="android-toggle" name="session" value="android"
                                {{ $configFacebook['session'] === 'android' ? 'checked' : '' }}>
                        </div>
                        <div class="platform">
                            <h6>Mobile Browser</h6>
                            <input type="radio" class="switch" id="mobile-toggle" name="session" value="mobile"
                                {{ $configFacebook['session'] === 'mobile' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <hr>
                    <div class="configurations">
                        @foreach($configFacebook['configurations'] as $key => $config)
                        <div class="form-group config-details">
                            <div class="feature">
                                <h6>{{ ucwords(str_replace('_', ' ', $key)) }}</h6>
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