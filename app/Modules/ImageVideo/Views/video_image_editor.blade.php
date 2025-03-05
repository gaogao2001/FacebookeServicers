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

<link rel="stylesheet" href="{{ asset('assets/admin/css/pagination.css') }}">
<script src="{{ asset('assets/admin/js/pagination.js') }}" defer></script>


<script>
    window.console = window.console || function(t) {};
</script>

@endsection

@section('content')

<div class="container-fluid content-wrapper " style="height:100vh;">
    <div class="row" id="topPart">
        <div class="col-3 border-end h-100">
            <video controls="" id="video">
                <source src="" type="video/mp4">
            </video>
        </div>
        <div class="col-9" id="editor">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs wrapper " id="editorTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="basic-video-tab" data-bs-toggle="tab" data-bs-target="#basic-video-form" type="button" role="tab" aria-controls="basic-video-form" aria-selected="true">
                        Basic Video
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="concat-videos-tab" data-bs-toggle="tab" data-bs-target="#concat-videos-form" type="button" role="tab" aria-controls="concat-videos-form" aria-selected="false">
                        Video + Video
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
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="concat-segments-tab" data-bs-toggle="tab" data-bs-target="#concat-segments-form" type="button" role="tab" aria-controls="concat-segments-form" aria-selected="false">
                        Concat Video Segments
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cut-video-tab" data-bs-toggle="tab" data-bs-target="#cut-video-form" type="button" role="tab" aria-controls="cut-video-form" aria-selected="false">
                        Cắt Video
                    </button>
                </li>
            </ul>
            <!-- Tab content -->
            <div class="tab-content wrapper " id="editorTabContent">
                <!-- Basic Video Tab -->
                <div class="tab-pane fade show active p-3" id="basic-video-form" role="tabpanel" aria-labelledby="basic-video-tab">
                    <div class="row">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h6><small>Video (Hình ảnh + Âm thanh)</small></h6>
                                <form method="POST" enctype="multipart/form-data" id="createVideoForm" class="row g-3">
                                    @csrf
                                    <div class="input-group mb-3">
                                        <!-- Ẩn input file dùng cho tải ảnh từ máy -->
                                        <input type="file" name="images[]" id="images" class="form-control" multiple required style="display: none;">
                                        <!-- Button để mở modal chọn nguồn hình ảnh -->
                                        <button type="button" class="btn btn-primary" id="btnSelectImages">Chọn hình ảnh</button>
                                    </div>
                                    <div class="input-group mb-3">
                                        <label for="audio" class="input-group-text btn btn-primary">Âm thanh</label>
                                        <input type="file" name="audio" id="audio" class="form-control" required>
                                    </div>
                                    <div class="input-group mb-3">
                                        <label for="totalDuration" class="input-group-text">Thời lượng</label>
                                        <input type="text" class="form-control" id="totalDuration" name="totalDuration" placeholder="Duration" title="Thời lượng" onchange="updateDuration()">
                                    </div>
                                    <div class="form-group mt-3">
                                        <label class="form-label" style="color: black;">Chế độ hiển thị hình ảnh:</label>
                                        <div class="d-flex align-items-center mt-2">
                                            <div class="form-check me-4">
                                                <input class="form-check-input" type="radio" name="displayMode" id="distributedMode" value="distributed" checked>
                                                <label style="color: white;" class="form-check-label" for="distributedMode">
                                                    Chia đều thời lượng cho mỗi hình
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="displayMode" id="loopMode" value="loop">
                                                <label style="color: white;" class="form-check-label" for="loopMode">
                                                    Vòng lặp (mỗi hình 5 giây)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-primary" id="createVideoBtn">Tạo Video</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-1 d-flex justify-content-center">
                                <div class="vr" style="height: 287px;"></div>
                            </div>
                            <div class="col-md-7">
                                <h1>Hình ảnh</h1>
                                <!-- Container hiển thị preview hình ảnh theo hàng ngang -->
                                <div id="selectedVideosContainer" class="d-flex flex-wrap gap-2">
                                    <!-- Các preview sẽ xuất hiện tại đây -->
                                </div>
                                <div id="pagination" class="pagination"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Thêm vào phần Tab content, ví dụ bên dưới tab Basic Video -->
                <!-- Tab content cho concat-videos-form -->
                <div class="tab-pane fade p-3" id="concat-videos-form" role="tabpanel" aria-labelledby="concat-videos-tab">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h6><small>Video (Video + Video + Âm thanh)</small></h6>
                            <p>Lưu ý: Khi chọn file ở máy có thể chọn 2 Video để ghép 1 lần</p>

                            <form method="POST" enctype="multipart/form-data" id="concatVideosForm" class="row g-3">
                                @csrf
                                <!-- Button để mở modal chọn video -->
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <button type="button" id="btnSelectVideos" class="btn btn-primary w-100">
                                            <i class="bi bi-file-earmark-play"></i> Chọn Video
                                        </button>
                                        <!-- Input ẩn để lưu danh sách file videos -->
                                        <input type="file" name="videos[]" id="concatVideos" class="form-control d-none" multiple accept="video/*">
                                    </div>
                                </div>

                                <!-- Đặt tên file output -->
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <label for="outputFile" class="input-group-text">Output File</label>
                                            <input type="text" name="outputFile" id="outputFile" class="form-control" placeholder="ví dụ:(tai_hehe.mp4)" required onblur="if(this.value && !this.value.endsWith('.mp4')) { this.value += '.mp4'; }">
                                        </div>
                                    </div>
                                </div>

                                <!-- Checkbox giữ âm thanh -->
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-12">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" value="1" id="keepVideoAudio" name="keepVideoAudio" checked>
                                            <label class="form-check-label" for="keepVideoAudio">
                                                Giữ âm thanh gốc của video?
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Audio mới (hiển thị khi không giữ âm thanh gốc) -->
                                <div class="row mb-3" id="audioConcatDiv" style="display:none;">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <label for="audioConcat" class="input-group-text">Audio mới</label>
                                            <input type="file" name="audioConcat" id="audioConcat" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <!-- Nút submit -->
                                <div class="row">
                                    <div class="col-md-12 text-end">
                                        <button type="button" class="btn btn-primary" id="concatVideosBtn">Ghép Video</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-1 d-flex justify-content-center">
                            <div class="vr" style="height: 287px;"></div>
                        </div>

                        <div class="col-md-7">
                            <h1>Video</h1>
                            <!-- Container hiển thị preview video theo hàng ngang -->
                            <div id="concatVideosContainer" class="d-flex flex-wrap gap-2">
                                <!-- Các preview sẽ xuất hiện tại đây -->
                            </div>
                            <div id="concatPagination" class="pagination"></div>
                        </div>
                    </div>
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
                <!-- Tab Extract Audio Form -->
                <!-- Locate the extract-audio-form section and add the preview container after the video selection row -->

                <!-- Update the layout for the video preview container -->
                <!-- Replace the existing video preview container with this structure -->

                <div class="tab-pane fade p-3" id="extract-audio-form" role="tabpanel" aria-labelledby="extract-audio-tab">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h6><small>Tách âm thanh từ video</small></h6>
                            <form action="/extract-audio" method="POST" enctype="multipart/form-data" id="extractAudioForm">
                                @csrf
                                <!-- Video selection button -->
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary extract-video-btn w-100">
                                            <i class="bi bi-file-earmark-play"></i> Chọn Video
                                        </button>
                                        <input type="file" name="video" id="extractVideoInput" class="form-control d-none" accept="video/*">
                                        <input type="hidden" name="video_url" id="extractVideoUrl">
                                        <input type="hidden" name="video_type" id="extractVideoType" value="local">
                                        <div class="selected-video-name mt-1 small text-truncate" id="extractVideoName"></div>
                                    </div>
                                </div>

                                <div class="input-group mb-3">
                                    <label for="outputAudio" class="input-group-text">Tên</label>
                                    <input type="text" name="outputAudio" id="outputAudio" class="form-control" placeholder="ví dụ:(tai_hehe.mp3)" required onblur="if(this.value && !this.value.endsWith('.mp3')) { this.value += '.mp3'; }">
                                </div>
                                <button type="submit" class="btn btn-primary">Tách Audio</button>
                            </form>
                        </div>

                        <div class="col-md-1 d-flex justify-content-center">
                            <div class="vr" style="height: 287px;"></div>
                        </div>

                        <div class="col-md-7">
                            <h1>Video</h1>
                            <!-- Video preview container now displayed horizontally -->
                            <div id="extractVideoPreview" class="d-flex flex-wrap gap-2">
                                <!-- Video preview will be added here dynamically -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- New Tab Pane for Concat Video Segments -->
                <div class="tab-pane fade p-3" id="concat-segments-form" role="tabpanel" aria-labelledby="concat-segments-tab">
                    <p>Lưu ý: Ở đây có thể ghép nhiều video lại với nhau; và có thể chọn thời gian bắt đầu và kết thúc để ghép</p>
                    <form method="POST" enctype="multipart/form-data" id="concatVideoSegmentsForm" action="/concat-video-segments">
                        @csrf
                        <!-- Container for each video segment input row -->
                        <div id="videoSegmentsContainer">
                            <div class="row mb-2 video-segment-row">
                                <div class="col-md-3">
                                    <!-- Thêm nút chọn video vào hàng đầu tiên -->
                                    <button type="button" class="btn btn-primary select-video-btn w-100">
                                        <i class="bi bi-file-earmark-play"></i> Chọn Video
                                    </button>
                                    <input type="file" name="videos[]" class="form-control d-none segment-video-input" accept="video/*">
                                    <div class="selected-video-name mt-1 small text-truncate"></div>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="segments[0][start]" class="form-control" placeholder="Start (giây)" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="segments[0][end]" class="form-control" placeholder="End (giây)" required>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="button" class="btn btn-danger btn-sm removeSegmentBtn">Xóa</button>
                                </div>
                                <!-- Input ẩn cho lưu trữ URL từ FileManager -->
                                <input type="hidden" name="segment_video_urls[]" class="segment-video-url">
                                <input type="hidden" name="segment_video_types[]" class="segment-video-type" value="local">
                            </div>
                        </div>

                        <!-- Button to add another video segment row -->
                        <button type="button" id="addVideoSegmentBtn" class="btn btn-secondary mb-3">
                            <i class="bi bi-plus-circle-fill"></i> Thêm video
                        </button>
                        <!-- Output File Input -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="outputSegmentsFile" class="input-group-text">Tên</label>
                                    <input type="text" name="outputFile" id="outputSegmentsFile" class="form-control" placeholder="ví dụ:(tai_hehe.mp4)" required onblur="if(this.value && !this.value.endsWith('.mp4')) { this.value += '.mp4'; }">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3" id="audioSegmentsDiv" style="display:none;">
                                    <label for="audioSegments" class="input-group-text">Audio mới</label>
                                    <input type="file" name="audio" id="audioSegments" class="form-control">
                                </div>
                            </div>
                        </div>
                        <!-- Checkbox: Giữ âm thanh gốc -->
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="keepSegmentsAudio" name="keepVideoAudio" checked>
                            <label class="form-check-label" for="keepSegmentsAudio">
                                Giữ âm thanh gốc cho video?
                            </label>
                        </div>
                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Ghép Video Segments</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Cập nhật tab cắt video với container hiển thị video -->
                <div class="tab-pane fade p-3" id="cut-video-form" role="tabpanel" aria-labelledby="cut-video-tab">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h6><small>Cắt một đoạn từ video</small></h6>
                            <form action="/cut-video" method="POST" enctype="multipart/form-data" id="cutVideoForm">
                                @csrf
                                <!-- Video Input -->
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary cut-video-btn w-100">
                                            <i class="bi bi-file-earmark-play"></i> Chọn Video
                                        </button>
                                        <input type="file" name="video" id="cutVideoInput" class="form-control d-none" accept="video/*">
                                        <input type="hidden" name="video_url" id="cutVideoUrl">
                                        <input type="hidden" name="video_type" id="cutVideoType" value="local">
                                        <div class="selected-video-name mt-1 small text-truncate" id="cutVideoName"></div>
                                    </div>
                                </div>

                                <!-- Thông tin cắt video -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label" style="color: black;">Thời gian bắt đầu (giây)</label>
                                        <input type="number" name="start_time" class="form-control" min="0" step="0.1" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" style="color: black;">Thời gian kết thúc (giây)</label>
                                        <input type="number" name="end_time" class="form-control" min="0" step="0.1" required>
                                    </div>
                                </div>

                                <!-- Checkbox: Giữ âm thanh gốc -->
                                <div class="mb-3 form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="keepCutAudio" name="keepAudio" checked>
                                    <label class="form-check-label" for="keepCutAudio">
                                        Giữ âm thanh gốc cho video?
                                    </label>
                                </div>

                                <!-- Audio Input (hiển thị khi không giữ audio gốc) -->
                                <div class="mb-3" id="audioCutDiv" style="display:none;">
                                    <label for="audioCut" class="form-label">Chọn file audio mới (mp3, wav)</label>
                                    <input type="file" class="form-control" id="audioCut" name="audio" accept="audio/*">
                                </div>

                                <!-- Tên file output -->
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="outputCutFile" style="color: black;" class="form-label">Tên file output</label>
                                        <input type="text" class="form-control" id="outputCutFile" name="outputFile" required placeholder="video_cut.mp4" onblur="if(this.value && !this.value.endsWith('.mp4')) { this.value += '.mp4'; }">
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary">Cắt Video</button>
                            </form>
                        </div>

                        <div class="col-md-1 d-flex justify-content-center">
                            <div class="vr" style="height: 287px;"></div>
                        </div>

                        <div class="col-md-7">
                            <h1>Video</h1>
                            <!-- Container hiển thị preview video -->
                            <div id="cutVideoContainer" class="d-flex flex-wrap gap-2">
                                <!-- Video preview sẽ xuất hiện tại đây -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="bottomPart" style="display: none; height: 300px; padding-top: 100px;">
        <div class="col-12 border-top h-100" id="timeline-editor">
            <div class="timeline ruler-wrapper" style="height: 280px; background-color: #f8f9fa;">

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

<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content wrapper">
            <div class="modal-header">
                <h5 class="modal-title">Xem demo video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body wrapper">
                <!-- Sử dụng thẻ video để hiển thị demo -->
                <video controls="" id="previewVideo" style="width: 100%;">
                    <source src="" type="video/mp4">
                </video>
            </div>
            <div class="modal-footer">
                <!-- Nút xuất file -->
                <button type="button" id="exportFileBtn" class="btn btn-primary">Xuất file</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chỉnh sửa</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imageOptionModal" tabindex="-1" role="dialog" aria-labelledby="imageOptionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content wrapper">
            <div class="modal-header">
                <h5 class="modal-title" id="imageOptionModalLabel">Chọn nguồn hình ảnh</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <button type="button" class="btn btn-primary" id="btnUploadFromLocal">Tải ảnh từ máy</button>
                <button type="button" class="btn btn-secondary" id="btnSelectFromFileManager">Chọn ảnh từ FileManager</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal FileManager (cho chọn nhiều hình và video) -->
<div class="modal fade" id="contentImageSelectorModal" tabindex="-1" role="dialog" aria-labelledby="contentImageSelectorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width:70.67%;">
        <div class="modal-content wrapper">
            <div class="modal-header">
                <!-- Nút Back để quay lại folder (nếu có) -->
                <button id="backContentImageButton" type="button" class="btn btn-secondary mr-2" style="display:none;" onclick="goBackContentMedia()">Back</button>
                <h5 class="modal-title" id="contentImageSelectorLabel">Chọn hình ảnh / video</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tab cho Images và Videos -->
                <ul class="nav nav-tabs mb-3" id="mediaTypeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="images-tab" data-bs-toggle="tab" data-bs-target="#images-content"
                            type="button" role="tab" aria-controls="images-content" aria-selected="true">Hình ảnh</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="videos-tab" data-bs-toggle="tab" data-bs-target="#videos-content"
                            type="button" role="tab" aria-controls="videos-content" aria-selected="false">Video</button>
                    </li>
                </ul>

                <div class="tab-content" id="mediaTypeTabsContent">
                    <!-- Tab Images -->
                    <div class="tab-pane fade show active" id="images-content" role="tabpanel" aria-labelledby="images-tab">
                        <p>Thư mục: <span id="currentContentImageFolder">root</span></p>
                        <div id="contentImageList" class="row">
                            <!-- Danh sách folder/hình sẽ được load tại đây -->
                        </div>
                    </div>

                    <!-- Tab Videos -->
                    <div class="tab-pane fade" id="videos-content" role="tabpanel" aria-labelledby="videos-tab">
                        <p>Thư mục: <span id="currentContentVideoFolder">root</span></p>
                        <div id="contentVideoList" class="row">
                            <!-- Danh sách folder/video sẽ được load tại đây -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btnConfirmFileManagerSelection">Xác nhận chọn</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="videoOptionModal" tabindex="-1" role="dialog" aria-labelledby="videoOptionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content wrapper">
            <div class="modal-header">
                <h5 class="modal-title" id="videoOptionModalLabel">Chọn nguồn video</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <button type="button" class="btn btn-primary" id="btnUploadVideosFromLocal">Tải video từ máy</button>
                <button type="button" class="btn btn-secondary" id="btnSelectVideosFromFileManager">Chọn video từ FileManager</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer.scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<!-- Main Video Editor -->
<script src="{{ asset('modules/video_image/js/video_editor.js') }}"></script>


@endsection