@extends ('admin.layouts.master')

@section('title', 'Admin Info')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="{{ asset('modules/zalo/css/zalo.css') }}">


@endsection

@section('content')
<div class="content-wrapper " style="background: #191c24;">

    <div class="container-fluid" style="padding: 20px; margin-left:20px;">
        <div class="row">
            <div class="col-md-8">
                <div class="wrapper">
                    <div class="row">
                        <!-- Thông tin cá nhân -->
                        <div class="col-md-4 border-right personal_background">
                            <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                                <img class="rounded-circle mt-5" src="{{ $zalo->avatar }}" width="150" alt="Profile Picture">
                                <hr>
                                <span class="font-weight-bold">{{ $zalo->zaloName }}</span>
                                <span>ID: {{ $zalo->userId }}</span>
                                <span>Status: {{ $zalo->status }}</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="p-3 py-5">
                                <form method="POST" action="{{ route('zalos.update', $zalo->_id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="password">Password</label>
                                            <input id="password" type="text" class="zalo-edit-form-control form-control" name="password" placeholder="Password" value="{{ old('password', $zalo->password) }}">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="user_agent">User Agent</label>
                                            <input id="user_agent" type="text" class="zalo-edit-form-control form-control" name="user_agent" placeholder="User Agent" vvalue="{{ old('user_agent', $zalo->userangent) }}">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="phone">Phone</label>
                                            <input id="phone" type="text" class="zalo-edit-form-control form-control" name="phone" placeholder="Phone" value="{{ old('phone', $zalo->phone) }}">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="zpw_enk">zpw_enk</label>
                                            <input id="zpw_enk" type="text" class="zalo-edit-form-control form-control" name="zpw_enk" placeholder="zpw_enk" value="{{ old('zpw_enk', $zalo->zpw_enk) }}">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="cookies">Cookies</label>
                                            <input id="cookies" type="text" class="zalo-edit-form-control form-control" name="cookies" placeholder="Cookies" value="{{ old('cookies', $zalo->cookies) }}">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="networkuse_ip">Proxy IP</label>
                                            <input id="networkuse_ip" type="text" class="zalo-edit-form-control form-control" name="networkuse[ip]" placeholder="Proxy IP" value="{{ old('networkuse.ip', $zalo->networkuse['ip'] ?? '') }}">
                                        </div>
                                        <div class="form-group  col-md-6">
                                            <label for="z_uuid">Z_UUID</label>
                                            <input id="z_uuid" type="text" class="zalo-edit-form-control form-control" name="z_uuid" placeholder="Z_UUID" value="{{ old('z_uuid', $zalo->z_uuid) }}">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="networkuse_port">Proxy Port</label>
                                            <input id="networkuse_port" type="number" class="zalo-edit-form-control form-control" name="networkuse_port" placeholder="Proxy Port" value="{{ old('networkuse_port', $zalo->networkuse['port'] ?? '') }}">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="proxy_userName">Proxy Username</label>
                                            <input id="networkuse_port" type="text" class="zalo-edit-form-control form-control" name="proxy_userName" value="{{ old('proxy_userName', $zalo->proxy_userName ?? '') }}">
                                        </div>

                                        <div class=" form-group col-md-6">
                                            <label for="proxy_password">Proxy Password</label>
                                            <input id="proxy_password" type="text" class="zalo-edit-form-control form-control" name="proxy_password" value="{{ old('proxy_password', $zalo->proxy_password ?? '') }}">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="networkuse">Network Use</label>
                                            <select id="networkuse" class="zalo-edit-form-control form-control" name="networkuse[type]">
                                                <option value="ssh" {{ (old('networkuse.type', $zalo->networkuse['type'] ?? '') == 'ssh') ? 'selected' : '' }}>Dùng SSH</option>
                                                <option value="dcom" {{ (old('networkuse.type', $zalo->networkuse['type'] ?? '') == 'dcom') ? 'selected' : '' }}>Dùng Dcom</option>
                                                <option value="proxy" {{ (old('networkuse.type', $zalo->networkuse['type'] ?? '') == 'proxy') ? 'selected' : '' }}>Dùng proxy</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="useAccount">Cron Dùng Account</label>
                                            <select id="useAccount" class="zalo-edit-form-control form-control" name="useAccount">
                                                <option value="YES" {{ (strtoupper(old('useAccount', $zalo->useAccount)) == 'YES') ? 'selected' : '' }}>YES</option>
                                                <option value="NO" {{ (strtoupper(old('useAccount', $zalo->useAccount)) == 'NO') ? 'selected' : '' }}>NO</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="note">Ghi chú</label>
                                            <textarea id="note" class="zalo-edit-form-control form-control" name="note" placeholder="Nhập nội dung" rows="4" style="resize: vertical;">{{ old('note', $zalo->note ?? '') }}</textarea>
                                        </div>

                                    </div>
                                    <div class="mt-5 text-right">
                                        <button class="btn btn-primary" type="submit" style="color: #000000;">Lưu dữ liệu</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Danh sách bạn bè -->
                <div class="card mt-5 wrapper" style=" backdrop-filter: blur(100px); ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Danh Sách {{ count($zalo->friends) }} Bạn Bè</h6>
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" id="search-input" class="zalo-edit-form-control form-control" placeholder="Tìm kiếm bạn bè..." style=" border-radius: 20px;">
                            <div class="input-group-append">
                                <span class="input-group-text" style="background: transparent; border: none;"><i class="fas fa-search" style="color: white;"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="friends-grid">
                            @foreach($zalo->friends as $friend)
                            <div class="friend" data-name="{{ $friend['displayName'] }}">
                                <img src="{{ $friend['avatar'] }}" alt="Avatar">
                                <h4>{{ $friend['displayName'] }}</h4>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="wrapper">
                    <h4>Thông tin cơ bản</h4>
                    <form>
                        @csrf
                        <div class="form-group">
                            <label for="role_name">Tên</label>
                            <input type="text" class="zalo-edit-form-control form-control" id="role_name" name="name" placeholder="Nhập tên quyền">
                        </div>
                        <div class="form-group">
                            <label for="role_description">Mô tả</label>
                            <textarea class="zalo-edit-form-control form-control" id="role_description" name="description" placeholder="Nhập mô tả"></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer.scripts')
<script>
    $(document).ready(function() {
        $("#search-input").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".friend").filter(function() {
                $(this).toggle($(this).attr("data-name").toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>
@endsection