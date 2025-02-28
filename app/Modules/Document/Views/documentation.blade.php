<div class="doc-container">
    <button class="doc-toggle-btn" title="Hướng dẫn sử dụng"><i class="fas fa-question-circle"></i></button>
    <div class="doc-sidebar" id="doc-sidebar">
        <button class="doc-close-btn" id="doc-close-btn">&times;</button>
        <div class="doc-title">Hướng Dẫn Sử Dụng</div>
        <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addPageModal">
            Thêm mới Tài liệu
        </button>

        <!-- Loại bỏ phần menu chọn trang -->

        <div class="doc-content" id="doc-content">
            <div class="doc-loading">Đang tải nội dung...</div>
        </div>
    </div>
</div>
<div class="modal fade" id="addPageModal" tabindex="-1" role="dialog" aria-labelledby="addPageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addPageForm">
            <div class="modal-content wrapper">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPageModalLabel">Thêm mới Tài liệu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Tên Page (tự động điền dựa trên URL hiện tại) -->
                    <div class="form-group">
                        <label for="pageId">Tên page (URL)</label>
                        <input type="text" class="form-control" id="pageId" name="id" placeholder="Sẽ được tự động điền theo URL hiện tại" required readonly>
                        <small class="form-text text-muted">Tự động lấy từ URL trang hiện tại</small>
                    </div>
                    <!-- Tiêu đề -->
                    <div class="form-group">
                        <label for="pageTitle">Tiêu đề</label>
                        <input type="text" class="form-control" id="pageTitle" name="title" placeholder="Tiêu đề của page" required>
                    </div>
                    <!-- Nội dung hướng dẫn -->
                    <div class="form-group">
                        <label for="pageContent">Nội dung hướng dẫn</label>
                        <textarea class="form-control" id="pageContent" name="content" placeholder="Nhập nội dung hướng dẫn" rows="5" required style="resize: vertical;"></textarea>
                    </div>
                    <!-- Hình ảnh/Video hướng dẫn -->
                    <div class="form-group">
                        <label for="media">Hình ảnh/Video (chọn file)</label>
                        <input type="file" class="form-control" id="media" name="media[]" accept="image/*,video/*" multiple>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </div>
        </form>
    </div>
</div>