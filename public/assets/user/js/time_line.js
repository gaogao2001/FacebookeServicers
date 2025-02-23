
$(document).ready(function() {

    
    let currentPage = window.currentPage;
    let hasMore = window.hasMore;
    let loading = false;

    // ---- Lazy Loading qua sự kiện scroll ----
    $(window).on('scroll', function() {
        if (!hasMore) return; // Hết data => dừng
        if (loading) return; // Đang load => dừng

        let scrollTop = $(window).scrollTop();
        let windowHeight = $(window).height();
        let docHeight = $(document).height();

        // Nếu cuộn gần đáy 300px => load
        if (scrollTop + windowHeight >= docHeight - 300) {
            loadMorePosts();
        }
    });

    // Hàm load thêm bài viết (Trang kế)
    function loadMorePosts() {
        loading = true;
        // Hiện spinner, ẩn text
        $('.btn-load-more-text').hide();
        $('.btn-load-more-loading').show();

        $.ajax({
            url: '{{ route("profile.getTimeline", ["uid" => $uid]) }}',
            type: 'GET',
            data: {
                page: currentPage + 1
            },
            dataType: 'json',
            success: function(response) {
                if (response.posts && response.posts.length > 0) {
                    // Xây dựng chuỗi HTML
                    let htmlContent = '';
                    // Nếu muốn bọc chung, có thể bọc <div class="central-meta item">
                    htmlContent += '<div class="central-meta item">';

                    // Duyệt từng post
                    response.posts.forEach(function(post) {
                        // Biến cục bộ
                        let avatar = post.avatar || '';
                        let fullname = post.fullname || 'Unknown User';
                        let attachments = post.attachments || {};
                        let media = attachments.media || '';
                        let type = attachments.type || 'link';
                        let creationTime = formatDate(post.creation_time || 0);
                        let message = post.message || '';
                        let reactorsCount = post.reactors_count || 0;
                        let commentsCount = post.comments_count || 0;
                        let shareCount = post.share_count || 0;

                        htmlContent += `
                            <div class="user-post">
                                <div class="friend-info">
                                    <figure>
                                        ${
                                            avatar
                                            ? `<img src="${avatar}" alt="">`
                                            : ''
                                        }
                                    </figure>
                                    <div class="friend-name">
                                        <div class="more">
                                            <div class="more-post-optns">
                                                <i class="ti-more-alt"></i>
                                                <ul>
                                                    <li><i class="fa fa-pencil-square-o"></i>Edit Post</li>
                                                    <li><i class="fa fa-trash-o"></i>Delete Post</li>
                                                    <li class="bad-report"><i class="fa fa-flag"></i>Report Post</li>
                                                    <li><i class="fa fa-address-card-o"></i>Boost This Post</li>
                                                    <li><i class="fa fa-clock-o"></i>Schedule Post</li>
                                                    <li><i class="fa fa-wpexplorer"></i>Select as featured</li>
                                                    <li><i class="fa fa-bell-slash-o"></i>Turn off Notifications</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <ins>
                                            <!-- Lấy fullname từ server hay từ $data['account']->fullname tùy ý -->
                                            <!-- Ví dụ: hiển thị Blade cứng: -->
                                            <a href="time-line.html" title="">{{ $data['account']->fullname }}</a>
                                            <!-- Hoặc hiển thị client post: -->
                                            <!-- <a href="time-line.html" title="">${fullname}</a> -->
                                            share <a href="#" title="">${type}</a>
                                        </ins>
                                        <span>
                                            <i class="fa fa-globe"></i>
                                            published: ${creationTime}
                                        </span>
                                    </div><!-- friend-name -->

                                    <div class="post-meta">
                                        ${
                                            media
                                            ? `
                                            <figure>
                                                <a href="${media}" title=""
                                                    data-strip-group="mygroup"
                                                    class="strip vdeo-link"
                                                    data-strip-options="width: 700,height: 450,youtube: { autoplay: 1 }">
                                                    <img src="${media}" alt="post media">
                                                    ${
                                                        (type === 'Video')
                                                        ? `<i>
                                                            <svg version="1.1" class="play"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    x="0px" y="0px"
                                                                    height="55px" width="55px"
                                                                    viewBox="0 0 100 100"
                                                                    enable-background="new 0 0 100 100"
                                                                    xml:space="preserve">
                                                                <path class="stroke-solid" fill="none" stroke=""
                                                                    d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5
                                                                    c26.4-0.6,47.4-21.8,47.2-47.7C97.3,23.7,75.7,2.3,49.9,2.5">
                                                                </path>
                                                                <path class="icon" fill=""
                                                                    d="M38,69c-1,0.5-1.8,0-1.8-1.1V32.1
                                                                    c0-1.1,0.8-1.6,1.8-1.1l34,18c1,0.5,1,1.4,0,1.9L38,69z">
                                                                </path>
                                                            </svg>
                                                            </i>`
                                                        : ''
                                                    }
                                                </a>
                                                <ul class="like-dislike">
                                                    <li>
                                                        <a class="bg-purple" href="#" title="Save to Pin Post">
                                                            <i class="fa fa-thumb-tack"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="bg-blue" href="#" title="Like Post">
                                                            <i class="ti-thumb-up"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="bg-red" href="#" title="dislike Post">
                                                            <i class="ti-thumb-down"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </figure>
                                            `
                                            : ''
                                        }

                                        <div class="description">
                                            <p>${message}</p>
                                        </div>

                                        <div class="we-video-info">
                                            <ul>
                                                <li>
                                                    <span class="views" title="views">
                                                        <i class="fa fa-eye"></i>
                                                        <ins>1.2k</ins>
                                                    </span>
                                                </li>
                                                <li>
                                                    <div class="likes heart" title="Like/Dislike">
                                                        ❤
                                                        <span>${reactorsCount}</span>
                                                    </div>
                                                </li>
                                                <li>
                                                    <span class="comment BtnLoadComment" title="Comments" data-id="{{ $post['post_id'] ?? null }}" data-uid="{{ $data['account']->uid ?? null }}">
                                                        <i class="fa fa-commenting"></i>
                                                        <ins>{{ $post['comments_count'] ?? 0 }}</ins>
                                                    </span>
                                                </li>
                                                <li>
                                                    <span>
                                                        <a class="share-pst" href="#" title="Share">
                                                            <i class="fa fa-share-alt"></i>
                                                        </a>
                                                        <ins>${shareCount}</ins>
                                                    </span>
                                                </li>
                                            </ul>

                                            <div class="users-thumb-list">
                                                <a data-toggle="tooltip" title="Anderw" href="#">
                                                    <img alt="" src="/user/images/resources/userlist-1.jpg">
                                                </a>
                                                <a data-toggle="tooltip" title="frank" href="#">
                                                    <img alt="" src="/user/images/resources/userlist-2.jpg">
                                                </a>
                                                <a data-toggle="tooltip" title="Sara" href="#">
                                                    <img alt="" src="/user/images/resources/userlist-3.jpg">
                                                </a>
                                                <a data-toggle="tooltip" title="Amy" href="#">
                                                    <img alt="" src="/user/images/resources/userlist-4.jpg">
                                                </a>
                                                <a data-toggle="tooltip" title="Ema" href="#">
                                                    <img alt="" src="/user/images/resources/userlist-5.jpg">
                                                </a>
                                                <span>
                                                    <strong>You</strong>,
                                                    <b>Sarah</b> and
                                                    <a href="#" title="">24+ more</a> liked
                                                </span>
                                            </div>
                                        </div>
                                    </div><!-- post-meta -->
                                </div><!-- friend-info -->

                                <!-- Khu vực bình luận (demo cứng) -->
                                <div class="coment-area" style="">
                                    <ul class="we-comet">
                                        
                                    </ul>
                                </div><!-- coment-area -->
                            </div><!-- user-post -->
                        `;
                    });

                    htmlContent += '</div><!-- .central-meta item -->';

                    // Thêm vào .loadMore
                    $('.loadMore').append(htmlContent);
                }

                // Cập nhật hasMore
                hasMore = response.hasMore;
                if (hasMore) {
                    currentPage++;
                } else {
                    // Hết => ẩn nút
                    $('.btn-load-more-timeline').hide();
                }
            },
            error: function(err) {
                console.log(err);
            },
            complete: function() {
                // Kết thúc loading
                loading = false;
                // Hiển thị lại text, ẩn spinner
                $('.btn-load-more-text').show();
                $('.btn-load-more-loading').hide();
            }
        });
    }

    // Hàm format date (unix second) => chuỗi hiển thị
    function formatDate(timestamp) {
        let dateObj = new Date(timestamp * 1000);
        let options = {
            year: 'numeric',
            month: 'long',
            day: '2-digit',
            hour: 'numeric',
            minute: '2-digit'
        };
        return dateObj.toLocaleString('en-US', options);
    }
});

