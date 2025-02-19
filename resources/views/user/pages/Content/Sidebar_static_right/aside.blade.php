<style>
    .carousel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
    }

    .carousel-header .prev-btn,
    .carousel-header .next-btn {
        font-size: 24px;
        /* Kích thước mũi tên */
        font-weight: bold;
        text-decoration: none;
        color: #333;
        /* Màu sắc mũi tên */
        transition: color 0.3s ease;
    }

    .carousel-header .prev-btn:hover,
    .carousel-header .next-btn:hover {
        color: #007bff;
        /* Màu sắc khi hover */
        cursor: pointer;
    }

    .title {
        flex-grow: 1;
        text-align: center;

        font-size: 18px;
        font-weight: bold;
        margin: 0;
    }
</style>

<aside class="sidebar static right">

    <div id="pageCarousel" class="carousel slide widget" data-ride="carousel" data-interval="5000">
        <div class="carousel-header d-flex justify-content-between align-items-center">
            <a class="prev-btn" href="#pageCarousel" role="button" data-slide="prev">
                &laquo; <!-- << -->
            </a>
            <h4 class="title">Page</h4>
            <a class="next-btn" href="#pageCarousel" role="button" data-slide="next">
                &raquo; <!-- >> -->
            </a>
        </div>
        <div class="carousel-inner">
			@if(!empty($data['account']->MultiAccount))
				@if(is_iterable($data['account']->MultiAccount) && count($data['account']->MultiAccount) > 0)
					@foreach($data['account']->MultiAccount as $key => $page)
					<div class="carousel-item @if($key === 0) active @endif">

						<div class="your-page">
							<figure>
								<a href="#" title=""><img src="{{$page->profile->profile_picture->uri}}" alt=""></a>
							</figure>
							<div class="page-meta">
								<a href="{{ route('android.main', ['uid' => $page->profile->id]) }}" title="" class="underline">{{$page->profile->name}}</a>
								<span><i class="ti-comment"></i><a href="insight.html" title="">Messages <em>9</em></a></span>
								<span><i class="ti-bell"></i><a href="insight.html" title="">Notifications <em>2</em></a></span>
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
									<li class="nav-item"><a class="active" href="#link1_{{$key}}" data-toggle="tab" data-ripple="">likes</a></li>
									<li class="nav-item"><a class="" href="#link2_{{$key}}" data-toggle="tab" data-ripple="">views</a></li>
								</ul>
								<!-- Tab panes -->
								<div class="tab-content">
									<div class="tab-pane active fade show" id="link1_{{$key}}">
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
									<div class="tab-pane fade" id="link2_{{$key}}">
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
					</div>
					@endforeach
				@else
					<p>The provided data is not iterable.</p>
				@endif
			@else
				<p>No account data available.</p>
			@endif
		</div>




        <!-- Carousel Controls -->
        <a class="carousel-control-prev" href="#pageCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#pageCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <div class="widget">
        <h4 class="widget-title">Explor Events <a title="" href="#" class="see-all">See All</a></h4>
        <div class="rec-events bg-purple">
            <i class="ti-gift"></i>
            <h6><a href="#" title="">Ocean Motel good night event in columbia</a></h6>
            <img src="/user/images/clock.png" alt="">
        </div>
        <div class="rec-events bg-blue">
            <i class="ti-microphone"></i>
            <h6><a href="#" title="">2016 The 3rd International Conference</a></h6>
            <img src="/user/images/clock.png" alt="">
        </div>
    </div><!-- explore events -->
    <div class="widget">
        <h4 class="widget-title">Profile intro</h4>
        <ul class="short-profile">
            <li>
                <span>about</span>
                <p>Hi, i am jhon kates, i am 32 years old and worked as a web developer in microsoft </p>
            </li>
            <li>
                <span>fav tv show</span>
                <p>Sacred Games, Spartcus Blood, Games of Theron </p>
            </li>
            <li>
                <span>favourit music</span>
                <p>Justin Biber, Shakira, Nati Natasah</p>
            </li>
        </ul>
    </div><!-- profile intro widget -->
    <div class="widget stick-widget">
        <h4 class="widget-title">Recent Links <a title="" href="#" class="see-all">See All</a></h4>
        <ul class="recent-links">
            <li>
                <figure><img src="/user/images/resources/recentlink-1.jpg" alt=""></figure>
                <div class="re-links-meta">
                    <h6><a href="#" title="">moira's fade reaches much farther than you think.</a></h6>
                    <span>2 weeks ago </span>
                </div>
            </li>
            <li>
                <figure><img src="/user/images/resources/recentlink-2.jpg" alt=""></figure>
                <div class="re-links-meta">
                    <h6><a href="#" title="">daniel asks if we want him to do the voice of doomfist</a></h6>
                    <span>3 months ago </span>
                </div>
            </li>
            <li>
                <figure><img src="/user/images/resources/recentlink-3.jpg" alt=""></figure>
                <div class="re-links-meta">
                    <h6><a href="#" title="">the pitnik overwatch scandals.</a></h6>
                    <span>1 day before</span>
                </div>
            </li>
        </ul>
    </div><!-- recent links -->


</aside>

<script>
    $(document).ready(function() {
        $('#pageCarousel').carousel({
            interval: 5000 // Tự động chuyển mỗi 5 giây
        });

        // Dừng carousel khi hover
        $('#pageCarousel').on('mouseenter', function() {
            $(this).carousel('pause');
        });

        // Tiếp tục carousel khi không hover
        $('#pageCarousel').on('mouseleave', function() {
            $(this).carousel('cycle');
        });
    });
</script>