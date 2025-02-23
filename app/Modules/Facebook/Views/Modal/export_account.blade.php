<style>
    /* Toàn bộ modal */
    #exportAccountModal .modal-content {
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
    #exportAccountModal .modal-header {
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
    #exportAccountModal .modal-body {
        padding: 20px;
    }

    /* Form input */
    #exportAccountModal .form-control {
        background-color: #ffffff;
        color: #333333;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 10px;
        font-size: 14px;
    }

    #exportAccountModal .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    /* Danh sách các tài khoản đã chọn */
    #exportAccountModal .list-group-item {
        background-color: #f8f9fa;
        color: #333333;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 5px;
        padding: 10px;
    }

    /* Footer của modal */
    #exportAccountModal .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
    }

    /* Nút */
    #exportAccountModal .btn {
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 14px;
    }

    #exportAccountModal .btn-secondary {
        background-color: #6c757d;
        color: #ffffff;
        border: none;
    }

    #exportAccountModal .btn-secondary:hover {
        background-color: #5a6268;
    }

    #exportAccountModal .btn-primary {
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

    #exportAccountModal .modal-dialog {
        max-width: 40%;
        /* to rộng bớt cũng được */
    }

    #exportAccountModal .modal-body {
        max-height: 60vh;

        overflow-y: auto;
    }
</style>

<!-- Modal Xóa Account -->
<div class="modal fade" id="exportAccountModal" tabindex="-1" role="dialog" aria-labelledby="exportAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <form id="exportAccountsForm" action="{{ route('facebook.export_accounts') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-body" style="max-height: 600px; overflow-y: auto;">

                    <div class="form-group">
                        <label for="exportUidTextArea">Nhập danh sách UID</label>
                        <textarea
                            class="form-control"
                            id="exportUidTextArea"
                            rows="6"
                            placeholder="Mỗi dòng 1 UID..."
                            style="resize: both;"></textarea>
                    </div>

                    <div id="exportHiddenUidContainer"></div>

                    <div class="form-group group-select-section" style="display: none;">
                        <label for="exportAccountGroup">Chọn Nhóm Tài Khoản</label>
                        <select class="form-control" id="exportAccountGroup" name="export_group">
                            @foreach($accountGroups as $group)
                            <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Đóng
                    </button>
                    <button
                        type="submit"
                        class="btn btn-danger">
                        Export
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>