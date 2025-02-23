document.addEventListener('DOMContentLoaded', function () {
    const sendDataAccountButton = document.getElementById('sendDataAccountButton');
    const selectedAccountsList = document.getElementById('selectedAccountsList');
    const sendAccountsForm = document.getElementById('sendAccountsForm');
    const selectedUidsContainer = document.getElementById('selectedUidsContainer');
    const groupSelectContainer = document.getElementById('groupSelectContainer');
    const groupAccountSelect = document.getElementById('groupAccount');

    if (sendDataAccountButton) {
        sendDataAccountButton.addEventListener('click', function (e) {
            e.preventDefault();

            const selectedItems = document.querySelectorAll('.checkItem:checked');
            selectedAccountsList.innerHTML = '';
            selectedUidsContainer.innerHTML = '';

            if (selectedItems.length === 0) {
                // Không có tài khoản nào được chọn, hiển thị chọn nhóm
                groupSelectContainer.style.display = 'block';
            } else {
                // Có tài khoản được chọn, ẩn chọn nhóm
                groupSelectContainer.style.display = 'none';

                selectedItems.forEach(item => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'selected_accounts[]';
                    hiddenInput.value = item.value; // UID của tài khoản
                    selectedUidsContainer.appendChild(hiddenInput);

                    // Thêm vào danh sách hiển thị trong modal
                    const listItem = document.createElement('li');
                    listItem.className = 'list-group-item';
                    listItem.textContent = `UID: ${item.value}`;
                    selectedAccountsList.appendChild(listItem);
                });
            }

            // Reset giá trị của select nhóm
            groupAccountSelect.value = '';

            // Hiển thị modal
            $('#sendDataAccountModal').modal('show');
        });
    }

    // Reset modal khi đóng
    $('#sendDataAccountModal').on('hidden.bs.modal', function () {
        selectedAccountsList.innerHTML = '';
        selectedUidsContainer.innerHTML = '';
        groupSelectContainer.style.display = 'none';
        groupAccountSelect.value = '';
    });

    const changeAccountGroupButton = document.getElementById('changeAccountGroupButton');
    const selectedAccountsContainer = document.getElementById('selectedAccountsContainer');
    const changeAccountGroupForm = document.getElementById('changeAccountGroupForm');
    const accountGroupSelect = document.getElementById('accountGroup');
    const newGroupInput = document.getElementById('newGroupInput');

    if (changeAccountGroupButton) {
        changeAccountGroupButton.addEventListener('click', function (e) {
            e.preventDefault();

            const selectedItems = document.querySelectorAll('.checkItem:checked');
            selectedAccountsContainer.innerHTML = '';

            if (selectedItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Chú ý',
                    text: 'Vui lòng chọn ít nhất một tài khoản.',
                });
                return;
            }

            selectedItems.forEach(item => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'selected_accounts[]';
                hiddenInput.value = item.value; // Giả sử item.value chứa UID
                selectedAccountsContainer.appendChild(hiddenInput);
            });

            // Hiển thị modal
            $('#changeAccountGroupModal').modal('show');
        });
    }

    if (accountGroupSelect) {
        accountGroupSelect.addEventListener('change', function () {
            if (this.value === 'add_new') {
                newGroupInput.style.display = 'block';
                document.getElementById('newGroupName').setAttribute('required', 'required');
            } else {
                newGroupInput.style.display = 'none';
                document.getElementById('newGroupName').removeAttribute('required');
            }
        });
    }

    // Xử lý submit form "Đổi Nhóm Tài Khoản"
    if (changeAccountGroupForm) {
        changeAccountGroupForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(changeAccountGroupForm);
            const selectedGroup = formData.get('account_group');

            // Kiểm tra nếu chọn "Thêm nhóm mới", lấy giá trị từ input
            if (selectedGroup === 'add_new') {
                const newGroupName = formData.get('new_group_name').trim();
                if (newGroupName === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Chú ý',
                        text: 'Vui lòng nhập tên nhóm mới.',
                    });
                    return;
                }
                formData.set('account_group', newGroupName); // Thay thế giá trị của account_group bằng tên nhóm mới
            }

            fetch(changeAccountGroupForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message,
                        }).then(() => {
                            // Tùy chọn: Reload lại trang hoặc cập nhật giao diện
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message,
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    }

    let uidArray = [];
    // --------------------
    // DELETE ACCOUNTS MODAL
    // --------------------
    // Lấy các phần tử liên quan
    const deleteAccountsButton = document.getElementById('deleteAccounts');
    const deleteAccountsForm = document.getElementById('deleteAccountsForm');
    const uidTextArea = document.getElementById('uidTextArea');

    // Thay vì #hiddenUidList (JSON), ta có 1 "container" để gắn input ẩn:
    const hiddenUidContainer = document.getElementById('hiddenUidContainer');

    if (deleteAccountsButton) {
        deleteAccountsButton.addEventListener('click', function (e) {
            e.preventDefault();

            // Xóa hidden inputs cũ (nếu còn sót)
            hiddenUidContainer.innerHTML = '';

            // Lấy UID từ checkbox
            const selectedItems = document.querySelectorAll('.checkItem:checked');

            // Lấy các dòng hiện có trong textarea (nếu user dán từ trước)
            let lines = uidTextArea.value
                .split('\n')
                .map(line => line.trim())
                .filter(line => line !== '');

            // Thêm UID từ checkbox vào `lines`
            selectedItems.forEach(item => {
                const uidVal = item.value.trim();
                if (uidVal && !lines.includes(uidVal)) {
                    lines.push(uidVal);
                }
            });

            // Cập nhật lại textarea
            uidTextArea.value = lines.join('\n');

            // Mở modal
            $('#deleteAccountModal').modal('show');
        });
    }

    if (deleteAccountsForm) {
        deleteAccountsForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Ngăn chặn gửi form mặc định

            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa các tài khoản đã chọn?',
                text: "Thao tác này không thể hoàn tác.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Xóa mọi input ẩn cũ
                    hiddenUidContainer.innerHTML = '';

                    // Tách các dòng trong textarea
                    let lines = uidTextArea.value
                        .split('\n')
                        .map(line => line.trim())
                        .filter(line => line !== '');

                    // Tạo input ẩn cho từng UID
                    lines.forEach(uid => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_accounts[]';
                        input.value = uid;
                        hiddenUidContainer.appendChild(input);
                    });

                    // Gửi form
                    deleteAccountsForm.submit();
                }
            });
        });
    }

    $('#deleteAccountModal').on('hidden.bs.modal', function () {
    });

    const exportAccountsButton = document.getElementById('exportAccount');
    const exportAccountsForm = document.getElementById('exportAccountsForm');
    const exportUidTextArea = document.getElementById('exportUidTextArea'); // ID mới cho Export
    const exportHiddenUidContainer = document.getElementById('exportHiddenUidContainer'); // ID mới cho Export
    const exportAccountGroup = document.getElementById('exportAccountGroup'); // Select nhóm tài khoản

    if (exportAccountsButton && exportAccountsForm && exportUidTextArea && exportHiddenUidContainer && exportAccountGroup) {
        exportAccountsButton.addEventListener('click', function (e) {
            e.preventDefault();

            const selectedItems = document.querySelectorAll('.checkItem:checked');
            exportUidTextArea.value = '';
            exportHiddenUidContainer.innerHTML = '';
            document.querySelector('#exportAccountModal .group-select-section').style.display = 'none';

            if (selectedItems.length > 0) {
                selectedItems.forEach(item => {
                    const uid = item.value;
                    exportUidTextArea.value += uid + '\n';
                    // Thêm input ẩn
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'uids[]';
                    hiddenInput.value = uid;
                    exportHiddenUidContainer.appendChild(hiddenInput);
                });
            } else {
                // Không có tài khoản được chọn, cho phép chọn nhóm
                document.querySelector('#exportAccountModal .group-select-section').style.display = 'block';
            }

            // Mở modal
            $('#exportAccountModal').modal('show');
        });

        exportAccountsForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Kiểm tra xem có UID nào được nhập không
            const uids = exportUidTextArea.value.trim();

            if (uids === '') {
                // Không có UID, kiểm tra xem nhóm đã được chọn chưa
                const selectedGroup = exportAccountGroup.value;
                if (!selectedGroup) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Chưa chọn nhóm',
                        text: 'Vui lòng chọn nhóm tài khoản để export.',
                    });
                    return;
                }
            }

            // Tạo FormData
            const formData = new FormData(exportAccountsForm);

            if (uids !== '') {
                // Nếu có UID, loại bỏ trường nhóm khỏi FormData
                formData.delete('export_group');
            } else {
                // Nếu không có UID, loại bỏ trường UID khỏi FormData
                formData.delete('uids[]');
            }
            // Thực hiện export
            fetch(exportAccountsForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json' // Thay đổi từ 'application/csv' thành 'application/json'
                },
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.blob();
                })
                .then(blob => {
                    // Tạo URL cho Blob
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    // Đặt tên file theo phản hồi từ server hoặc mặc định
                    a.download = 'export_accounts.json';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);

                    // Thông báo thành công
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: 'Export tài khoản đã hoàn tất.',
                    });
                    // Đóng modal
                    $('#exportAccountModal').modal('hide');
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: error.message || 'Đã xảy ra lỗi khi export tài khoản.',
                    });
                });
        });

        // Reset modal khi đóng
        $('#exportAccountModal').on('hidden.bs.modal', function () {
            exportAccountsForm.reset();
            exportUidTextArea.value = '';
            exportHiddenUidContainer.innerHTML = '';
            document.querySelector('#exportAccountModal .group-select-section').style.display = 'none';
        });
    }

    $('#importAccount').on('click', function () {
        $('#importAccountModal').modal('show');
    });

    // Xử lý submit form Import
    $('#importAccountForm').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: window.routes.importAccounts, // Sử dụng biến route từ Blade
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Đang xử lý...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: response.message,
                }).then(() => {
                    location.reload();
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: xhr.responseJSON.message || 'Đã xảy ra lỗi khi import tài khoản.',
                });
            }
        });
    });

    const openChangeStatusModalButton = document.getElementById('openChangeStatusModal');

    if (openChangeStatusModalButton) {
        openChangeStatusModalButton.addEventListener('click', function () {
            $('#changeStatusModal').modal('show');
        });
    }

    const changeStatusForm = document.getElementById('changeStatusForm');
    const newStatusSelect = document.getElementById('newStatus');
    const selectedItems = document.querySelectorAll('.checkItem:checked');

    if (changeStatusForm) {
        changeStatusForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Ngăn chặn hành động submit mặc định

            const selectedAccounts = Array.from(document.querySelectorAll('.checkItem:checked')).map(item => item.value);
            const newStatus = newStatusSelect.value;

            if (!newStatus) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Lỗi',
                    text: 'Vui lòng chọn trạng thái mới.',
                });
                return;
            }
            const requestData = {
                status: newStatus,
                account_ids: selectedAccounts.length > 0 ? selectedAccounts : null, // Nếu không chọn tài khoản nào, gửi null để thay đổi tất cả
            };

            // Gửi AJAX request đến controller
            fetch('/facebook/change-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify(requestData),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: 'Trạng thái tài khoản đã được thay đổi thành công.',
                        }).then(() => {
                            location.reload(); // Reload lại trang sau khi thay đổi thành công
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message || 'Đã xảy ra lỗi trong quá trình thay đổi trạng thái.',
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Không thể thay đổi trạng thái tài khoản. Vui lòng thử lại sau.',
                    });
                });
        });
    }

    const multiMessageCommentLink = document.getElementById('multiMessageComment');

    if (multiMessageCommentLink) {
        multiMessageCommentLink.addEventListener('click', function (e) {
            e.preventDefault();
            // Giả sử các checkbox có class là .checkItem
            const selectedItems = document.querySelectorAll('.checkItem:checked');
    
            if (selectedItems.length > 0) {
                let uidArray = [];
                selectedItems.forEach(function(item) {
                    uidArray.push(item.value);
                });
                // Xây dựng URL với query parameter selected_accounts[]
                const url = window.routes.multi_message_comment_page + '?selected_accounts[]=' + uidArray.join('&selected_accounts[]=');
                window.location.href = url;
            } else {
                // Nếu không có checkbox được chọn, hiển thị modal để chọn nhóm
                $('#multiMessageCommentModal').modal('show');
            }
        });
    }
    
    $('#multiMessageCommentForm').on('submit', function (e) {
        e.preventDefault();
        const group = $('#groupAccount2').val();
        console.log("Giá trị nhóm được chọn:", group); // Debug
    
        // Nếu chưa chọn nhóm
        if (!group) {
            Swal.fire({
                icon: 'warning',
                title: 'Chọn nhóm tài khoản',
                text: 'Vui lòng chọn một nhóm tài khoản trước khi xác nhận.'
            });
            return;
        }
        
        // Xây dựng URL với query parameter group_account
        const url = window.routes.multi_message_comment_page + '?group_account=' + encodeURIComponent(group);
        window.location.href = url;
    });



});
