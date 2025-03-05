
$(document).ready(function () {

    function getSystemInfo() {
        $.ajax({
            url: '/system-info',
            type: 'GET',
            success: function (response) {
                $('#currentCpu').text(response.currentCpu);
                $('#ramUsage').text(response.ramUsage);
                $('#diskUsage').text(response.diskUsage);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching system info:', error);
            }
        });
    }

    getSystemInfo();
    setInterval(getSystemInfo, 5000); // Update every 5 seconds


    $(document).ready(function () {
        $('#rebootButton').on('click', function () {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: 'Thiết bị sẽ khởi động lại sau thao tác này',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đúng, Khởi động lại!',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/reboot', $.param({ _token: '{{ csrf_token() }}' }), function (result) {
                        if (result.success == true) {
                            Swal.fire({
                                title: 'Đã hoàn tất',
                                text: 'Thiết bị đang khởi động lại.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                setTimeout(function () {
                                    location.reload();
                                }, 30);
                            });
                        } else {
                            Swal.fire({
                                title: 'Lỗi',
                                text: result.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    }, 'json');
                }
            });
        });

        $('#shutdownButton').on('click', function () {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: 'Thiết bị sẽ tắt lại sau thao tác này',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đúng, Tắt máy!',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/shutdown', $.param({ _token: '{{ csrf_token() }}' }), function (result) {
                        if (result.success == true) {
                            Swal.fire({
                                title: 'Đã hoàn tất',
                                text: 'Thiết bị đã tắt.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                setTimeout(function () {
                                    location.reload();
                                }, 30);
                            });
                        } else {
                            Swal.fire({
                                title: 'Lỗi',
                                text: result.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    }, 'json');
                }
            });
        });

        $('.SeviceControler').on('click', function () {
            _that = $(this);
            _action = _that.data("action");
            _name = _that.data("name");
            _option = _that.data("option");

            let confirmText, successText;

            // Xác định thông báo dựa trên _option
            switch (_option) {
                case 'ON':
                    confirmText = `Dịch vụ "${_name}" sẽ được bật.`;
                    successText = `Dịch vụ "${_name}" đã được bật.`;
                    break;
                case 'OFF':
                    confirmText = `Dịch vụ "${_name}" sẽ bị tắt.`;
                    successText = `Dịch vụ "${_name}" đã bị tắt.`;
                    break;
                case 'RESTART':
                    confirmText = `Dịch vụ "${_name}" sẽ khởi động lại.`;
                    successText = `Dịch vụ "${_name}" đã được khởi động lại.`;
                    break;
                default:
                    confirmText = 'Bạn có chắc chắn thực hiện hành động này?';
                    successText = 'Hành động đã hoàn tất.';
            }

            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Đúng, ${_option}!`,
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(_action, $.param({ _token: $('meta[name="csrf-token"]').attr('content'), name: _name, option: _option }), function (result) {
                        if (result.success == true) {
                            Swal.fire({
                                title: 'Đã hoàn tất',
                                text: successText,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                setTimeout(function () {
                                    location.reload();
                                }, 30);
                            });
                        } else {
                            Swal.fire({
                                title: 'Lỗi',
                                text: result.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    }, 'json');
                }
            });
        });

    });

    $(document).ready(function () {

        // Hàm cập nhật số thông báo chưa đọc (dựa trên is_read == false)
        function updateNotificationCount() {
            $.ajax({
                url: '/notification',
                method: 'GET',
                dataType: 'json',
                // Dùng tham số latest để lấy danh sách thông báo mới nhất (hàm backend sẽ trả về danh sách tất cả hoặc theo limit)
                data: { latest: true },
                success: function (response) {
                    if (response.data && response.data.length > 0) {
                        // Đếm số thông báo chưa đọc theo trường is_read == false
                        const unreadCount = response.data.filter(item => !item.is_read).length;
                        const $countSpan = $('#notificationDropdown').find('.count');
                        if (unreadCount > 0) {
                            $countSpan.text(unreadCount);
                            $countSpan.show();
                        } else {
                            $countSpan.hide();
                        }
                    } else {
                        $('#notificationDropdown').find('.count').hide();
                    }
                },
                error: function (error) {
                    console.error('Lỗi khi lấy số lượng thông báo:', error);
                    $('#notificationDropdown').find('.count').hide();
                }
            });
        }

        // Gọi hàm cập nhật ngay khi trang load và định kỳ (mỗi 30 giây)
        updateNotificationCount();
        setInterval(updateNotificationCount, 30000);

        let notificationsLoaded = false;

        // Bắt sự kiện khi dropdown hiện ra (sự kiện của Bootstrap)
        $(document).on('show.bs.dropdown', function (e) {
            if (e.target.id === 'notificationDropdown' || $(e.target).find('#notificationDropdown').length) {
                if (!notificationsLoaded) {
                    loadNotifications();
                    notificationsLoaded = true;
                }
            }
        });
    
        // Bắt sự kiện khi dropdown ẩn đi
        $(document).on('hidden.bs.dropdown', function (e) {
            if (e.target.id === 'notificationDropdown' || $(e.target).find('#notificationDropdown').length) {
                notificationsLoaded = false; // Đặt lại trạng thái để lần sau load lại
            }
        });
    
        // Tách hàm load thông báo ra để tái sử dụng
        function loadNotifications() {
            $.ajax({
                url: '/notification',
                method: 'GET',
                dataType: 'json',
                data: { latest: true },
                success: function (response) {
                    const dropdownMenu = $('#notificationDropdown').siblings('.dropdown-menu');
                    dropdownMenu.empty();
                    dropdownMenu.append('<h6 class="p-3 mb-0">Notifications</h6>');
                    dropdownMenu.append('<div class="dropdown-divider"></div>');
    
                    if (response.data && response.data.length > 0) {
                        // Sắp xếp thông báo giảm dần theo thời gian
                        const sortedNotifications = response.data.sort((a, b) => new Date(b.create_time) - new Date(a.create_time));
                        // Lấy 3 thông báo mới nhất
                        const notifications = sortedNotifications.slice(0, 3);
    
                        notifications.forEach(function (item) {
                            dropdownMenu.append(`
                                <a class="dropdown-item preview-item showDropdownNotification" href="#" data-id="${item._id.$oid}">
                                    <div class="preview-thumbnail">
                                        <div class="preview-icon bg-dark rounded-circle">
                                            <i class="mdi mdi-calendar text-success"></i>
                                        </div>
                                    </div>
                                    <div class="preview-item-content">
                                        <p class="preview-subject mb-1">${item.notifi_type}</p>
                                        <p class="text-muted ellipsis mb-0">${item.content_notifi}</p>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                            `);
                        });
                    } else {
                        // Hiển thị "No Notification!" khi không có thông báo
                        dropdownMenu.append(`
                            <div class="text-center p-3">
                                <p>Không có thông báo mới !</p>
                            </div>
                            <div class="dropdown-divider"></div>
                        `);
                    }
    
                    // Thêm mục "See all notifications" với sự kiện click
                    dropdownMenu.append('<p class="p-3 mb-0 text-center see-all-notifications" style="cursor:pointer;">See all notifications</p>');
                },
                error: function () {
                    console.error('Lỗi khi tải danh sách thông báo');
                    const dropdownMenu = $('#notificationDropdown').siblings('.dropdown-menu');
                    dropdownMenu.empty();
                    dropdownMenu.append('<h6 class="p-3 mb-0">Notifications</h6>');
                    dropdownMenu.append('<div class="dropdown-divider"></div>');
                    dropdownMenu.append('<div class="text-center p-3"><p>No Notification!</p></div>');
                    dropdownMenu.append('<div class="dropdown-divider"></div>');
                    dropdownMenu.append('<p class="p-3 mb-0 text-center see-all-notifications" style="cursor:pointer;">See all notifications</p>');
                }
            });
        }


        // Sự kiện click cho thông báo trong dropdown: hiển thị modal chi tiết (giống nút "Xem" trong trang notification)
        $(document).on('click', '.showDropdownNotification', function (e) {
            e.preventDefault();
            e.stopPropagation(); // Ngăn dropdown đóng lại
            const id = $(this).data('id');
            $.ajax({
                url: '/notifications',
                method: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({ ids: [id] }),
                success: function (response) {
                    if (Array.isArray(response) && response.length > 0) {
                        const notification = response[0];
                        Swal.fire({
                            title: notification.notifi_type.toUpperCase(),
                            html: `<textarea readonly style="resize: both; width: 500px; height: 300px;">${notification.content_notifi}</textarea>
                                   <p><small>${notification.create_time}</small></p>`,
                            icon: 'info',
                            confirmButtonText: 'Đóng'
                        }).then(() => {
                            // Reload lại số thông báo chưa đọc sau khi nhấn "Đóng"
                            updateNotificationCount();
                        });
                    } else {
                        Swal.fire({
                            title: 'Thông báo',
                            text: 'Không tìm thấy thông báo.',
                            icon: 'warning',
                            confirmButtonText: 'Đóng'
                        }).then(() => {
                            updateNotificationCount();
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Có lỗi xảy ra, vui lòng thử lại.',
                        icon: 'error',
                        confirmButtonText: 'Đóng'
                    }).then(() => {
                        updateNotificationCount();
                    });
                }
            });
        });

        // Khi click "See all notifications" chuyển hướng tới /notification-page
        $(document).on('click', '.see-all-notifications', function () {
            window.location.href = '/notification-page';
        });

        // ... các hàm và xử lý cho cập nhật, xóa thông báo trong trang notification (không đổi) ...
    });
});