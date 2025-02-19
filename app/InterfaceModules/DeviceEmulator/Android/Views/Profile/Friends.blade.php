@extends ('user.layouts.master')

@section('title', 'Profile')

@section('head.scripts')

@endsection

@section('content')
<section>
    <div class="gap2 gray-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row merged20" id="page-contents">
                        @include('Android::Profile.Profile_baner')

                        <div class="col-lg-12">
                            <div class="central-meta">
                                <div class="title-block">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="align-left">
                                                <h5>
                                                    Friend / Followers
                                                    <span style="font-size: 11px">{{ $totalFriends }}</span>
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row merged20">
                                                <div class="col-lg-7 col-md-7 col-sm-7">
                                                    <form method="post">
                                                        <input type="text" placeholder="Search Friend">
                                                        <button type="submit">
                                                            <i class="fa fa-search"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <div class="select-options">
                                                        <select class="select">
                                                            <option>Sort by</option>
                                                            <option>A to Z</option>
                                                            <option>See All</option>
                                                            <option>Newest</option>
                                                            <option>Oldest</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-md-1 col-sm-1">
                                                    <div class="option-list">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                        <ul>
                                                            <li><a title="" href="#">Show Friends Public</a></li>
                                                            <li><a title="" href="#">Show Friends Private</a></li>
                                                            <li><a title="" href="#">Mute Notifications</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- title-block -->
                            </div><!-- central-meta -->

                            {{-- Khu vực danh sách bạn bè --}}
                            <div class="central-meta padding30">
                                <div class="row merged20 friend-list-wrapper">
                                    @if ($friends && count($friends) > 0)

                                    @foreach ($friends as $friend)
                                    <div class="col-lg-3 col-md-6 col-sm-6 friend-item">
                                        <div class="friend-block">
                                            <div class="more-opotnz">
                                                <i class="fa fa-ellipsis-h"></i>
                                                <ul>
                                                    <li><a href="#" title="">Block</a></li>
                                                    <li><a href="#" title="">UnBlock</a></li>
                                                    <li><a href="#" title="">Mute Notifications</a></li>
                                                    <li><a href="#" title="">Hide from friend list</a></li>
                                                </ul>
                                            </div>
                                            <figure>
                                                @php
                                                $avatarUrl = isset($friend['profile_picture']) && !empty($friend['profile_picture']) ? $friend['profile_picture'] : asset('/user/images/resources/default.jpg');
                                                $headers = @get_headers($avatarUrl);

                                                if(!$headers || strpos($headers[0], '200') === false) {
                                                $avatarUrl = asset('/user/images/resources/user2.jpg');
                                                }
                                                @endphp
                                                <img src="{{ $avatarUrl }}" alt="">
                                            </figure>
                                            <div class="frnd-meta">
                                                <div class="frnd-name">
                                                    <a href="#" title="">
                                                        {{ $friend['name'] ?? 'No Name' }}
                                                    </a>
                                                    <span>
                                                        {{ $friend['location'] ?? 'Location not available' }}
                                                    </span>
                                                </div>
                                                <a class="send-mesg" href="#" title="">Message</a>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <div class="col-lg-12 text-center">
                                        <p>No friends to display.</p>
                                    </div>
                                    @endif
                                </div>

                                <div class="lodmore text-center mt-3">
                                    <span>
                                        @if ($friends && count($friends) > 0)
                                        Viewing 1-{{ min(count($friends), 100 * $currentPage) }}
                                        @else
                                        Viewing 0
                                        @endif
                                        of {{ $totalFriends }} friends
                                    </span>
                                    {{-- Không cần nút Load More, ta dùng lazy scroll --}}
                                </div>
                            </div><!-- central-meta -->
                        </div><!-- col-lg-12 -->
                    </div><!-- row merged20 -->
                </div><!-- col-lg-12 -->
            </div><!-- row -->
        </div><!-- container -->
    </div><!-- gap2 gray-bg -->
</section><!-- content -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // script trong Friends.blade.php
    let currentPage = {
        {
            $currentPage
        }
    };
    const uid = "{{ $uid }}";
    const totalFriends = {
        {
            $totalFriends
        }
    };
    let hasMore = {
        {
            $hasMore ? 'true' : 'false'
        }
    };
    let loading = false;

    function updateViewCount() {
        const loadedFriends = $('.friend-item').length;
        const viewEnd = loadedFriends < totalFriends ? loadedFriends : totalFriends;
        $('.lodmore span').text(`Viewing 1-${viewEnd} of ${totalFriends} friends`);
    }

    function loadMoreFriends() {
        if (loading) return;
        loading = true;

        currentPage++;

        $.ajax({
            url: `/Android/Getfriend/${uid}`,
            type: 'GET',
            data: {
                page: currentPage
            },
            success: function(response) {
                if (response.friends && response.friends.length > 0) {
                    let html = '';
                    response.friends.forEach(friend => {
                        html += `
                    <div class="col-lg-3 col-md-6 col-sm-6 friend-item">
                        <div class="friend-block">
                            <div class="more-opotnz">
                                <i class="fa fa-ellipsis-h"></i>
                                <ul>
                                    <li><a href="#" title="">Block</a></li>
                                    <li><a href="#" title="">UnBlock</a></li>
                                    <li><a href="#" title="">Mute Notifications</a></li>
                                    <li><a href="#" title="">Hide from friend list</a></li>
                                </ul>
                            </div>
                            <figure>
                                <img src="${friend.profile_picture}" alt="" onerror="this.src='/user/images/resources/user2.jpg'">
                            </figure>
                            <div class="frnd-meta">
                                <div class="frnd-name">
                                    <a href="#" title="">
                                        ${friend.name || 'No Name'}
                                    </a>
                                    <span>
                                        ${friend.location || 'Location not available'}
                                    </span>
                                </div>
                                <a class="send-mesg" href="#" title="">Message</a>
                            </div>
                        </div>
                    </div>`;
                    });
                    $('.friend-list-wrapper').append(html);
                    updateViewCount();

                    hasMore = response.hasMore;
                } else {
                    hasMore = false;
                }
            },
            error: function() {
                alert("Đã xảy ra lỗi khi tải thêm bạn bè");
            },
            complete: function() {
                loading = false;
            }
        });
    }


    $(window).on('scroll', function() {
        if (!hasMore || loading) return;
        let scrollTop = $(window).scrollTop();
        let windowHeight = $(window).height();
        let docHeight = $(document).height();

        if (scrollTop + windowHeight >= docHeight - 300) {
            loadMoreFriends();
        }
    });
</script>
@endsection