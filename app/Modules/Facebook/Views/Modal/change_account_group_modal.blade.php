<style>
    /* Toàn bộ modal */
    #changeAccountGroupModal .modal-content {
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
    #changeAccountGroupModal .modal-header {
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
    #changeAccountGroupModal .modal-body {
        padding: 20px;
    }

    /* Form input */
    #changeAccountGroupModal .form-control {
        background-color: #ffffff;
        color: #333333;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 10px;
        font-size: 14px;
    }

    #changeAccountGroupModal .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    /* Footer của modal */
    #changeAccountGroupModal .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
    }

    /* Nút */
    #changeAccountGroupModal .btn {
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 14px;
    }

    #changeAccountGroupModal .btn-secondary {
        background-color: #6c757d;
        color: #ffffff;
        border: none;
    }

    #changeAccountGroupModal .btn-secondary:hover {
        background-color: #5a6268;
    }

    #changeAccountGroupModal .btn-primary {
        background-color: #007bff;
        color: #ffffff;
        border: none;
    }

    #changeAccountGroupModal .btn-primary:hover {
        background-color: #0056b3;
    }

    /* Style cho input thêm nhóm mới */
    #newGroupInput {
        display: none;
        margin-top: 10px;
    }
</style>

<div class="modal fade" id="changeAccountGroupModal" tabindex="-1" role="dialog" aria-labelledby="changeAccountGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="changeAccountGroupForm" action="{{ route('facebook.change_account_group') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Đổi Nhóm Tài Khoản</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="accountGroup">Chọn Nhóm Tài Khoản</label>
                        <select class="form-control" id="accountGroup" name="account_group" required>
                            @foreach($accountGroups as $group)
                            <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                            <option value="add_new">Thêm nhóm mới</option>
                        </select>
                    </div>
                    <!-- Input để thêm nhóm mới -->
                    <div class="form-group" id="newGroupInput">
                        <label for="newGroupName">Tên Nhóm Mới</label>
                        <input type="text" class="form-control" id="newGroupName" name="new_group_name" placeholder="Nhập tên nhóm mới">
                    </div>
                    <!-- Chứa các UID tài khoản đã chọn -->
                    <div id="selectedAccountsContainer"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Xác Nhận</button>
                </div>
            </div>
        </form>
    </div>
</div>