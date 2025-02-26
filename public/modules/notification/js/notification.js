document.addEventListener('DOMContentLoaded', function () {

    let currentPage = 1;
    let lastPage = 1;
    let isLoading = false;

    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    const notificationList = document.getElementById('notificationList');
    const loadingIndicator = document.getElementById('loading');
    const paginationContainer = document.getElementById('pagination');
    const selectAll = document.getElementById('selectAll');
    const bulkActionButtons = document.getElementById('bulkActionButtons');
    const selectedActionButtons = document.getElementById('selectedActionButtons');

    function updateNotificationCountTitle() {
        $.ajax({
            url: '/notification',
            method: 'GET',
            dataType: 'json',
            data: { page: 1 },
            success: function (data) {
                if (data.data) {
                    // Lấy tổng số thông báo
                    const totalCount = data.data.length;
                    document.getElementById('notificationCountTitle').innerText =
                        `Quản lí Thông báo (${totalCount})`;
                }
            },
            error: function (error) {
                console.error('Error updating notification count title:', error);
            }
        });
    }

    function loadNotification(page = 1) {
        if (isLoading) return;
        isLoading = true;
        loadingIndicator.style.display = 'block';

        $.ajax({
            url: '/notification',
            method: 'GET',
            dataType: 'json',
            data: { page: page },
            success: function (data) {
                notificationList.innerHTML = '';
                data.data.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.classList.add(item.is_read ? 'read' : 'unread');
                    row.innerHTML = `
                    <td><input type="checkbox" class="selectNotification" data-id="${item._id.$oid}"></td>
                    <td>${index + 1}</td>
                    <td>${timeAgo(item.create_time)}</td>
                    <td>${item.notifi_type}</td>
                    <td class="truncate-message">${item.content_notifi}</td>
                    <td class="status ${item.is_read ? 'read' : 'unread'} btn btn-inverse-primary btn-fw ">
                        ${item.is_read ? '<i class="fa fa-envelope-open"></i> Đã đọc' : '<i class="fa fa-envelope"></i> Chưa đọc'}
                    </td>
                    <td>
                        <button type="button" class="btn btn-inverse-info btn-fw showBtn" data-id="${item._id.$oid}">
                            <i class="fa fa-eye" style="color: white;"></i> Xem
                        </button>
                        <button type="button" class="btn btn-inverse-danger btn-fw deleteBtn" data-id="${item._id.$oid}">
                            <i class="fa fa-trash"></i> Xóa
                        </button>
                    </td>
                `;
                    notificationList.appendChild(row);
                });

                currentPage = data.currentPage;
                lastPage = data.lastPage;
                renderPagination(paginationContainer, currentPage, lastPage, loadNotification);

                loadingIndicator.style.display = 'none';
                isLoading = false;

                // Cập nhật trạng thái các hành động và tiêu đề số thông báo
                updateSelectedActions();
                updateNotificationCountTitle();
            },
            error: function (error) {
                console.error('Error loading notification:', error);
                loadingIndicator.style.display = 'none';
                isLoading = false;
            }
        });
    }

    // Khi checkbox "selectAll" thay đổi
    selectAll.addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.selectNotification');
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = selectAll.checked;
        });
        // Không ẩn bulkActionButtons, chúng luôn hiển thị.
        updateSelectedActions();
    });


    // Lắng nghe sự kiện thay đổi trên từng checkbox thông báo dùng delegation
    notificationList.addEventListener('change', function (e) {
        if (e.target.classList.contains('selectNotification')) {
            // Nếu có checkbox nào không được tick thì bỏ chọn "selectAll"
            if (!e.target.checked) {
                selectAll.checked = false;
            }
            updateSelectedActions();
        }
    });
    function updateSelectedActions() {
        const selectedCount = document.querySelectorAll('.selectNotification:checked').length;
        if (selectedCount > 0) {
            // Nếu có checkbox được chọn, hiển thị container hành động cho các mục đã chọn
            selectedActionButtons.style.display = 'block';
            // Ẩn container bulkActionButtons
            bulkActionButtons.style.display = 'none';
        } else {
            // Nếu không có checkbox nào được chọn, ẩn container đã chọn và hiển thị container bulk
            selectedActionButtons.style.display = 'none';
            bulkActionButtons.style.display = 'block';
        }
    }

    // Hàm chuyển đổi thời gian
    function timeAgo(dateString) {
        const now = new Date();
        const parts = dateString.split(/[\s\/:]+/);
        if (parts.length === 6) {
            dateString = `${parts[2]}-${parts[1]}-${parts[0]}T${parts[3]}:${parts[4]}:${parts[5]}`;
        }
        const date = new Date(dateString);
        const seconds = Math.floor((now - date) / 1000);
        let interval = Math.floor(seconds / 31536000);
        if (interval >= 1) {
            return interval + " năm trước";
        }
        interval = Math.floor(seconds / 2592000);
        if (interval >= 1) {
            return interval + " tháng trước";
        }
        interval = Math.floor(seconds / 86400);
        if (interval >= 1) {
            return interval + " ngày trước";
        }
        interval = Math.floor(seconds / 3600);
        if (interval >= 1) {
            return interval + " giờ trước";
        }
        interval = Math.floor(seconds / 60);
        if (interval >= 1) {
            return interval + " phút trước";
        }
        return Math.floor(seconds) + " giây trước";
    }

    loadNotification(currentPage);

    // Xử lý sự kiện click cho button "Xem"
    $(document).on('click', '.showBtn', function () {
        const id = $(this).data('id');

        $.ajax({
            url: '/notifications',
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({ ids: [id] }), // truyền mảng chứa id
            success: function (response) {
                // response trả về là một mảng thông báo
                if (Array.isArray(response) && response.length > 0) {
                    const notification = response[0];
                    Swal.fire({
                        title: notification.notifi_type.toUpperCase(),
                        html: `<textarea readonly style="resize: both; width: 500px; height: 300px;">${notification.content_notifi}</textarea>
                               <p><small>${notification.create_time}</small></p>`,
                        icon: 'info',
                        confirmButtonText: 'Đóng'
                    }).then(() => {
                        loadNotification(currentPage);
                    });
                } else {
                    Swal.fire({
                        title: 'Thông báo',
                        text: 'Không tìm thấy thông báo.',
                        icon: 'warning',
                        confirmButtonText: 'Đóng'
                    });
                }
            },
            error: function (error) {
                Swal.fire({
                    title: 'Lỗi',
                    text: 'Có lỗi xảy ra, vui lòng thử lại.',
                    icon: 'error',
                    confirmButtonText: 'Đóng'
                });
            }
        });
    });

    // Hàm chung để cập nhật trạng thái "đã đọc"
    function markNotificationsAsRead(ids) {
        if (ids.length === 0) {
            Swal.fire({
                title: 'Chú ý',
                text: 'Chưa chọn thông báo nào!',
                icon: 'warning',
                confirmButtonText: 'Đóng'
            });
            return;
        }

        $.ajax({
            url: '/notifications',
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({ ids: ids }),
            success: function (response) {
                let msg = '';

                // Nếu backend trả về message trong response (cho trường hợp "Đọc tất cả")
                if (response.message) {
                    msg = response.message;
                } else if (Array.isArray(response) && response.length > 0) {
                    msg = `Đã cập nhật ${response.length} thông báo đã đọc thành công.`;
                } else {
                    msg = 'Cập nhật thành công.';
                }

                Swal.fire({
                    title: 'Thành công',
                    text: msg,
                    icon: 'success',
                    confirmButtonText: 'Đóng'
                }).then(() => {
                    loadNotification(currentPage);
                });
            },
            error: function (error) {
                Swal.fire({
                    title: 'Lỗi',
                    text: 'Có lỗi xảy ra, vui lòng thử lại.',
                    icon: 'error',
                    confirmButtonText: 'Đóng'
                });
            }
        });
    }

    // Xử lý sự kiện click cho nút "Đọc tất cả"
    $(document).on('click', '#readAllBtn', function () {
        // Gửi payload với mảng chứa 'all' để cập nhật toàn bộ thông báo trong database
        markNotificationsAsRead(['all']);
    });

    // Xử lý sự kiện click cho nút "Đọc các mục đã chọn"
    $(document).on('click', '#readSelectedBtn', function () {
        let ids = [];
        document.querySelectorAll('.selectNotification:checked').forEach(function (checkbox) {
            ids.push(checkbox.dataset.id);
        });
        markNotificationsAsRead(ids);
    });


    // Hàm chung để xóa thông báo với xác nhận trước khi gọi API
    function confirmAndDeleteNotifications(ids) {
        if (ids.length === 0) {
            Swal.fire({
                title: 'Chú ý',
                text: 'Chưa chọn thông báo nào!',
                icon: 'warning',
                confirmButtonText: 'Đóng'
            });
            return;
        }

        Swal.fire({
            title: 'Xác nhận xóa',
            text: 'Bạn có chắc chắn muốn xóa những thông báo đã chọn?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                // Nếu người dùng xác nhận, gọi API xóa
                $.ajax({
                    url: '/delete-notifications',
                    method: 'POST',
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify({ ids: ids }),
                    success: function (response) {
                        let msg = '';

                        if (response.message) {
                            msg = response.message;
                        } else if (Array.isArray(response) && response.length > 0) {
                            msg = `Đã xóa ${response.length} thông báo thành công.`;
                        } else {
                            msg = 'Xóa thành công.';
                        }

                        Swal.fire({
                            title: 'Thành công',
                            text: msg,
                            icon: 'success',
                            confirmButtonText: 'Đóng'
                        }).then(() => {
                            loadNotification(currentPage);
                        });
                    },
                    error: function (error) {
                        Swal.fire({
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra, vui lòng thử lại.',
                            icon: 'error',
                            confirmButtonText: 'Đóng'
                        });
                    }
                });
            }
        });
    }

    // Xử lý sự kiện click cho nút "Xóa từng thông báo"
    $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        confirmAndDeleteNotifications([id]);
    });

    // Xử lý sự kiện click cho nút "Xóa tất cả"
    $(document).on('click', '#deleteAllBtn', function () {
        // Với "Xóa tất cả" truyền mảng với 'all' để xóa toàn bộ thông báo trong database
        confirmAndDeleteNotifications(['all']);
    });

    // Xử lý sự kiện click cho nút "Xóa các mục đã chọn"
    $(document).on('click', '#deleteSelectedBtn', function () {
        let ids = [];
        document.querySelectorAll('.selectNotification:checked').forEach(function (checkbox) {
            ids.push(checkbox.dataset.id);
        });
        confirmAndDeleteNotifications(ids);
    });
});