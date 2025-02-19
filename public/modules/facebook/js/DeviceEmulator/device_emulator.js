$(document).ready(function () {
	// Lấy CSRF token từ meta
	const csrfToken = $('meta[name="csrf-token"]').attr('content');

	function getLiveSessions() {
        return JSON.parse(localStorage.getItem('liveSessions') || "[]");
    }

    function saveLiveSessions(sessions) {
        localStorage.setItem('liveSessions', JSON.stringify(sessions));
    }

    // Hàm kiểm tra trạng thái live dựa trên uid và url (kiểm tra nếu có phiên phù hợp tồn tại)
    function isLiveButtonActive(currentUid, currentUrl) {
        const sessions = getLiveSessions();
        let matchedSessions = sessions.filter(session => session.uid == currentUid);
        if (matchedSessions.length === 0) {
            return false;
        }
        matchedSessions = matchedSessions.filter(session => session.url === currentUrl);
        return matchedSessions.length > 0 ? matchedSessions[0] : false;
    }

    function clearModal() {
        $('#modal-video source').attr('src', '');
        $('#modal-video')[0].load();
        $('#post-content').val('');
        $('#post-content-container').slideUp();
        $('#cut-video-container').slideUp();
    }

    // Hàm cập nhật trạng thái nút live-video dựa trên localStorage
    function updateLiveButton() {
        $('button#live-video').each(function () {
            var $btn = $(this);
            var btnUid = $btn.attr('data-uid');
            var btnUrl = $btn.attr('data-url');
            let liveSession = isLiveButtonActive(btnUid, btnUrl);
            if (liveSession) {
                $btn.attr('data-live', 'true');
                $btn.attr('data-video-id', liveSession.video_id);
                $btn.text('Stop live').removeClass('btn-warning').addClass('btn-danger');
            } else {
                $btn.attr('data-live', 'false');
                $btn.removeAttr('data-video-id');
                $btn.text('Phát live').removeClass('btn-danger').addClass('btn-warning');
            }
        });
    }

	function openCustomModal() {
        $('#video-modal').css('display', 'block');
        updateLiveButton();
    }
    // Gọi updateLiveButton mỗi khi mở modal (bạn cần đảm bảo sự kiện mở modal được trigger)
    // Ví dụ:
	// $(document).on('click', '#video-modal', function () {
	// 	updateLiveButton();
	// });

    // $('#video-modal').on('show', function () {
    //     updateLiveButton();
    // });

	$(document).on('click', '#video-tab', function () {
		openCustomModal();
	});

    function handleTwoClick(buttonSelector, ajaxUrl) {
        $(document).on('click', buttonSelector, function () {
            var _that = $(this);
            var currentUid = _that.attr('data-uid');
            var currentUrl = _that.attr('data-url');

            // Khi modal mở, ngay lập tức cập nhật nút dựa trên localStorage
            if (_that.attr('id') === 'live-video' && $('#video-modal').is(':visible')) {
                var liveSession = isLiveButtonActive(currentUid, currentUrl);
                if (liveSession) {
                    _that.attr('data-live', 'true');
                    _that.attr('data-video-id', liveSession.video_id);
                    _that.text('Stop live').removeClass('btn-warning').addClass('btn-danger');
                } else {
                    _that.attr('data-live', 'false');
                    _that.removeAttr('data-video-id');
                    _that.text('Phát live').removeClass('btn-danger').addClass('btn-warning');
                }
            }

            // Nếu nút đang ở trạng thái live => thực hiện dừng live
            if (_that.attr('id') === 'live-video' && _that.attr('data-live') === 'true') {
                var videoId = _that.attr('data-video-id');
                showLoading();
                $.ajax({
                    url: '/stop-live',
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        video_id: videoId,
                        uid: currentUid
                    },
                    success: function (result) {
                        hideLoading();
                        if (result.status === true) {
                            let sessions = getLiveSessions();
                            sessions = sessions.filter(session => !(session.uid == currentUid && session.url == currentUrl && session.video_id == videoId));
                            saveLiveSessions(sessions);
                            _that.attr('data-live', 'false');
                            _that.removeAttr('data-video-id');
                            _that.text('Phát live').removeClass('btn-danger').addClass('btn-warning');
                            clearModal();
                            Swal.fire('Thành công', 'Live video đã dừng', 'success')
                                .then(() => { window.location.reload(); });
                        } else {
                            Swal.fire('Lỗi', result.message || 'Không thể dừng live video', 'error');
                        }
                    },
                    error: function () {
                        hideLoading();
                        Swal.fire('Lỗi', 'Không thể dừng live video', 'error');
                    }
                });
                return false;
            }

            // Quy trình 2 lần click: lần click đầu mở textarea, click thứ 2 gửi dữ liệu
            var firstClick = _that.data('first-click');
            if (firstClick === 0 || firstClick === "0") {
                $('#post-content-container').slideDown();
                _that.data('first-click', 1);
                return false;
            }

            var content = $('#post-content').val();
            $('#post-content-container').slideUp();
            $('#post-content').val('');
            _that.data('first-click', 0);

            var _action = ajaxUrl;
            var _data = {
                _token: csrfToken,
                url: _that.attr('data-url'),
                type: _that.attr('data-type'),
                uid: currentUid,
                content: content
            };

            _data.url = _data.url.replace('http://192.168.1.6/FileData/Video/', '/var/www/FacebookService/public/FileData/Video/');

            if (_that.attr('id') === 'live-video') {
                showLoading();
            } else {
                var loadingIcon = $('<i class="fas fa-spinner fa-spin loading-icon"></i>');
                _that.append(loadingIcon);
            }

            $.post(_action, _data, function (result) {
                if (_that.attr('id') === 'live-video') {
                    hideLoading();
                } else {
                    _that.find('.loading-icon').remove();
                }
                if (result.status === true || result.status === 'success') {
                    if (_that.attr('id') === 'live-video' && result.data && result.data.video_id) {
                        _that.attr('data-live', 'true');
                        _that.attr('data-video-id', result.data.video_id);
                        _that.text('Stop live').removeClass('btn-warning').addClass('btn-danger');
                        let sessions = getLiveSessions();
                        sessions.push({
                            uid: currentUid,
                            url: currentUrl,
                            video_id: result.data.video_id
                        });
                        saveLiveSessions(sessions);
                    }
                    Swal.fire('Thành công', result.message || 'Đăng thành công!', 'success')
                        .then(() => { window.location.reload(); });
                } else {
                    Swal.fire('Lỗi!', result.message || 'Thao tác thất bại. Vui lòng thử lại.', 'error');
                }
            }, 'json').fail(function () {
                if (_that.attr('id') === 'live-video') {
                    hideLoading();
                } else {
                    _that.find('.loading-icon').remove();
                }
                Swal.fire('Lỗi!', 'Thao tác thất bại. Vui lòng thử lại.', 'error');
            });

            return false;
        });
    }
    
    // Gắn sự kiện 2-click cho các nút
    handleTwoClick('#upload-video', '/post-video');
    handleTwoClick('#upload-reel', '/post-reels');
    handleTwoClick('#live-video', '/live-video');

    // Khi trang reload, cập nhật trạng thái live dựa theo localStorage
    updateLiveButton();



	$(document).on('click', '#delete-video', function () {
		var videoUrl = $('#modal-video').find('source').attr('src'); // Lấy URL video hiện tại
		var uid = $('#addUid').val(); // Lấy UID từ input ẩn hoặc dữ liệu hiện tại

		if (!videoUrl) {
			Swal.fire('Lỗi!', 'Không tìm thấy video để xóa.', 'error');
			return false;
		}

		Swal.fire({
			title: 'Bạn có chắc chắn muốn xóa video này?',
			text: 'Hành động này không thể hoàn tác!',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Xóa',
			cancelButtonText: 'Hủy'
		}).then(function (result) {
			if (result.isConfirmed) {
				var _that = $(this);
				var _data = {
					_token: csrfToken,
					video: videoUrl // Gửi URL video để xác định tệp
				};
				var _action = `/delete-video/${uid}`;
				var loadingIcon = $('<i class="fas fa-spinner fa-spin loading-icon"></i>');
				_that.append(loadingIcon);

				$.post(_action, _data, function (response) {
					_that.find('.loading-icon').remove();
					if (response.status === true) {
						Swal.fire('Thành công!', 'Video đã được xóa.', 'success');
						$('#video-modal').fadeOut(); // Ẩn modal sau khi xóa thành công
						$('#modal-video')[0].pause(); // Dừng video
						// Cập nhật danh sách video (tùy chọn)
						// loadVideos();
					} else {
						Swal.fire('Lỗi!', response.error || 'Không thể xóa video. Vui lòng thử lại.', 'error');
					}
				}, 'json').fail(function () {
					_that.find('.loading-icon').remove();
					Swal.fire('Lỗi!', 'Không thể xóa video. Vui lòng thử lại.', 'error');
				});
			}
		});

		return false;
	});

	$(document).on('click', '#export-video', function () {
		var _that = $(this);
		var _data = {
			_token: csrfToken,
			url: _that.data('url'), // Lấy giá trị từ data-url
			type: _that.data('type'),  // Lấy giá trị từ data-type
			uid: _that.data('uid')  // Lấy giá trị từ data-uid
		};
		var _action = '/export-video';
		var loadingIcon = $('<i class="fas fa-spinner fa-spin loading-icon"></i>');
		_that.append(loadingIcon);
		$.post(_action, _data, function (result) {
			_that.find('.loading-icon').remove();
			if (result.status === 'success') {
				Swal.fire('Thành công', 'Video đã được trích xuất Image thành công.', 'success');
			} else {
				Swal.fire('Lỗi!', 'Không thể trích xuất Image. Vui lòng thử lại.', 'error');
				_that.find('.loading-icon').remove();
			}
		}, 'json').fail(function () {
			Swal.fire('Lỗi!', 'Không thể trích xuất Image. Vui lòng thử lại.', 'error');
			_that.find('.loading-icon').remove();
		});
		return false;
	});


	$(document).on('click', '#btnReloadSession', function (e) {
		e.preventDefault();
		const _that = $(this);
		const uid = _that.data('uid');
		const sessionVal = _that.data('session') || '';

		Swal.fire({
			title: 'Phiên làm việc hiện tại: ' + sessionVal,
			text: 'Bạn có muốn làm mới phiên này không?',
			icon: 'info',
			showCancelButton: true,
			confirmButtonText: 'Có',
			cancelButtonText: 'Không'
		}).then((result) => {
			if (result.isConfirmed) {
				showLoading();
				$.post('/DeviceEmulator/RenewSession', {
					uid: uid,
					_token: $('meta[name="csrf-token"]').attr('content')
				})
					.done(function (response) {
						Swal.fire({
							icon: 'success',
							title: response.title || 'Thành công',
							text: response.msg || 'Phiên đã được làm mới thành công!'
						});
					})
					.fail(function (error) {
						Swal.fire({
							icon: 'error',
							title: 'Lỗi',
							text: error.responseJSON?.message || 'Không thể Renew session'
						});
					})
					.always(function () {
						hideLoading();
						_that.prop('disabled', false);
					});
				_that.prop('disabled', true);
			}
		});
	});


	//cắt video

	$(document).on('click', '#cut-video', function () {
		$('#cut-video-container').slideToggle();
	});

	$(document).on('click', '#confirm-cut', function () {
		var start = $('#cut-start').val();
		var end = $('#cut-end').val();
		var videoSrc = $('#modal-video source').attr('src');
		var uid = $('#addUid').val();

		if (!start || !end) {
			Swal.fire('Lỗi!', 'Vui lòng nhập đầy đủ thời gian bắt đầu và kết thúc.', 'error');
			return;
		}

		$.ajax({
			url: '/cut-video',
			method: 'POST',
			data: {
				_token: csrfToken,
				uid: uid,
				video_path: videoSrc,
				start: start,
				end: end
			},
			beforeSend: function () {
				$('#confirm-cut').prop('disabled', true).text('Đang cắt...');
			},
			success: function (response) {
				if (response.status === 'success') {
					Swal.fire('Thành công', 'Video đã được cắt thành công.', 'success');
					$('#modal-video source').attr('src', response.new_video_path);
					$('#modal-video')[0].load();
				} else {
					Swal.fire('Lỗi!', response.message, 'error');
				}
			},
			complete: function () {
				$('#confirm-cut').prop('disabled', false).text('Xác nhận Cắt');
				$('#cut-video-container').slideUp();
			},
			error: function () {
				Swal.fire('Lỗi!', 'Không thể cắt video. Vui lòng thử lại.', 'error');
			}
		});
	});


	function showLoading() {
		console.log("Hiển thị loading...");
		$('body').append(`
			<div id="loadingOverlay" style="
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: rgba(0, 0, 0, 0.5);
				display: flex;
				justify-content: center;
				align-items: center;
				z-index: 9999;
			">
				<div class="loader"></div>
			</div>
		`);
	}

	// Ẩn loader
	function hideLoading() {
		console.log("Ẩn loading...");
		$('#loadingOverlay').remove();
	}


});