
<style>
    .wrapper {
        background: #000;
        /* Đặt nền màu đen hoàn toàn */
        border: 2px solid rgba(225, 225, 225, .2);
        /* backdrop-filter: blur(10px); */
        /* Loại bỏ hiệu ứng mờ */
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 1px 1px;
    }

    .stories-wraper.active {
        display: flex;
    }

    .stories-wraper {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #000;
        /* Đặt nền màu đen hoàn toàn */
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    /* Các CSS còn lại không thay đổi */
    .status-story1 {
        position: relative;
        width: 90%;
        max-width: 600px;
        text-align: center;
    }

    .status-story1 {
        display: inline-block;
        height: 100vh;
        vertical-align: middle;
        width: 100%;
        padding: 26px 159px 0px 0px;
    }

    .close-story {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 24px;
        color: white;
        cursor: pointer;
        z-index: 1001;
    }

    .story-content {
        position: relative;
    }

    .story-controls {
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: space-between;
        transform: translateY(-50%);
        z-index: 1000;
    }

    .story-controls button {
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        color: #000;
    }

    #story-video {
        width: 100%;
        height: auto;
        padding-top: 80px;
    }

    /* Story Controls */
    .story-controls {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        /* Chiếm toàn bộ chiều cao video */
        display: flex;
        justify-content: space-between;
    }

    .clickable-area {
        flex: 1;
        /* Chia đều hai phần bằng nhau */
        cursor: pointer;
        height: 150%;
        /* Chiều cao toàn bộ video */
        background: rgba(0, 0, 0, 0.2);
        /* Màu nền nhạt để người dùng nhận biết (có thể bỏ nếu muốn trong suốt) */
        z-index: 10;
    }

    .clickable-area.prev-story {
        left: 0;
        /* Vùng bên trái */
    }

    .clickable-area.next-story {
        right: 0;
        /* Vùng bên phải */
    }

    .story-header {
        display: flex;
        align-items: center;
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 1001;
        padding-top: 80px
    }

    /* CSS cho trạng thái mặc định của nút */
    .send-invitation {
        display: inline-block;
        padding: 5px 15px;
        color: white;
        background-color: #007bff; /* Màu xanh dương */
        border: none;
        border-radius: 4px;
        cursor: pointer;
        position: relative; /* Để sử dụng ::after */
        text-align: center;
    }

    /* CSS cho trạng thái loading */
    .send-invitation.loading {
        pointer-events: none; /* Ngăn click khi đang loading */
        color: transparent; /* Ẩn text */
        background-color: #0056b3; /* Xanh dương đậm khi loading */
    }

    .send-invitation.loading::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 16px;
        height: 16px;
        border: 2px solid white;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    /* CSS cho hiệu ứng spin */
    @keyframes spin {
        from {
            transform: translate(-50%, -50%) rotate(0deg);
        }
        to {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }

    /* CSS cho trạng thái completed */
    .send-invitation.completed {
        background-color: #28a745; /* Màu xanh lá cây */
        color: white;
        pointer-events: none; /* Ngăn click */
    }




</style>

<div class="central-meta">
    <span class="create-post">Recent Stories <a href="#" title="">See All</a></span>
    <div class="story-postbox">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="story-box">
                    <figure>
                        <img src="/user/images/resources/story-1.jpg" alt="">
                        <span>Add Your Story</span>
                    </figure>
                    <div class="story-thumb" data-toggle="tooltip" title="Add Your Story">
                        <i class="fa fa-plus"></i>
                    </div>
                </div>
            </div>
            @if(isset($data['Stories']->stories) && is_array($data['Stories']->stories))
            @php
            $allStories = $data['Stories']->stories; // Lưu toàn bộ danh sách story vào biến
            @endphp
            @foreach(array_slice($data['Stories']->stories, 0, 3) as $story)
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="story-box">
                    <figure>
                        <img src="{{ optional($story->thumbnail_story_to_show)->image ?? '/user/images/resources/story-1.jpg' }}" alt="{{ $story->name }}" style="width: 120px; height: 237px;">
                        <span>{{ $story->name }}</span>
                    </figure>
                    <div class="story-thumb" data-toggle="tooltip" title="{{ $story->name }}">
                        <img src="{{ $story->picture }}" alt="{{ $story->name }}" style="width: 50%;">
                    </div>
                </div>
            </div>
            @endforeach
            @else
            <p>Không có Stories nào để hiển thị.</p>
            @endif
        </div>

        <!-- Modal to Show Story -->
        <div class="stories-wraper">
            <div class="status-story1">
                <span class="close-story" style="padding: 82px 187px;">&times;</span>
                <div class="story-content">

                    <div class="story-header">
                        <img id="story-thumbnail" src="" alt="" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
                        <span id="story-name" style="font-size: 16px; color: white;"></span>
                    </div>

                    <video id="story-video" controls autoplay></video>

                    <div class="story-controls">
                        <div class="clickable-area prev-story"></div>
                        <div class="clickable-area next-story"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>



<div class="central-meta">
    <span class="create-post">Có thể bạn quen ({{ count($data['Suggestions']) }}) <a href="timeline-friends2.html"
            title="">Tất cả</a></span>

            <ul class="frndz-list">
    @foreach($data['Suggestions'] as $suggestion)
    <li>
        <img src="{{ $suggestion->picture }}" alt="">
        <div class="sugtd-frnd-meta">
            <a href="#" title="{{ $suggestion->name }}" class="suggestion-name">{{ Str::limit($suggestion->name, 10, '...') }}</a>
            <span>{{ $suggestion->mutual_friends }} Bạn chung</span>
            <ul class="add-remove-frnd" style="display: flex; align-items: center;">
                <li class="add-tofrndlist" style="margin-right: 10px;">
                    <a class="add-friend" href="#" title="Add Friend" data-uid="{{ $suggestion->uid }}">
                        <i class="fa fa-user-plus"></i>
                    </a>
                </li>
                <li class="remove-frnd">
                    <a href="#"  class="remove-friend" title="Remove Friend" data-uid="{{ $suggestion->uid }}">
                        <i class="fa fa-user-times"></i>
                    </a>
                </li>
            </ul>
        </div>
    </li>
    @endforeach
</ul>

</div><!-- friends list -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Xử lý sự kiện click của nút invite
    $(document).on('click', '.add-friend', function(e) {
        e.preventDefault();
        const uid_invitation = $(this).data('uid');
        const $this = $(this);

        const $icon = $this.find('i');
        $icon.removeClass('fa-user-plus').addClass('fa-spinner fa-spin');
        $.ajax({
            url: '{{ route("profile.addFriend", ":uid") }}'.replace(':uid', {{$data['account']->uid}}),
            type: 'POST',
            data: {
                uid_invitation: uid_invitation
            },
            success: function(response) {
                // Kiểm tra response.response thay vì response
                if (response.response === true) {
                    // Thay đổi HTML thành biểu tượng dấu tích "V"
                    $this.closest('.add-tofrndlist').html(`
                        <a href="#" title="Friend Request Sent" class="friend-sent">
                            <i class="fa fa-check" style="color: green;"></i>
                        </a>
                    `);
                    // Hiển thị toast thông báo thành công
                    toastr.success('Gửi lời mời kết bạn thành công', 'Thành công');
                } else {
                    // Hiển thị toast thông báo lỗi
                    toastr.error(response.response || 'Gửi lời mời kết bạn thất bại', 'Lỗi');
                    // Khôi phục icon ban đầu
                    $icon.removeClass('fa-spinner fa-spin').addClass('fa-user-plus');
                }
            },
            error: function(xhr) {
                console.error('Lỗi khi gửi lời mời:', xhr.responseText);
                // Chuyển lại icon add khi có lỗi
                $icon.removeClass('fa-spinner fa-spin').addClass('fa-user-plus');
                // Hiển thị toast thông báo lỗi
                toastr.error('Có lỗi xảy ra. Vui lòng thử lại.', 'Lỗi');
            }
        });
    });

    $(document).on('click', '.remove-friend', function(e) {
        e.preventDefault();
        const uid_invitation = $(this).data('uid');
        const $this = $(this);

        // Thay đổi trạng thái nút thành loading
        $this.addClass('loading');
        $this.prop('disabled', true);

        $.ajax({
            url: '{{ route("profile.removeFriend", ":uid") }}'.replace(':uid', '{{ $data["account"]->uid }}'),
            type: 'POST',
            data: {
                uid_invitation: uid_invitation,
                _token: $('meta[name="csrf-token"]').attr('content') // Đảm bảo gửi CSRF token
            },
            success: function(response) {
                if (response.response === true) {
                    // Hiển thị toast thông báo thành công
                    toastr.success('Đã xóa bạn thành công');
                    // Reload lại danh sách gợi ý kết bạn
                    location.reload();
                } else {
                    // Hiển thị toast thông báo lỗi
                    toastr.error(response.response || 'Xóa bạn thất bại');
                    // Khôi phục trạng thái nút
                    $this.removeClass('loading');
                    $this.prop('disabled', false);
                }
            },
            error: function(xhr) {
                // Hiển thị toast thông báo lỗi
                toastr.error('Đã xảy ra lỗi. Vui lòng thử lại.');
                // Khôi phục trạng thái nút
                $this.removeClass('loading');
                $this.prop('disabled', false);
            }
        });
    });
</script>

<script>
    // List of stories
    const stories = @json($allStories ?? []); // Lấy toàn bộ story từ backend
    let currentStoryIndex = 0;

    // Show story modal and play video
    function showStory(index) {
        const story = stories[index];
        if (!story || !story.thumbnail_story_to_show) return;

        const videoSrc = story.thumbnail_story_to_show.video || null;
        const imageSrc = story.thumbnail_story_to_show.image || '/user/images/resources/story-1.jpg'; // Ảnh đại diện
        const storyName = story.name || 'Unknown'; // Tên story

        const modal = $('.stories-wraper');
        const videoElement = $('#story-video');
        const thumbnailElement = $('#story-thumbnail');
        const nameElement = $('#story-name');

        // Gắn dữ liệu vào modal
        if (videoSrc) {
            videoElement.attr('src', videoSrc);
            thumbnailElement.attr('src', imageSrc);
            nameElement.text(storyName);

            modal.addClass('active');
            currentStoryIndex = index;
        }
    }

    // Handle next story
    function nextStory() {
        const nextIndex = currentStoryIndex + 1;
        if (nextIndex < stories.length) {
            currentStoryIndex = nextIndex;
            showStory(currentStoryIndex);
        } else {
            alert('No more stories available.');
            $('.stories-wraper').removeClass('active');
        }
    }

    // Handle previous story
    function prevStory() {
        const prevIndex = currentStoryIndex - 1;
        if (prevIndex >= 0) {
            currentStoryIndex = prevIndex;
            showStory(currentStoryIndex);
        } else {
            alert('This is the first story.');
        }
    }

    // Event Listeners
    function closeStoryModal() {
        $('.stories-wraper').removeClass('active');
        $('#story-video').attr('src', ''); // Ngắt kết nối video
    }

    // Event Listeners
    $(document).ready(function() {
        // Mở modal và hiển thị story
        $('.story-box').on('click', function() {
            const index = $(this).closest('.col-lg-3').index(); // Lấy đúng chỉ số của story trên giao diện
            if (index >= 0 && index < stories.length) {
                showStory(index); // Truyền chỉ số của story để hiển thị
            }
        });

        // Handle Click on Left Area (Previous Story)
        $('.prev-story').on('click', prevStory);

        // Handle Click on Right Area (Next Story)
        $('.next-story').on('click', nextStory);

        // Auto-play next story when video ends
        $('#story-video').on('ended', nextStory);

        // Close modal when close button is clicked
        $('.close-story').on('click', closeStoryModal);

        $('.send-invitation').on('click', function(e) {
            e.preventDefault();
            const uid = $(this).data('uid');
            const csrfToken = $('#csrf-token').val(); // Lấy CSRF token từ input

            $.ajax({
                url: '{{ route("profile.addFriend", ":uid") }}'.replace(':uid', uid),
                type: 'POST',
                data: {
                    _token: csrfToken, // Gửi CSRF token cùng dữ liệu
                    uid: uid
                },
                success: function(response) {
                    // Handle success (e.g., display a success message)
                },
                error: function(xhr) {
                    // Handle error (e.g., display an error message)
                }
            });
        });
    });
</script>