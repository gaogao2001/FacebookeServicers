<!-- PHP -->
<style>
    /* Toàn bộ modal */
    #multiMessageCommentModal .modal-content {
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
    #multiMessageCommentModal .modal-header {
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
    #multiMessageCommentModal .modal-body {
        padding: 20px;
    }

    /* Form input */
    #multiMessageCommentModal .form-control {
        background-color: #ffffff;
        color: #333333;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 10px;
        font-size: 14px;
    }

    #multiMessageCommentModal .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    /* Footer của modal */
    #multiMessageCommentModal .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
    }

    /* Nút */
    #multiMessageCommentModal .btn {
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 14px;
    }

    #multiMessageCommentModal .btn-secondary {
        background-color: #6c757d;
        color: #ffffff;
        border: none;
    }

    #multiMessageCommentModal .btn-secondary:hover {
        background-color: #5a6268;
    }

    #multiMessageCommentModal .btn-primary {
        background-color: #007bff;
        color: #ffffff;
        border: none;
    }

    #multiMessageCommentModal .btn-primary:hover {
        background-color: #0056b3;
    }

    /* Style cho input thêm nhóm mới */
    #newGroupInput {
        display: none;
        margin-top: 10px;
    }
</style>

<div class="modal fade" id="multiMessageCommentModal" tabindex="-1" role="dialog" aria-labelledby="multiMessageCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="multiMessageCommentForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Multi Message && Comment</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Chọn nhóm tài khoản -->
                    <div class="form-group group-select">
                        <label>Chọn Nhóm Tài Khoản</label>
                        <select id="groupAccount2" name="group_account" class="form-control">
                            <option value="">--Chọn nhóm--</option>
                            @foreach($accountGroups as $group)
                            <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Xác nhận</button>
                </div>
            </div>
        </form>
    </div>
</div>