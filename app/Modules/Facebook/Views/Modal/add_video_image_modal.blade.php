<div class="modal fade" id="addVideoModal" tabindex="-1" aria-labelledby="addVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="padding-top: 129px; padding-left: 19%;" role="document">
        <div class="modal-content" style="background-color: #191c24;">
            <div class="modal-header">
                <h5 class="modal-title" id="addVideoModalLabel">Thêm Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Nav Tabs -->
                <ul class="nav nav-pills mb-3" id="addVideoNav" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="nav-url-tab" data-bs-toggle="pill" data-bs-target="#nav-url" type="button" role="tab" aria-controls="nav-url" aria-selected="true">Thêm bằng URL</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="nav-file-tab" data-bs-toggle="pill" data-bs-target="#nav-file" type="button" role="tab" aria-controls="nav-file" aria-selected="false">Thêm bằng Tệp</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="nav-url2-tab" data-bs-toggle="pill" data-bs-target="#nav-url2" type="button" role="tab" aria-controls="nav-url2" aria-selected="false">Thêm bằng URL Lấy tất cả </button>
                    </li>

                </ul>
                <!-- Tab Content -->
                <div class="tab-content" id="addVideoTabContent">
                    @php
                    // Kiểm tra và gán UID từ $accounts hoặc $fanpage
                    $uid = isset($accounts) && !empty($accounts->uid) ? $accounts->uid : (isset($fanpage) && !empty($fanpage->page_id) ? $fanpage->page_id : null);
                    @endphp

                    @if (!$uid)
                    <div class="alert alert-danger">UID không tồn tại. Vui lòng kiểm tra dữ liệu.</div>
                    @endif
                    <!-- Thêm bằng URL -->
                    <div class="tab-pane fade show active" id="nav-url" role="tabpanel" aria-labelledby="nav-url-tab">
                        <form id="addVideo">
                            @csrf
                            <input type="hidden" name="uid" id="addUid" value="{{ $uid ?? '' }}">

                            <div class="form-group">
                                <label for="addUrl">URL</label>
                                <input type="text" class="form-control" id="addUrl" name="url" placeholder="URL" required>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="addExtractFrames" name="extract_frames" value="1">
                                    <label class="form-check-label" for="addExtractFrames">Click vào checkbox nếu bạn muốn tải luôn hình ảnh.</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>

                    <!-- Thêm bằng Tệp -->
                    <div class="tab-pane fade" id="nav-file" role="tabpanel" aria-labelledby="nav-file-tab">
                        <form id="addMediaFile" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="uid" value="{{ $uid }}">
                            <div class="form-group">
                                <label for="uploadFile">Chọn tệp</label>
                                <input type="file" class="form-control" id="uploadFile" name="file" accept="video/*,image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="nav-url2" role="tabpanel" aria-labelledby="nav-url2-tab">
                        <form id="addVideo2">
                            @csrf
                            <input type="hidden" name="uid" id="addUid2" value="{{ $uid ?? '' }}">
                            <div class="form-group">
                                <label for="addUrl2">URL (Lưu ý : URL trang cá nhân)</label>
                                <input type="text" class="form-control" id="addUrl2" name="url" placeholder="URL Trang cá nhân" required>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="addExtractFrames2" name="extract_frames" value="1">
                                    <label class="form-check-label" for="addExtractFrames2">Click vào checkbox nếu bạn muốn tải luôn hình ảnh.</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" data-uid="{{$uid}}">Submit</button>
                        </form>
                        <div class="form-group mt-3">
                            <label for="videoUIDResult">Video ID</label>
                            <textarea id="videoUIDResult" class="form-control" rows="8" readonly placeholder="Kết quả video UID sẽ hiển thị ở đây" style="resize: vertical;"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>