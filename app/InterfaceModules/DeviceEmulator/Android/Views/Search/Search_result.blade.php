@extends ('user.layouts.master')

@section('title', 'Profile')

@section('head.scripts')
<link href="{{ asset('InterfaceModules/DeviceEmulator/Android/Search/css/search.css') }}" rel="stylesheet">

@endsection

@section('content')
<section>
    <div class="gap2 gray-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row merged20" id="page-contents">
                        <div class="col-lg-12">
                            <div class="search-meta">
                                <span>Kết quả tìm kiếm của bạn cho " <i>{{$keySearch}}</i> " </span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <aside class="sidebar static left">
                                <div class="widget">
                                    <h4 class="widget-title">Filter Search</h4>
                                    <form class="c-form search" method="post">
                                        <div>
                                            <label>Gender</label>
                                            <div class="form-radio">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" checked="checked" name="radio"><i class="check-box"></i>Male
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="radio"><i class="check-box"></i>Female
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="radio"><i class="check-box"></i>Custom
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label>Post From</label>
                                            <div class="form-radio">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" checked="checked" name="radio"><i class="check-box"></i>Public
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="radio"><i class="check-box"></i>You
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="radio"><i class="check-box"></i>Your Friends
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="radio"><i class="check-box"></i>Your Group and pages
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label>Post Type</label>
                                            <div class="form-radio">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" checked="checked" name="radio"><i class="check-box"></i>All Posts
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="radio"><i class="check-box"></i>Posts you seen
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label>Post in Group</label>
                                            <div class="form-radio">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" checked="checked" name="radio"><i class="check-box"></i>Any Group
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="radio"><i class="check-box"></i>your Group
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label>Location</label>
                                            <div class="form-radio">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" checked="checked" name="radio"><i class="check-box"></i>World wide
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="radio"><i class="check-box"></i>your Country
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <a href="#" title="">
                                                        Targeted location
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="advertisment-box">
                                    <h4 class="">advertisment</h4>
                                    <figure>
                                        <a href="#" title="Advertisment"><img src="/user/images/resources/ad-widget.gif" alt=""></a>
                                    </figure>
                                </div>
                                <div class="widget">
                                    <div class="banner medium-opacity purple">
                                        <div class="bg-image" style="background-image: url(/user/images/resources/baner-widgetbg.jpg)"></div>
                                        <div class="baner-top">
                                            <span><img alt="" src="/user/images/book-icon.png"></span>
                                            <i class="fa fa-ellipsis-h"></i>
                                        </div>
                                        <div class="banermeta">
                                            <p>
                                                create your own favourit page.
                                            </p>
                                            <span>like them all</span>
                                            <a data-ripple="" title="" href="#">start now!</a>
                                        </div>
                                    </div>
                                </div>

                            </aside>
                        </div><!-- sidebar -->
                        <div class="col-lg-7">
                            <div class="search-tab">
                                <ul class="nav nav-tabs tab-btn">
                                    <li class="nav-item"><a class="active" href="#All" data-toggle="tab">All</a></li>
                                    <li class="nav-item"><a class="" href="#people" data-toggle="tab">People</a></li>
                                    <li class="nav-item"><a class="" href="#pages" data-toggle="tab">Pages</a></li>
                                    <li class="nav-item"><a class="" href="#groups" data-toggle="tab">Groups</a></li>
                                    <li class="nav-item"><a class="" href="#photos" data-toggle="tab">Photos</a></li>
                                    <li class="nav-item"><a class="" href="#videos" data-toggle="tab">Videos</a></li>
                                    <li class="nav-item"><a class="" href="#posts" data-toggle="tab">Posts</a></li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div class="tab-pane active fade show " id="All" >
                                        <div class="central-meta item " >
                                            <span class="create-post">People<a title="" href="#">See All</a></span>
                                            @foreach(array_slice($users->list_user, 0, 4) as $user)
                                            <div class="pit-friends">
                                                <figure><a href="#" title=""><img src="{{$user->avatar}}" alt=""></a></figure>
                                                <div class="pit-frnz-meta">
                                                    <a href="#" title="">{{$user->name}}</a>
                                                    <i>{{$user->text}}</i>
                                                    <ul class="add-remove-frnd">
                                                        <li class="add-tofrndlist">
                                                            <a title="Add friend" href="#"><i class="fa fa-user-plus" data-uid="{{ $user->uid }}"></i></a>
                                                        </li>
                                                        <li class="remove-frnd">
                                                            <a title="Send Message" href="#"><i class="fa fa-comment"></i></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- searched peoples -->
                                        <div class="central-meta item">
                                            <span class="create-post">Groups<a title="" href="#">See All</a></span>
                                            @foreach(array_slice($groups->list_user, 0, 4) as $group)
                                            <div class="pit-groups">
                                                <figure><a href="#" title=""><img src="{{$group->avatar}}" alt=""></a></figure>
                                                <div class="pit-groups-meta">
                                                    <a href="#" title="">{{$group->name}}</a>
                                                    <i>{{$group->text}}</i>
                                                    <ul class="add-remove-frnd">
                                                        <li class="add-tofrndlist">
                                                            <a title="Add friend" href="#"><i class="fa fa-plus" data-uid="{{ $group->uid }}"></i> Join</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- searched groups -->
                                        <div class="central-meta item">
                                            <span class="create-post">Pages<a title="" href="#">See All</a></span>
                                            @foreach(array_slice($pages->list_user, 0, 4) as $page)
                                            <div class="pit-pages">
                                                <figure><a href="#" title=""><img src="{{$page->avatar}}" alt=""></a></figure>
                                                <div class="pit-pages-meta">
                                                    <a href="#" title="">{{$page->name}}</a>
                                                    <i>{{$page->text}}</i>
                                                    <ul class="add-remove-frnd">
                                                        <li class="add-tofrndlist">
                                                            <a title="Like" href="#"><i class="fa fa-thumbs-up"  data-uid="{{ $page->uid }}"></i> Like</a>
                                                        </li>
                                                        <li class="remove-frnd">
                                                            <a title="Follow" href="#"><i class="fa fa-user-plus" data-uid="{{ $page->uid }}" ></i> Follow</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- searched pages -->
                                    </div>
                                    <div class="tab-pane fade" id="people">
                                        <div class="central-meta item">
                                            <span class="create-post">People<a title="" href="#">See All</a></span>
                                            @foreach($users->list_user as $user)
                                            <div class="pit-friends">
                                                <figure><a href="#" title=""><img src="{{$user->avatar}}" alt=""></a></figure>
                                                <div class="pit-frnz-meta">
                                                    <a href="#" title="">{{$user->name}}</a>
                                                    <i>{{$user->text}}</i>
                                                    <ul class="add-remove-frnd">
                                                        <li class="add-tofrndlist">
                                                            <a title="Add friend" href="#"><i class="fa fa-user-plus" data-uid="{{ $user->uid }}"></i></a>
                                                        </li>
                                                        <li class="remove-frnd">
                                                            <a title="Send Message" href="#"><i class="fa fa-comment"></i></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- searched peoples -->
                                    </div>
                                    <div class="tab-pane fade" id="pages">
                                        <div class="central-meta item">
                                            <span class="create-post">Pages<a title="" href="#">See All</a></span>
                                            @foreach($pages->list_user as $page)
                                            <div class="pit-pages">
                                                <figure><a href="#" title=""><img src="{{$page->avatar}}" alt=""></a></figure>
                                                <div class="pit-pages-meta">
                                                    <a href="#" title="">{{$page->name}}</a>
                                                    <i>{{$page->text}}</i>
                                                    <ul class="add-remove-frnd">
                                                        <li class="add-tofrndlist">
                                                            <a title="Like" href="#"><i class="fa fa-thumbs-up" data-uid="{{ $page->uid }}" ></i> Like</a>
                                                        </li>
                                                        <li class="remove-frnd">
                                                            <a title="Follow" href="#"><i class="fa fa-user-plus" data-uid="{{ $page->uid }}" ></i> Follow</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- searched pages -->
                                    </div>
                                    <div class="tab-pane fade" id="groups">
                                        <div class="central-meta item">
                                            <span class="create-post">Groups<a title="" href="#">See All</a></span>
                                            @foreach($groups->list_user as $group)
                                            <div class="pit-groups">
                                                <figure><a href="#" title=""><img src="{{$group->avatar}}" alt=""></a></figure>
                                                <div class="pit-groups-meta">
                                                    <a href="#" title="">{{$group->name}}</a>
                                                    <i>{{$group->text}}</i>
                                                    <ul class="add-remove-frnd">
                                                        <li class="add-tofrndlist">
                                                            <a title="Add friend" href="#"><i class="fa fa-plus"  data-uid="{{ $group->uid }}"></i> Join</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- searched groups -->
                                    </div>
                                    <div class="tab-pane fade" id="photos">
                                        <div class="central-meta item">
                                            <div class="user-post">
                                                <div class="friend-info">
                                                    <figure>
                                                        <img src="/user/images/resources/nearly2.jpg" alt="">
                                                    </figure>
                                                    <div class="friend-name">
                                                        <div class="more">
                                                            <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                                <ul>
                                                                    <li><i class="fa fa-comment"></i>Send Message</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <ins><a href="time-line.html" title="">Jack Carter</a> Post Album</ins>
                                                        <span><i class="fa fa-globe"></i> published: September,15 2020 19:PM </span>

                                                    </div>
                                                    <div class="post-meta">
                                                        <figure>
                                                            <div class="img-bunch">
                                                                <div class="row">
                                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                                        <figure>
                                                                            <a class="strip" href="/user/images/resources/album6.jpg" title="" data-strip-group="mygroup" data-strip-group-options="loop: false">
                                                                                <img src="/user/images/resources/album6.jpg" alt="">
                                                                            </a>
                                                                        </figure>
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                                        <figure>
                                                                            <a class="strip" href="/user/images/resources/album5.jpg" title="" data-strip-group="mygroup" data-strip-group-options="loop: false">
                                                                                <img src="/user/images/resources/album5.jpg" alt="">
                                                                            </a>
                                                                        </figure>
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                                        <figure>
                                                                            <a class="strip" href="/user/images/resources/album4.jpg" title="" data-strip-group="mygroup" data-strip-group-options="loop: false">
                                                                                <img src="/user/images/resources/album4.jpg" alt="">
                                                                            </a>
                                                                            <div class="more-photos">
                                                                                <span>+15</span>
                                                                            </div>
                                                                        </figure>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </figure>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="central-meta item">
                                            <div class="user-post">
                                                <div class="friend-info">
                                                    <figure>
                                                        <img src="/user/images/resources/nearly1.jpg" alt="">
                                                    </figure>
                                                    <div class="friend-name">
                                                        <div class="more">
                                                            <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                                <ul>
                                                                    <li><i class="fa fa-comment"></i>Send Message</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <ins><a href="time-line.html" title="">Silly</a> Picture</ins>
                                                        <span><i class="fa fa-globe"></i> published: September,15 2020 19:PM </span>

                                                    </div>
                                                    <div class="post-meta">
                                                        <figure>
                                                            <div class="img-bunch">
                                                                <div class="row">
                                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                                        <figure>
                                                                            <a class="strip" href="/user/images/resources/album6.jpg" title="" data-strip-group="mygroup" data-strip-group-options="loop: false">
                                                                                <img src="/user/images/resources/album6.jpg" alt="">
                                                                            </a>
                                                                        </figure>
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                                        <figure>
                                                                            <a class="strip" href="/user/images/resources/album5.jpg" title="" data-strip-group="mygroup" data-strip-group-options="loop: false">
                                                                                <img src="/user/images/resources/album5.jpg" alt="">
                                                                            </a>
                                                                        </figure>
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                                        <figure>
                                                                            <a class="strip" href="/user/images/resources/album4.jpg" title="" data-strip-group="mygroup" data-strip-group-options="loop: false">
                                                                                <img src="/user/images/resources/album4.jpg" alt="">
                                                                            </a>
                                                                        </figure>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </figure>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="videos">
                                        <div class="central-meta item">
                                            <span class="create-post">Videos<a title="" href="#">See All</a></span>
                                            <div class="user-post">
                                                <div class="friend-info">
                                                    <figure>
                                                        <img src="/user/images/resources/nearly2.jpg" alt="">
                                                    </figure>
                                                    <div class="friend-name">
                                                        <div class="more">
                                                            <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                                <ul>
                                                                    <li><i class="fa fa-comment"></i>Send Message</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <ins><a href="time-line.html" title="">Jack Carter</a> video</ins>
                                                        <span><i class="fa fa-globe"></i> published: August,15 2020 19:PM </span>

                                                    </div>
                                                    <div class="post-meta searched">
                                                        <div class="linked-image align-right">
                                                            <a href="https://www.youtube.com/watch?v=MIbbtEjdYrc" title="" data-strip-group="mygroup" class="strip" data-strip-options="width: 700,height: 450,youtube: { autoplay: 1 }">
                                                                <img src="/user/images/resources/search-1.jpg" alt="">
                                                                <i>
                                                                    <svg version="1.1" class="play" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" height="30px" width="30px"
                                                                        viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                                                                        <path class="stroke-solid" fill="none" stroke="" d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7
																	C97.3,23.7,75.7,2.3,49.9,2.5" />
                                                                        <path class="icon" fill="" d="M38,69c-1,0.5-1.8,0-1.8-1.1V32.1c0-1.1,0.8-1.6,1.8-1.1l34,18c1,0.5,1,1.4,0,1.9L38,69z" />
                                                                    </svg>
                                                                </i>
                                                            </a>
                                                        </div>
                                                        <div class="detail">
                                                            <p>Lorem ipsum dolor sit amet, consectetur ipisicing elit...</p>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="user-post">
                                                <div class="friend-info">
                                                    <figure>
                                                        <img src="/user/images/resources/nearly2.jpg" alt="">
                                                    </figure>
                                                    <div class="friend-name">
                                                        <div class="more">
                                                            <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                                <ul>
                                                                    <li><i class="fa fa-comment"></i>Send Message</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <ins><a href="time-line.html" title="">Jack Carter</a> video</ins>
                                                        <span><i class="fa fa-globe"></i> published: August,15 2020 19:PM </span>

                                                    </div>
                                                    <div class="post-meta searched">
                                                        <div class="linked-image align-right">
                                                            <a href="https://www.youtube.com/watch?v=MIbbtEjdYrc" title="" data-strip-group="mygroup" class="strip" data-strip-options="width: 700,height: 450,youtube: { autoplay: 1 }">
                                                                <img src="/user/images/resources/search-2.jpg" alt="">
                                                                <i>
                                                                    <svg version="1.1" class="play" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" height="30px" width="30px"
                                                                        viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                                                                        <path class="stroke-solid" fill="none" stroke="" d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7
																	C97.3,23.7,75.7,2.3,49.9,2.5" />
                                                                        <path class="icon" fill="" d="M38,69c-1,0.5-1.8,0-1.8-1.1V32.1c0-1.1,0.8-1.6,1.8-1.1l34,18c1,0.5,1,1.4,0,1.9L38,69z" />
                                                                    </svg>
                                                                </i>
                                                            </a>
                                                        </div>
                                                        <div class="detail">
                                                            <p>Lorem ipsum dolor sit amet, consectetur ipisicing elit...</p>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="user-post">
                                                <div class="friend-info">
                                                    <figure>
                                                        <img src="/user/images/resources/nearly2.jpg" alt="">
                                                    </figure>
                                                    <div class="friend-name">
                                                        <div class="more">
                                                            <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                                <ul>
                                                                    <li><i class="fa fa-comment"></i>Send Message</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <ins><a href="time-line.html" title="">Jack Carter</a> video</ins>
                                                        <span><i class="fa fa-globe"></i> published: August,15 2020 19:PM </span>

                                                    </div>
                                                    <div class="post-meta searched">
                                                        <div class="linked-image align-right">
                                                            <a href="https://www.youtube.com/watch?v=MIbbtEjdYrc" title="" data-strip-group="mygroup" class="strip" data-strip-options="width: 700,height: 450,youtube: { autoplay: 1 }">
                                                                <img src="/user/images/resources/search-3.jpg" alt="">
                                                                <i>
                                                                    <svg version="1.1" class="play" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" height="30px" width="30px"
                                                                        viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                                                                        <path class="stroke-solid" fill="none" stroke="" d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7
																	C97.3,23.7,75.7,2.3,49.9,2.5" />
                                                                        <path class="icon" fill="" d="M38,69c-1,0.5-1.8,0-1.8-1.1V32.1c0-1.1,0.8-1.6,1.8-1.1l34,18c1,0.5,1,1.4,0,1.9L38,69z" />
                                                                    </svg>
                                                                </i>
                                                            </a>
                                                        </div>
                                                        <div class="detail">
                                                            <p>Lorem ipsum dolor sit amet, consectetur ipisicing elit...</p>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- post with video -->
                                    </div>
                                    <div class="tab-pane fade" id="posts">
                                        <div class="central-meta item">
                                            <span class="create-post">Posts<a title="" href="#">See All</a></span>
                                            <div class="user-post">
                                                <div class="friend-info">
                                                    <figure>
                                                        <img src="/user/images/resources/nearly2.jpg" alt="">
                                                    </figure>
                                                    <div class="friend-name">
                                                        <div class="more">
                                                            <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                                <ul>
                                                                    <li><i class="fa fa-comment"></i>Send Message</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <ins><a href="time-line.html" title="">Jack Carter</a> Post</ins>
                                                        <span><i class="fa fa-globe"></i> published: September,15 2020 19:PM </span>

                                                    </div>
                                                    <div class="post-meta searched">
                                                        <div class="linked-image align-right">
                                                            <a href="#" title=""><img src="/user/images/resources/search-2.jpg" alt=""></a>
                                                        </div>
                                                        <div class="detail">
                                                            <p>Lorem ipsum dolor sit amet, consectetur ipisicing elit, sed do eiusmod tempor incididunt ut labor... </p>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="user-post">
                                                <div class="friend-info">
                                                    <figure>
                                                        <img src="/user/images/resources/nearly2.jpg" alt="">
                                                    </figure>
                                                    <div class="friend-name">
                                                        <div class="more">
                                                            <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                                <ul>
                                                                    <li><i class="fa fa-comment"></i>Send Message</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <ins><a href="time-line.html" title="">Jack Carter</a> Post</ins>
                                                        <span><i class="fa fa-globe"></i> published: September,15 2020 19:PM </span>

                                                    </div>
                                                    <div class="post-meta searched">
                                                        <div class="linked-image align-right">
                                                            <a href="#" title=""><img src="/user/images/resources/search-1.jpg" alt=""></a>
                                                        </div>
                                                        <div class="detail">
                                                            <p>Lorem ipsum dolor sit amet, consectetur ipisicing elit, sed do eiusmod tempor incididunt ut labor... </p>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="user-post">
                                                <div class="friend-info">
                                                    <figure>
                                                        <img src="/user/images/resources/nearly2.jpg" alt="">
                                                    </figure>
                                                    <div class="friend-name">
                                                        <div class="more">
                                                            <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                                <ul>
                                                                    <li><i class="fa fa-comment"></i>Send Message</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <ins><a href="time-line.html" title="">Jack Carter</a> Post</ins>
                                                        <span><i class="fa fa-globe"></i> published: September,15 2020 19:PM </span>

                                                    </div>
                                                    <div class="post-meta searched">
                                                        <div class="linked-image align-right">
                                                            <a href="#" title=""><img src="/user/images/resources/search-3.jpg" alt=""></a>
                                                        </div>
                                                        <div class="detail">
                                                            <p>Lorem ipsum dolor sit amet, consectetur ipisicing elit, sed do eiusmod tempor incididunt ut labor... </p>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- post with image -->
                                    </div>

                                </div>
                                <div class="central-meta item">
                                    <span class="create-post">Related Searches</span>
                                    <ul class="related-searches">
                                        <li><a href="#" title="">jack carter jr.</a></li>
                                        <li><a href="#" title="">jack carter Pool</a></li>
                                        <li><a href="#" title="">jack carter fdny </a></li>
                                        <li><a href="#" title="">jack carter chevrolet cadillac </a></li>
                                        <li><a href="#" title="">jack jack </a></li>
                                    </ul>
                                </div><!-- Related Searches -->
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <aside class="sidebar">
                                <div class="wiki-box">
                                    <h4>
                                        <img src="/user/images/wiki.png" alt="">
                                        Content from the Wikipedia article <a href="https://en.wikipedia.org/wiki/Jack_Carter_(politician)" title="" target="_blank">Jack Carter</a>
                                    </h4>
                                    <p>
                                        John William Carter is an American businessman and politician who unsuccessfully ran for the United States Senate in Nevada in 2006.
                                        <span>Born:</span> July 3, 1947 (age 72) <span>Education:</span> Emory University, Georgia Institute of Technology, Georgia Southwestern State University
                                        <a class="underline" href="https://en.wikipedia.org/wiki/Jack_Carter_(politician)" target="_blank" title="">Read More</a>
                                    </p>
                                    <div class="helpful">
                                        <span>Was this information helpful?</span>
                                        <ul class="add-remove-frnd">
                                            <li class="add-tofrndlist">
                                                <a href="#" title="Add friend"><i class="fa fa-thumbs-up"></i></a>
                                            </li>
                                            <li class="remove-frnd">
                                                <a href="#" title="Send Message"><i class="fa fa-thumbs-down"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="advertisment-box stick-widget">
                                    <h4 class="">advertisment</h4>
                                    <figure><a href="#" title=""><img src="/user/images/ad-baner.jpg" alt=""></a></figure>
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Map tab id to corresponding typeFind value
        const typeFindMap = {
            'people': 'user',
            'pages': 'page',
            'groups': 'group'
        };

        // Store initial end_cursor values from blade variables
        let endCursors = {
            'people': "{{ $users->end_cursor }}",
            'pages': "{{ $pages->end_cursor }}",
            'groups': "{{ $groups->end_cursor }}"
        };

        let isLoading = false;

        function showLoading() {
            console.log("Hiển thị loading...");
            $('body').append(`
                <div id="loadingOverlay">
                    <div class="loader"></div>
                </div>
            `);
        }

        function hideLoading() {
            console.log("Ẩn loading...");
            $('#loadingOverlay').remove();
        }

        // Generate HTML for a "People" item
        function generateUserHtml(user) {
            let html = '<div class="pit-friends">';
            html += '<figure><a href="#" title=""><img src="' + user.avatar + '" alt=""></a></figure>';
            html += '<div class="pit-frnz-meta">';
            html += '<a href="#" title="">' + user.name + '</a>';
            html += '<i>' + user.text + '</i>';
            html += '<ul class="add-remove-frnd">';
            html += '<li class="add-tofrndlist"><a title="Add friend" href="#"><i class="fa fa-user-plus" data-uid="' + user.uid + '"></i></a></li>';
            html += '<li class="remove-frnd"><a title="Send Message" href="#"><i class="fa fa-comment"></i></a></li>';
            html += '</ul>';
            html += '</div>';
            html += '</div>';
            return html;
        }

        // Generate HTML for a "Pages" item
        function generatePageHtml(page) {
            let html = '<div class="pit-pages">';
            html += '<figure><a href="#" title=""><img src="' + page.avatar + '" alt=""></a></figure>';
            html += '<div class="pit-pages-meta">';
            html += '<a href="#" title="">' + page.name + '</a>';
            html += '<i>' + page.text + '</i>';
            html += '<ul class="add-remove-frnd">';
            html += '<li class="add-tofrndlist"><a title="Like" href="#"><i class="fa fa-thumbs-up"></i> Like</a></li>';
            html += '<li class="remove-frnd"><a title="Follow" href="#"><i class="fa fa-star"></i></a></li>';
            html += '</ul>';
            html += '</div>';
            html += '</div>';
            return html;
        }

        // Generate HTML for a "Groups" item
        function generateGroupHtml(group) {
            let html = '<div class="pit-groups">';
            html += '<figure><a href="#" title=""><img src="' + group.avatar + '" alt=""></a></figure>';
            html += '<div class="pit-groups-meta">';
            html += '<a href="#" title="">' + group.name + '</a>';
            html += '<i>' + group.text + '</i>';
            html += '<ul class="add-remove-frnd">';
            html += '<li class="add-tofrndlist"><a title="Add friend" href="#"><i class="fa fa-plus"></i> Join</a></li>';
            html += '</ul>';
            html += '</div>';
            html += '</div>';
            return html;
        }

        function loadMoreResults($tabPane, typeFind) {
            const keySearch = "{{ $keySearch }}";
            // Retrieve current end_cursor from the element's data attribute or initial value
            let currentCursor = $tabPane.data('end-cursor') || endCursors[$tabPane.attr('id')];
            console.log("Loading more for", $tabPane.attr('id'), "with cursor:", currentCursor);
            if (!currentCursor) {
                console.log("No more results for", $tabPane.attr('id'));
                return;
            }
            isLoading = true;
            showLoading(); // Show loading indicator before starting AJAX call
            $.ajax({
                url: "{{ route('android.searchResult', ['uid' => $uid]) }}",
                method: 'GET',
                dataType: 'json',
                data: {
                    'key-search': keySearch,
                    'end_cursor': currentCursor,
                    'typeFind': typeFind
                },
                success: function(response) {
                    console.log("AJAX response:", response);
                    if (response.status && response.list_user && response.list_user.length) {
                        let html = "";
                        if (typeFind === "user") {
                            $.each(response.list_user, function(index, item) {
                                html += generateUserHtml(item);
                            });
                        } else if (typeFind === "page") {
                            $.each(response.list_user, function(index, item) {
                                html += generatePageHtml(item);
                            });
                        } else if (typeFind === "group") {
                            $.each(response.list_user, function(index, item) {
                                html += generateGroupHtml(item);
                            });
                        }
                        // Append the generated HTML to the current tab-pane's container
                        $tabPane.find('.central-meta.item').append(html);
                        // Update the end_cursor for subsequent pagination requests
                        $tabPane.data('end-cursor', response.end_cursor);
                        console.log("Updated cursor for", $tabPane.attr('id'), "to:", response.end_cursor);
                    } else {
                        console.log("No new items returned.");
                        $tabPane.data('end-cursor', '');
                    }
                    isLoading = false;
                    hideLoading(); // Hide loading indicator on success
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", status, error);
                    isLoading = false;
                    hideLoading(); // Hide loading indicator on error
                }
            });
        }

        // Listen for scroll events to trigger lazy loading
        $(window).on('scroll', function() {
            let scrollPos = $(window).scrollTop() + $(window).height();
            console.log("Scroll pos:", scrollPos, "Document height:", $(document).height());
            // Find the currently active tab-pane (skip the "All" tab)
            let $activeTab = $('.tab-pane.active.fade.show');
            if (!$activeTab.length) {
                console.log("No active tab found.");
                return;
            }
            let tabId = $activeTab.attr('id');
            if (tabId === 'All') {
                console.log("Active tab is All; skipping lazy load.");
                return;
            }
            // If user scrolled near the bottom, load more results
            if (scrollPos >= $(document).height() - 100) {
                if (isLoading) {
                    console.log("Already loading...");
                    return;
                }
                let typeFind = typeFindMap[tabId];
                if (!typeFind) {
                    console.log("No typeFind mapped for tab:", tabId);
                    return;
                }
                loadMoreResults($activeTab, typeFind);
            }
        });
    });

    $(document).on('click', '#people  .pit-friends .add-tofrndlist a', function(e) {
            e.preventDefault();
            const uid_invitation = $(this).find('i').data('uid');
            const $thisLink = $(this);
            const $icon = $thisLink.find('i');

            // Change icon to spinner
            $icon.removeClass('fa-user-plus').addClass('fa-spinner fa-spin');

            $.ajax({
                url: '{{ route("profile.addFriend", ":uid") }}'.replace(':uid', {{ $data["account"]->uid }}),
                type: 'POST',
                data: {
                    uid_invitation: uid_invitation
                },
                success: function(response) {
                    if (response.response === true) {
                        $thisLink.closest('.add-tofrndlist').html(`
                            <a href="#" title="Friend Request Sent" class="friend-sent">
                                <i class="fa fa-check" style="color: green;"></i>
                            </a>
                        `);
                        toastr.success('Gửi lời mời kết bạn thành công', 'Thành công');
                    } else {
                        toastr.error(response.response || 'Gửi lời mời kết bạn thất bại', 'Lỗi');
                        $icon.removeClass('fa-spinner fa-spin').addClass('fa-user-plus');
                    }
                },
                error: function(xhr) {
                    console.error('Lỗi khi gửi lời mời:', xhr.responseText);
                    $icon.removeClass('fa-spinner fa-spin').addClass('fa-user-plus');
                    toastr.error('Có lỗi xảy ra. Vui lòng thử lại.', 'Lỗi');
                }
            });
        });

        // Join Group handler (for Groups tab only)
        $(document).on('click', '#groups .pit-groups .add-tofrndlist a', function(e) {
            e.preventDefault();
            const groupUid = $(this).find('i').data('uid');
            const $thisLink = $(this);
            const $icon = $thisLink.find('i');

            // Change icon to spinner
            $icon.removeClass('fa-plus').addClass('fa-spinner fa-spin');

            $.ajax({
                url: '{{ route("profile.joinGroup", ":uid") }}'.replace(':uid', {{ $data["account"]->uid }}),
                type: 'POST',
                data: {
                    uidGroup: groupUid
                },
                success: function(response) {
                    if (response.response && response.response.status === false) {
                        toastr.error(response.response.message || 'Join group failed', 'Error');
                        $icon.removeClass('fa-spinner fa-spin').addClass('fa-plus');
                    } else {
                        $thisLink.closest('.add-tofrndlist').html(`
                            <a href="#" title="Joined Group" class="group-joined">
                                <i class="fa fa-check" style="color: green;"></i> Joined
                            </a>
                        `);
                        toastr.success('Joined group successfully', 'Success');
                    }
                },
                error: function(xhr) {
                    console.error('Error joining group:', xhr.responseText);
                    $icon.removeClass('fa-spinner fa-spin').addClass('fa-plus');
                    toastr.error('Có lỗi xảy ra khi tham gia nhóm. Vui lòng thử lại.', 'Lỗi');
                }
            });
        });
    ;


    $(document).on('click', '.pit-pages .add-tofrndlist a', function(e) {
        e.preventDefault();
        var pageUid = $(this).find('i').data('uid');
        sendLikeFollow(pageUid, 'like', $(this));
    });

    // Xử lý sự kiện click cho nút Follow
    $(document).on('click', '.pit-pages .remove-frnd a', function(e) {
        e.preventDefault();
        var pageUid = $(this).find('i').data('uid');
        sendLikeFollow(pageUid, 'follow', $(this));
    });

    function sendLikeFollow(pageUid, action, $element) {
        // Lấy uid của account từ biến Blade (đảm bảo biến $data["account"] tồn tại trong Blade)
        var accountUid = {{ $data["account"]->uid }};
        var url = '{{ route("profile.likeFollow", ":uid") }}'.replace(':uid', accountUid);
        
        // Thêm hiệu ứng spinner (tùy chọn)
        var $icon = $element.find('i');
        if (action === 'like') {
            $icon.removeClass('fa-thumbs-up').addClass('fa-spinner fa-spin');
        } else if (action === 'follow') {
            $icon.removeClass('fa-user-plus').addClass('fa-spinner fa-spin');
        }
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                action: action,
                page_id: pageUid
            },
            success: function(response) {
                if(response.response === 'Thành công'){
                    toastr.success(action === 'like' ? 'Đã Like Page' : 'Đã Follow Page', 'Thành công', { timeOut: 2000 });
                    // Đổi icon spinner thành icon check khi thành công
                    $icon.removeClass('fa-spinner fa-spin fa-thumbs-up fa-user-plus').addClass('fa fa-check');
                    $icon.attr('style', 'color: green;');
                } else {
                    toastr.error(response.response || 'Thao tác thất bại', 'Lỗi');
                    // Khôi phục icon ban đầu nếu không thành công
                    if(action === 'like'){
                        $icon.removeClass('fa-spinner fa-spin').addClass('fa-thumbs-up');
                    } else if(action === 'follow'){
                        $icon.removeClass('fa-spinner fa-spin').addClass('fa-user-plus');
                    }
                }
            },
            error: function(xhr) {
                toastr.error('Có lỗi xảy ra, vui lòng thử lại.', 'Lỗi');
                if(action === 'like'){
                    $icon.removeClass('fa-spinner fa-spin').addClass('fa-thumbs-up');
                } else if(action === 'follow'){
                    $icon.removeClass('fa-spinner fa-spin').addClass('fa-user-plus');
                }
            }
        });
    }

</script>
@endsection