<!-- Bootstrap Modal -->
<div class="modal fade" id="folderModal" tabindex="-1" aria-labelledby="folderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn btn-secondary" id="backButton">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <h5 class="modal-title mx-auto" id="folderModalLabel">Thư mục gốc</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Hiển thị đường dẫn hiện tại -->
                <div id="currentFolderName" class="mb-3">Đường dẫn hiện tại:</div>
                <!-- Nội dung thư mục sẽ được tải ở đây -->
                <div id="popupContent"></div>
            </div>
            <div class="modal-footer">
                <input type="text" id="newFolderInput" class="form-control me-2" placeholder="Nhập tên thư mục mới">
                <button type="button" class="btn btn-info" id="createFolderButton">Tạo Thư Mục</button>
                <button type="button" class="btn btn-success" id="selectPathButton">Chọn Đường Dẫn Này</button>
                <button type="button" class="btn btn-primary" id="resetPathButton">Đặt Lại</button> <!-- Nút Đặt Lại -->
            </div>

        </div>
    </div>
</div>