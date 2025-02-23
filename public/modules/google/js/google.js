$(document).ready(function () {

	const csrfToken = $('meta[name="csrf-token"]').attr('content');

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': csrfToken
		}
	});

	fetchAccounts();
	setInterval(updateAuthCodes, 10000); // Refresh codes every 10 seconds

	function showAlert(type, message) {
		Swal.fire({
			icon: type,
			title: message,
			showConfirmButton: false,
			timer: 3000
		});
	}

	$(document).on('click', '#addAccount', function () {
		$('#editAccountModal').modal('show');
		$('#editAccountForm').attr('data-id', '');
		$('#editAccountForm')[0].reset();
	});

	$(document).on('change', '#selectAll', function () {
		$('.selectItem').prop('checked', $(this).prop('checked'));
		updateSelectedEmails();
	});

	$(document).on('change', '.selectItem', function () {
		updateSelectedEmails();
	});

	// Nút xóa được chọn
	$(document).on('click', '#deleteSelected', function () {
		const selectedEmails = $('#emails').val();

		$('#deleteAccountModal').modal('show');

	});

	function updateSelectedEmails() {
		const selectedEmails = $('.selectItem:checked').map(function () {
			return $(this).val();
		}).get();

		$('#emails').val(selectedEmails.join('\n')); // Cập nhật vào input
	}



	function fetchAccounts() {
		$.ajax({
			url: '/googles',
			type: 'GET',
			success: function (data) {
				var accountList = $('#accountList');
				accountList.empty();

				$.each(data, function (index, account) {
					var row = `
                        <tr>
						  <td><input type="checkbox" class="selectItem" value="${account.username}"></td>
                            <td>${index + 1}</td>
                            <td>${account.username}</td>
                            <td>${account.siteDomain}</td>
                            <td>${account.keyCode}</td>
                            <td>
                                <span class="generated-code" data-key="${account.keyCode}"></span>
                            </td>
                            <td>${account.password}</td>
                            <td>${account.notes}</td>
                            <td>${new Date(account.created_at).toLocaleString()}</td>
                            <td>${new Date(account.updated_at).toLocaleString()}</td>
                            <td>
                                <button class="btn btn-inverse-light edit-account" data-id="${account._id}">Sửa</button>
                                <button class="btn btn-inverse-danger delete-account" data-id="${account._id}">Xóa</button>
                            </td>
                        </tr>`;
					accountList.append(row);
				});

				updateAuthCodes();
			},
			error: function () {
				showAlert('error', 'Lỗi khi lấy danh sách tài khoản.');
			}
		});
	}

	function updateAuthCodes() {
		$('.generated-code').each(function () {
			var element = $(this);
			var keyCode = element.data('key');

			$.ajax({
				url: '/generate-google-code',
				type: 'POST',
				data: { keyCode: keyCode },
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function (response) {
					element.text(response.code);
				},
				error: function () {
					element.text('Lỗi mã');
				}
			});
		});
	}

	$(document).on('click', '.edit-account', function () {
		_that = $(this);
		_accountId = _that.data('id');
		_action = `/googles/${_accountId}`;

		Swal.fire({
			title: 'Đang tải thông tin...',
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		$.post(_action, {}, function (account) {
			Swal.close();
			$('#editAccountModal').modal('show');
			$('#editAccountForm').attr('data-id', _accountId);
			$('#editUsername').val(account.username);
			$('#editEmail').val(account.email);
			$('#editSiteDomain').val(account.siteDomain);
			$('#editKeyCode').val(account.keyCode);
			$('#editPassword').val(account.password);
			$('#editNotes').val(account.notes);
		}, 'json').fail(function () {
			Swal.close();
			showAlert('error', 'Không thể tải thông tin tài khoản.');
		});
		return false;
	});


	$(document).on('submit', '#editAccountForm', function (e) {
		e.preventDefault();
		_that = $(this);
		_accountId = _that.attr('data-id');
		_method = _accountId ? 'PUT' : 'POST';
		_action = _accountId ? `/googles/${_accountId}` : '/googles';
		_postData = _that.serialize();
		_headers = { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') };
		_originalContent = _that.html();

		Swal.fire({
			title: 'Đang xử lý...',
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		$.post(_action, _postData + '&_method=' + _method, function () {
			Swal.close();
			$('#editAccountModal').modal('hide');
			showAlert('success', 'Thành công!');
			setTimeout(function () {
				fetchAccounts();
			}, 1000);
		}, 'json').fail(function () {
			Swal.close();
			showAlert('error', 'Thất bại!');
		});
		return false;
	});


	$(document).on('click', '.delete-account', function () {
		_that = $(this);
		_accountId = _that.data('id');

		Swal.fire({
			title: 'Bạn có chắc không?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Có, xóa!',
			cancelButtonText: 'Không'
		}).then((result) => {
			if (result.isConfirmed) {
				_originalContent = _that.html();
				_action = `/googles/${_accountId}`;
				_postData = { _method: 'DELETE' };
				_headers = { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') };

				Swal.fire({
					title: 'Đang xóa tài khoản...',
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});

				$.post(_action, _postData, function () {
					Swal.close();
					showAlert('success', 'Đã xóa tài khoản.');
					setTimeout(function () {
						fetchAccounts();
					}, 1000);
				}).fail(function () {
					Swal.close();
					showAlert('error', 'Không thể xóa tài khoản.');
				});
			}
		});
		return false;
	});

	$(document).on('submit', '#deleteAccountForm', function (e) {
		e.preventDefault(); // Chặn hành động mặc định của form

		const emailsInput = $('#emails').val();
		const emails = emailsInput ? emailsInput.split('\n').map(email => email.trim()).filter(email => email) : [];

		if (emails.length === 0) {
			Swal.fire('Không có tài khoản nào để xóa.');
			return;
		}

		// Hiển thị hộp thoại xác nhận
		Swal.fire({
			title: 'Bạn có chắc chắn?',
			text: 'Thao tác này sẽ xóa các tài khoản đã chọn!',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Có, xóa!',
			cancelButtonText: 'Không'
		}).then(result => {
			if (result.isConfirmed) {
				// Nếu người dùng xác nhận, gửi yêu cầu AJAX
				$.ajax({
					url: '/googles/delete-by-emails',
					type: 'POST',
					data: { emails: emails },
					headers: {
						'X-CSRF-TOKEN': csrfToken
					},
					success: function (response) {
						Swal.fire({
							title: 'Thông báo',
							text: response.message, // Hiển thị thông báo từ backend
							icon: 'success',
							timer: 3000,
							showConfirmButton: false
						});
						fetchAccounts(); // Tải lại danh sách tài khoản
						$('#deleteAccountModal').modal('hide');
					},
					error: function (xhr) {
						Swal.fire({
							title: 'Thông báo',
							text: xhr.responseJSON?.message || 'Đã xảy ra lỗi không xác định.',
							icon: 'error',
							timer: 3000,
							showConfirmButton: false
						});
					}
				});

			} else {
				// Nếu người dùng hủy bỏ, không làm gì cả
				Swal.fire('Hủy bỏ thao tác.', '', 'info');
			}
		});
	});

	$(document).on('click', '#deleteAllGoogleButton', function () {
		Swal.fire({
			title: 'Bạn có chắc chắn?',
			text: "Bạn sẽ không thể khôi phục lại các tài khoản đã xóa!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Đúng, xóa tất cả!',
			cancelButtonText: 'Hủy'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: '/googles/delete_all',
					method: 'POST',
					success: function (data) {
						if (data.message) {
							Swal.fire(
								'Đã xóa!',
								data.message,
								'success'
							).then(() => {
								fetchAccounts(); // Reload the accounts list
								$('#selectAll').prop('checked', false);
							});
						} else if (data.error) {
							Swal.fire(
								'Lỗi!',
								data.error,
								'error'
							);
						}
					},
					error: function (error) {
						Swal.fire(
							'Lỗi!',
							'Có lỗi xảy ra khi xóa dữ liệu.',
							'error'
						);
						console.error('Error:', error);
					}
				});
			}
		});
	});


});
