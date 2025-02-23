@extends ('user.layouts.master')

@section('title', 'admin')

@section('head.scripts')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<style>
    .delete-post {
        cursor: pointer;
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
@endsection

@section('content')
<section>
    <div class="gap2 gray-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row merged20" id="page-contents">
                        <div class="user-profile">
                            <figure>
                                <img src="/user/images/resources/profile-image2.jpg" alt="">
                                <ul class="profile-controls">
                                    <li><a href="#" title="Add friend" data-toggle="tooltip"><i class="fa fa-user-plus"></i></a></li>
                                    <li><a href="#" title="Follow" data-toggle="tooltip"><i class="fa fa-star"></i></a></li>
                                    <li><a class="send-mesg" href="#" title="Send Message" data-toggle="tooltip"><i class="fa fa-comment"></i></a></li>
                                    <li>
                                        <div class="edit-seting" title="Edit Profile image"><i class="fa fa-sliders"></i>
                                            <ul class="more-dropdown">
                                                <li><a href="setting.html" title="">Update Profile Photo</a></li>
                                                <li><a href="setting.html" title="">Update Header Photo</a></li>
                                                <li><a href="setting.html" title="">Account Settings</a></li>
                                                <li><a href="support-and-help.html" title="">Find Support</a></li>
                                                <li><a class="bad-report" href="#" title="">Report Profile</a></li>
                                                <li><a href="#" title="">Block Profile</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </figure>
                        </div><!-- user profile banner  -->
                        <div class="user-feature-info">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-3">
                                    <div class="user-figure">
                                        <figure><img src="{{$data['account']->avatar}}" alt=""></figure>
                                        <div class="author-meta">
                                            <h5><a href="#" title="">{{$data['account']->fullname}}</a></h5>
                                            <span>Web Developer</span>
                                            <ins>Microsoft Inc Ltd.</ins>
                                        </div>
                                        <a href="setting.html" title="">edit your profile</a>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-9">
                                    <div class="user-inf-meta">
                                        <ul class="user-info">
                                            <li><span>22</span> Followers of your porfile</li>
                                            <li><span>102</span> People view your profile in past <b>90</b> days</li>
                                            <li><span>152</span> Connections</li>
                                            <li><span>1.5k</span> Shares</li>
                                        </ul>
                                        <ol class="pit-rate">
                                            <li class="rated"><i class="fa fa-star"></i></li>
                                            <li class="rated"><i class="fa fa-star"></i></li>
                                            <li class="rated"><i class="fa fa-star"></i></li>
                                            <li class="rated"><i class="fa fa-star"></i></li>
                                            <li class=""><i class="fa fa-star"></i></li>
                                        </ol>
                                        <span>4.7/5</span>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <hr>
                                    <ul class="profile-menu" style="display: flex; list-style: none; padding: 0; margin: 0;">
                                        <li style="margin-right: 15px;">
                                            <a class="active" href="{{ route('profile.showMyPost', ['uid' => $uid]) }}">Shop</a>
                                        </li>
                                        <li style="margin-right: 15px;">
                                            <a href="{{ route('profile.showMarket', ['uid' => $uid]) }}">Market Place</a>
                                        </li>
                                        <li style="margin-right: 15px;">
                                            <a href="shop-cart.html">Cart</a>
                                        </li>
                                        <li style="margin-right: 15px;">
                                            <a href="shop-checkout.html">Checkout</a>
                                        </li>
                                        <li>
                                            <a href="shop-detail.html">Prod Detail</a>
                                        </li>
                                        
                                    </ul>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="sharing-tools">
                                        <div class="we-video-info">
                                            <ul>
                                                <li>
                                                    <span class="views" title="views">
                                                        <i class="fa fa-eye"></i>
                                                        <ins>1.2k</ins>
                                                    </span>
                                                </li>
                                                <li>
                                                    <div class="likes heart" title="Like/Dislike">❤ <span>2K</span></div>
                                                </li>
                                                <li>
                                                    <span>
                                                        <a class="share-pst" href="#" title="Share">
                                                            <i class="fa fa-share-alt"></i>
                                                        </a>
                                                        <ins>20</ins>
                                                    </span>
                                                </li>
                                            </ul>
                                            <div class="share-to-other">
                                                <ul>
                                                    <li><a title="" href="#" class="facebook-color"><i class="fa fa-facebook-square"></i></a></li>
                                                    <li><a title="" href="#" class="twitter-color"><i class="fa fa-twitter-square"></i></a></li>
                                                    <li><a title="" href="#" class="dribble-color"><i class="fa fa-dribbble"></i></a></li>
                                                    <li><a title="" href="#" class="instagram-color"><i class="fa fa-instagram"></i></a></li>
                                                    <li><a title="" href="#" class="pinterest-color"><i class="fa fa-pinterest-square"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-12 col-md-12">
                            <div class="central-meta">
                                <div class="job-search-form">
                                    <div class="jobbox-title">
                                        <h5>Looking for a job?</h5>
                                        <span>Explore top rated jobs and Employers</span>
                                    </div>
                                    <a href="#" title=""><i class="fa fa-search-plus"></i> Advance Search</a>
                                    <form method="post" class="c-form">
                                        <div class="row merged10">
                                            <div class="col-lg-2 col-md-3">
                                                <span class="add-loc">
                                                    <input type="text" placeholder="location">
                                                    <i class="fa fa-map-marker"></i>
                                                </span>

                                                <div class="searchbylocation">
                                                    <div class="add-location-post">
                                                        <span>Drag map point to selected area</span>
                                                        <div class="row">

                                                            <div class="col-lg-6">
                                                                <label class="control-label">Lat :</label>
                                                                <input type="text" class="" id="us3-lat" />
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label>Long :</label>
                                                                <input type="text" class="" id="us3-lon" />
                                                            </div>
                                                        </div>
                                                        <!-- map -->
                                                        <div id="us3"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-9">
                                                <input type="text" placeholder="Description">
                                            </div>
                                            <div class="col-lg-3 col-md-6">
                                                <select>
                                                    <option>Accounts</option>
                                                    <option>Data Entry</option>
                                                    <option>Designing</option>
                                                    <option>Web Developing</option>
                                                    <option>Backend Designing</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-2 col-md-6 ">
                                                <input type="text" placeholder="Price">
                                            </div>
                                            <div class="col-lg-1 col-md-12">
                                                <button type="submit" class="main-btn">Find</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="loadMore">
                                @foreach($posts->reponse as $post)
                                <div class="central-meta item">
                                    <div class="classic-post">
                                        <figure>
                                            <img src="{{$post->thumbnail}}" alt="" width="135" height="115">
                                            <span>Super Hot</span>
                                        </figure>
                                        <div class="classic-pst-meta">
                                            <div class="more">
                                                <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                    <ul>
                                                        <li><i class="fa fa-pencil-square-o"></i>Edit Post</li>
                                                        <li>
                                                            <a href="#" class="delete-post" data-id="{{ $post->id }}">
                                                                <i class="fa fa-trash-o"></i> Delete Post
                                                            </a>
                                                        </li>
                                                        <li class="bad-report"><i class="fa fa-flag"></i>Report This Post</li>
                                                        <li><i class="fa fa-clock-o"></i>Schedule Post</li>
                                                        <li><i class="fa fa-wpexplorer"></i>Select as featured</li>
                                                        <li><i class="fa fa-bell-slash-o"></i>Turn off Notifications</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <h4><i class="fa fa-check-circle" title="verified"></i> <a href="#" title=""> {{$post->title}}</a></h4>
                                            <p>Beautiful Rider bike for sale. 450cc new tyre long seat... </p>
                                            <span class="prise">{{$post->price}}</span>
                                            <div class="location-area">

                                                <span><i class="fa fa-map-marker"></i> Việt Nam</span>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div><!-- centerl meta -->
                        <div class="col-lg-4">
                            <aside class="sidebar static right">
                                <div class="widget">
                                    <h4 class="widget-title">Complete Your Profile</h4>
                                    <div class="pit-reg-complete">
                                        <div class="progresdiv" data-percent="86">
                                            <svg class="progres" width="90" height="90" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                                <circle r="50" cx="50" cy="50" fill="transparent" stroke-dasharray="502.4" stroke-dashoffset="0"></circle>
                                                <circle class="bar" r="50" cx="50" cy="50" fill="transparent" stroke-dasharray="502.4" stroke-dashoffset="0"></circle>
                                            </svg>
                                        </div>
                                        <div class="reg-comp-meta">
                                            <p>filling your profile details will help you to meet the right people</p>
                                            <ul>
                                                <li><i class="fa fa-envelope-o bg-red"></i> <span>Profile Information 20%</span>
                                                    <a href="#" title="" class="underline">Add</a>
                                                </li>
                                                <li><i class="fa fa-envelope-o bg-blue"></i> <span>your Email 10%</span>
                                                    <a href="#" title="" class="underline">Add</a>
                                                </li>
                                                <li><i class="fa fa-phone bg-purple"></i> <span>your phone number 10%</span>
                                                    <a href="#" title="" class="underline">Add</a>
                                                </li>
                                                <li><i class="fa fa-map-marker bg-green"></i> <span>Your Location 10%</span>
                                                    <a href="#" title="" class="underline">Add</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div><!-- complete profile widget -->

                            </aside>
                        </div><!-- sidebar -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section><!-- content -->

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

    $(document).on('click', '.delete-post', function() { // Sử dụng lớp thay vì ID

        showLoading(); // Show loading indicator

        const uid = '{{ $uid }}';
        const postId = $(this).data('id');
        console.log(`UID: ${uid}, PostID: ${postId}`);

        const postElement = $(this).closest('.central-meta.item');

        $.ajax({
            url: `/Android/DeletePost/${uid}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: postId
            },
            success: function(response) {
                hideLoading(); // Hide loading indicator
                console.log('Response:', response);
                if (response.response === 'Xóa bài thành công !') {
                    postElement.remove();
                    toastr.success(response.response);
                } else {
                    toastr.error(response.response);
                }
            },
            error: function(xhr, status, error) {
                hideLoading(); // Hide loading indicator
                console.error('Error:', error);
                toastr.error('An error occurred while deleting the post.');
            }
        });

    });
</script>
@endsection


@section('footer.scripts')

@endsection