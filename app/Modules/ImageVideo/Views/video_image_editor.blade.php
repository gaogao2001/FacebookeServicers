@extends('admin.layouts.master')

@section('title', 'Image video')

@section('head.scripts')
<meta charset="UTF-8">
<link rel="apple-touch-icon" type="image/png"
    href="https://cpwebassets.codepen.io/assets/favicon/apple-touch-icon-5ae1a0698dcc2402e9712f7d01ed509a57814f994c660df9f7a952f3060705ee.png">
<meta name="apple-mobile-web-app-title" content="CodePen">
<link rel="icon" type="image/x-icon"
    href="https://cpwebassets.codepen.io/assets/favicon/favicon-aec34940fbc1a6e787974dcd360f2c6b63348d4b1f4e06c77743096d55480f33.ico">
<link rel="mask-icon" type="image/x-icon"
    href="https://cpwebassets.codepen.io/assets/favicon/logo-pin-b4b4269c16397ad2f0f7a01bcdf513a1994f4c94b8af2f191c09eb0d601762b1.svg"
    color="#111">
<script
    src="https://cpwebassets.codepen.io/assets/common/stopExecutionOnTimeout-2c7831bb44f98c1391d6a4ffda0e1fd302503391ca806e7fcc7b9b87197aec26.js"></script>
<title>Video editor</title>
<link rel="canonical" href="https://codepen.io/Julien-Coron/pen/eYbrNmL">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    :root {
        --ruler-offset-left: 80px;
        --ruler-offset-right: 80px;
        --cursor-width: 80px;
        --ruler-color: rgba(73, 73, 73, 0.4);
    }

    #topPart {
        height: 33%;
        min-height: 220px;
    }

    .btn-add {
        position: absolute;
        top: 20px;
        left: 2px;
        color: #0d6efd;
        font-size: xx-large;
        cursor: pointer;
    }

    .sticky-top {
        background-color: white;
    }

    .ruler-wrapper {
        padding-left: calc(var(--ruler-offset-left) - 2px);
        padding-right: calc(var(--ruler-offset-right) - 2px);
    }

    .ruler {
        height: 36px;
        border-bottom: 1px solid darkgray;
        background-image:
            linear-gradient(180deg,
                #ffffff 70%,
                transparent 70%),
            linear-gradient(90deg,
                transparent 0%,
                rgba(73, 73, 73, 0.4) 0%,
                rgba(73, 73, 73, 0.4) 2%,
                transparent 2%,

                transparent 10%,
                rgba(73, 73, 73, 0.4) 10%,
                rgba(73, 73, 73, 0.4) 12%,
                transparent 12%,

                transparent 20%,
                rgba(73, 73, 73, 0.4) 20%,
                rgba(73, 73, 73, 0.4) 22%,
                transparent 22%,

                transparent 30%,
                rgba(73, 73, 73, 0.4) 30%,
                rgba(73, 73, 73, 0.4) 32%,
                transparent 32%,

                transparent 40%,
                rgba(73, 73, 73, 0.4) 40%,
                rgba(73, 73, 73, 0.4) 42%,
                transparent 42%,

                transparent 50%,
                rgba(73, 73, 73, 0.4) 50%,
                rgba(73, 73, 73, 0.4) 52%,
                transparent 52%,

                transparent 60%,
                rgba(73, 73, 73, 0.4) 60%,
                rgba(73, 73, 73, 0.4) 62%,
                transparent 62%,

                transparent 70%,
                rgba(73, 73, 73, 0.4) 70%,
                rgba(73, 73, 73, 0.4) 72%,
                transparent 72%,

                transparent 80%,
                rgba(73, 73, 73, 0.4) 80%,
                rgba(73, 73, 73, 0.4) 82%,
                transparent 82%,

                transparent 90%,
                rgba(73, 73, 73, 0.4) 90%,
                rgba(73, 73, 73, 0.4) 92%,
                transparent 92%);
        background-size: 30px 22px;
        background-repeat: repeat-x;
        background-position: 0px 13px;
    }

    .ruler .unit {
        font-size: smaller;
        color: gray;
        position: absolute;
        top: 5px;
        height: 30px;
        background-image:
            linear-gradient(180deg,
                #ffffff 50%,
                transparent 50%),
            linear-gradient(90deg,
                var(--ruler-color) 0%,
                var(--ruler-color) 5%,
                transparent 6%);
        width: 2em;
        ;
    }


    .timer {
        border: 1px solid gray;
        border-radius: 5px;
        padding: 0px 2px;
        font-size: small;
        position: absolute;
        background-color: rgba(255, 255, 255, 0.75);
    }

    .left {
        left: 0;
    }

    .right {
        right: 0;
    }

    .cursor {
        position: absolute;
        top: 19px;
        width: var(--cursor-width);
        cursor: grab;
        height: auto;
        left: calc(2px + var(--cursor-width) * -0.5);
    }

    .cursor .symbol {
        position: absolute;
        left: calc(50% - 8px);
        border-bottom: 8px solid black;
        border-right: 8px solid black;
        width: 12px;
        height: 12px;
        rotate: 45deg;
        border-style: outset;
    }

    .cursor .currentValue {
        position: absolute;
        top: -19px;
    }

    video {
        /* override other styles to make responsive */
        width: 100% !important;
        height: 100% !important;
    }

    .timeline {
        overflow-x: hidden;
        overflow-y: scroll;
        position: relative;
        background-color: white;
    }

    .cursor-timeline {
        border-left: 1px solid black;
        width: 1px;
        position: absolute;
        left: calc(var(--ruler-offset-left) - 2px);
        z-index: 10;
        /* height will be computed in javascript */
    }

    .h-line {
        background-image:
            linear-gradient(0deg,
                rgba(73, 73, 73, 0.5) 0,
                rgba(73, 73, 73, 0.5) 5%,
                transparent 6%);
    }

    .h-line:hover {
        background-color: #dae4f7;
    }

    .item {
        position: relative;
        border-left: 1px solid rgba(73, 73, 73, 0.5);
        border-right: 1px solid rgba(73, 73, 73, 0.5);
        cursor: pointer;
        /* border top */
        background-image: linear-gradient(0deg,
                rgba(73, 73, 73, 0.5) 0,
                rgba(73, 73, 73, 0.5) 3%,
                transparent 6%);
        background-color: #5480D3;
        /* dynamic*/
        width: 40px;
        left: 55px;
        height: 26px;
    }

    .item.selected {
        background: repeating-linear-gradient(45deg,
                #606dbc,
                #606dbc 10px,
                #465298 10px,
                #465298 20px);
    }

    .item:hover {
        border-left: 2px solid rgba(73, 73, 73, 0.5);
        border-right: 2px solid rgba(73, 73, 73, 0.5);
    }

    .item .title {
        font-size: smaller;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        color: white;
        padding-top: 2px;
        padding-left: 2px;
    }

    button.dropdown-toggle {
        padding: 0px 2px;
        font-size: smaller;
    }

    .dropdown .dropdown-content {
        display: none;
        position: absolute;
        top: -2px;
        z-index: 15;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown-menu {
        font-size: smaller;
    }

    .wrapper {
        background: transparent;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 1px 1px;
    }

    .form-control {
        width: 100%;
        background-color: transparent;
        color: #FFFFFF;
        border: none;
        outline: none;
        border: 2px solid rgba(255, 255, 255, .2);
        font-size: 16px;
        padding: 20px;
        box-shadow: #000000 0 0 10px;
    }

    .form-control:hover,
    .form-control:focus {
        background-color: #FFFFFF;
        color: black;
    }

    .mb-3 input[type="file"] {
        padding-right: 10px;
        /* Tạo khoảng trống giữa file chọn */
    }
</style>

<script>
    window.console = window.console || function(t) {};
</script>

@endsection

@section('content')

<div class="container-fluid content-wrapper " style="height:100vh;">
    <div class="row" id="topPart">
        <div class="col-3 border-end h-100">
            <video controls="" id="video">
                <source src="https://getsamplefiles.com/download/mp4/sample-5.mp4" type="video/mp4">
            </video>
        </div>
        <div class="col-9" id="editor">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="editorTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="basic-video-tab" data-bs-toggle="tab" data-bs-target="#basic-video-form" type="button" role="tab" aria-controls="basic-video-form" aria-selected="true">
                        Basic Video
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="concat-videos-tab" data-bs-toggle="tab" data-bs-target="#concat-videos-form" type="button" role="tab" aria-controls="concat-videos-form" aria-selected="false">
                        Concat Videos
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="overlay-tab" data-bs-toggle="tab" data-bs-target="#overlay-form" type="button" role="tab" aria-controls="overlay-form" aria-selected="false">
                        Overlay
                    </button>
                </li>
            </ul>

            <!-- Tab content -->
            <div class="tab-content" id="editorTabContent">
                <!-- Basic Video Tab -->
                <div class="tab-pane fade show active p-3" id="basic-video-form" role="tabpanel" aria-labelledby="basic-video-tab">
                    <form method="POST" enctype="multipart/form-data" id="createVideoForm">
                        @csrf
                        <div class="input-group mb-3" style="width: 300px;">
                            <label for="images" class="input-group-text">Hình ảnh</label>
                            <input type="file" name="images[]" id="images" class="form-control" multiple required>
                        </div>
                        <div class="input-group mb-3" style="width: 300px;">
                            <label for="audio" class="input-group-text">Âm thanh</label>
                            <input type="file" name="audio" id="audio" class="form-control" required>
                        </div>
                        <div class="input-group mb-3" style="width: 200px;">
                            <label for="totalDuration" class="input-group-text">Thời lượng</label>
                            <input type="text" class="form-control" id="totalDuration" name="totalDuration" placeholder="Duration" title="Thời lượng" onchange="updateDuration()">
                        </div>
                        <button type="button" class="btn btn-primary" id="createVideoBtn">Tạo Video</button>
                    </form>
                </div>
                <!-- Thêm vào phần Tab content, ví dụ bên dưới tab Basic Video -->
                <div class="tab-pane fade p-3" id="concat-videos-form" role="tabpanel" aria-labelledby="concat-videos-tab">
                    <form method="POST" enctype="multipart/form-data" id="concatVideosForm">
                        @csrf
                        <!-- Hàng 1: Chọn video và đặt tên file output -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <label for="videos" class="input-group-text">Videos</label>
                                    <input type="file" name="videos[]" id="videos" class="form-control" multiple required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <label for="outputFile" class="input-group-text">Output File</label>
                                    <input type="text" name="outputFile" id="outputFile" class="form-control" placeholder="output.mp4" required>
                                </div>
                            </div>
                        </div>

                        <!-- Hàng 2: Checkbox giữ âm thanh gốc và chọn audio mới (nếu không giữ) -->
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="keepVideoAudio" name="keepVideoAudio" checked>
                                    <label class="form-check-label" for="keepVideoAudio">
                                        Giữ âm thanh gốc của video?
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-8" id="audioConcatDiv" style="display:none;">
                                <div class="input-group">
                                    <label for="audioConcat" class="input-group-text">Audio mới</label>
                                    <input type="file" name="audioConcat" id="audioConcat" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Hàng 3: Checkbox áp dụng chuyển cảnh và các tùy chọn liên quan -->
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="applyTransition" name="applyTransition">
                                    <label class="form-check-label" for="applyTransition">
                                        Áp dụng chuyển cảnh (chỉ cho 2 video)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-8" id="transitionOptions" style="display: none;">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="input-group">
                                            <label for="transitionType" class="input-group-text">Loại</label>
                                            <input type="text" name="transitionType" id="transitionType" class="form-control" placeholder="fade" value="fade">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="input-group">
                                            <label for="transitionDuration" class="input-group-text">Thời lượng</label>
                                            <input type="text" name="transitionDuration" id="transitionDuration" class="form-control" placeholder="1" value="1">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="input-group">
                                            <label for="transitionOffset" class="input-group-text">Offset</label>
                                            <input type="text" name="transitionOffset" id="transitionOffset" class="form-control" placeholder="4" value="4">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="input-group">
                                            <label for="targetWidth" class="input-group-text">Chiều rộng</label>
                                            <input type="text" name="targetWidth" id="targetWidth" class="form-control" placeholder="1280" value="1280">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="input-group">
                                            <label for="targetHeight" class="input-group-text">Chiều cao</label>
                                            <input type="text" name="targetHeight" id="targetHeight" class="form-control" placeholder="720" value="720">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hàng 4: Nút submit (ghép video) -->
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="button" class="btn btn-primary" id="concatVideosBtn">Ghép Video</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Overlay Tab -->
                <div class="tab-pane fade p-3" id="overlay-form" role="tabpanel" aria-labelledby="overlay-tab">
                    <!-- Nội dung xử lý Overlay hoặc các tác vụ khác -->
                    <form id="formEditItem" action="/" style="display: block;">
                        <input type="hidden" id="itemId" value="">
                        <div class="row ">
                            <div class="col-5 pb-2">
                                <label for="startTime" class="form-label" style="color:white">Thời gian xuất hiện</label>
                                <div class="d-flex flex-row">
                                    <div class="input-group me-2" style="width:100px;">
                                        <input type="text" class="form-control" id="startTime" placeholder="00:00:00.000" title="Bắt đầu" onchange="updateTimeline()">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" id="button-startTime" title="Lấy vị trí của con trỏ" onclick="setStartTimeWithCursor()">
                                            <i class="bi bi-align-start"></i>
                                        </button>
                                    </div>
                                    <div class="input-group" style="width:100;">
                                        <input type="text" class="form-control" id="endTime" placeholder="00:00:00.000" title="Kết thúc" onchange="updateTimeline()">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" id="button-endTime" title="Lấy vị trí của con trỏ" onclick="setEndTimeWithCursor()">
                                            <i class="bi bi-align-end"></i>
                                        </button>
                                    </div>

                                </div>
                            </div>
                            <div class="col-5 pb-2">
                                <label class="form-label" style="color:white">Vị trí trong video</label>
                                <div class="d-flex flex-row">
                                    <div class="input-group me-2" style="width:160px;">
                                        <span class="input-group-text">X <i class="bi bi-arrow-right-short"></i></span>
                                        <input type="text" class="form-control" id="pos_x" placeholder="0" title="Trục X" onchange="markItemChanged(true)">
                                    </div>
                                    <div class="input-group" style="width:160px;">
                                        <span class="input-group-text">Y <i class="bi bi-arrow-down-short"></i></span>
                                        <input type="text" class="form-control" id="pos_y" placeholder="0" title="Trục Y" onchange="markItemChanged(true)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="text" class="form-label" style="color:white">Văn bản</label>
                                <input type="text" class="form-control" id="text" placeholder="" onchange="markItemChanged(true)">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="btn btn-primary" onclick="playItem()">Phát</div>
                            </div>
                            <div class="col-6 d-flex justify-content-evenly">
                                <div class="btn btn-primary position-relative" onclick="applyUpdates()">
                                    Áp dụng
                                    <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle" style="display: none" id="alertUpdate">
                                        <span class="visually-hidden">Thay đổi chưa lưu</span>
                                    </span>
                                </div>
                                <div class="btn btn-secondary" onclick="cancelUpdates()">Hủy thay đổi</div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="bottomPart" style="height: 236px;padding-top: 50px">
        <div class="col-12 border-top h-100" id="timeline-editor">
            <div class="timeline ruler-wrapper" style="height: 235px;">

                <div class="btn-add" onclick="addItem()">
                    <i class="bi bi-plus-circle-fill"></i>
                </div>

                <div class="sticky-top ">
                    <div class="ruler">
                        <!-- Các đơn vị sẽ được thêm vào ở đây -->
                        <span class="unit" style="left: 0px"></span><span class="unit"
                            style="left: 57.28058510638298px">1</span><span class="unit"
                            style="left: 114.56117021276596px">2</span><span class="unit"
                            style="left: 171.84175531914894px">3</span><span class="unit"
                            style="left: 229.12234042553192px">4</span><span class="unit"
                            style="left: 229.12234042553192px">4</span><span class="unit"
                            style="left: 286.4029255319149px">5</span><span class="unit"
                            style="left: 343.6835106382979px">6</span><span class="unit"
                            style="left: 400.9640957446809px">7</span><span class="unit"
                            style="left: 458.24468085106383px">8</span><span class="unit"
                            style="left: 515.5252659574469px">9</span><span class="unit"
                            style="left: 572.8058510638298px">10</span><span class="unit"
                            style="left: 630.0864361702128px">11</span><span class="unit"
                            style="left: 687.3670212765958px">12</span><span class="unit"
                            style="left: 744.6476063829788px">13</span><span class="unit"
                            style="left: 801.9281914893618px">14</span><span class="unit"
                            style="left: 859.2087765957447px">15</span><span class="unit"
                            style="left: 916.4893617021277px">16</span><span class="unit"
                            style="left: 973.7699468085108px">17</span><span class="unit"
                            style="left: 1031.0505319148938px">18</span><span class="unit"
                            style="left: 1088.3311170212767px">19</span><span class="unit"
                            style="left: 1145.6117021276596px">20</span><span class="unit"
                            style="left: 1202.8922872340427px">21</span><span class="unit"
                            style="left: 1260.1728723404256px">22</span><span class="unit"
                            style="left: 1317.4534574468084px">23</span><span class="unit"
                            style="left: 1374.7340425531916px">24</span><span class="unit"
                            style="left: 1432.0146276595747px">25</span><span class="unit"
                            style="left: 1489.2952127659576px">26</span><span class="unit"
                            style="left: 1546.5757978723404px">27</span><span class="unit"
                            style="left: 1603.8563829787236px">28</span><span class="unit"
                            style="left: 1661.1369680851064px">29</span><span class="unit"
                            style="left: 1718.4175531914893px">30</span>
                    </div>
                    <div class="cursor ui-draggable ui-draggable-handle">
                        <span class="symbol"></span>
                        <span class="timer currentValue">
                            00:00:00.000
                        </span>
                    </div>
                </div>
                <div class="cursor-timeline" style="height: 199px;"></div>

                <div class="h-line dropdown">
                    <div class="item ui-draggable ui-draggable-handle" id="item_0" data-start="00:00:01"
                        data-end="00:00:10" data-pos_x="10" data-pos_y="100" data-css_class="redBg"
                        title="Hello World!" style="width: 515.525px; left: 57.2806px;">
                        <div class="title">Hello World!</div>
                        <div class="dropdown-content btn-group" style="left: 519.516px;">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                Actions
                            </button>
                            <div class="dropdown" style="position: relative; left: 519.516px;">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Thao tác
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <h6 class="dropdown-header">Chỉnh sửa ...</h6>
                                    </li>
                                    <li><span class="dropdown-item" onclick="setStartTimeWithCursor()">Đặt điểm bắt đầu theo con trỏ</span></li>
                                    <li><span class="dropdown-item" onclick="setEndTimeWithCursor()">Đặt điểm kết thúc theo con trỏ</span></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <h6 class="dropdown-header">Hiển thị ...</h6>
                                    </li>
                                    <li><span class="dropdown-item" onclick="moveToStartOf('item_0')">Bắt đầu</span></li>
                                    <li><span class="dropdown-item" onclick="moveToEndOf('item_0')">Kết thúc</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer.scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<script id="rendered-js">
    const cursorWidth = $(".cursor").width();
    const rulerWidth = $(".ruler").width();
    const rulerWrapperOffset = parseInt($(".ruler-wrapper").css("padding-left").replace('px', ''));

    var cursorIsDragged = false;
    var videoDuration = 0;

    $('#createVideoBtn').click(function(e) {
        e.preventDefault();
        var form = $('#createVideoForm')[0];
        var formData = new FormData(form);

        $.ajax({
            url: '/create-basic-video',
            type: 'POST',
            data: formData,
            contentType: false, // phải đặt false khi sử dụng FormData
            processData: false, // không chuyển đổi dữ liệu sang chuỗi
            success: function(response) {
                // Xử lý khi tạo video thành công
                Swal.fire("Thành công!", response.message, "success");
                // Bạn có thể cập nhật giao diện, hiển thị video kết quả,...
            },
            error: function(xhr) {
                // Xử lý lỗi
                Swal.fire("Lỗi!", 'Có lỗi xảy ra: ' + xhr.responseJSON.message, "error");
            }
        });
    });

    // Hiển thị/ẩn phần chọn file audio khi không giữ audio gốc
    $('#keepVideoAudio').change(function() {
        if (!this.checked) {
            $('#audioConcatDiv').show();
        } else {
            $('#audioConcatDiv').hide();
        }
    });

    // Hiển thị/ẩn phần tùy chọn chuyển cảnh
    $('#applyTransition').change(function() {
        if (this.checked) {
            $('#transitionOptions').show();
        } else {
            $('#transitionOptions').hide();
        }
    });
    $('#applyTransition').change(function() {
        if (this.checked) {
            $('#transitionOptions').slideDown(function() {
                $('#bottomPart').css('padding-top', '100px');
            });
            // Thêm required cho các input chuyển cảnh
            $('#transitionOptions input').attr('required', true);
        } else {
            $('#transitionOptions').slideUp(function() {
                $('#bottomPart').css('padding-top', '50px');
            });
            $('#transitionOptions input').removeAttr('required');
        }
    });

    $('#concatVideosBtn').click(function(e) {
        e.preventDefault();
        var form = $('#concatVideosForm')[0];
        var formData = new FormData(form);

        $.ajax({
            // Chỉnh sửa URL cho phù hợp với route xử lý backend (ví dụ: /concat-videos)
            url: $('#concatVideosForm').attr('action') || '/create-video-with-audio',
            type: 'POST',
            data: formData,
            contentType: false, // phải đặt false khi sử dụng FormData
            processData: false, // không chuyển đổi dữ liệu sang chuỗi
            success: function(response) {
                Swal.fire("Thành công!", response.message, "success");
            },
            error: function(xhr) {
                Swal.fire("Lỗi!", 'Có lỗi xảy ra: ' + (xhr.responseJSON.message || 'Vui lòng kiểm tra lại.'), "error");
            }
        });
    });


    $(document).ready(function() {

        __setTimelineHeight();
        $(window).on("resize", function() {
            __setTimelineHeight();
        });

        __prepareDragCursor();

        $("video").on(
            "timeupdate",
            function(event) {
                moveCursorToSecond(this.currentTime);
            }
        );

        setTimeout(() => {
            videoDuration = $("video").get(0).duration;

            // Add seconds in the ruler
            __addUnits();

        }, 100);

        setTimeout(() => {
            __loadItems();
        }, 200);
    });

    function __loadItems() {
        // TODO: Call ajax to get all items
        // If success:
        __addItem("item_0", "00:00:01", "00:00:10", 10, 100, "redBg", "Hello World!");
    }

    function __setTimelineHeight() {
        $("#bottomPart").height($("body").height() - $("#topPart").height());
        $(".timeline").height($("#timeline-editor").height());
        $(".cursor-timeline").height($("#timeline-editor").height() -
            $(".sticky-top").height()
        );
    }

    /******************* HELPER function for conversions ***********************/

    function treeDec(value) {
        return ((value < 100) ? ((value < 10) ? ("00") : ("0")) : ("")) + value;
    }

    function twoDec(value) {
        return ((value < 10) ? ("0") : ("")) + value;
    }

    /**
     * Converts as hh:mm:ss.sss a timer in second
     * 
     * @param {integer} seconds 
     */
    function convertSecondsToHms(seconds) {
        var nbMitutes = Math.trunc(seconds / 60);
        var nbHours = Math.trunc(nbMitutes / 60);
        var nbSeconds = Math.trunc(seconds - (nbMitutes * 60));
        nbMitutes -= nbHours * 60;
        var millisec = Math.trunc((seconds - nbSeconds) * 1000);
        return twoDec(nbHours) + ":" + twoDec(nbMitutes) + ":" + twoDec(nbSeconds) + "." + treeDec(millisec);
    }

    /**
     * Converts as second a timer in hh:mm:ss.sss
     * 
     * @param {integer} seconds 
     */
    function convertHmsToSeconds(hmsString) {
        if (hmsString.indexOf(":") == -1) {
            console.error("No ':' in hmsString...");
            return NaN;
        }
        const data = hmsString.split(":");
        if (data.length !== 3) {
            console.error("Not enought items in " + data);
            return NaN;
        }
        const h = parseInt(data[0]);
        if (h == NaN) {
            console.error("Conversion to integer failed with " + data[0]);
            return NaN;
        }

        const m = h * 60 + parseInt(data[1]);
        if (m == NaN) {
            console.error("Conversion to integer failed with " + data[1]);
            return NaN;
        }
        const value = m * 60 + parseFloat(data[2]);
        if (value == NaN) {
            console.error("Conversion to float failed with " + data[2]);
        }
        return value;
    }

    /**
     * Converts as pixel a timer in second
     * 
     * @param {integer} seconds 
     */
    function timerSecondsToPixel(currentTime) {
        return rulerWidth * (currentTime / videoDuration);
    }

    /**
     * This function is triggered by the video player, when:
     *  1. the video plays
     *  2. the currentTime attribute is set on the video object
     * 
     * @param {number} timerSeconds 
     * @returns 
     */
    function moveCursorToSecond(timerSeconds) {
        // Update timers
        $(".currentValue").html(convertSecondsToHms(timerSeconds));

        // Move current time vertical bar
        const ratio = timerSeconds / videoDuration;
        const leftOffsetForTimeline = rulerWrapperOffset + (ratio * rulerWidth) - 1;
        $(".cursor-timeline").css({
            left: leftOffsetForTimeline + "px"
        });

        // Move cursor
        if (cursorIsDragged) {
            return;
        }
        const leftOffsetForCursor = (ratio * rulerWidth) - (cursorWidth / 2) + 2;
        $(".cursor").css({
            left: leftOffsetForCursor + "px"
        });

    }

    /*************** HELPER function to move the video and the cursor **************/

    /**
     * Move the cursor and the video at a timer, in hh:mm:ss.sss
     * 
     * @param {string} timerHms 
     */
    function moveToHms(timerHms) {
        // This will call the update of the cursor
        moveToSecond(convertHmsToSeconds(timerHms));
    }

    /**
     * Move the cursor and the video at a timer, in percent of the video
     * 
     * @param {number} ratio (value in [0 ... 1])
     */
    function moveToPercent(ratio) {
        // This will call the update of the cursor
        moveToSecond(ratio * videoDuration);
    }

    /**
     * Move the cursor and the video at a timer, in second
     * 
     * @param {number} timerSecond 
     */
    function moveToSecond(timerSecond) {
        if (!timerSecond) {
            return;
        }
        // This will call the update of the cursor
        $("video").get(0).currentTime = Math.round(timerSecond * 100) / 100;
    }


    /**
     * Make the cursor draggable on the X-Axis
     */
    function __prepareDragCursor() {
        // Get current offset of ruler
        const leftBorder = -cursorWidth / 2 + 2;
        const rightBorder = rulerWidth - cursorWidth / 2;
        $(".cursor").draggable({
            axis: "x",
            cursor: "move",
            drag: function(event, ui) {
                cursorIsDragged = true;

                var leftPosition = ui.position.left;
                if (leftPosition < leftBorder) {
                    ui.position.left = leftBorder;
                } else if (leftPosition > rightBorder) {
                    ui.position.left = rightBorder;
                }
                $('video').trigger('pause');

                ratio = (leftPosition - leftBorder) / rulerWidth;
                moveToPercent(ratio);

            },
            stop: function(event, ui) {
                cursorIsDragged = false;
            }
        });
    }

    function __addItem(itemId, startTime, endTime, pos_x, pos_y, css_class, text) {
        var template = '<div class="h-line dropdown">' +
            '<div class="item" id="' + itemId + '" data-start="' + startTime + '" data-end="' + endTime + '" ' +
            'data-pos_x="' + pos_x + '" data-pos_y="' + pos_y + '" data-css_class="' + css_class + '" title="' + text + '">' +
            '<div class="title">' + text + '</div>' +
            '<div class="dropdown-content btn-group">' +
            '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" ' +
            'data-bs-auto-close="true" aria-expanded="false">Actions</button>' +
            '<ul class="dropdown-menu">' +
            '<li><h6 class="dropdown-header">Modifier ...</h6></li>' +
            '<li><span class="dropdown-item" onclick="setStartTimeWithCursor()">le début sur le curseur</span></li>' +
            '<li><span class="dropdown-item" onclick="setEndTimeWithCursor()">la fin sur curseur</span></li>' +
            '<li><hr class="dropdown-divider"></li>' +
            '<li><h6 class="dropdown-header">Afficher ...</h6></li>' +
            '<li><span class="dropdown-item" onclick="moveToStartOf(\'' + itemId + '\')">Le début</span></li>' +
            '<li><span class="dropdown-item" onclick="moveToEndOf(\'' + itemId + '\')">La fin</span></li>' +
            '</ul>' +
            '</div>' +
            '</div>' +
            '</div>';

        $(".timeline").append(template);

        // Tính toán số pixel từ thời gian
        var startSecond = convertHmsToSeconds(startTime);
        var endSecond = convertHmsToSeconds(endTime);
        moveItem(itemId, startSecond, endSecond, false);

        var leftBorder = 0;
        var rightBorder = rulerWidth - 3;

        $("#" + itemId).draggable({
            axis: "x",
            cursor: "move",
            drag: function(event, ui) {
                selectItem(itemId);
                var leftPosition = ui.position.left;
                var rightPotition = ui.position.left + ui.helper.width();
                if (leftPosition < leftBorder) {
                    ui.position.left = leftBorder;
                } else if (rightPotition > rightBorder) {
                    ui.position.left = rightBorder - ui.helper.width();
                }
                var startTimeSecond = (ui.position.left / rulerWidth) * videoDuration;
                var endTimeSecond = ((ui.position.left + ui.helper.width()) / rulerWidth) * videoDuration;
                __afterMoveItem(itemId, startTimeSecond, endTimeSecond, true);
            }
        });

        $("#" + itemId).on("click", function(event) {
            selectItem(this.id);
        });
    }

    function selectItem(itemId) {
        if (itemId == null) {
            $("#formEditItem").hide();
            return;
        }

        const jqItem = $("#" + itemId);
        if (jqItem.hasClass("selected")) {
            // Do not select the selected item
            return;
        }

        // Unselect the selectedItem and check if updates are not saved
        if ($("#formEditItem").is(":visible") && $("#alertUpdate").is(":visible") && $("#formEditItem #itemId").val() != itemId) {
            if (!confirm("Des modifications n'ont pas été sauvegardées. Annuler pour appliquer les changements.")) {
                return;
            } else {
                // Discard changes: move the previous item where it should be.
                cancelUpdates(true);
            }
        }

        // Select the item in the timeline
        $(".item").removeClass("selected");
        jqItem.addClass("selected");

        // Fill the form with values
        $("#formEditItem #itemId").val(jqItem.get(0).id);
        $("#formEditItem #startTime").val(jqItem.data("start"));
        $("#formEditItem #endTime").val(jqItem.data("end"));
        $("#formEditItem #pos_x").val(jqItem.data("pos_x"));
        $("#formEditItem #pos_y").val(jqItem.data("pos_y"));
        $("#formEditItem #css_class").val(jqItem.data("css_class"));
        $("#formEditItem #text").val(jqItem.attr("title"));
        markItemChanged(false);

        // Show the form
        $("#formEditItem").show();
    }

    function __addUnits() {
        // Add the 0
        const template = '<span class="unit" style="left: 0px"></span>';
        $(".ruler").append(template);

        for (let i = 1; i < videoDuration; i++) {
            const leftPosition = timerSecondsToPixel(i);
            const template = '<span class="unit" style="left: ${leftPosition}px">${i}</span>';
            $(".ruler").append(template);
        }
    }

    function moveToStartOf(itemId) {
        moveToHms($("#" + itemId).data("start"));
    }

    function moveToEndOf(itemId) {
        moveToHms($("#" + itemId).data("end"));
    }

    function setStartTimeWithCursor() {
        const startTimeSecond = $("video").get(0).currentTime;
        const startTimeHms = convertSecondsToHms(startTimeSecond);
        $("#formEditItem #startTime").val(startTimeHms);
        const itemId = $("#formEditItem #itemId").val();

        // Apply modification in the item of the timeline
        const endTimeSecond = convertHmsToSeconds($("#formEditItem #endTime").val());
        moveItem(itemId, startTimeSecond, endTimeSecond);
    }

    function setEndTimeWithCursor() {
        const endTimeSecond = $("video").get(0).currentTime;
        const endTimeHms = convertSecondsToHms(endTimeSecond);
        $("#formEditItem #endTime").val(endTimeHms);
        const itemId = $("#formEditItem #itemId").val();

        const startTimeSecond = convertHmsToSeconds($("#formEditItem #startTime").val());

        if (endTimeSecond <= startTimeSecond) {
            endTimeSecond = startTimeSecond + 1;
        }
        if (startTimeSecond !== NaN && endTimeSecond !== NaN) {
            moveItem(itemId, startTimeSecond, endTimeSecond);
        }
        markItemChanged(true);

    }

    function updateTimeline() {
        const itemId = $("#formEditItem #itemId").val();
        const startTimeSecond = convertHmsToSeconds($("#formEditItem #startTime").val());
        const endTimeSecond = convertHmsToSeconds($("#formEditItem #endTime").val());
        if (endTimeSecond <= startTimeSecond) {
            endTimeSecond = startTimeSecond + 1;
        }
        if (startTimeSecond !== NaN && endTimeSecond !== NaN) {
            moveItem(itemId, startTimeSecond, endTimeSecond);
        }
        markItemChanged(true);
    }

    function moveItem(itemId, startTimeSecond, endTimeSecond, changed) {
        const itemLeft = timerSecondsToPixel(startTimeSecond);
        const itemWidth = timerSecondsToPixel(endTimeSecond) - itemLeft;

        $("#" + itemId).css({
            width: itemWidth,
            left: itemLeft
        });

        __afterMoveItem(itemId, startTimeSecond, endTimeSecond, changed);

    }

    function __afterMoveItem(itemId, startTimeSecond, endTimeSecond, changed) {

        const startTimeHms = convertSecondsToHms(startTimeSecond);
        const endTimeHms = convertSecondsToHms(endTimeSecond);

        // Check that the popup menu still visible
        const itemLeft = $("#" + itemId).position().left;
        const itemWidth = $("#" + itemId).width();
        if (itemLeft + itemWidth > (rulerWidth * 0.5)) {
            $("#" + itemId + " .dropdown-content").css({
                left: -70
            });
        } else {
            $("#" + itemId + " .dropdown-content").css({
                left: itemWidth + 6
            });
        }

        // Update the form if it's visible, for the current item
        if ($("#formEditItem").is(":visible") && $("#itemId").val() == itemId) {
            $("#formEditItem #startTime").val(startTimeHms);
            $("#formEditItem #endTime").val(endTimeHms);

            if (changed === undefined) {
                markItemChanged(true);
            } else {
                markItemChanged(changed);
            }
        }

    }

    function playItem() {
        const startTimeSecond = convertHmsToSeconds($("#formEditItem #startTime").val());
        moveToSecond(startTimeSecond);
        $("video").get(0).play();
    }

    function cancelUpdates(wasWarned) {
        if (wasWarned === false) {
            if ($("#alertUpdate").is(":visible")) {
                if (!confirm("Voulez-vous abandonner vos modifications ?")) {
                    return;
                }
            }
        }

        // Get the current timers
        const itemId = $("#formEditItem #itemId").val();
        const startTimeSecond = convertHmsToSeconds($("#" + itemId).data("start"));
        const endTimeSecond = convertHmsToSeconds($("#" + itemId).data("end"));
        moveItem(itemId, startTimeSecond, endTimeSecond, false);

        $("#formEditItem").hide();
        $("#formEditItem #itemId").val("");
        $(".item").removeClass("selected");

    }

    function addItem() {
        const startTimeSecond = $("video").get(0).currentTime;
        const startTimeHms = convertSecondsToHms(startTimeSecond);
        const nbItems = $(".item").length;
        __addItem("item_" + nbItems, startTimeHms, convertSecondsToHms(startTimeSecond + 1), 0, 0, "", "sous-titre");
        selectItem("item_" + nbItems);

    }

    function markItemChanged(hasChanged) {
        if (hasChanged) {
            // Show the red bullet
            $("#alertUpdate").show();
        } else {
            // Hide the red bullet
            $("#alertUpdate").hide();
        }
    }

    function applyUpdates() {

        // TODO: Call Ajax to save in database
        // If success:

        const itemId = $("#formEditItem #itemId").val();

        $("#" + itemId).data("start", $("#formEditItem #startTime").val());
        $("#" + itemId).data("end", $("#formEditItem #endTime").val());
        $("#" + itemId).data("pos_x", $("#formEditItem #pos_x").val());
        $("#" + itemId).data("pos_y", $("#formEditItem #pos_y").val());
        $("#" + itemId).data("css_class", $("#formEditItem #css_class").val());
        $("#" + itemId).attr("title", $("#formEditItem #text").val());
        $("#" + itemId + " .title").html($("#formEditItem #text").val());
        markItemChanged(false);
    }

    $('#editor').show();
    $('#editorTabs a#basic-video-tab').tab('show');
</script>
@endsection