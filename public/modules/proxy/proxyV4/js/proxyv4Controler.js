$(document).on('click', '.btnCreateProxyV4', function (event) {
    event.preventDefault();

    let _that = $(this);
    let _form = _that.closest("form");
    let _action = _form.attr("action") || "/CreateProxyV4";
    let _redirect = _form.data("redirect") || window.location.href;
    let csrfToken = $('meta[name="csrf-token"]').attr('content');

    let _data = _form.serialize() + '&_token=' + csrfToken;

    $.post(_action, _data, function (result) {
        if (result.status) { // Sử dụng `status` thay vì `success`
            Swal.fire({
                icon: 'success',
                title: "Thành Công",
                text: result.message || "Cấu hình đã được tạo thành công!"
            }).then(() => {
                window.location.assign(_redirect);
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: "Lỗi",
                text: result.message || "Đã xảy ra lỗi, vui lòng thử lại!"
            });
        }
    }, 'json').fail(function () {
        Swal.fire({
            icon: 'error',
            title: "Lỗi",
            text: "Không thể gửi yêu cầu, vui lòng thử lại!"
        });
    }).always(function () {
        _that.html("Lưu Thông Tin");
        setTimeout(function () {
            window.location.reload();
        }, 3000);
    });
});


$(document).on('click', '.btnConnect', function (event) {
    event.preventDefault();

    let _that = $(this);
    let name = _that.data('name');
    let _url = "/proxyv4/connect";
    let csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.post(_url, { name: name, _token: csrfToken }, function (result) {
        if (result.status) {
            Swal.fire({
                icon: 'success',
                title: "Thành Công",
                text: result.message || "Proxy đã được kết nối thành công!"
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: "Lỗi",
                text: result.message || "Không thể kết nối proxy, vui lòng thử lại!"
            });
        }
    }, 'json').fail(function () {
        Swal.fire({
            icon: 'error',
            title: "Lỗi",
            text: "Không thể gửi yêu cầu, vui lòng thử lại!"
        });
    }).always(function () {
        setTimeout(function () {
            window.location.reload();
        }, 3000);
    });
});

$(document).on('click', '.btnDisconnect', function (event) {
    event.preventDefault();

    let _that = $(this);
    let name = _that.data('name');
    let _url = "/proxyv4/disconnect";
    let csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.post(_url, { name: name, _token: csrfToken }, function (result) {
        if (result.status) {
            Swal.fire({
                icon: 'success',
                title: "Thành Công",
                text: result.message || "Proxy đã được ngắt kết nối thành công!"
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: "Lỗi",
                text: result.message || "Không thể ngắt kết nối proxy, vui lòng thử lại!"
            });
        }
    }, 'json').fail(function () {
        Swal.fire({
            icon: 'error',
            title: "Lỗi",
            text: "Không thể gửi yêu cầu, vui lòng thử lại!"
        });
    }).always(function () {
        setTimeout(function () {
            window.location.reload();
        }, 3000);
    });
});

$(document).on('click', '.BtnReloadIp', function (event) {
    event.preventDefault();

    let _that = $(this);
    let name = _that.data('name');
    let _url = "/proxyv4/BtnReloadIp";
    let csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.post(_url, { name: name, _token: csrfToken }, function (result) {
        if (result.status) {
            Swal.fire({
                icon: 'success',
                title: "Thành Công",
                text: result.message || "Proxy đã được reload thành công!"
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: "Lỗi",
                text: result.message || "Không thể reload proxy, vui lòng thử lại!"
            });
        }
    }, 'json').fail(function () {
        Swal.fire({
            icon: 'error',
            title: "Lỗi",
            text: "Không thể gửi yêu cầu, vui lòng thử lại!"
        });
    }).always(function () {
        setTimeout(function () {
            window.location.reload();
        }, 3000);
    });
});

$(document).on('click', '.btnDelete', function (event) {
    event.preventDefault();

    let _that = $(this);
    let name = _that.data('name');
    let _url = "/proxyv4/delete";
    let csrfToken = $('meta[name="csrf-token"]').attr('content');

    Swal.fire({
        title: 'Bạn có chắc muốn xóa?',
        text: "Hành động này không thể hoàn tác!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(_url, { name: name, _token: csrfToken }, function (result) {
                if (result.status) {
                    Swal.fire({
                        icon: 'success',
                        title: "Thành Công",
                        text: result.message || "Tệp cấu hình đã được xóa thành công!"
                    });

                    // Xóa dòng trong bảng
                    _that.closest('tr').remove();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: "Lỗi",
                        text: result.message || "Không thể xóa proxy, vui lòng thử lại!"
                    });
                }
            }, 'json').fail(function () {
                Swal.fire({
                    icon: 'error',
                    title: "Lỗi",
                    text: "Không thể gửi yêu cầu, vui lòng thử lại!"
                });
            }).always(function () {
                setTimeout(function () {
                    window.location.reload();
                }, 3000);
            });
        }
    });
});
