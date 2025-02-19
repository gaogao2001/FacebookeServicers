@php
use Carbon\Carbon;

$reactionList = array(
'115940658764963' => 'haha',
'1635855486666999' => 'like',
'1678524932434102' => 'love'
);
@endphp
<style>
    .react {
        display: block;
        margin-top: 130px;
    }

    .react-me {
        font-family: Helvetica;
        cursor: pointer;
        display: block;
        position: relative;

        .inner {
            position: absolute;
            bottom: 100%;
            padding-bottom: 15px;
        }

        &:hover .react-box {
            display: block;
        }
    }

    .react-box {
        list-style-type: none;
        margin: 0;
        display: none;
        padding: 0 5px;
        border-radius: 150px;
        background: #fff;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);

        ul {
            margin: 0;
            padding: 0;
        }

        li {
            display: inline-block;
            width: 48px;
            height: 48px;
            transform: scale(.8) translateY(0);
            background: url("https://static.xx.fbcdn.net/rsrc.php/v2/yh/r/sqhTN9lgaYm.png") no-repeat;
            background-size: cover;
            transition: transform 200ms ease;
            position: relative;

            &.like {
                background-position: 0 -144px;
            }

            &.love {
                background-position: 0 -192px;
            }

            &.haha {
                background-position: 0 -96px;
            }

            &.wow {
                background-position: 0 -288px;
            }

            &.sad {
                background-position: 0 -240px;
            }

            &.angry {
                background-position: 0 0;
            }

            &:before {
                content: attr(data-hover);
                position: absolute;
                bottom: 120%;
                left: 50%;
                transform: translateX(-50%);
                color: #fff;
                padding: 0 8px;
                border-radius: 20px;
                font-family: Helvetica, Verdana, Arial;
                font-weight: bold;
                line-height: 20px;
                font-size: 12px;
                background: rgba(0, 0, 0, 0.76);
                display: none;
            }

            &:hover {
                transform: scale(1) translateY(-5px);
                transition: transform 200ms ease;

                &:before {
                    display: inline-block;
                }
            }
        }
    }

    .credit {
        margin-top: 30px;
        font-size: 12px;
        font-family: Helvetica;
    }

    .linkback {
        margin-top: 30px;
        font-family: Helvetica;
        display: block;
    }

    .react-box ul {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }

    .we-comment p {
        white-space: pre-wrap;
        word-wrap: break-word;
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
<div class="loadMore">

    @foreach($data['newfeed']->post_data as $posts)



    <div class="central-meta item" data-post='@json($posts)'>
        <div class="user-post">
            <div class="friend-info">
                <figure>
                    <img src="/user/images/resources/nearly1.jpg" alt="">
                </figure>
                <div class="friend-name">
                    <div class="more">
                        <div class="more-post-optns"><i class="ti-more-alt"></i>
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
                    <ins><a href="time-line.html" title=""> {{$posts->user_name}}</a> Post Album</ins>
                    <span>
                        <i class="fa fa-globe"></i>
                        published:
                        {{ \Carbon\Carbon::parse($posts->creation_time ?? now())->format('F, d Y h:i A') }}
                    </span>
                </div>
                <div class="post-meta">
                    <p>
                        {{ $posts->post_content }}
                    </p>
                    <figure>
                        @if (!empty($posts->attachments))
                        <div class="img-bunch">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <figure>
                                        <a href="#" title="" data-toggle="modal" data-target="#img-comt">
                                            <img src="{{ $posts->attachments[0] }}" alt="">
                                        </a>
                                    </figure>
                                </div>
                            </div>
                        </div>
                        @endif

                    </figure>
                    <div class="we-video-info">
                        <ul>
                            <li>
                                <span class="views" title="views">
                                    <i class="fa fa-eye"></i>
                                    <ins>1.2k</ins>
                                </span>
                            </li>
                            <li>
                                <div class="likes heart react-me" title="Like/Dislike">❤ <span>{{ $posts->reactors_count ??  0 }}</span>
                                    <div class="inner">
                                        <div class="react-box">
                                            <ul style="justify-content: flex-start; gap: 5px;">
                                                @foreach($reactionList as $id => $reaction)
                                                <li class="{{ $reaction }}" data-hover="{{ ucfirst($reaction) }}" data-reaction-id="{{ $id }}"></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <span class="comment BtnLoadComment" title="{{ $posts->message ?? null }}" data-id="{{ $posts->post_id ?? null }}" data-uid="{{ $data['account']->uid ?? null }}">
                                <i class="fa fa-commenting"></i>
                                <ins>{{ $posts->comment_count ?? 0 }}</ins>
                            </span>

                            <li>
                                <span>
                                    <a class="share-pst" href="#" title="Share">
                                        <i class="fa fa-share-alt"></i>
                                    </a>
                                    <ins>{{ $posts->reshares_count ?? null }}</ins>
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
                            <span><strong>You</strong>, <b>Sarah</b> and <a href="#" title="">24+ more</a> liked</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="coment-area" style="display: none;">
                <ul class="we-comet">


                </ul>
            </div>
        </div>
    </div>

    @endforeach
</div>

<script>
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

    function hideLoading() {
        console.log("Ẩn loading...");
        $('#loadingOverlay').remove();
    }

    // Add this script to your post.blade.php file, preferably within the existing <script> tag

    $(document).ready(function() {
        $('.react-box li').on('click', function() {
            var $centralMeta = $(this).closest('.central-meta');
            var reactionId = $(this).data('reaction-id');
            var uid = $centralMeta.find('[data-uid]').data('uid');
            var postData = $centralMeta.data('post');

            $.ajax({
                url: "{{ route('profile.addReaction', ['uid' => ':uid']) }}".replace(':uid', uid),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    reaction_id: reactionId,
                    post_data: postData
                },
                beforeSend: function() {
                    showLoading();
                },
                success: function(response) {
                    hideLoading();
                    if (response.response) {
                        toastr.success(response.response, "Success", {
                            "closeButton": true,
                            "progressBar": true
                        });
                        if (response.newCount) {
                            // Update the reactors count for the specific post
                            $centralMeta.find('.reactors_count').text(response.newCount);
                        }
                    } else {
                        toastr.error(response.message || "An error occurred.", "Error", {
                            "closeButton": true,
                            "progressBar": true
                        });
                    }
                },
                error: function(xhr) {
                    hideLoading();
                    toastr.error("An error occurred.", "Error", {
                        "closeButton": true,
                        "progressBar": true
                    });
                }
            });
        });
    });
</script>