

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@elseif(session('warning'))
<div class="alert alert-warning">
    {{ session('warning') }}
</div>
@endif
<style>
    .password-container {
        position: relative;
        width: 100%;
    }

    .password-container input {
        width: 100%;
        padding-right: 40px;
        /* Để chừa khoảng trống cho icon */
    }

    .password-container .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: none;
        cursor: pointer;
        padding: 0;
        font-size: 18px;
        color: #6c757d;
        /* Màu xám nhẹ */
    }

    .password-container .toggle-password:focus {
        outline: none;
    }
</style>



<form method="POST" action="{{ route('facebook.update', $accounts->_id) }}">
    @csrf
    @method('PUT')
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="uid">UID</label>
            <input id="text" type="text" class="face-edit-form-control form-control" name="uid" value="{{ $accounts->uid }}">
        </div>
        <div class="form-group col-md-4">
            <label for="password">Password</label>
            <div style="display: flex; align-items: center;">
                <div class="password-container" style="flex: 1;">
                    <input type="text" class="form-control" name="password" id="password" value="{{ $accounts->password }}">
                    <button class="toggle-password BtnViewPassword" type="button">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <button class="btn btn-secondary ml-2 BtnRandomPassword" data-target="#password">
                    <i class="fas fa-random"></i>
                </button>
            </div>
        </div>
        <div class="form-group col-md-4">
            <label for="birthday">Ngày Sinh</label>
            <input id="birthday" type="text" class="face-edit-form-control form-control" name="birthday" value="{{ $accounts->birthday }}">
        </div>
        <div class="form-group col-md-4">
            <label for="email">Email</label>
            <input id="email"
                type="text"
                class="face-edit-form-control form-control"
                name="email"
                placeholder="Nhập email"
                value="{{ $accounts->email ?? '' }}"
                oninput="validateEmail()">

            <small id="emailError" class="text-danger" style="display: none;">Định dạng email không đúng hoặc rỗng.</small>
        </div>
        <div class="form-group col-md-4">
            <label for="phone">Số điện thoại</label>
            <input id="phone" type="phone" class="face-edit-form-control form-control" name="phone" value="{{ $accounts->phone ?? null }}">
        </div>
        <div class="form-group col-md-4">
            <label for="gender">Giới tính</label>
            <select id="gender" class="face-edit-form-control form-control" name="gender" style="color: black;">
                <option value="male" {{ isset($accounts->gender) && $accounts->gender == 'male' ? 'selected' : '' }}>Nam</option>
                <option value="female" {{ isset($accounts->gender) && $accounts->gender == 'female' ? 'selected' : '' }}>Nữ</option>
            </select>
        </div>

        <div class="form-group col-md-4">
            <label for="password_email">Password Email</label>
            <div style="display: flex; align-items: center;">
                <div class="password-container" style="flex: 1;">
                    <input type="text" class="form-control" name="password_email" id="password_email" value="{{ $accounts->password_email ?? null }}">
                    <button class="toggle-password BtnViewPassword" type="button">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <button class="btn btn-secondary ml-2 BtnRandomPassword" data-target="#password_email">
                    <i class="fas fa-random"></i>
                </button>
            </div>
        </div>
        <div class="form-group col-md-4">
            <label for="qr_code">QR Code</label>
            <input id="qrcode" type="text" class="face-edit-form-control form-control" name="qrcode" value="{{ $accounts->qrcode ?? null}}">
        </div>
        <div class="form-group col-md-4">
            <label for="latitude">Vĩ độ</label>
            <input id="latitude" type="text" class="face-edit-form-control form-control" name="latitude" value="{{ $accounts->latitude ?? null }}">
        </div>
        <div class="form-group col-md-4">
            <label for="longitude">Kinh độ</label>
            <input id="longitude" type="text" class="face-edit-form-control form-control" name="longitude" value="{{ $accounts->longitude ?? null }}">
        </div>
        <div class="form-group col-md-4">
            <label for="last_ip_connect">IP kết nối cuối</label>
            <input id="last_ip_connect" type="text" class="face-edit-form-control form-control" name="last_ip_connect" value="{{ $accounts->last_ip_connect ?? null }}">
        </div>
        <div class="form-group col-md-4">
            <label for="networkuse">Network Use</label>
            <select id="networkuse" class="face-edit-form-control form-control" name="networkuse[type]" value="{{ isset($accounts->networkuse['type']) ? $accounts->networkuse['type'] : '' }}">
                <option value="interfaces" {{ isset($accounts->networkuse['type']) && $accounts->networkuse['type'] == 'interfaces' ? 'selected' : '' }}>Dùng Interfaces</option>
                <option value="ssh" {{ isset($accounts->networkuse['type']) && $accounts->networkuse['type'] == 'ssh' ? 'selected' : '' }}>Dùng SSH</option>
                <option value="dcom" {{ isset($accounts->networkuse['type']) && $accounts->networkuse['type'] == 'dcom' ? 'selected' : '' }}>Dùng Dcom</option>
                <option value="proxy" {{ isset($accounts->networkuse['type']) && $accounts->networkuse['type'] == 'proxy' ? 'selected' : '' }}>Dùng proxy</option>
            </select>

        </div>
        <div class="form-group col-md-4">
            <label for="networkuse_ip">Proxy IP</label>
            <input id="networkuse_ip" type="text" class="face-edit-form-control form-control" name="networkuse[ip]" value="{{ $accounts->networkuse->ip ?? null }}">
        </div>
        <div class="form-group col-md-4">
            <label for="networkuse_port">Proxy Port</label>
            <input id="networkuse_port" type="text" class="face-edit-form-control form-control" name="networkuse[port]" value="{{ $accounts->networkuse->port ?? 0 }}">
        </div>
        <div class="form-group col-md-4">
            <label for="proxy_userName">Proxy User name</label>
            <input id="proxy_userName" type="text" class="face-edit-form-control form-control" name="networkuse[username]" value="{{ $accounts->networkuse->username ?? ''}}">
        </div>
        <div class="form-group col-md-4">
            <label for="proxy_password">Proxy Password</label>
            <input id="proxy_password" type="text" class="face-edit-form-control form-control" name="networkuse[password]" value="{{ $accounts->networkuse->password ?? ''}}">
        </div>
        <div class="form-group col-md-4">
            <label for="android_mobile_cookies">Mobile Cookies</label>
            <input id="android_mobile_cookies" type="text" class="face-edit-form-control form-control" name="android_mobile[cookies]" value="{{ $accounts->android_mobile->cookies ?? '' }}">
        </div>
        <div class="form-group col-md-4">
            <label for="android_mobile_user_agent">Mobile User Agent</label>
            <input id="android_mobile_user_agent" type="text" class="face-edit-form-control form-control" name="android_mobile[UserAgent]" value="{{ $accounts->android_mobile->UserAgent ?? '' }}">
        </div>

        <div class="form-group col-md-4">
            <label for="windows_device_user_agent">Windows User Agent</label>
            <input id="windows_device_user_agent" type="text" class="face-edit-form-control form-control" name="windows_device[UserAgent]" value="{{ $accounts->windows_device->UserAgent ?? ''}}">
        </div>
        <div class="form-group col-md-4">
            <label for="windows_device_business_token">Windows Business Token</label>
            <input id="windows_device_business_token" type="text" class="face-edit-form-control form-control" name="windows_device[Business_Token]" value="{{ $accounts->windows_device->Business_Token ?? ''}}">
        </div>
        <div class="form-group col-md-4">
            <label for="windows_device_ads_token">Ads Token</label>
            <input id="windows_device_ads_token" type="text" class="face-edit-form-control form-control" name="windows_device[Ads_Token]" value="{{ $accounts->windows_device->Ads_Token ?? ''}}">
        </div>
        <div class="form-group col-md-4">
            <label for="android_user_agent">Android User Agent</label>
            <input id="android_user_agent" type="text" class="face-edit-form-control form-control" name="android_device[UserAgent]" value="{{ $accounts->android_device->UserAgent ?? ''}}">
        </div>

        <div class="form-group col-md-4">
            <label for="android_cookies">Android Cookies</label>
            <input id="android_cookies" type="text" class="face-edit-form-control form-control" name="android_mobile[cookies]" value="{{ $accounts->android_mobile->cookies ?? ''}}">
        </div>
        <div class="form-group col-md-4">
            <label for="access_token">Access Token</label>
            <input id="access_token" type="text" class="face-edit-form-control form-control" name="android_device[access_token]" value="{{ $accounts->android_device->access_token ?? ''}}">
        </div>
        <div class="form-group col-md-4">
            <label for="device_id">Device ID</label>
            <input id="device_id" type="text" class="face-edit-form-control form-control" name="android_device[device_id]" value="{{ $accounts->android_device->device_id ?? ''}}">
        </div>
        <div class="form-group col-md-4">
            <label for="advertiser_id">Advertiser ID</label>
            <input id="advertiser_id" type="text" class="face-edit-form-control form-control" name="android_device[advertiser_id]" value="{{ $accounts->android_device->advertiser_id ?? ''}}">
        </div>

        <div class="form-group col-md-4">
            <label for="family_device_id">Family Device ID</label>
            <input id="family_device_id" type="text" class="face-edit-form-control form-control" name="android_device[family_device_id]" value="{{ $accounts->android_device->family_device_id ?? ''}}">
        </div>
        <div class="form-group col-md-4">
            <label for="machine_id">Machine ID</label>
            <input id="phone" type="text" class="face-edit-form-control form-control" name="android_device[machine_id]" value="{{ $accounts->android_device->machine_id ?? ''}}">
        </div>
        <div class="form-group col-md-4">
            <label for="sim_serials">Sim Serials</label>
            <input id="sim_serials" type="text" class="face-edit-form-control form-control" name="android_device[sim_serials]" value="{{ $accounts->android_device->sim_serials ?? ''}}">
        </div>
        <div class="form-group col-md-4">
            <label for="jazoest">jazoest</label>
            <input id="jazoest" type="text" class="face-edit-form-control form-control" name="android_device[jazoest]" value="{{ $accounts->android_device->jazoest ?? ''}}">
        </div>

        <div class="form-group col-md-6">
            <label for="facebook_app_version">Facebook App Version</label>
            <select id="facebook_app_version" class="face-edit-form-control form-control" name="facebook_app_version">
                <option value="402.0.0.0.19">Facebook Android APK 402.0.0.0.19</option>
                <option value="402.0.0.0.14">Facebook Android APK 402.0.0.0.14</option>
            </select>
        </div>

        <div class="form-group col-md-6">
            <label for="status">Status</label>
            <select id="status" name="status" class="face-edit-form-control form-control">
                @foreach($allStatus as $status)
                <option value="{{ $status }}" {{ (isset($accounts->status) && $accounts->status == $status) ? 'selected' : '' }}>
                    {{ $status }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-4">
            <label for="plus_cookies">Plus Cookies</label>
            <textarea id="plus_cookies" class="face-edit-form-control form-control" rows="5" style="resize: vertical;"><?= !empty($accounts->windows_device->cookies) ? $accounts->windows_device->cookies . '; useragent=' . base64_encode($accounts->windows_device->UserAgent) . '; _uafec=' . urlencode($accounts->windows_device->UserAgent) . ';' : "" ?></textarea>
        </div>

        <div class="form-group col-md-4">
            <label for="android_device_cookies">Windows Cookies</label>
            <textarea id="android_device_cookies" class="face-edit-form-control form-control" name="windows_device[cookies]" rows="5" style="resize: vertical;">{{ $accounts->windows_device->cookies ?? ''}}</textarea>
        </div>
        <div class="form-group col-md-4">
            <label for="android_device_user_agent">Android User Agent App</label>
            <textarea id="android_device_user_agent" class="face-edit-form-control form-control" name="android_device[UserAgentApp]" rows="5" style="resize: vertical;">{{ $accounts->android_device->UserAgentApp ?? ''}}</textarea>
        </div>
        <div class="form-group col-md-12">
            <label for="note">Ghi chú</label>
            <textarea id="note" class="face-edit-form-control form-control" name="note" placeholder="Nhập nội dung" rows="6" style="resize: vertical;">{{ $accounts->note ?? ''}}</textarea>
        </div>
    </div>
    <div class="mt-5 text-right">
        <button class="btn btn-primary" type="submit" style="color: #000000;">Lưu dữ liệu</button>
    </div>
</form>
<script>
    $(document).ready(function() {
        $(document).on('click', '.BtnRandomPassword', function(e) {
            e.preventDefault();
            var _that = $(this);
            var target = $(_that.data('target'));
            target.prop('disabled', true);
            _that.prop('disabled', true);
            $.get('{{ route("facebook.createPassword") }}', function(response) {
                if (response.password) {
                    target.val(response.password.trim());
                }
                Swal.fire('Thành công', 'Tạo password mới thành công !.', 'success');
                target.prop('disabled', false);
                _that.prop('disabled', false);
            }).fail(function() {
                Swal.fire('Lỗi!', 'Tạo mới mật khẩu thất bại', 'error');
                target.prop('disabled', false);
                _that.prop('disabled', false);
            });
        });

        $(document).on('click', '.BtnViewPassword', function(e) {
            e.preventDefault();
            var _that = $(this);
            var target = _that.closest('.password-container').find('input');
            var currentPassword = target.val().trim();
            target.prop('disabled', true);
            _that.prop('disabled', true);
            $.post('{{ route("facebook.showPassword") }}', {
                    uid: '{{ $accounts->uid }}',
                    password: currentPassword
                })
                .done(function(response) {
                    target.val(response.password.trim());
                })
                .fail(function() {
                    Swal.fire('Lỗi!', 'Hiển thị password thất bại', 'error');
                })
                .always(function() {
                    target.prop('disabled', false);
                    _that.prop('disabled', false);
                });
        });
    });
</script>