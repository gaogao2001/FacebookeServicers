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

<link rel="stylesheet" href="{{ asset('modules/video_image/css/video_editor.css') }}">

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
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="extract-audio-tab" data-bs-toggle="tab" data-bs-target="#extract-audio-form" type="button" role="tab" aria-controls="extract-audio-form" aria-selected="false">
                        Tách audio
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
                        <!-- <div class="row mb-3 align-items-center">
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
                        </div> -->

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
                <div class="tab-pane fade p-3" id="extract-audio-form" role="tabpanel" aria-labelledby="extract-audio-tab">
                    <form action="/extract-audio" method="POST" enctype="multipart/form-data" id="extractAudioForm">
                        @csrf
                        <div class="input-group mb-3" style="width: 300px;">
                            <label for="video" class="input-group-text">Video</label>
                            <input type="file" name="video" id="video" class="form-control" required>
                        </div>
                        <div class="input-group mb-3" style="width: 300px;">
                            <label for="outputAudio" class="input-group-text">Tên</label>
                            <input type="text" name="outputAudio" id="outputAudio" class="form-control" placeholder="output.mp3" required onblur="if(this.value && !this.value.endsWith('.mp3')) { this.value += '.mp3'; }">
                        </div>
                        <button type="submit" class="btn btn-primary">Tách Audio</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="bottomPart" style="height: 236px;padding-top: 150px">
        <div class="col-12 border-top h-100" id="timeline-editor">
            <div class="timeline ruler-wrapper" style="height: 235px;">

                <div class="btn-add" onclick="addItem()">
                    <i class="bi bi-plus-circle-fill"></i>
                </div>

                <div class="sticky-top ">
                    <div class="ruler">

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
<script src="{{ asset('modules/video_image/js/video_editor.js') }}"></script>
@endsection