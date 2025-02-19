<style>
    /* Toàn bộ modal */
    #sendDataAccountModal .modal-content {
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
    #sendDataAccountModal .modal-header {
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
    #sendDataAccountModal .modal-body {
        padding: 20px;
    }

    /* Form input */
    #sendDataAccountModal .form-control {
        background-color: #ffffff;
        color: #333333;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 10px;
        font-size: 14px;
    }

    #sendDataAccountModal .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    /* Danh sách các tài khoản đã chọn */
    #sendDataAccountModal .list-group-item {
        background-color: #f8f9fa;
        color: #333333;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 5px;
        padding: 10px;
    }

    /* Footer của modal */
    #sendDataAccountModal .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
    }

    /* Nút */
    #sendDataAccountModal .btn {
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 14px;
    }

    #sendDataAccountModal .btn-secondary {
        background-color: #6c757d;
        color: #ffffff;
        border: none;
    }

    #sendDataAccountModal .btn-secondary:hover {
        background-color: #5a6268;
    }

    #sendDataAccountModal .btn-primary {
        background-color: #007bff;
        color: #ffffff;
        border: none;
    }

    #sendDataAccountModal .btn-primary:hover {
        background-color: #0056b3;
    }

    /* Style cho select nhóm */
    #sendDataAccountModal .form-group.group-select {
        display: none;
        /* Ẩn mặc định */
        margin-top: 10px;
    }

    .input-group-text {
        background-color: #0056b3; /* Màu nền xanh nhạt hơn */
        color: #ffffff; /* Màu chữ trắng */
        border: none;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .input-group-text:hover {
        background-color: #ffffff; /* Màu nền trắng */
        color: #0056b3; /* Màu chữ xanh đậm */
    }
</style>
<div class="modal fade" id="sendDataAccountModal" tabindex="-1" role="dialog" aria-labelledby="sendDataAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="sendAccountsForm" action="{{ route('facebook.send_accounts') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chuyển Đổi Server Cho Account</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="access_token">Server Access_token</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="access_token" name="access_token" required>
                            <span class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="right" title="Đây là mã token có từ tài khoản admin của server đích">
                                <i class="fas fa-question-circle"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="targetUrl">Target URL</label>
                        <div class="input-group">
                            <input type="url" class="form-control" id="targetUrl" name="target_url" placeholder="http://domain.com" required>
                            <span class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="right" title="Địa chỉ server đích">
                                <i class="fas fa-question-circle"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Selected Accounts</label>
                        <ul id="selectedAccountsList" class="list-group">
                            <!-- Selected accounts sẽ được thêm vào đây -->
                        </ul>
                    </div>
                    <!-- Thêm ẩn các UID đã chọn để gửi đi -->
                    <div id="selectedUidsContainer"></div>
                    <!-- Chọn Gửi Theo Nhóm (Ẩn mặc định) -->
                    <div class="form-group group-select" id="groupSelectContainer">
                        <label for="groupAccount">Gửi tài khoản theo nhóm</label>
                        <span data-bs-toggle="tooltip" data-bs-placement="right" title="Chọn nhóm để gửi tài khoản">
                            <i class="fas fa-question-circle"></i>
                        </span>
                        <select class="form-control" id="groupAccount" name="group_account">
                            <option value="">-- Chọn nhóm --</option>
                            @foreach($accountGroups as $group)
                            <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary BtnTransferAccountServer">Send Data</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>