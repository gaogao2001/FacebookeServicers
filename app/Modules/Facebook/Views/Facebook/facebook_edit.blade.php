@extends('admin.layouts.master')

@section('title', 'Responsive Profile Page')

@section('head.scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
<link rel="stylesheet" href="{{ asset('modules/facebook/css/facebook_edit.css') }}">
<script src="{{ asset('modules/facebook/js/video_image.js') }}" defer></script>
<script src="{{ asset('modules/facebook/js/DeviceEmulator/device_emulator.js') }}" defer></script>

<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* Chiều cao tối thiểu cho #map */
    #map {
        height: 500px !important;
        width: 100% !important;

    }

    .login-buttons {
        display: flex;
        justify-content: space-between;
    }

    .mail-item {
        display: block !important;
        /* Đảm bảo mỗi email là một dòng riêng */
        margin-bottom: 8px;
        /* Tạo khoảng cách giữa các email */
        padding-bottom: 5px;
        /* Tạo không gian giữa nội dung và đường kẻ dưới (nếu có) */
        border-bottom: 1px solid #2c2f36;
        /* Tạo đường kẻ dưới để tách biệt */
        color: #d1d1d1;
        /* Đảm bảo màu sắc đúng */
    }
</style>

@endsection

@section('content')
<div class="content-wrapper " style="background: #191c24;">
    @if(session('messager'))
    <input type="hidden" id="swalMessageMessager" value="{{ session('messager') }}">
    @endif
    <div class="header__wrapper facebook-edit " style="padding:20px">
        <header></header>
        <div class="cols__container card wrapper">
            <div class="left__col">
                <div class="img__container">
                    @php
                    $avatarUrl = isset($accounts->avatar) && !empty($accounts->avatar) ? $accounts->avatar : asset('admin/img/6681204.png');
                    $headers = @get_headers($avatarUrl);
                    if(!$headers || strpos($headers[0], '200') === false) {
                    $avatarUrl = asset('assets/admin/img/6681204.png');
                    }
                    @endphp
                    <img src="{{ $avatarUrl }}" />
                    <span style="color: {{ $accounts->status == 'LIVE' ? 'green' : 'red' }}"></span>
                </div>
                <a href="https://fb.com/{{$accounts->uid}}" target="_blank">
                    <h2>{{$accounts->fullname ?? null}}</h2>
                </a>
                <div class="form-group">
                    <label for="email" class="form-label" style="font-weight: bold; color: #ffffff;">Email</label>
                    <div id="email" class="form-value" style="color: #d1d1d1; list-style: none; padding-left: 0; margin-top: 10px;">
                        @php
                        // Giải mã chuỗi JSON thành mảng PHP
                        $emails = is_string($accounts->email) ? json_decode($accounts->email, true) : $accounts->email;
                        @endphp
                        @if(!empty($emails) && is_array($emails))
                        @foreach($emails as $email)
                        <div class="mail-item">{{ $email }}</div>
                        @endforeach
                        @else
                        <div class="mail-item">{{ $accounts->email }}</div>

                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label for="birthday" class="form-label" style="font-weight: bold; color: #ffffff;">Ngày sinh</label>
                    <p id="birthday" class="form-value" style="color: #d1d1d1; margin-top: 10px;">{{ $accounts->birthday }}</p>
                </div>

                <button
                    type="button"
                    class="btn btn-primary btnShowMapModal"
                    data-modal-id="{{ $accounts->_id }}">
                    <i class="fas fa-map-marker-alt"></i> Checkin Location
                </button>

                <button
                    type="button"
                    class="btn btn-secondary btnReloadSession"
                    data-uid="{{ $accounts->uid }}"
                    data-session="{{ $accounts->config_auto->session ?? 'android' }}"
                    id="btnReloadSession">
                    <i class="fas fa-sync-alt"></i> Reload Session
                    <span class="spinner-border spinner-border-sm ml-2 d-none" role="status" aria-hidden="true" id="reloadSpinner"></span>
                </button>
                <ul class="about">
                    <li><span>
                            <td>{{ isset($accounts->post_data->count) ? $accounts->post_data->count : ($accounts->post_data ?? 0) }}</td>
                        </span>Bài viết</li>
                    <li><span>{{ isset($accounts->friends->count) ? $accounts->friends->count : ($accounts->friends ?? 0) }}</span>Friends</li>
                    <li><span>Trạng thái </span>{{$accounts->status}}</li>
                </ul>
                <div class="content">
                    <div class="login-buttons">
                        <a href="{{ route('android.main', ['uid' => $accounts->uid]) }}" target="_blank" class="btn btn-success" style="padding: 5px;">
                            <i class="fab fa-android"></i> Login Android
                        </a>
                        <a href="https://windows-login-url.com" target="_blank" class="btn btn-primary" style="padding: 5px;">
                            <i class="fab fa-windows"></i> Login Windows
                        </a>
                        <a href="https://mobile-login-url.com" target="_blank" class="btn btn-info" style="padding: 5px;">
                            <i class="fas fa-mobile-alt"></i> Login Mobile
                        </a>
                    </div>
                    <ul>
                        <li><i class="fab fa-twitter"></i></li>
                        <i class="fab fa-pinterest"></i>
                        <i class="fab fa-facebook"></i>
                        <i class="fab fa-dribbble"></i>
                    </ul>
                    @if(!is_null($ImgQrcode))
						<div class="Qr__code" style="margin-top: 20px;">
							<img src="data:image/png;base64,{{ $ImgQrcode }}" alt="QR Code">
							<div class="auth-code" style="margin-top: 10px;">
								<button class="btn btn-success generated-code" data-key="{{ $accounts->qrcode ?? null }}" onclick="copyToClipboard(this)">Đang tải...</button>
							</div>
							<script>
								function copyToClipboard(element) {
									var tempInput = document.createElement("input");
									tempInput.value = element.textContent;
									document.body.appendChild(tempInput);
									tempInput.select();
									document.execCommand("copy");
									document.body.removeChild(tempInput);
									Swal.fire({
										icon: 'success',
										title: 'Copied!',
										text: 'Đã sao chép mã: ' + tempInput.value,
										timer: 1500,
										showConfirmButton: false
									});
								}
							</script>
						</div>
					@endif

                </div>
            </div>
            <div class="right__col" style="margin-left:-17% !important">
                <div class="p-3 py-5">
                    <!-- Nav Tabs -->
                    <div class="card-body">
                        <div class="nav-pills-container">
                            <ul class="nav nav-pills nav-pills-light" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="pills-info-tab" data-toggle="pill" href="#facebook-infos-tab" role="tab" target="_blank">Thông tin </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-friends-tab" data-toggle="pill" href="#show-friends-tab" role="tab" target="_blank">Bạn bè</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-groups-tab" data-toggle="pill" href="#show-groups-tab" role="tab" target="_blank">Nhóm</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-media-tab" data-toggle="pill" href="#media-tab" role="tab" target="_blank">Video/Hình ảnh cục bộ</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-automation-tab" data-toggle="pill" href="#automation-tab" role="tab" target="_blank">Cài đặt tự động</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-history-tab" data-toggle="pill" href="#history-tab" role="tab" target="_blank">Nhật ký</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-description-tab" data-toggle="pill" href="#description-tab" role="tab" target="_blank">System History</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content mt-3" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="facebook-infos-tab" role="tabpanel">
                                @include('Facebook::Form.facebook_edit_form')
                            </div>
                            <div class="tab-pane fade" id="show-friends-tab" role="tabpanel">
                                @include('Facebook::Form.show-fiends')
                            </div>
                            <div class="tab-pane fade" id="show-groups-tab" role="tabpanel">
                                @include('Facebook::Form.show-group')
                            </div>
                            <div class="tab-pane fade" id="media-tab" role="tabpanel">

                                @include('Facebook::Form.img_video_facebook')
                            </div>
                            <div class="tab-pane fade" id="automation-tab" role="tabpanel">

                                @include('Facebook::Form.facebook_config_auto_form', ['configData' => $accounts->config_auto, 'id' => $accounts->_id])
                            </div>
                            <div class="tab-pane fade" id="history-tab" role="tabpanel">
                                @include('Facebook::Form.history', ['uid' => $accounts->uid])
                            </div>
                            <div class="tab-pane fade" id="log-tab" role="tabpanel">
                                <!-- Nội dung cho Nhật kí -->
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Modal Thêm url hình video/hình ảnh . k có bỏ ở trong img_blade được vì bị xung đột css-->
    @include('Facebook::Modal.add_video_image_modal')
    <!-- Modal  video. k có bỏ ở trong show_video_blade -->
    @include('Facebook::Modal.show_video_modal')

    @include('admin.pages.map_modal', [
    'modalId' => $accounts->_id,
    'formAction' => route('facebook.updateCoordinates', $accounts->_id)
    ])

</div>


@endsection

@section('footer.scripts')

<script src="{{ asset('assets/admin/js/map.js') }}"></script>


<script>
    $(document).ready(function() {
        // Tìm kiếm bạn bè trong card
        $(".search-input").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            // Tìm container cha của input để lọc đúng phần tử
            $(this).closest('.card').find(".friend").filter(function() {
                $(this).toggle($(this).attr("data-name").toLowerCase().indexOf(value) > -1);
            });
        });

        // Xử lý tab và lưu trạng thái vào localStorage
        var activeTab = localStorage.getItem('activeTab');
        if (activeTab) {
            $('#pills-tab a[href="' + activeTab + '"]').tab('show');
        } else {
            // Nếu không có thì hiển thị tab đầu tiên
            $('#pills-tab a:first').tab('show');
        }

        // Lưu tab đang hoạt động vào localStorage khi người dùng chuyển tab
        $('#pills-tab a').on('shown.bs.tab', function(e) {
            var tabId = $(e.target).attr('href');
            localStorage.setItem('activeTab', tabId);
        });

    });

    // Cập nhật chosen_structure khi có thay đổi ở các checkbox

    function updateAuthCodes() {
        $('.generated-code').each(function() {
            var element = $(this);
            var keyCode = element.data('key');

            $.ajax({
                url: '/generate-google-code',
                type: 'POST',
                data: {
                    keyCode: keyCode
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    element.text(response.code);
                },
                error: function() {
                    element.text('Lỗi mã');
                }
            });
        });
    }

    updateAuthCodes();
    setInterval(updateAuthCodes, 10000);
</script>


@endsection