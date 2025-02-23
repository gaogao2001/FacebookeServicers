<div class="container-fluid " style="padding-top:10px; ">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="card wrapper" style=" padding: 0px 0px !important;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0" id="linkCountTitle">Hình ảnh/video</h4>
                    <div class="button-container" style="display: flex; justify-content: flex-end; margin-right:50px;">
                        <button type="button" class="btn btn-info btn-rounded btn-fw" data-bs-toggle="modal" data-bs-target="#addVideoModal">
                            Thêm Video
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="nav-pills-container">
                <ul class="nav nav-pills nav-pills-light" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="pills-image-tab" data-bs-toggle="pill" href="#image-tab" role="tab">Hình ảnh </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-video-tab" data-bs-toggle="pill" href="#video-tab" role="tab">Video </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content mt-3" id="pills-tabContent">
                <div class="tab-pane fade show active" id="image-tab" role="tabpanel">
                    @include('Facebook::Modal.show_image')
                </div>
                <div class="tab-pane fade" id="video-tab" role="tabpanel">
                    @include('Facebook::Modal.show_video')
                </div>
            </div>
        </div>
    </div>

</div>