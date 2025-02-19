$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Cấu hình CSRF Token cho các yêu cầu AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    $('.search-backup').on('keyup', function () {
        var value = $(this).val().toLowerCase();
        $('.backup-file').filter(function () {
            $(this).toggle($(this).data('name').toLowerCase().indexOf(value) > -1);
        });
    });

    // Hiển thị thông báo (Alert) với SweetAlert2
    function showAlert(type, message, timer = 5000) {
        if (!message || typeof message !== 'string') {
            console.error('Thông báo không hợp lệ:', message);
            return;
        }

        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 không được tải.');
            return;
        }

        Swal.fire({
            icon: type,
            title: message,
            showConfirmButton: timer === 0,
            timer: timer > 0 ? timer : undefined
        });
    }

    // Hiển thị hộp thoại xác nhận (Confirm Dialog)
    function showConfirmDialog(title, text) {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Có',
            cancelButtonText: 'Không'
        });
    }

    // Sự kiện cho checkbox chọn tất cả
    $('#selectAll').on('change', function () {
        $('.file-checkbox').prop('checked', $(this).prop('checked'));
        updateToggleButton();
    });

    // Gọi updateToggleButton khi checkbox của từng file thay đổi
    $(document).on('change', '.file-checkbox', function () {
        updateToggleButton();
    });

    $(document).on('change', '#dataSelect', function () {
        _that = $(this);
        _databaseName = _that.val();
        _collectionSelect = $('#collectionSelect');

        _collectionSelect.empty().append('<option value="">{{ __("Chọn collection cần sao lưu") }}</option>');

        if (!_databaseName) {
            _collectionSelect.prop('disabled', true);
            $('#backupButton').prop('disabled', true);
            return false;
        }

        _action = `/get-collections/${_databaseName}`;
        _postData = {};

        $.get(_action, function (result) {
            if (result.status && result.collections) {
                result.collections.forEach(collection => {
                    _collectionSelect.append(`<option value="${collection}">${collection}</option>`);
                });
                _collectionSelect.prop('disabled', false);
                $('#backupButton').prop('disabled', false);
            } else {
                showAlert('error', 'Không thể tải danh sách collections.');
            }
        }, 'json').fail(function () {
            showAlert('error', 'Yêu cầu không thành công.');
        });

        return false;
    });

    $(document).on('click', '#backupButton', function () {
        _that = $(this);
        _databaseName = $('#dataSelect').val();
        _collectionName = $('#collectionSelect').val();

        if (!_databaseName) {
            showAlert('error', 'Vui lòng chọn database để sao lưu.', 5000);
            return false;
        }

        _action = '/backup-database';
        _postData = { database_name: _databaseName };
        if (_collectionName) _postData.collection_name = _collectionName;

        showConfirmDialog('Xác nhận sao lưu', 'Bạn có chắc chắn muốn sao lưu?').then((result) => {
            if (result.isConfirmed) {
                _originalContent = _that.html();
                Swal.fire({
                    title: 'Đang sao lưu dữ liệu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.post(_action, _postData, function (response) {
                    Swal.close();
                    showAlert(response.response.status ? 'success' : 'error', response.response.message, 5000);
                    _that.html(_originalContent);
                    if (response.response.status) {
                        setTimeout(function () {
                            window.location.reload();
                        }, 3000);
                    }
                }, 'json');
            }
        });
        return false;
    });



    $(document).on('change', '#dataSelect', function () {
        _that = $(this);
        _databaseName = _that.val();
        _collectionSelect = $('#collectionSelect');

        _collectionSelect.empty().append('<option value="">{{ __("Chọn collection cần sao lưu") }}</option>');

        if (!_databaseName) {
            _collectionSelect.prop('disabled', true);
            $('#backupButton').prop('disabled', true);
            return false;
        }

        _action = `/get-collections/${_databaseName}`;
        _postData = {};

        $.post(_action, _postData, function (result) {
            if (result.status && result.collections) {
                result.collections.forEach(collection => {
                    _collectionSelect.append(`<option value="${collection}">${collection}</option>`);
                });
                _collectionSelect.prop('disabled', false);
                $('#backupButton').prop('disabled', false);
            } else {
                showAlert('error', 'Không thể tải danh sách collections.');
            }
        }, 'json');

        return false;
    });

    $(document).on('click', '#backupButton', function () {
        _that = $(this);
        _databaseName = $('#dataSelect').val();
        _collectionName = $('#collectionSelect').val();

        if (!_databaseName) {
            showAlert('error', 'Vui lòng chọn database để sao lưu.', 5000);
            return false;
        }

        _action = '/backup-database';
        _postData = { database_name: _databaseName };
        if (_collectionName) _postData.collection_name = _collectionName;

        showConfirmDialog('Xác nhận sao lưu', 'Bạn có chắc chắn muốn sao lưu?').then((result) => {
            if (result.isConfirmed) {
                _originalContent = _that.html();
                Swal.fire({
                    title: 'Đang sao lưu dữ liệu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.post(_action, _postData, function (response) {
                    Swal.close();
                    showAlert(response.response.status ? 'success' : 'error', response.response.message, 5000);
                    _that.html(_originalContent);
                    if (response.response.status) {
                        setTimeout(function () {
                            window.location.reload();
                        }, 3000);
                    }
                }, 'json');
            }
        });
        return false;
    });

    $(document).on('click', '.restore-button', function () {
        _that = $(this);
        _backupFile = _that.data('file');

        showConfirmDialog('Xác nhận khôi phục', 'Bạn có chắc muốn khôi phục từ file sao lưu này?').then((result) => {
            if (result.isConfirmed) {
                _originalContent = _that.html();
                Swal.fire({
                    title: 'Đang khôi phục dữ liệu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                _action = '/restore-database';
                _postData = { backup_file: _backupFile };

                $.post(_action, _postData, function (response) {
                    Swal.close();
                    showAlert(response.response.status ? 'success' : 'error', response.response.message, 5000);
                    _that.html(_originalContent);
                    if (response.response.status) {
                        setTimeout(function () {
                            window.location.reload();
                        }, 3000);
                    }
                }, 'json');
            }
        });
        return false;
    });

    $(document).on('click', '.delete-button', function () {
        _that = $(this);
        _backupFile = _that.data('file');

        showConfirmDialog('Xác nhận xóa', 'Bạn có chắc chắn muốn xóa file này?').then((result) => {
            if (result.isConfirmed) {
                _originalContent = _that.html();
                Swal.fire({
                    title: 'Đang xóa file...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                _action = '/delete-backup';
                _postData = { backup_file: _backupFile };

                $.post(_action, _postData, function (response) {
                    Swal.close();
                    showAlert(response.response.status ? 'success' : 'error', response.response.message, 5000);
                    _that.html(_originalContent);
                    if (response.response.status) {
                        setTimeout(function () {
                            window.location.reload();
                        }, 3000);
                    }
                }, 'json');
            }
        });
        return false;
    });


    $(document).on('click', '.download-button', function () {
        const backupFile = $(this).data('file');

        // Create a form to submit the POST request
        const form = $('<form>', {
            method: 'POST',
            action: '/download-backup'
        });

        // Append the CSRF token and backup file path
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: csrfToken
        }));
        form.append($('<input>', {
            type: 'hidden',
            name: 'backup_file',
            value: backupFile
        }));

        // Append the form to the body and submit
        $('body').append(form);
        form.submit();
    });


    $('#addFile').on('click', function () {
        $('#uploadInput').click();
    });

    $('#uploadInput').on('change', function () {
        const file = this.files[0];
        if (!file) return;

        if (!file.name.endsWith('.gz')) {
            showAlert('error', 'Vui lòng chọn file .gz');
            return;
        }

        const formData = new FormData();
        formData.append('backup_file', file);

        Swal.fire({
            title: 'Đang tải file lên...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '/upload-backup',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (response) {
                Swal.close();
                showAlert(response.response.status ? 'success' : 'error', response.response.message);
                if (response.response.status) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                }
            },
            error: function () {
                Swal.close();
                showAlert('error', 'Đã xảy ra lỗi khi tải file lên.');
            }
        });
    });

    let originalAddFileHtml = $('#addFile').prop('outerHTML');

    function updateToggleButton() {
        const selectedCount = $('.file-checkbox:checked').length;
        if (selectedCount > 0) {
            // Nếu có checkbox được chọn -> chuyển thành nút Xóa mục đã chọn
            $('#addFile').replaceWith(`<button id="addFile" class="btn btn-danger" style="flex: none;">
                <i class="fas fa-trash"></i> Xóa mục đã chọn
            </button>`);
            // Gán sự kiện click thực hiện xóa hàng loạt
            $('#addFile').off('click').on('click', function () {
                const selectedFiles = $('.file-checkbox:checked').map(function () {
                    return $(this).val();
                }).get();

                if (selectedFiles.length === 0) {
                    showAlert('error', 'Vui lòng chọn ít nhất một file để xóa!');
                    return;
                }

                showConfirmDialog('Xác nhận xóa', 'Bạn có chắc chắn muốn xóa các file đã chọn không?').then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/delete-multiple-backup',
                            type: 'POST',
                            data: JSON.stringify({ files: selectedFiles }),
                            contentType: 'application/json',
                            success: function (response) {
                                showAlert(response.response.status ? 'success' : 'error', response.response.message);
                                if (response.response.status) {
                                    location.reload();
                                }
                            },
                            error: function () {
                                showAlert('error', 'Đã xảy ra lỗi trong quá trình xóa.');
                            }
                        });
                    }
                });
            });
        } else {
            // Nếu không có checkbox được chọn -> hiển thị lại nút Tải file lên từ máy
            $('#addFile').replaceWith(originalAddFileHtml);
            // Gán sự kiện click mặc định để kích hoạt input file
            $('#addFile').off('click').on('click', function () {
                $('#uploadInput').click();
            });
        }
    }


});
