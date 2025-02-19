// ...existing code...



$(document).on('click', '.btnLuuThongTin', function (event) {
	event.preventDefault();

	let _that = $(this);
	let _form = _that.closest("form");
	let _action = _form.attr("action") || "/your-post-endpoint";
	let _redirect = _form.data("redirect") || window.location.href;
	let _data = _form.serialize();

	$.post(_action, _data, function (result) {
		if (result.success) {
			Swal.fire({
				icon: 'success',
				title: result.title || "Thành Công",
				text: result.msg || "Cập nhật thành công!"
			}).then(() => {
				window.location.assign(_redirect);
			});
		} else {
			Swal.fire({
				icon: 'success',
				title: result.title || "Lỗi",
				text: result.msg || "Đã xảy ra lỗi, vui lòng thử lại!"
			});
		}
		setTimeout(function () {
			window.location.reload();
		}, 3000);
	}, 'json').fail(function () {
		setTimeout(function () {
			window.location.reload();
		}, 3000);
	}).always(function () {
		_that.html("Lưu Thông Tin");
	});
});


$(document).on('change', '.network-switch input[type="checkbox"]', function () {
    const _that = $(this); // Lấy checkbox đang được click
    const _action = _that.data('action'); // Lấy URL từ data-action
    const portName = _that.closest('.network-switch').find('.port-label').text().trim();
    const status = _that.is(':checked') ? 1 : 0;

    // Chuẩn bị dữ liệu
    const _data = {
        port_name: portName,
        status: status,
        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
    };

    // Gửi POST request
    $.post(_action, _data, function (result) {
        if (result.label === 'success') {
            // Hiển thị thông báo thành công với Swal.fire
            Swal.fire({
                icon: 'success',
                title: result.title || "Thành Công",
                text: result.msg || "Cập nhật thành công!"
            });
            
            // Kiểm tra xem có lệnh command hay không
            if (result.command) {
                console.log('Command to execute:', result.command);
            }

            // Kiểm tra output trả về
            if (result.output && result.output.length > 0) {
                console.log('Command Output:', result.output);
            }

            // Xử lý thành công (nếu cần thêm logic khác)
            if (result.return_var === 0) {
                console.log('Command executed successfully');
            }
        } else {
            // Hiển thị thông báo lỗi với Swal.fire
            Swal.fire({
                icon: 'error',
                title: result.title || "Lỗi",
                text: result.msg || "Đã xảy ra lỗi, vui lòng thử lại!"
            });
        }
        setTimeout(function () {
            window.location.reload();
        }, 3000);
    }, 'json')
    .fail(function (xhr) {
        // Xử lý lỗi HTTP hoặc lỗi kết nối
        Swal.fire({
            icon: 'error',
            title: "Lỗi",
            text: "Không thể kết nối đến server. Vui lòng thử lại!"
        });
        console.error('Error:', xhr.responseText);
        setTimeout(function () {
            window.location.reload();
        }, 3000);
    });

    return false;
});
