$(document).ready(function () {

    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    let page = 1;
    let loading = false;
    let hasMore = true;
    let searchQuery = '';
    let debounceTimeout = null;

    function showAlert(type, message) {
        Swal.fire({
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000
        });
    }

    function loadEmailScans(reset = false) {
        if (reset) {
            page = 1;
            hasMore = true;
            $('#emailScanList').empty();
        }

        if (loading || !hasMore) return;

        loading = true;
        $('#loading').show();

        $.ajax({
            url: '/email-scan',
            method: 'GET',
            data: {
                page: page,
                search: searchQuery
            },
            success: function (response) {
                if (!response.data || !Array.isArray(response.data)) {
                    showAlert('error', 'Dữ liệu trả về không hợp lệ.');
                    return;
                }

                if (reset && response.data.length === 0) {
                    $('#emailScanList').html('<tr><td colspan="12" style="text-align:center;">Không tìm thấy dữ liệu.</td></tr>');
                    hasMore = false;
                    return;
                }

                response.data.forEach(emailScan => {
                    const formattedSinhnhat = emailScan.sinhnhat ? dayjs(emailScan.sinhnhat).format('DD/MM/YYYY') : '';
                    const dataName = `${emailScan.uid} ${emailScan.email} ${emailScan.fullname} ${emailScan.quocgia} ${emailScan.quequan}`.toLowerCase();

                    $('#emailScanList').append(`
                        <tr data-name="${dataName}">
                            <td><input type="checkbox" class="selectItem" value="${emailScan._id.$oid}"></td>
                            <td>${emailScan.uid || ''}</td>
                            <td>${emailScan.email || ''}</td>
                            <td>${emailScan.domain || ''}</td>
                            <td>${emailScan.phone || ''}</td>
                            <td>${emailScan.fullname || ''}</td>
                            <td>${emailScan.quocgia || ''}</td>
                            <td>${emailScan.quequan || ''}</td>
                            <td>${emailScan.follow || ''}</td>
                            <td>${emailScan.friend || ''}</td>
                            <td>${formattedSinhnhat}</td>
                            <td>
                                <button class="btn btn-inverse-light edit-email-scan" data-id="${emailScan._id.$oid}">Chỉnh sửa</button>
                                <button class="btn btn-inverse-danger delete-email-scan" data-id="${emailScan._id.$oid}">Xóa</button>
                            </td>
                        </tr>
                    `);
                });

                hasMore = response.currentPage < response.lastPage;
                if (hasMore) page++;

                $('#emailCount').text(response.data.length);
            },
            error: function () {
                showAlert('error', 'Không thể tải danh sách Email Scan.');
            },
            complete: function () {
                loading = false;
                $('#loading').hide();
            }
        });
    }

    loadEmailScans();

    $('#tableContainer').on('scroll', function () {
        if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 10) {
            loadEmailScans();
        }
    });

    $("#searchInput").on("keyup", function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            searchQuery = $(this).val().trim().toLowerCase();
            loadEmailScans(true);
        }, 300); // 300ms debounce
    });

    $('#emailScanTable').on('change', '#selectAll', function () {
        $('.selectItem').prop('checked', this.checked);
    });

    $('#emailScanList').on('change', '.selectItem', function () {
        if (!this.checked) $('#selectAll').prop('checked', false);
        if ($('.selectItem:checked').length === $('.selectItem').length) $('#selectAll').prop('checked', true);
    });

    $('#emailScanList').on('click', '.edit-email-scan', function () {
        const emailScanId = $(this).data('id');
        $.ajax({
            url: `/email-scan/${emailScanId}`,
            method: 'GET',
            success: function (emailScan) {
                $('#editUid').val(emailScan.uid || '');
                $('#editEmail').val(emailScan.email || '');
                $('#editDomain').val(emailScan.domain || '');
                $('#editPhone').val(emailScan.phone || '');
                $('#editFullname').val(emailScan.fullname || '');
                $('#editQuocgia').val(emailScan.quocgia || '');
                $('#editQuequan').val(emailScan.quequan || '');
                $('#editFollow').val(emailScan.follow || '');
                $('#editFriend').val(emailScan.friend || '');
                $('#editSinhnhat').val(emailScan.sinhnhat ? dayjs(emailScan.sinhnhat).format('YYYY-MM-DD') : '');
                $('#editEmailScan').modal('show');
                $('#editUserForm').data('id', emailScan._id.$oid).data('action', 'edit');
            },
            error: function () {
                showAlert('error', 'Không thể tải thông tin Email Scan.');
            }
        });
    });

    $('#addEmailScan').on('click', function (e) {
        e.preventDefault();
        $('#editUserForm').trigger('reset').data('action', 'add').removeData('id');
        $('#editSinhnhat').val('');
        $('#editEmailScan').modal('show');
    });

    $('#emailScanList').on('click', '.delete-email-scan', function () {
        const emailScanId = $(this).data('id');
        Swal.fire({
            title: 'Bạn có chắc chắn muốn xóa?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Có',
            cancelButtonText: 'Không'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/email-scan/${emailScanId}`,
                    method: 'DELETE',
                    success: function () {
                        showAlert('success', 'Xóa thành công.');
                        loadEmailScans(true);
                    },
                    error: function () {
                        showAlert('error', 'Không thể xóa.');
                    }
                });
            }
        });
    });

    $('#editUserForm').on('submit', function (e) {
        e.preventDefault();
        const action = $(this).data('action');
        const emailScanId = $(this).data('id');
        const url = action === 'edit' ? `/email-scan/${emailScanId}` : '/email-scan';
        const method = action === 'edit' ? 'PUT' : 'POST';
        const formData = $(this).serialize();

        $.ajax({
            url: url,
            method: method,
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.status === true) {
                    showAlert('success', response.message);
                    $('#editEmailScan').modal('hide');
                    loadEmailScans(true);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessages = 'Lỗi: ';
                    for (const field in errors) {
                        errorMessages += `${errors[field].join(', ')} `;
                    }
                    showAlert('error', errorMessages);
                } else {
                    showAlert('error', 'Không thể lưu thông tin.');
                }
            }
        });
    });
});
