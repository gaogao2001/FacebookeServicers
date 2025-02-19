<style>
    /* Toàn bộ modal */
    #importAccountModal .modal-content {
        background-color: #ffffff;
        /* Màu nền trắng */
        border-radius: 8px;
        /* Bo góc */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        /* Đổ bóng */
        color: #333333;
        /* Màu chữ chính */
    }

    /* Header của modal */
    #importAccountModal .modal-header {
        border-bottom: 1px solid #e9ecef;
        padding: 15px 20px;
        background-color: #f8f9fa;
        /* Màu nền header */
        color: #495057;
        /* Màu chữ header */
        font-size: 18px;
        font-weight: 500;
    }

    /* Body của modal */
    #importAccountModal .modal-body {
        padding: 20px;
    }

    /* Form input */
    #importAccountModal .form-control {
        background-color: #ffffff;
        color: #333333;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 10px;
        font-size: 14px;
    }

    #importAccountModal .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    /* Danh sách các tài khoản đã chọn */
    #importAccountModal .list-group-item {
        background-color: #f8f9fa;
        color: #333333;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 5px;
        padding: 10px;
    }

    /* Footer của modal */
    #importAccountModal .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
    }

    /* Nút */
    #importAccountModal .btn {
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 14px;
    }

    #importAccountModal .btn-secondary {
        background-color: #6c757d;
        color: #ffffff;
        border: none;
    }

    #importAccountModal .btn-secondary:hover {
        background-color: #5a6268;
    }

    #importAccountModal .btn-primary {
        background-color: #007bff;
        color: #ffffff;
        border: none;
    }

    #seleteAccountModal .btn-primary:hover {
        background-color: #0056b3;
    }

    /* Style cho select nhóm */
    #seleteAccountModal .form-group.group-select {
        display: none;
        /* Ẩn mặc định */
        margin-top: 10px;
    }

    .input-group-text {
        background-color: #0056b3;
        /* Màu nền xanh nhạt hơn */
        color: #ffffff;
        /* Màu chữ trắng */
        border: none;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .input-group-text:hover {
        background-color: #ffffff;
        /* Màu nền trắng */
        color: #0056b3;
        /* Màu chữ xanh đậm */
    }

    .uid-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .uid-select {
        width: 30%;
    }

    /* Tạo lưới 3 cột, mỗi cột chiếm 1/3 chiều rộng, khoảng cách giữa các mục là 10px */
    .uid-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        /* Hoặc margin nếu thích */
    }

    .uid-item {
        position: relative;
        /* Cho phép đặt nút xóa chồng lên nếu muốn */
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 10px;
        color: #333333;
    }

    /* Nút xóa nhỏ nằm ở góc, nếu muốn */
    .uid-remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgb(15, 15, 15);
        color: rgb(238, 233, 233);
        border: none;
        border-radius: 50%;
        font-size: 11px;
        padding: 3px 6px;
        cursor: pointer;
    }

    .uid-remove-btn:hover {
        background: #c82333;
    }

    #importAccountModal .modal-dialog {
        max-width: 40%;
        /* to rộng bớt cũng được */
    }

    #importAccountModal .modal-body {
        max-height: 60vh;

        overflow-y: auto;
    }
</style>

<div class="modal fade" id="importAccountModal" tabindex="-1" role="dialog" aria-labelledby="importAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="importAccountForm" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importAccountModalLabel">Import Tài Khoản</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="importFile">Chọn file JSON:</label>
                        <input type="file" class="form-control" id="importFile" name="import_file" accept=".json" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>