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


@endsection

@section('content')
@section('content')
@if(session('success'))
<input type="hidden" id="swalMessageSuccess" value="{{ session('success') }}">
@endif
@if(session('error'))
<input type="hidden" id="swalMessageError" value="{{ session('error') }}">
@endif
<div class="content-wrapper " style="background: #191c24;">
    <div class="header__wrapper facebook-edit " style="padding:20px">
        <header></header>
        <div class="cols__container card wrapper">
            <div class="left__col">
                <div class="img__container">
                @php
                    $avatarUrl = isset($fanpage->avatar) && !empty($fanpage->avatar) ? $fanpage->avatar : asset('admin/img/6681204.png');
                    $headers = @get_headers($avatarUrl);
                    if(!$headers || strpos($headers[0], '200') === false) {
                        $avatarUrl = asset('assets/admin/img/6681204.png');
                    }
                @endphp
                <img src="{{ $avatarUrl }}" />
                <span></span>
                </div>
                <h2>{{$fanpage->page_name}}</h2>
                <p>{{$fanpage->page_id}} </p>

                <ul class="about">
                    <li>
                        Like
                        <span>
                            <td>{{ isset($fanpage->likes) ? $fanpage->likes : ($fanpage->likes ?? 0) }}</td>
                        </span>
                    </li>
                    <li>
                        Post
                        <span>
                            <td>{{ isset($fanpage->followers) ? $fanpage->followers : ($fanpage->followers ?? 0) }}</td>
                        </span>
                    </li>
                    <li>
                        Followers
                        <span>
                            <td>{{ isset($fanpage->post) ? $fanpage->post : ($fanpage->post ?? 0) }}</td>
                        </span>
                    </li>
                </ul>
                <button
                    type="button"
                    class="btn btn-primary btnShowMapModal"
                    data-modal-id="{{ $fanpage->_id }}">
                    Checkin Location
                </button>
                <div class="content">
                    @php
                    $quotes = [
                    "Smile, breathe and go slowly. - Thich Nhat Hanh",
                    "The only limit to our realization of tomorrow is our doubts of today. - Franklin D. Roosevelt",
                    "The future belongs to those who believe in the beauty of their dreams. - Eleanor Roosevelt",
                    "Do not watch the clock. Do what it does. Keep going. - Sam Levenson",
                    "Keep your face always toward the sunshine—and shadows will fall behind you. - Walt Whitman"
                    ];
                    $randomQuote = $quotes[array_rand($quotes)];
                    @endphp
                    <p>{{ $randomQuote }}</p>
                    <ul>
                        <li><i class="fab fa-twitter"></i></li>
                        <i class="fab fa-pinterest"></i>
                        <i class="fab fa-facebook"></i>
                        <i class="fab fa-dribbble"></i>
                    </ul>
                </div>
            </div>
            <div class="right__col" style="margin-left:-17% !important">
                <div class="p-3 py-5">
                    <!-- Nav Tabs -->
                    <div class="card-body">
                        <div class="nav-pills-container">
                            <ul class="nav nav-pills nav-pills-light" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="pills-info-tab" data-bs-toggle="pill" href="#fanpage-infos-tab" role="tab">Thông tin </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-friends-tab" data-bs-toggle="pill" href="#show-friends-tab" role="tab">Bạn bè</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-groups-tab" data-bs-toggle="pill" href="#show-groups-tab" role="tab">Nhóm</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-media-tab" data-bs-toggle="pill" href="#media-tab" role="tab">Video/Hình ảnh cục bộ</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-automation-tab" data-bs-toggle="pill" href="#automation-tab" role="tab">Cài đặt tự động</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-log-tab" data-bs-toggle="pill" href="#log-tab" role="tab">Nhật kí</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-description-tab" data-bs-toggle="pill" href="#description-tab" role="tab">System History</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content mt-3" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="fanpage-infos-tab" role="tabpanel">
                                @include('Fanpage::Form.fanpage_edit_form')
                            </div>
                            <div class="tab-pane fade" id="show-friends-tab" role="tabpanel">
                                @include('Fanpage::Form.fanpage_friend')
                            </div>
                            <div class="tab-pane fade" id="show-groups-tab" role="tabpanel">
                                @include('Fanpage::Form.fanpage_group')
                            </div>
                            <div class="tab-pane fade" id="media-tab" role="tabpanel">

                                @include('Facebook::Form.img_video_facebook')
                            </div>
                            <div class="tab-pane fade" id="automation-tab" role="tabpanel">
                                @include('Fanpage::Form.fanpage_config_auto', ['configData' => $fanpage->config_auto, 'id' => $fanpage->_id])
                            </div>
                            <div class="tab-pane fade" id="log-tab" role="tabpanel">
                                <!-- Nội dung cho Nhật kí -->
                            </div>
                            <div class="tab-pane fade" id="history-tab" role="tabpanel">
                                <!-- Nội dung cho System History -->
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
    'modalId' => $fanpage->_id,
    'formAction' => route('fanpage.updateCoordinates', $fanpage->_id)
    ])

</div>



@endsection

@section('footer.scripts')
<script src="{{ asset('assets/admin/js/map.js') }}"></script>


@endsection