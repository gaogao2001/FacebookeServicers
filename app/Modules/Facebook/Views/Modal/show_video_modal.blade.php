<!-- Modal -->

<style>
    #cut-video-container .row {
        width: 100%;
    }

    #cut-video-container .col-md-4 {
        padding-right: 10px;
        padding-left: 10px;
    }

    #post-content {
        width: 100%;
        background-color: transparent;
        color: rgb(14, 13, 13);
        outline: none;
        font-size: 19px;
        padding: 7px;
        box-shadow: 0 0 10px #000000;
    }

    #post-content::placeholder {
        color: rgb(243, 239, 239);
    }

    .wrapper {
        background: transparent;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 1px 1px;
    }

    .cut-input {
        width: 100%;
        background-color: transparent;
        color: rgb(14, 13, 13);
        outline: none;
        font-size: 19px;
        padding: 7px;
        box-shadow: 0 0 10px #000000;
    }

    .modal-close-btn {
        background-color: rgb(230, 224, 225);
        border: none;
        color: #000;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        font-size: 24px;
        line-height: 35px;
        text-align: center;
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
        outline: none;
    }
    .modal-close-btn:hover {
        background-color: #c82333;
    }
</style>

<div id="video-modal" class="custom-modal-container open-video-modal" style="display: none;">
    <div class="custom-modal-content wrapper">
        <button class="modal-close-btn">&times;</button>
        <video id="modal-video" controls>
            <source src="" type="video/mp4">
            Trình duyệt của bạn không hỗ trợ video tag.
        </video>
        <!-- Container cho nội dung bài đăng, ẩn mặc định -->
        <div id="post-content-container" style="display: none; margin-top: 10px;">
            <textarea id="post-content" class="form-control" placeholder="Nội dung bài đăng (tùy chọn)" style="color: #FFFFFF;"></textarea>
        </div>
        <div id="cut-video-container" style="display: none; margin-top: 10px;">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" id="cut-start" class="form-control cut-input" placeholder="Bắt đầu " style="color: #FFFFFF;">
                </div>
                <div class="col-md-4">
                    <input type="text" id="cut-end" class="form-control cut-input" placeholder="Kết thúc" style="color: #FFFFFF;">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button id="confirm-cut" class="btn btn-inverse-success btn-sm" style="width: 100%;height: 100%;">Xác nhận Cắt</button>
                </div>
            </div>
        </div>
        <div class="modal-actions">
            <button id="upload-video" class="btn btn-success" data-url="your-upload-video-url" data-type="video" data-uid="123" data-first-click="0">
                Đăng video
            </button>
            <button id="upload-reel" class="btn btn-info" data-url="your-upload-reel-url" data-type="reel" data-uid="124" data-first-click="0">
                Đăng reel
            </button>
            <button id="live-video" class="btn btn-warning" data-url="your-live-video-url" data-type="live" data-uid="125" data-first-click="0">
                Phát live
            </button>
            <button id="delete-video" class="btn btn-danger">Xóa</button>
            <button id="export-video" class="btn btn-secondary">Export Video</button>
            <button id="cut-video" class="btn btn-info">Cắt Video</button>
        </div>

    </div>
</div>