<style>
    /* Toàn bộ modal */
    #deleteAccountModal .modal-content {
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
    #deleteAccountModal .modal-header {
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
    #deleteAccountModal .modal-body {
        padding: 20px;
    }

    /* Form input */
    #deleteAccountModal .form-control {
        background-color: #ffffff;
        color: #333333;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 10px;
        font-size: 14px;
    }

    #deleteAccountModal .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    /* Danh sách các tài khoản đã chọn */
    #deleteAccountModal .list-group-item {
        background-color: #f8f9fa;
        color: #333333;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 5px;
        padding: 10px;
    }

    /* Footer của modal */
    #deleteAccountModal .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
    }

    /* Nút */
    #deleteAccountModal .btn {
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 14px;
    }

    #deleteAccountModal .btn-secondary {
        background-color: #6c757d;
        color: #ffffff;
        border: none;
    }

    #deleteAccountModal .btn-secondary:hover {
        background-color: #5a6268;
    }

    #deleteAccountModal .btn-primary {
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

    #deleteAccountModal .modal-dialog {
        max-width: 40%;
        /* to rộng bớt cũng được */
    }

    #deleteAccountModal .modal-body {
        max-height: 60vh;

        overflow-y: auto;
    }
</style>



<!-- Modal Xóa Account -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <form id="deleteAccountsForm" action="{{ route('facebook.delete_accounts') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xóa Tài Khoản</h5>
                </div>
                <div class="modal-body" style="max-height: 600px; overflow-y: auto;">

                    <!-- Textarea để dán/thêm nhiều UID -->
                    <div class="form-group">
                        <label for="uidTextArea">Nhập danh sách UID</label>
                        <textarea
                            class="form-control"
                            id="uidTextArea"
                            rows="6"
                            placeholder="Mỗi dòng 1 UID..."
                            style="resize: both;"></textarea>
                    </div>

                    <!-- 
                        Container ẩn để lưu các <input type="hidden" name="selected_accounts[]"> 
                        (tạo tự động trong JS)
                    -->
                    <div id="hiddenUidContainer"></div>

                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button
                        type="submit"
                        class="btn btn-danger">
                        Delete Selected Accounts
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>