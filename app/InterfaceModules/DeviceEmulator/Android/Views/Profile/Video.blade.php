@extends('user.layouts.master')

@section('title', 'Videos')
@section('head.scripts')

<style>
    /* Sử dụng style từ Search_result.blade.php */
    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 100000;
    }

    .loader {
        width: fit-content;
        font-size: 40px;
        font-family: system-ui, sans-serif;
        font-weight: bold;
        text-transform: uppercase;
        color: #0000;
        -webkit-text-stroke: 1px #fff;
        background: linear-gradient(-60deg, #0000 45%, rgb(247, 120, 46) 0 55%, #0000 0) 0 / 300% 100% no-repeat text;
        animation: l3 2s linear infinite;
    }

    .loader:before {
        content: "Loading";
    }

    @keyframes l3 {
        0% {
            background-position: 100%;
        }

        100% {
            background-position: 0;
        }
    }
</style>

@endsection

@section('content')

<section>
    <div class="gap2 gray-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row merged20" id="page-contents">
                        @include('Android::Profile.Profile_baner')

                        <div class="col-lg-3">
                            <aside class="sidebar static">
                                <div class="widget">
                                    <h4 class="widget-title">Explor Events <a class="see-all" href="#" title="">See All</a></h4>
                                    <div class="rec-events bg-purple">
                                        <i class="ti-gift"></i>
                                        <h6><a title="" href="#">Ocean Motel good night event in columbia</a></h6>
                                        <img alt="" src="/user/images/clock.png">
                                    </div>
                                    <div class="rec-events bg-blue">
                                        <i class="ti-microphone"></i>
                                        <h6><a title="" href="#">2016 The 3rd International Conference</a></h6>
                                        <img alt="" src="/user/images/clock.png">
                                    </div>
                                </div>
                                <div class="widget">
                                    <h4 class="widget-title">Shortcuts</h4>
                                    <ul class="naves">
                                        <li>
                                            <i class="ti-clipboard"></i>
                                            <a href="newsfeed.html" title="">News feed</a>
                                        </li>
                                        <li>
                                            <i class="ti-mouse-alt"></i>
                                            <a href="inbox.html" title="">Inbox</a>
                                        </li>
                                        <li>
                                            <i class="ti-files"></i>
                                            <a href="fav-page.html" title="">My pages</a>
                                        </li>
                                        <li>
                                            <i class="ti-user"></i>
                                            <a href="timeline-friends.html" title="">friends</a>
                                        </li>
                                        <li>
                                            <i class="ti-image"></i>
                                            <a href="timeline-photos.html" title="">/user/images</a>
                                        </li>
                                        <li>
                                            <i class="ti-video-camera"></i>
                                            <a href="timeline-videos.html" title="">videos</a>
                                        </li>
                                        <li>
                                            <i class="ti-comments-smiley"></i>
                                            <a href="messages.html" title="">Messages</a>
                                        </li>
                                        <li>
                                            <i class="ti-bell"></i>
                                            <a href="notifications.html" title="">Notifications</a>
                                        </li>
                                        <li>
                                            <i class="ti-share"></i>
                                            <a href="people-nearby.html" title="">People Nearby</a>
                                        </li>
                                        <li>
                                            <i class="fa fa-bar-chart-o"></i>
                                            <a href="insights.html" title="">insights</a>
                                        </li>
                                        <li>
                                            <i class="ti-power-off"></i>
                                            <a href="landing.html" title="">Logout</a>
                                        </li>
                                    </ul>
                                </div><!-- Shortcuts -->

                                <div class="widget stick-widget">
                                    <h4 class="widget-title">Profile intro</h4>
                                    <ul class="short-profile">
                                        <li>
                                            <span>about</span>
                                            <p>Hi, i am jhon kates, i am 32 years old and worked as a web developer in microsoft company. </p>
                                        </li>
                                        <li>
                                            <span>fav tv show</span>
                                            <p>Sacred Games, Spartcus Blood, Games of theron</p>
                                        </li>
                                        <li>
                                            <span>favourit music</span>
                                            <p>Justin Biber, Nati Natsha, Shakira</p>
                                        </li>
                                    </ul>
                                </div><!-- profile intro widget -->

                            </aside>
                        </div><!-- sidebar -->
                        <div class="col-lg-9">
                            <div class="central-meta">
                                <div class="title-block">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="align-left">
                                                <h5>Videos <span>{{ count($VideoData->info)}}</span></h5>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row merged20">
                                                <div class="col-lg-7 col-md-7 col-sm-7">
                                                    <form method="post">
                                                        <input type="text" placeholder="Search Video">
                                                        <button type="submit"><i class="fa fa-search"></i></button>
                                                    </form>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <div class="select-options">
                                                        <select class="select">
                                                            <option>Sort by</option>
                                                            <option>A to Z</option>
                                                            <option>See All</option>
                                                            <option>Newest</option>
                                                            <option>oldest</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-md-1 col-sm-1">
                                                    <div class="option-list">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                        <ul>
                                                            <li class="active"><i class="fa fa-check"></i><a title="" href="#">Show Public</a></li>
                                                            <li><a title="" href="#">Show only Friends</a></li>
                                                            <li><a title="" href="#">Hide all Posts</a></li>
                                                            <li><a title="" href="#">Mute Notifications</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- title block -->
                            <div class="central-meta">
                                <div class="row merged5">
                                    @if(isset($VideoData->info) && count($VideoData->info))
                                    @foreach($VideoData->info as $video)
                                    @if(is_object($video) && isset($video->id))
                                    <div class="col-lg-4 col-md-3 col-sm-6 col-xs-6">
                                        <div class="item-box">
                                            @if(isset($video->player_url) && isset($video->image))
                                            <div style="display: flex; justify-content: center; align-items: center; width: 100%; height: 100%; padding: 10px;">
                                                <div style="position: relative; width: 500px; aspect-ratio: 5/9; cursor: pointer;">
                                                    <div onclick="openVideoModal('{{ $video->player_url }}')" target="_blank" style="display: block; position: relative;">
                                                        <img src="{{ $video->image }}" alt="Video Thumbnail" width="360" height="640" style="width: 235px; height: 418px; object-fit: cover; border: none;" />
                                                        <div style="position: absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
                                                            <i class="fa fa-play-circle" style="font-size: 64px; color: rgba(255,255,255,0.8);"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                            <p style="text-align: center;">Video không hỗ trợ nhúng hoặc không hợp lệ.</p>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach
                                    @else
                                    <p>Không có video nào.</p>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
<div id="videoModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 200000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 80%; max-width: 800px;">
        <span onclick="closeVideoModal()" style="position: absolute; top: 8px; right: 9px; background: #fff; border-radius: 50%; padding: 1px 13px; cursor: pointer; font-size: 18px;">&times;</span>
        <iframe id="videoIframe" src="" frameborder="0" style="width: 100%; height: 450px;" allowfullscreen></iframe>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let end_cursor = "{{ $VideoData->end_cursor }}";
        let isLoading = false;
        let has_next_page = true;

        function showLoading() {
            console.log("Hiển thị loading...");
            if ($('#loadingOverlay').length === 0) {
                $('body').append(`
                    <div id="loadingOverlay">
                        <div class="loader"></div>
                    </div>
                `);
            }
        }

        function hideLoading() {
            console.log("Ẩn loading...");
            $('#loadingOverlay').remove();
        }

        // ... các hàm generateVideoHtml và loadMoreVideos không thay đổi ...

        function generateVideoHtml(video) {
            if (video.player_url && video.image) {
                let html = '<div class="col-lg-4 col-md-3 col-sm-6 col-xs-6">';
                html += '<div class="item-box">';
                html += '<div style="display: flex; justify-content: center; align-items: center; width: 100%; height: 100%; padding: 10px;">';
                html += '<div style="position: relative; width: 500px; aspect-ratio: 5/9; cursor: pointer;">';
                html += '<div onclick="openVideoModal(\'' + video.player_url + '\')" target="_blank" style="display: block; position: relative;">';
                html += '<img src="' + video.image + '" alt="Video Thumbnail" width="360" height="640" style="width: 235px; height: 418px; object-fit: cover; border: none;" />';
                html += '<div style="position: absolute; top:50%; left:50%; transform:translate(-50%, -50%);">';
                html += '<i class="fa fa-play-circle" style="font-size: 64px; color: rgba(255,255,255,0.8);"></i>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                return html;
            } else {
                let html = '<div class="col-lg-4 col-md-3 col-sm-6 col-xs-6">';
                html += '<div class="item-box">';
                html += '<p style="text-align: center;">Video không hỗ trợ nhúng hoặc không hợp lệ.</p>';
                html += '</div>';
                html += '</div>';
                return html;
            }
        }

        function loadMoreVideos() {
            if (isLoading || !end_cursor || !has_next_page) return;
            isLoading = true;
            showLoading();

            $.ajax({
                url: "{{ route('profile.showVideo', ['uid' => $uid]) }}",
                method: 'GET',
                dataType: 'json',
                data: {
                    end_cursor: end_cursor
                },
                success: function(response) {
                    hideLoading();
                    if (response.status && response.info && response.info.length) {
                        let html = "";
                        $.each(response.info, function(index, video) {
                            html += generateVideoHtml(video);
                        });
                        $('.row.merged5').append(html);
                    }
                    end_cursor = response.end_cursor ? response.end_cursor : "";
                    has_next_page = response.has_next_page;
                    isLoading = false;
                },
                error: function(xhr, status, error) {
                    hideLoading();
                    console.error("Lỗi khi tải video thêm:", error);
                    isLoading = false;
                }
            });
        }

        $(window).on('scroll', function() {
            let scrollPos = $(window).scrollTop() + $(window).height();
            if (scrollPos >= $(document).height() - 100) {
                loadMoreVideos();
            }
        });

    });

    function openVideoModal(url) {
        $('#videoIframe').attr('src', url);
        $('#videoModal').fadeIn();
    }

    // Hàm đóng modal và xoá src của iframe
    function closeVideoModal() {
        $('#videoModal').fadeOut(function() {
            $('#videoIframe').attr('src', '');
        });
    }
</script>
@endsection