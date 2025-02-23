$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    let currentPage = 1;
    const perPage = 100;

    // Thiết lập CSRF token cho các yêu cầu AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Hàm hiển thị thông báo sử dụng SweetAlert
    function showAlert(type, message) {
        Swal.fire({
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000
        });
    }


    // Hàm tải danh sách link
    function loadLinks(page = 1) {
        $.ajax({
            url: '/links',
            method: 'GET',
            data: {
                page: page,
                per_page: perPage
            },
            success: function (response) {
                console.log(response);
                $('#linkList').empty();

                response.data.forEach(link => {

                    $('#linkList').append(`
                        <tr>
                            <td data-label="Chọn"><input type="checkbox" class="selectItem" data-id="${link._id.$oid}"></td>
                            <td data-label="Domain">${link.domain}</td>
                            <td data-label="UID">${link.facebook_uid || 'Không xác định'}</td>
                            <td data-label="Hash ID">${link.md5}</td>
                            <td data-label="URL">${link.url}</td>
                            <td data-label="Được thêm bởi">${link.added_by}</td>
                            <td data-label="Action">
                                <button class="btn btn-inverse-light edit-link" data-id="${link._id.$oid}">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>
                                <button class="btn btn-inverse-danger delete-link" data-id="${link._id.$oid}">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    `);
                });
                $('#linkCountTitle').text(`Quản lí dữ liệu URL :Số lượng (${response.total})`);

                currentPage = response.currentPage;



                renderPagination(
                    document.getElementById('pagination'),
                    response.currentPage,
                    response.lastPage,
                    function (page) {
                        loadLinks(page);
                    }
                );
            },
            error: function () {
                showAlert('error', 'Không thể tải danh sách Links.');
            }
        });
    }


    // Xử lý sự kiện cho checkbox "Chọn tất cả"
    $('#linkTable').on('change', '#selectAll', function () {
        $('.selectItem').prop('checked', this.checked);
    });

    // Xử lý sự kiện cho checkbox cá nhân để cập nhật trạng thái "Chọn tất cả"
    $('#linkList').on('change', '.selectItem', function () {
        if (!this.checked) {
            $('#selectAll').prop('checked', false);
        } else if ($('.selectItem:checked').length === $('.selectItem').length) {
            $('#selectAll').prop('checked', true);
        }
    });

    // Xử lý sự kiện hiển thị modal chỉnh sửa
    $('#linkList').on('click', '.edit-link', function () {
        const linkId = $(this).data('id');
        $.ajax({
            url: `/links/${linkId}`,
            method: 'GET',
            success: function (link) {
                $('#editUrl').val(link.url);
                $('#editLink').modal('show');
                $('#editLinkForm').data('id', linkId).data('action', 'edit');
            },
            error: function () {
                showAlert('error', 'Không thể tải thông tin Link.');
            }
        });
    });

    // Xử lý sự kiện xóa link
    $('#linkList').on('click', '.delete-link', function () {
        const linkId = $(this).data('id');
        Swal.fire({
            title: 'Bạn có chắc chắn muốn xóa?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Có',
            cancelButtonText: 'Không'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/links/${linkId}`,
                    method: 'DELETE',
                    success: function () {
                        showAlert('success', 'Xóa thành công.');
                        loadLinks(currentPage);
                    },
                    error: function (xhr) {
                        if (xhr.status === 403) {
                            const response = xhr.responseJSON;
                            showAlert('error', response.data.message || 'Không có quyền xoá.');
                        } else {
                            showAlert('error', 'Không thể xóa Link.');
                        }
                    }
                });
            }
        });
    });

    // Xử lý sự kiện thêm link mới
    $('#addLink').on('click', function () {
        $('#editLinkForm').trigger('reset').data('action', 'add').removeData('id');
        $('#editLink').modal('show');
    });

    // Xử lý sự kiện gửi form chỉnh sửa/thêm
    $('#editLinkForm').on('submit', function (e) {
        e.preventDefault();

        const action = $(this).data('action');
        const linkId = $(this).data('id');
        const url = action === 'edit' ? `/links/${linkId}` : '/links';
        const method = action === 'edit' ? 'PUT' : 'POST';

        const formData = $(this).serialize();

        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function (response) {
                if (response.data.status) {
                    showAlert('success', response.data.message);
                    $('#editLink').modal('hide');
                    loadLinks(currentPage);
                } else {
                    showAlert('error', response.data.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 409) {
                    const response = xhr.responseJSON;
                    const message = (response && response.data && response.data.message) ? response.data.message : 'Link đã tồn tại';
                    showAlert('error', message);
                } else {
                    showAlert('error', 'Đã xảy ra lỗi.');
                }
            }
        });
    });

    // Tải danh sách link ban đầu
    loadLinks();
});