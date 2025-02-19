
$(document).ready(function() {

    function getSystemInfo() {
        $.ajax({
            url: '/system-info',
            type: 'GET',
            success: function(response) {
                $('#currentCpu').text(response.currentCpu);
                $('#ramUsage').text(response.ramUsage);
                $('#diskUsage').text(response.diskUsage);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching system info:', error);
            }
        });
    }

    getSystemInfo();
    setInterval(getSystemInfo, 5000); // Update every 5 seconds
	
	
	$(document).ready(function() {
    $('#rebootButton').on('click', function() {
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
                $.post('/reboot', $.param({ _token: '{{ csrf_token() }}' }), function(result) {
                    if (result.success == true) {
                        Swal.fire({
                            title: 'Đã hoàn tất',
                            text: 'Thiết bị đang khởi động lại.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            setTimeout(function() {
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

    $('#shutdownButton').on('click', function() {
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
                $.post('/shutdown', $.param({ _token: '{{ csrf_token() }}' }), function(result) {
                    if (result.success == true) {
                        Swal.fire({
                            title: 'Đã hoàn tất',
                            text: 'Thiết bị đã tắt.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            setTimeout(function() {
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
	
	$('.SeviceControler').on('click', function() {
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
				$.post(_action, $.param({ _token: $('meta[name="csrf-token"]').attr('content'), name: _name, option: _option }), function(result) {
					if (result.success == true) {
						Swal.fire({
							title: 'Đã hoàn tất',
							text: successText,
							icon: 'success',
							confirmButtonText: 'OK'
						}).then(() => {
							setTimeout(function() {
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

});