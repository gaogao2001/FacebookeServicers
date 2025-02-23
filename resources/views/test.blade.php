@extends ('user.layouts.master')

@section('title', 'admin')

@section('head.scripts')

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
                                <div class="edit-pp">
                                    <label class="fileContainer">
                                        <i class="fa fa-camera"></i>
                                        <input type="file">
                                    </label>
                                </div>
                                <img src="/user/images/resources/shop-topbg.jpg" alt="">
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
                                <ol class="pit-rate">
                                    <li class="rated"><i class="fa fa-star"></i></li>
                                    <li class="rated"><i class="fa fa-star"></i></li>
                                    <li class="rated"><i class="fa fa-star"></i></li>
                                    <li class="rated"><i class="fa fa-star"></i></li>
                                    <li class=""><i class="fa fa-star"></i></li>
                                    <li><span>4.7/5</span></li>
                                </ol>
                            </figure>

                            <div class="profile-section">
                                <div class="row">
                                    <div class="col-lg-2 col-md-3">
                                        <div class="profile-author">
                                            <div class="profile-author-thumb">
                                                <img alt="author" src="/user/images/resources/shop-dp.jpg">
                                                <div class="edit-dp">
                                                    <label class="fileContainer">
                                                        <i class="fa fa-camera"></i>
                                                        <input type="file">
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="author-content">
                                                <a class="h4 author-name" href="about.html">Mega Mart</a>
                                                <div class="country">Ontario, CA</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-10 col-md-9">
                                        <ul class="profile-menu">
                                            <li>
                                                <a class="active" href="shop.html">Shop</a>
                                            </li>
                                            <li>
                                                <a class="" href="shop2.html">Market Place</a>
                                            </li>
                                            <li>
                                                <a class="" href="shop-cart.html">Cart</a>
                                            </li>
                                            <li>
                                                <a class="" href="shop-checkout.html">Checkout</a>
                                            </li>
                                            <li>
                                                <a class="" href="shop-detail.html">Prod Detail</a>
                                            </li>
                                            <li>
                                                <div class="more">
                                                    <i class="fa fa-ellipsis-h"></i>
                                                    <ul class="more-dropdown">
                                                        <li>
                                                            <a href="timeline-friends2.html">Followers</a>
                                                        </li>
                                                        <li>
                                                            <a href="timeline-photos.html">Photos</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                        </ul>
                                        <ol class="folw-detail">
                                            <li><span>Posts</span><ins>101</ins></li>
                                            <li><span>Followers</span><ins>1.3K</ins></li>
                                            <li><span>Following</span><ins>22</ins></li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div><!-- user profile banner  -->
                        <div class="col-lg-12">
                            <div class="gap2 no-top">
                                <div class="central-meta no-margin">
                                    <ul class="nave-area">
                                        <li><a href="#" title=""><i class="fa fa-home"></i> Home</a></li>
                                        <li><a href="#" title=""><i class="fa fa-toggle-off"></i> used</a></li>
                                        <li><a href="#" title=""><i class="fa fa-heart"></i> New</a></li>
                                        <li><a href="#" title=""><i class="fa fa-clock-o"></i> Save list</a></li>
                                        <li><a href="#" title=""><i class="fa fa-free-code-camp"></i> Popular</a></li>
                                        <li><a href="#" title=""><i class="fa fa-bell-o"></i> Promotions</a></li>
                                        <li><a href="#" title=""><i class="fa fa-clone"></i> Categories</a>
                                            <ul class="mega-menu">
                                                <li><a href="#" title="" data-ripple=""><i class="fa fa-cutlery"><img src="/user/images/resources/blur-image.jpg" alt=""></i><span>Mobile</span></a></li>
                                                <li><a href="#" title="" data-ripple=""><i class="fa fa-television"><img src="/user/images/resources/blur-image2.jpg" alt=""></i><span>Vehicle</span></a></li>
                                                <li><a href="#" title="" data-ripple=""><i class="fa fa-venus-double"><img src="/user/images/resources/blur-image3.jpg" alt=""></i><span>Flat/House</span></a></li>
                                                <li><a href="#" title="" data-ripple=""><i class="fa fa-female"><img src="/user/images/resources/blur-image.jpg" alt=""></i><span>Furniture</span></a></li>
                                                <li><a href="#" title="" data-ripple=""><i class="fa fa-music"><img src="/user/images/resources/blur-image4.jpg" alt=""></i><span>Electronics</span></a></li>

                                                <li><a href="#" title="" data-ripple=""><i class="fa fa-podcast"><img src="/user/images/resources/blur-image.jpg" alt=""></i><span>Services</span></a></li>
                                                <li><a href="#" title="" data-ripple=""><i class="fa fa-grav"><img src="/user/images/resources/blur-image5.jpg" alt=""></i><span>Auto Parts</span></a></li>
                                                <li><a href="#" title="" data-ripple=""><i class="fa fa-plane"><img src="/user/images/resources/blur-image2.jpg" alt=""></i><span>Computer</span></a></li>
                                                <li><a href="#" title="" data-ripple=""><i class="fa fa-newspaper"><img src="/user/images/resources/blur-image4.jpg" alt=""></i><span>Bicycle</span></a></li>
                                                <li><a href="#" title="" data-ripple=""><i class="fa fa-headphones"><img src="/user/images/resources/blur-image3.jpg" alt=""></i><span>Business </span></a></li>
                                                <li><a href="#" title="" data-ripple=""><i class="fa fa-television"><img src="/user/images/resources/blur-image5.jpg" alt=""></i><span>Medical</span></a></li>
                                                <li><a href="#" title="" data-ripple=""><i class="fa fa-film"><img src="/user/images/resources/blur-image.jpg" alt=""></i><span>Movies/Books</span></a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                    <ul class="align-right user-ben">
                                        <li class="search-for">
                                            <a data-ripple="" class="circle-btn search-data" title="" href="#"><i class="ti-search"></i></a>
                                            <form class="searchees" method="post">
                                                <span class="cancel-search"><i class="ti-close"></i></span>
                                                <input type="text" placeholder="Search in Posts">
                                                <button type="submit"></button>
                                            </form>
                                        </li>
                                        <li class="more">
                                            <a href="#" title="" class="circle-btn" data-ripple=""><i class="fa fa-ellipsis-h"></i>
                                            </a>
                                            <ul class="more-dropdown">
                                                <li>
                                                    <a href="#">Statics</a>
                                                </li>
                                                <li>
                                                    <a href="#">Events</a>
                                                </li>
                                                <li>
                                                    <a href="#">Report Profile</a>
                                                </li>
                                                <li>
                                                    <a href="#">Block Profile</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a href="#" title="Folow us" class="circle-btn" data-ripple=""><i class="fa fa-star"></i></a>
                                        </li>
                                        <li><a href="#" title="" class="main-btn create-pst" data-ripple="">Add New Post</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div><!-- nave list -->
                        <div class="col-lg-3">
                            <aside class="sidebar static left">
                                <div class="widget">
                                    <h4 class="widget-title">Recent Posts <a class="see-all" href="#" title="">see all</a></h4>
                                    <ul class="recent-photos">
                                        <li>
                                            <a href="#" title=""><img src="/user/images/resources/classic-new1.jpg" alt=""></a>
                                        </li>
                                        <li>
                                            <a href="#" title=""><img src="/user/images/resources/classic-new2.jpg" alt=""></a>
                                        </li>
                                        <li>
                                            <a href="#" title=""><img src="/user/images/resources/classic-new3.jpg" alt=""></a>
                                        </li>
                                        <li>
                                            <a href="#" title=""><img src="/user/images/resources/classic-new4.jpg" alt=""></a>
                                        </li>
                                        <li>
                                            <a href="#" title=""><img src="/user/images/resources/classic-new5.jpg" alt=""></a>
                                        </li>
                                        <li>
                                            <a href="#" title=""><img src="/user/images/resources/classic-new6.jpg" alt=""></a>
                                        </li>
                                        <li>
                                            <a href="#" title=""><img src="/user/images/resources/classic-new7.jpg" alt=""></a>
                                        </li>
                                        <li>
                                            <a href="#" title=""><img src="/user/images/resources/classic-new8.jpg" alt=""></a>
                                        </li>
                                        <li>
                                            <a href="#" title=""><img src="/user/images/resources/classic-new9.jpg" alt=""></a>
                                        </li>
                                    </ul>
                                </div><!-- recent post-->
                                <div class="widget">
                                    <h4 class="widget-title">Page Community</h4>
                                    <ul class="fav-community">
                                        <li><i class="fa fa-address-card"></i> About <p>We are motel hotel from Los Angeles, now based in San Francisco, come and enjoy!</p>
                                        </li>
                                        <li><i class="fa fa-users"></i><a href="#" title="">invite friends</a> to like this page</li>
                                        <li><i class="fa fa-thumbs-up"></i>13,33,454 People like this</li>
                                        <li><i class="fa fa-rss"></i>13,33,454 People follow this</li>
                                        <li><i class="fa fa-share-alt"></i>13,540 People share this</li>
                                        <li><i class="fa fa-bookmark"></i><a href="#" title="">category</a> Entertainment</li>
                                        <li><i class="fa fa-globe"></i><a href="https://wpkixx.com/" title="">www.wpkixx.com</a></li>
                                        <li><i class="fa fa-map-marker"></i><a href="http://wpkixx.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="5b14383e3a350436342f3e371b22342e29363a323775383436">[email&#160;protected]</a></li>
                                    </ul>
                                </div><!-- Page Community-->
                                <div class="widget">
                                    <h4 class="widget-title">Twitter feed</h4>
                                    <ul class="twiter-feed">
                                        <li>
                                            <i class="fa fa-twitter"></i>
                                            <span>
                                                <i>jhon william</i>
                                                @jhonwilliam
                                            </span>
                                            <p>tomorrow with the company we were working and 5 child run away from the working place. <a href="#" title="">#daydream5k</a> </p>
                                            <em>2 hours ago</em>
                                        </li>
                                        <li>
                                            <i class="fa fa-twitter"></i>
                                            <span>
                                                <i>Kelly watson</i>
                                                @kelly
                                            </span>
                                            <p>tomorrow with the company we were working and 5 child run away from the working place. <a href="#" title="">#daydream5k</a> </p>
                                            <em>2 hours ago</em>
                                        </li>
                                        <li>
                                            <i class="fa fa-twitter"></i>
                                            <span>
                                                <i>Jony bravo</i>
                                                @jonibravo
                                            </span>
                                            <p>tomorrow with the company we were working and 5 child run away from the working place. <a href="#" title="">#daydream5k</a> </p>
                                            <em>2 hours ago</em>
                                        </li>
                                    </ul>
                                </div><!-- twitter feed-->
                                <div class="advertisment-box">
                                    <h4 class="">advertisment</h4>
                                    <figure>
                                        <a href="#" title="Advertisment"><img src="/user/images/resources/ad-widget.gif" alt=""></a>
                                    </figure>
                                </div><!-- ad banner -->
                                <div class="widget stick-widget">
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
                            </aside>
                        </div><!-- sidebar -->
                        <div class="col-lg-6">
                            <div class="central-meta item">
                                <div class="classic-post">
                                    <figure>
                                        <img src="/user/images/resources/classic10.jpg" alt="">
                                        <span>Super Hot</span>
                                    </figure>
                                    <div class="classic-pst-meta">
                                        <div class="more">
                                            <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                <ul>
                                                    <li><i class="fa fa-pencil-square-o"></i>Edit Post</li>
                                                    <li><i class="fa fa-trash-o"></i>Delete Post</li>
                                                    <li class="bad-report"><i class="fa fa-flag"></i>Report This Post</li>
                                                    <li><i class="fa fa-clock-o"></i>Schedule Post</li>
                                                    <li><i class="fa fa-wpexplorer"></i>Select as featured</li>
                                                    <li><i class="fa fa-bell-slash-o"></i>Turn off Notifications</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <h4><i class="fa fa-check-circle" title="verified"></i> <a href="#" title=""> Yamaha Bike 450cc Red Matalic</a></h4>
                                        <p>Beautiful Rider bike for sale. 450cc new tyre long seat... </p>
                                        <span class="prise">$10,000</span>
                                        <div class="location-area">
                                            <i>Last Updated: Dec,12 2020</i>
                                            <span><i class="fa fa-map-marker"></i> onatrio, Canada</span>
                                            <a href="#" class="main-btn" title="">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- shop product post -->
                            <div class="central-meta">
                                <span class="create-post">Featured Posts <a href="#" title="">See All</a></span>
                                <ul class="suggested-frnd-caro">
                                    <li>
                                        <img src="/user/images/resources/classified2.jpg" alt="">
                                        <div class="sugtd-frnd-meta">
                                            <a href="#" title="">teddy bear China</a>
                                            <span>2 mutual friend</span>
                                            <ul class="add-remove-frnd">
                                                <li class="add-tofrndlist"><a href="#" title="Add friend"><i class="fa fa-star"></i></a></li>
                                                <li class="remove-frnd send-mesg"><a href="#" title="remove friend"><i class="fa fa-comment"></i></a></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <img src="/user/images/resources/classified3.jpg" alt="">
                                        <div class="sugtd-frnd-meta">
                                            <a href="#" title="">MB Cycle</a>
                                            <span><a href="#" title="">Emmy</a> is mutual friend</span>
                                            <ul class="add-remove-frnd">
                                                <li class="add-tofrndlist"><a href="#" title="Add friend"><i class="fa fa-star"></i></a></li>
                                                <li class="remove-frnd send-mesg"><a href="#" title="remove friend"><i class="fa fa-comment"></i></a></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <img src="/user/images/resources/classified4.jpg" alt="">
                                        <div class="sugtd-frnd-meta">
                                            <a href="#" title="">Leather bag</a>
                                            <span>5 mutual friend</span>
                                            <ul class="add-remove-frnd">
                                                <li class="add-tofrndlist"><a href="#" title="Add friend"><i class="fa fa-star"></i></a></li>
                                                <li class="remove-frnd send-mesg"><a href="#" title="remove friend"><i class="fa fa-comment"></i></a></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <img src="/user/images/resources/classified5.jpg" alt="">
                                        <div class="sugtd-frnd-meta">
                                            <a href="#" title="">Women bag</a>
                                            <span>1 mutual friend</span>
                                            <ul class="add-remove-frnd">
                                                <li class="add-tofrndlist"><a href="#" title="Add friend"><i class="fa fa-star"></i></a></li>
                                                <li class="remove-frnd send-mesg"><a href="#" title="remove friend"><i class="fa fa-comment"></i></a></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <img src="/user/images/resources/classified6.jpg" alt="">
                                        <div class="sugtd-frnd-meta">
                                            <a href="#" title="">Super Glasses</a>
                                            <span>3 mutual friend</span>
                                            <ul class="add-remove-frnd">
                                                <li class="add-tofrndlist"><a href="#" title="Add friend"><i class="fa fa-star"></i></a></li>
                                                <li class="remove-frnd send-mesg"><a href="#" title="remove friend"><i class="fa fa-comment"></i></a></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <img src="/user/images/resources/classified1.jpg" alt="">
                                        <div class="sugtd-frnd-meta">
                                            <a href="#" title="">Iphone X</a>
                                            <span>1 mutual friend</span>
                                            <ul class="add-remove-frnd">
                                                <li class="add-tofrndlist"><a href="#" title="Add friend"><i class="fa fa-star"></i></a></li>
                                                <li class="remove-frnd send-mesg"><a href="#" title="remove friend"><i class="fa fa-comment"></i></a></li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div><!-- suggested friends -->
                            <div class="load-more">
                                <div class="central-meta item">
                                    <div class="classic-post">
                                        <figure>
                                            <img src="/user/images/resources/classic1.jpg" alt="">
                                            <span>Super Hot</span>
                                        </figure>
                                        <div class="classic-pst-meta">
                                            <div class="more">
                                                <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                    <ul>
                                                        <li><i class="fa fa-pencil-square-o"></i>Edit Post</li>
                                                        <li><i class="fa fa-trash-o"></i>Delete Post</li>
                                                        <li class="bad-report"><i class="fa fa-flag"></i>Report This Post</li>
                                                        <li><i class="fa fa-clock-o"></i>Schedule Post</li>
                                                        <li><i class="fa fa-wpexplorer"></i>Select as featured</li>
                                                        <li><i class="fa fa-bell-slash-o"></i>Turn off Notifications</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <h4><i class="fa fa-check-circle" title="verified"></i> <a href="#" title=""> Beautiful Iphone X mobile</a></h4>
                                            <p>Beautiful House loacated at a very simple location </p>
                                            <span class="prise">$30,000</span>
                                            <div class="location-area">
                                                <i>Last Updated: Jan,12 2020</i>
                                                <span><i class="fa fa-map-marker"></i> Toronto, Canada</span>
                                                <a href="#" class="main-btn" title="">Add to Cart</a>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- shop product post -->
                                <div class="central-meta item">
                                    <div class="classic-post">
                                        <figure>
                                            <img src="/user/images/resources/classic2.jpg" alt="">
                                            <span class="yellow">Featured</span>
                                        </figure>
                                        <div class="classic-pst-meta">
                                            <div class="more">
                                                <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                    <ul>
                                                        <li><i class="fa fa-pencil-square-o"></i>Edit Post</li>
                                                        <li><i class="fa fa-trash-o"></i>Delete Post</li>
                                                        <li class="bad-report"><i class="fa fa-flag"></i>Report This Post</li>
                                                        <li><i class="fa fa-clock-o"></i>Schedule Post</li>
                                                        <li><i class="fa fa-wpexplorer"></i>Select as featured</li>
                                                        <li><i class="fa fa-bell-slash-o"></i>Turn off Notifications</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <h4><i class="fa fa-check-circle" title="verified"></i> <a href="#" title=""> Honda 350cc bike for sale</a></h4>
                                            <p>Used conditon honda bike is for sale running 13000km </p>
                                            <span class="prise">$3000</span>
                                            <div class="location-area">
                                                <i>Last Updated: May,21 2020</i>
                                                <span><i class="fa fa-map-marker"></i> Ontario, Canada</span>
                                                <a href="#" class="main-btn" title="">Add to Cart</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="central-meta item">
                                    <div class="classic-post">
                                        <figure>
                                            <img src="/user/images/resources/classic3.jpg" alt="">
                                            <span class="red">New</span>
                                        </figure>
                                        <div class="classic-pst-meta">
                                            <div class="more">
                                                <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                    <ul>
                                                        <li><i class="fa fa-pencil-square-o"></i>Edit Post</li>
                                                        <li><i class="fa fa-trash-o"></i>Delete Post</li>
                                                        <li class="bad-report"><i class="fa fa-flag"></i>Report This Post</li>
                                                        <li><i class="fa fa-clock-o"></i>Schedule Post</li>
                                                        <li><i class="fa fa-wpexplorer"></i>Select as featured</li>
                                                        <li><i class="fa fa-bell-slash-o"></i>Turn off Notifications</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <h4><i class="fa fa-check-circle" title="verified"></i> <a href="#" title=""> Ladies Hand Bag</a></h4>
                                            <p>Blue beautiful audi car model 2002 is for sale good condition... </p>
                                            <span class="prise">$15,000</span>
                                            <div class="location-area">
                                                <i>Last Updated: June,13 2020</i>
                                                <span><i class="fa fa-map-marker"></i> Ontario, Canada</span>
                                                <a href="#" class="main-btn" title="">Add to Cart</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="central-meta item">
                                    <div class="classic-post">
                                        <figure>
                                            <img src="/user/images/resources/classic4.jpg" alt="">
                                        </figure>
                                        <div class="classic-pst-meta">
                                            <div class="more">
                                                <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                    <ul>
                                                        <li><i class="fa fa-pencil-square-o"></i>Edit Post</li>
                                                        <li><i class="fa fa-trash-o"></i>Delete Post</li>
                                                        <li class="bad-report"><i class="fa fa-flag"></i>Report This Post</li>
                                                        <li><i class="fa fa-clock-o"></i>Schedule Post</li>
                                                        <li><i class="fa fa-wpexplorer"></i>Select as featured</li>
                                                        <li><i class="fa fa-bell-slash-o"></i>Turn off Notifications</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <h4><i class="fa fa-check-circle" title="verified"></i> <a href="#" title=""> Canon DSLR Camera in cheap rate</a></h4>
                                            <p>Dslr camera is available for sale very good condtion one month used... </p>
                                            <span class="prise">$150</span>
                                            <div class="location-area">
                                                <i>Last Updated: Aug,10 2020</i>
                                                <span><i class="fa fa-map-marker"></i> Ontario, Canada</span>
                                                <a href="#" class="main-btn" title="">Add to Cart</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="central-meta item">
                                    <div class="classic-post">
                                        <figure>
                                            <img src="/user/images/resources/classic5.jpg" alt="">
                                            <span>Super Hot</span>
                                        </figure>
                                        <div class="classic-pst-meta">
                                            <div class="more">
                                                <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                    <ul>
                                                        <li><i class="fa fa-pencil-square-o"></i>Edit Post</li>
                                                        <li><i class="fa fa-trash-o"></i>Delete Post</li>
                                                        <li class="bad-report"><i class="fa fa-flag"></i>Report This Post</li>
                                                        <li><i class="fa fa-clock-o"></i>Schedule Post</li>
                                                        <li><i class="fa fa-wpexplorer"></i>Select as featured</li>
                                                        <li><i class="fa fa-bell-slash-o"></i>Turn off Notifications</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <h4><i class="fa fa-check-circle" title="verified"></i> <a href="#" title=""> CAT Leather Bag</a></h4>
                                            <p>White BMW car Model 1998 225000Km running </p>
                                            <span class="prise">$3500</span>
                                            <div class="location-area">
                                                <i>Last Updated: Jan,12 2020</i>
                                                <span><i class="fa fa-map-marker"></i> Toronto, Canada</span>
                                                <a href="#" class="main-btn" title="">Add to Cart</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="central-meta item">
                                    <div class="classic-post">
                                        <figure>
                                            <img src="/user/images/resources/classic6.jpg" alt="">
                                            <span class="yellow">Featured</span>
                                        </figure>
                                        <div class="classic-pst-meta">
                                            <div class="more">
                                                <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                    <ul>
                                                        <li><i class="fa fa-pencil-square-o"></i>Edit Post</li>
                                                        <li><i class="fa fa-trash-o"></i>Delete Post</li>
                                                        <li class="bad-report"><i class="fa fa-flag"></i>Report This Post</li>
                                                        <li><i class="fa fa-clock-o"></i>Schedule Post</li>
                                                        <li><i class="fa fa-wpexplorer"></i>Select as featured</li>
                                                        <li><i class="fa fa-bell-slash-o"></i>Turn off Notifications</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <h4><i class="fa fa-check-circle" title="verified"></i> <a href="#" title=""> Police Glasses for Men </a></h4>
                                            <p>Blue matelic car old for sale. good condition new poshish </p>
                                            <span class="prise">$2200</span>
                                            <div class="location-area">
                                                <i>Last Updated: Jan,12 2020</i>
                                                <span><i class="fa fa-map-marker"></i> Toronto, Canada</span>
                                                <a href="#" class="main-btn" title="">Add to Cart</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="central-meta item">
                                    <div class="classic-post">
                                        <figure>
                                            <img src="/user/images/resources/classic7.jpg" alt="">
                                            <span class="red">New</span>
                                        </figure>
                                        <div class="classic-pst-meta">
                                            <div class="more">
                                                <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                    <ul>
                                                        <li><i class="fa fa-pencil-square-o"></i>Edit Post</li>
                                                        <li><i class="fa fa-trash-o"></i>Delete Post</li>
                                                        <li class="bad-report"><i class="fa fa-flag"></i>Report This Post</li>
                                                        <li><i class="fa fa-clock-o"></i>Schedule Post</li>
                                                        <li><i class="fa fa-wpexplorer"></i>Select as featured</li>
                                                        <li><i class="fa fa-bell-slash-o"></i>Turn off Notifications</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <h4><i class="fa fa-check-circle" title="verified"></i> <a href="#" title=""> Bicycle BMW new for sale </a></h4>
                                            <p>Red bicycle bmw for sale. new condition </p>
                                            <span class="prise">$300</span>
                                            <div class="location-area">
                                                <i>Last Updated: Jan,12 2020</i>
                                                <span><i class="fa fa-map-marker"></i> Toronto, Canada</span>
                                                <a href="#" class="main-btn" title="">Add to Cart</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="central-meta item">
                                    <div class="classic-post">
                                        <figure>
                                            <img src="/user/images/resources/classic8.jpg" alt="">
                                            <span class="red">New</span>
                                        </figure>
                                        <div class="classic-pst-meta">
                                            <div class="more">
                                                <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                    <ul>
                                                        <li><i class="fa fa-pencil-square-o"></i>Edit Post</li>
                                                        <li><i class="fa fa-trash-o"></i>Delete Post</li>
                                                        <li class="bad-report"><i class="fa fa-flag"></i>Report This Post</li>
                                                        <li><i class="fa fa-clock-o"></i>Schedule Post</li>
                                                        <li><i class="fa fa-wpexplorer"></i>Select as featured</li>
                                                        <li><i class="fa fa-bell-slash-o"></i>Turn off Notifications</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <h4><i class="fa fa-check-circle" title="verified"></i> <a href="#" title=""> Exercise cycle machine </a></h4>
                                            <p>cycle running machine for sale in good condition...</p>
                                            <span class="prise">$800</span>
                                            <div class="location-area">
                                                <i>Last Updated: Mar,11 2020</i>
                                                <span><i class="fa fa-map-marker"></i> Toronto, Canada</span>
                                                <a href="#" class="main-btn" title="">Add to Cart</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="central-meta item">
                                    <div class="classic-post">
                                        <figure>
                                            <img src="/user/images/resources/classic9.jpg" alt="">
                                            <span class="">Super Hot</span>
                                        </figure>
                                        <div class="classic-pst-meta">
                                            <div class="more">
                                                <div class="more-post-optns"><i class="ti-more-alt"></i>
                                                    <ul>
                                                        <li><i class="fa fa-pencil-square-o"></i>Edit Post</li>
                                                        <li><i class="fa fa-trash-o"></i>Delete Post</li>
                                                        <li class="bad-report"><i class="fa fa-flag"></i>Report This Post</li>
                                                        <li><i class="fa fa-clock-o"></i>Schedule Post</li>
                                                        <li><i class="fa fa-wpexplorer"></i>Select as featured</li>
                                                        <li><i class="fa fa-bell-slash-o"></i>Turn off Notifications</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <h4><i class="fa fa-check-circle" title="verified"></i> <a href="#" title=""> Dell Laptop for sale </a></h4>
                                            <p>Dell Laptop N5050 for sale 3 months used 8GB ram...</p>
                                            <span class="prise">$700</span>
                                            <div class="location-area">
                                                <i>Last Updated: Mar,11 2020</i>
                                                <span><i class="fa fa-map-marker"></i> Toronto, Canada</span>
                                                <a href="#" class="main-btn" title="">Add to Cart</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- centerl meta -->
                        <div class="col-lg-3">
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
                                <div class="widget">
                                    <h4 class="widget-title">Your page</h4>
                                    <div class="your-page">
                                        <figure>
                                            <a href="#" title=""><img src="/user/images/resources/friend-avatar9.jpg" alt=""></a>
                                        </figure>
                                        <div class="page-meta">
                                            <a href="#" title="" class="underline">My Creative Page</a>
                                            <span><i class="ti-comment"></i><a href="insight.html" title="">Messages <em class="bg-blue">9</em></a></span>
                                            <span><i class="ti-bell"></i><a href="insight.html" title="">Notifications <em class="bg-purple">2</em></a></span>
                                        </div>
                                        <ul class="page-publishes">
                                            <li>
                                                <span><i class="ti-pencil-alt"></i>Publish</span>
                                            </li>
                                            <li>
                                                <span><i class="ti-camera"></i>Photo</span>
                                            </li>
                                            <li>
                                                <span><i class="ti-video-camera"></i>Live</span>
                                            </li>
                                            <li>
                                                <span><i class="fa fa-user-plus"></i>Invite</span>
                                            </li>
                                        </ul>
                                        <div class="page-likes">
                                            <ul class="nav nav-tabs likes-btn">
                                                <li class="nav-item"><a class="active" href="#link1" data-toggle="tab" data-ripple="">likes</a></li>
                                                <li class="nav-item"><a class="" href="#link2" data-toggle="tab" data-ripple="">views</a></li>
                                            </ul>
                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <div class="tab-pane active fade show " id="link1">
                                                    <span><i class="ti-heart"></i>884</span>
                                                    <a href="#" title="weekly-likes">35 new likes this week</a>
                                                    <div class="users-thumb-list">
                                                        <a href="#" title="Anderw" data-toggle="tooltip">
                                                            <img src="/user/images/resources/userlist-1.jpg" alt="">
                                                        </a>
                                                        <a href="#" title="frank" data-toggle="tooltip">
                                                            <img src="/user/images/resources/userlist-2.jpg" alt="">
                                                        </a>
                                                        <a href="#" title="Sara" data-toggle="tooltip">
                                                            <img src="/user/images/resources/userlist-3.jpg" alt="">
                                                        </a>
                                                        <a href="#" title="Amy" data-toggle="tooltip">
                                                            <img src="/user/images/resources/userlist-4.jpg" alt="">
                                                        </a>
                                                        <a href="#" title="Ema" data-toggle="tooltip">
                                                            <img src="/user/images/resources/userlist-5.jpg" alt="">
                                                        </a>
                                                        <a href="#" title="Sophie" data-toggle="tooltip">
                                                            <img src="/user/images/resources/userlist-6.jpg" alt="">
                                                        </a>
                                                        <a href="#" title="Maria" data-toggle="tooltip">
                                                            <img src="/user/images/resources/userlist-7.jpg" alt="">
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="link2">
                                                    <span><i class="fa fa-eye"></i>440</span>
                                                    <a href="#" title="weekly-likes">440 new views this week</a>
                                                    <div class="users-thumb-list">
                                                        <a href="#" title="Anderw" data-toggle="tooltip">
                                                            <img src="/user/images/resources/userlist-1.jpg" alt="">
                                                        </a>
                                                        <a href="#" title="frank" data-toggle="tooltip">
                                                            <img src="/user/images/resources/userlist-2.jpg" alt="">
                                                        </a>
                                                        <a href="#" title="Sara" data-toggle="tooltip">
                                                            <img src="/user/images/resources/userlist-3.jpg" alt="">
                                                        </a>
                                                        <a href="#" title="Amy" data-toggle="tooltip">
                                                            <img src="/user/images/resources/userlist-4.jpg" alt="">
                                                        </a>
                                                        <a href="#" title="Ema" data-toggle="tooltip">
                                                            <img src="/user/images/resources/userlist-5.jpg" alt="">
                                                        </a>
                                                        <a href="#" title="Sophie" data-toggle="tooltip">
                                                            <img src="/user/images/resources/userlist-6.jpg" alt="">
                                                        </a>
                                                        <a href="#" title="Maria" data-toggle="tooltip">
                                                            <img src="/user/images/resources/userlist-7.jpg" alt="">
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- page like widget -->
                                <div class="widget">
                                    <div class="weather-widget low-opacity bluesh">
                                        <div class="bg-image" style="background-image: url(/user/images/resources/weather.jpg)"></div>
                                        <span class="refresh-content"><i class="fa fa-refresh"></i></span>
                                        <div class="weather-week">
                                            <div class="icon sun-shower">
                                                <div class="cloud"></div>
                                                <div class="sun">
                                                    <div class="rays"></div>
                                                </div>
                                                <div class="rain"></div>
                                            </div>
                                        </div>
                                        <div class="weather-infos">
                                            <span class="weather-tem">25</span>
                                            <h3>Cloudy Skyes<i>Sicklervilte, New Jersey</i></h3>
                                            <div class="weather-date skyblue-bg">
                                                <span>MAY<strong>21</strong></span>
                                            </div>
                                        </div>
                                        <div class="monthly-weather">
                                            <ul>
                                                <li>
                                                    <span>Sun</span>
                                                    <a href="#" title=""><i class="wi wi-day-sunny"></i></a>
                                                    <em>40°</em>
                                                </li>
                                                <li>
                                                    <span>Mon</span>
                                                    <a href="#" title=""><i class="wi wi-day-cloudy"></i></a>
                                                    <em>10°</em>
                                                </li>
                                                <li>
                                                    <span>Tue</span>
                                                    <a href="#" title=""><i class="wi wi-day-hail"></i></a>
                                                    <em>20°</em>
                                                </li>
                                                <li>
                                                    <span>Wed</span>
                                                    <a href="#" title=""><i class="wi wi-day-lightning"></i></a>
                                                    <em>34°</em>
                                                </li>
                                                <li>
                                                    <span>Thu</span>
                                                    <a href="#" title=""><i class="wi wi-day-showers"></i></a>
                                                    <em>22°</em>
                                                </li>
                                                <li>
                                                    <span>Fri</span>
                                                    <a href="#" title=""><i class="wi wi-day-windy"></i></a>
                                                    <em>26°</em>
                                                </li>
                                                <li>
                                                    <span>Sat</span>
                                                    <a href="#" title=""><i class="wi wi-day-sunny-overcast"></i></a>
                                                    <em>30°</em>
                                                </li>
                                            </ul>
                                        </div>

                                    </div><!-- Weather Widget -->
                                </div><!-- weather-->
                                <div class="widget">
                                    <h4 class="widget-title">Invite Friends <a class="see-all" href="#" title="">See All</a></h4>
                                    <ul class="invitepage">
                                        <li>
                                            <figure>
                                                <img src="/user/images/resources/friend-avatar.jpg" alt="">
                                                <a href="#">Jack carter</a>
                                            </figure>
                                            <a class="invited" href="#" title=""><i class="ti-check"></i></a>
                                        </li>
                                        <li>
                                            <figure>
                                                <img src="/user/images/resources/friend-avatar2.jpg" alt="">
                                                <a href="#">Emma watson</a>
                                            </figure>
                                            <a href="#" title="">Invite</a>
                                        </li>
                                        <li>
                                            <figure>
                                                <img src="/user/images/resources/friend-avatar3.jpg" alt="">
                                                <a href="#">Andrew</a>
                                            </figure>
                                            <a href="#" title="">Invite</a>
                                        </li>
                                        <li>
                                            <figure>
                                                <img src="/user/images/resources/friend-avatar4.jpg" alt="">
                                                <a href="#">Moona Singh</a>
                                            </figure>
                                            <a class="invited" href="#" title=""><i class="ti-check"></i></a>
                                        </li>
                                        <li>
                                            <figure>
                                                <img src="/user/images/resources/friend-avatar5.jpg" alt="">
                                                <a href="#">Harry pooter</a>
                                            </figure>
                                            <a href="#" title="">Invite</a>
                                        </li>
                                    </ul>

                                </div><!-- page invitation widget -->
                                <div class="widget stick-widget">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</section><!-- content -->


@endsection

@section('footer.scripts')

@endsection