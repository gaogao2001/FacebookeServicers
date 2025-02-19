<!-- show_video.blade.php -->

<link rel="stylesheet" href="{{ asset('modules/facebook/css/video.css') }}">

<div class="container">


    <h2>Danh Sách Video</h2>
    <div id="videos-list" class="row">
        <!-- Videos will load here -->
    </div>
    <div id="loading" class="text-center my-3" style="display: none;">
        <p>Đang tải thêm video...</p>
    </div>
    @php
        // Xác định uid từ controller truyền vào
        $uid = isset($accounts) ? $accounts->uid : (isset($fanpage) ? $fanpage->page_id : null);
    @endphp


</div>

<script>
    $(document).ready(function() {

       

        const uid = "{{ $uid }}"; // Gán UID vào biến JavaScript
        let page = 1; // Trang đầu tiên
        let loading = false; // Trạng thái tải
        const limit = 6; // Số lượng video mỗi lần tải
        let hasErrorNotified = false; // Trạng thái lỗi đã được thông báo

        function loadVideos() {
            if (loading) return;
            loading = true;
            $('#loading').show();

            $.ajax({
                url: "{{ url('get-video') }}/" + uid,
                type: 'GET',
                dataType: 'json',
                data: {
                    page: page,
                    limit: limit
                },
                success: function(data) {
                    if (data.videos.length > 0) {
                        data.videos.forEach(function(video) {
                            $('#videos-list').append(
                                '<div class="col-md-4">' +
                                '<div class="video-container" style="position: relative;">' +
                                '<video>' +
                                '<source src="' + video + '" type="video/mp4">' +
                                'Trình duyệt của bạn không hỗ trợ video tag.' +
                                '</video>' +
                                '<div class="video-overlay" style="' +
                                'position: absolute;' +
                                'top: 0;' +
                                'left: 0;' +
                                'width: 100%;' +
                                'height: 100%;' +
                                'cursor: pointer;' +
                                'background: rgba(0, 0, 0, 0.3);' +
                                'display: flex;' +
                                'align-items: center;' +
                                'justify-content: center;' +
                                'color: white;' +
                                'font-size: 30px;' +
                                '">' +
                                '&#9658;' + // Biểu tượng Play
                                '</div>' +
                                '<button class="delete-video remove-existing-image" data-video="' + video + '" style="position: absolute; top: 5px; right: 5px;">x</button>' +
                                '</div>' +
                                '</div>'
                            );
                        });
                        page++; // Tăng số trang sau khi tải thành công
                        hasErrorNotified = false; // Reset trạng thái lỗi
                        if (!data.hasMore) {
                            $(window).off('scroll'); // Dừng sự kiện scroll nếu không còn video
                        }
                    } else if (page === 1) {
                        $('#videos-list').html('<p class="text-center text-muted">Hiện tại tài khoản này chưa có video.</p>');
                    }
                    $('#loading').hide();
                    loading = false;
                },
                error: function(xhr) {
                    $('#loading').hide();
                    if (!hasErrorNotified) { // Chỉ hiển thị lỗi nếu chưa được thông báo
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Lỗi khi tải video: ' + xhr.statusText
                        });
                        hasErrorNotified = true; // Đánh dấu lỗi đã được thông báo
                    }
                    loading = false;
                }
            });
        }

        // Tải video lần đầu tiên
        loadVideos();

        // Tải thêm video khi cuộn đến cuối trang
        $(window).on('scroll', function() {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                loadVideos();
            }
        });

        // Xử lý sự kiện xóa video
        $('#videos-list').on('click', '.delete-video', function() {
            const videoUrl = $(this).data('video'); // Lấy URL video cần xóa
            const parentElement = $(this).closest('.col-md-4'); // Lấy phần tử chứa video
            Swal.fire({
            title: 'Bạn có chắc chắn muốn xóa video này?',
            text: 'Hành động này không thể hoàn tác!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy',
            }).then((result) => {
            if (result.isConfirmed) {
                // Gửi yêu cầu xóa đến server
                $.ajax({
                url: "{{ route('deleteVideo', ['id' => 'UID_PLACEHOLDER']) }}".replace('UID_PLACEHOLDER', uid),
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}", // CSRF token
                    video: videoUrl
                },
                success: function() {
                    Swal.fire('Xóa thành công!', 'Video đã được xóa.', 'success');
                    parentElement.remove();

                    // Nếu modal đang mở, đóng modal
                    if ($('#video-modal').is(':visible')) {
                    $('#video-modal').fadeOut(function() {
                        $('#modal-video')[0].pause();
                        $('#modal-video')[0].currentTime = 0;
                    });
                    }

                    // Kiểm tra nếu danh sách video trống
                    if ($('#videos-list .col-md-4').length === 0) {
                    if (page > 1) {
                        page--; // Quay lại trang trước nếu có
                    }
                    loadVideos(); // Gọi lại hàm loadVideos để tải video mới
                    }
                },
                error: function() {
                    Swal.fire('Lỗi!', 'Không thể xóa video. Vui lòng thử lại sau.', 'error');
                }
                });
            }
            });
        });

        // Hiển thị modal khi click vào lớp phủ video
        $('#videos-list').on('click', '.video-overlay', function() {
            // Lấy URL video từ sibling video
            const videoUrl = $(this).siblings('video').find('source').attr('src');

            // Tạm dừng tất cả video trong danh sách
            $('#videos-list video').each(function() {
                this.pause(); // Tạm dừng video
                this.currentTime = 0; // Đặt lại thời gian
            });

            // Đặt URL vào modal video
            $('#modal-video source').attr('src', videoUrl);
            $('#modal-video')[0].load(); // Tải lại video trong modal
            $('#video-modal .modal-actions button').each(function() {
				$(this).attr('data-url', videoUrl);
				$(this).attr('data-uid', uid);
				$(this).attr('data-type', 'profile');
			});
            $('#video-modal').fadeIn(function() {
                $('#modal-video')[0].play();
            }); 
        });

        // Đóng modal
        $('.modal-close-btn').on('click', function() {
            $('#video-modal').fadeOut(function() {
                $('#modal-video')[0].pause();
                $('#modal-video')[0].currentTime = 0;
            });
        });

        $('#video-modal').on('click', function(e) {
            if ($(e.target).is('#video-modal')) {
                $(this).fadeOut(function() {
                    $('#modal-video')[0].pause();
                    $('#modal-video')[0].currentTime = 0;
                });
            }
        });
    });
</script>