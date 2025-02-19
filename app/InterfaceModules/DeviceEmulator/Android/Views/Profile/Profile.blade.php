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
						<div class="col-lg-4 col-md-4">
							<aside class="sidebar">
								<div class="central-meta stick-widget">
									<span class="create-post">Thôngng tin cá nhân</span>
									<div class="personal-head">

										<span class="f-title"><i class="fa fa-birthday-cake"></i>
											Ngày sinh:</span>
										<p>
											{{ is_string($data['account']->birthday) ? $data['account']->birthday : null }}
										</p>

										@php
											$emails = json_decode($data['account']->email, true);
										@endphp

										@if(is_array($emails))
											<span class="f-title"><i class="fa fa-handshake-o"></i> Email:</span>
											@foreach($emails as $email)
											<p>{{ $email }}</p>
											@endforeach
										@endif
										@foreach($data['info']->profile_info as $info)
											@if($info->name === 'INTRO_CARD_WORK')
												<span class="f-title"><i class="fa fa-briefcase"></i> Làm việc tại:</span>
												<p>{{ $info->text }}</p>
											@elseif($info->name === 'INTRO_CARD_EDUCATION')
												<span class="f-title"><i class="fa fa-graduation-cap"></i> Học ở:</span>
												<p>{{ $info->text }}</p>
											@elseif($info->name === 'INTRO_CARD_CURRENT_CITY')
												<span class="f-title"><i class="fa fa-map-marker"></i> Sống tại:</span>
												<p>{{ $info->text }}</p>
											@elseif($info->name === 'INTRO_CARD_HOMETOWN')
												<span class="f-title"><i class="fa fa-medkit"></i> Đến từ:</span>
												<p>{{ $info->text }}</p>
											@endif
										@endforeach

										</p>
										<!-- <span class="f-title"><i class="fa fa-medkit"></i> Blood group:</span>
										<p>
											B+
										</p>
										<span class="f-title"><i class="fa fa-male"></i>Giới Tính :</span>
										<p>
											Male
										</p>
										<span class="f-title"><i class="fa fa-globe"></i> Thành phố:</span>
										<p>
											San Francisco, California, USA
										</p>
										<span class="f-title"><i class="fa fa-briefcase"></i> Nghề nghiệp:</span>
										<p>
											UI/UX Designer
										</p>
										<span class="f-title"><i class="fa fa-handshake-o"></i> Tham gia vào Facebook:</span>
										<p>
											December 20, 2001
										</p> -->


										<!-- <span class="f-title"><i class="fa fa-envelope"></i> Email &
											Website:</span>
										<p>
											<a href="wpkixx.html" title="">www.wpkixx.com</a> <a
												href="http://wpkixx.com/cdn-cgi/l/email-protection"
												class="__cf_email__"
												data-cfemail="ebbb829f858280ab92849e99868a8287c5888486">[email&#160;protected]</a>
										</p> -->

									</div>
								</div>
							</aside>
						</div>
						<div class="col-lg-8 col-md-8">
							<div class="central-meta">
								<span class="create-post">General Info<a href="#" title="">See All</a></span>
								<div class="row">
									<div class="col-lg-6">
										<div class="gen-metabox">
											<span><i class="fa fa-puzzle-piece"></i> Sở thích :</span>
											<p>
												Tôi thích đi xe đạp, bơi lội và tập thể dục. Tôi cũng thích đọc tạp chí thiết kế, tìm kiếm trên internet và xem phim Hollywood hay khi trời mưa bên ngoài.
											</p>
											<?php
											$quotes = [
												"Tôi thích đi xe đạp, bơi lội và tập thể dục.",
												"Tôi cũng thích đọc tạp chí thiết kế.Yêu anh Tài",
												"Tìm kiếm trên internet là một trong những sở thích của tôi.",
												"Tôi thích xem phim Hollywood hay khi trời mưa bên ngoài."
											];
											$randomQuote = $quotes[array_rand($quotes)];
											?>
											<p>{{ $randomQuote }}</p>
										</div>
										<div class="gen-metabox">
											<span><i class="fa fa-plus"></i> Others Interests</span>
											<p>
												Yêu anh Tài đẹp trai.
											</p>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="gen-metabox">
											<span><i class="fa fa-mortar-board"></i> Học vấn:</span>
											<p>
												Learning code in hoang quy CEO <a
													href="#" title="">Hoang quy IT</a>
											</p>
										</div>
										<div class="gen-metabox">
											<span><i class="fa fa-certificate"></i> Work and experience</span>
											<p>
												Currently working in the "color hands" web development agency
												from the last 5 five years as <a href="#" title="">Senior UI/UX
													Designer</a>
											</p>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="gen-metabox no-margin">
											<span><i class="fa fa-sitemap"></i> Social Networks</span>
											<ul class="sociaz-media">
												<li><a class="facebook" href="#" title=""><i
															class="fa fa-facebook"></i></a></li>
												<li><a class="twitter" href="#" title=""><i
															class="fa fa-twitter"></i></a></li>
												<li><a class="google" href="#" title=""><i
															class="fa fa-google-plus"></i></a></li>
												<li><a class="vk" href="#" title=""><i class="fa fa-vk"></i></a>
												</li>
												<li><a class="instagram" href="#" title=""><i
															class="fa fa-instagram"></i></a></li>

											</ul>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="gen-metabox no-margin">
											<span><i class="fa fa-trophy"></i> Badges</span>
											<ul class="badged">
												<li><img src="/user/images/badges/badge2.png" alt=""></li>
												<li><img src="/user/images/badges/badge19.png" alt=""></li>
												<li><img src="/user/images/badges/badge21.png" alt=""></li>
												<li><img src="/user/images/badges/badge3.png" alt=""></li>
												<li><img src="/user/images/badges/badge4.png" alt=""></li>
											</ul>
										</div>
									</div>
								</div>
							</div>
							<div class="central-meta">
								<span class="create-post">Favourit Movies & TV Shows (33) <a href="#"
										title="">See All</a></span>
								<div class="row">
									<div class="col-lg-4 col-md-6 col-sm-6">
										<div class="fav-play">
											<figure>
												<img src="/user/images/resources/tvplay1.jpg" alt="">
											</figure>
											<span class="tv-play-title">Attaturk Tv Series 2017 </span>
										</div>
									</div>
									<div class="col-lg-4 col-md-6 col-sm-6">
										<div class="fav-play">
											<figure>
												<img src="/user/images/resources/tvplay2.jpg" alt="">
											</figure>
											<span class="tv-play-title">Thor Hollywood Movie 2017 </span>
										</div>
									</div>
									<div class="col-lg-4 col-md-6 col-sm-6">
										<div class="fav-play">
											<figure>
												<img src="/user/images/resources/tvplay3.jpg" alt="">
											</figure>
											<span class="tv-play-title">Spider Men 2015 </span>
										</div>
									</div>
								</div>
							</div>
							<div class="central-meta">
									<span class="create-post">Có thể bạn quen ({{ count($data['Suggestions']) }}) <a href="timeline-friends2.html"title="">Tất cả</a></span>
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
														<a href="#" title="Remove Friend" data-uid="{{ $suggestion->uid }}">
																<i class="fa fa-user-times"></i>
															</a>
														</li>
													</ul>
												</div>
											</li>
										@endforeach
									</ul>
							</div><!-- friends list -->



							<div class="central-meta">
								<span class="create-post">Photos (580) <a href="timeline-photos.html"
										title="">See All</a></span>
								<ul class="photos-list">
									<li>
										<div class="item-box">
											<a class="strip" href="/user/images/resources/photo-22.jpg" title=""
												data-strip-group="mygroup"
												data-strip-group-options="loop: false">
												<img src="/user/images/resources/photo2.jpg" alt=""></a>
											<div class="over-photo">
												<div class="likes heart" title="Like/Dislike">❤ <span>15</span>
												</div>
												<span>20 hours ago</span>
											</div>
										</div>
									</li>
									<li>
										<div class="item-box">
											<a class="strip" href="/user/images/resources/photo-33.jpg" title=""
												data-strip-group="mygroup"
												data-strip-group-options="loop: false">
												<img src="/user/images/resources/photo3.jpg" alt=""></a>
											<div class="over-photo">
												<div class="likes heart" title="Like/Dislike">❤ <span>20</span>
												</div>
												<span>20 days ago</span>
											</div>
										</div>
									</li>
									<li>
										<div class="item-box">
											<a class="strip" href="/user/images/resources/photo-44.jpg" title=""
												data-strip-group="mygroup"
												data-strip-group-options="loop: false">
												<img src="/user/images/resources/photo4.jpg" alt=""></a>
											<div class="over-photo">
												<div class="likes heart" title="Like/Dislike">❤ <span>155</span>
												</div>
												<span>Yesterday</span>
											</div>
										</div>
									</li>
									<li>
										<div class="item-box">
											<a class="strip" href="/user/images/resources/photo-55.jpg" title=""
												data-strip-group="mygroup"
												data-strip-group-options="loop: false">
												<img src="/user/images/resources/photo5.jpg" alt=""></a>
											<div class="over-photo">
												<div class="likes heart" title="Like/Dislike">❤ <span>201</span>
												</div>
												<span>3 weeks ago</span>
											</div>
										</div>
									</li>
									<li>
										<div class="item-box">
											<a class="strip" href="/user/images/resources/photo-66.jpg" title=""
												data-strip-group="mygroup"
												data-strip-group-options="loop: false">
												<img src="/user/images/resources/photo6.jpg" alt=""></a>
											<div class="over-photo">
												<div class="likes heart" title="Like/Dislike">❤ <span>81</span>
												</div>
												<span>2 months ago</span>
											</div>
										</div>
									</li>
									<li>
										<div class="item-box">
											<a class="strip" href="/user/images/resources/photo-77.jpg" title=""
												data-strip-group="mygroup"
												data-strip-group-options="loop: false">
												<img src="/user/images/resources/photo7.jpg" alt=""></a>
											<div class="over-photo">
												<div class="likes heart" title="Like/Dislike">❤ <span>98</span>
												</div>
												<span>1 day</span>
											</div>
										</div>
									</li>
									<li>
										<div class="item-box">
											<a class="strip" href="/user/images/resources/photo-88.jpg" title=""
												data-strip-group="mygroup"
												data-strip-group-options="loop: false">
												<img src="/user/images/resources/photo8.jpg" alt=""></a>
											<div class="over-photo">
												<div class="likes heart" title="Like/Dislike">❤ <span>87</span>
												</div>
												<span>23 hours ago</span>
											</div>
										</div>
									</li>
								</ul>
							</div>
							<div class="central-meta">
								<span class="create-post">Videos (33) <a href="timeline-videos.html"
										title="">See All</a></span>
								<ul class="videos-list">
									<li>
										<div class="item-box">
											<a href="https://www.youtube.com/watch?v=fF382gwEnG8&amp;t=1s"
												title="" data-strip-group="mygroup" class="strip"
												data-strip-options="width: 700,height: 450,youtube: { autoplay: 1 }"><img
													src="/user/images/resources/vid-11.jpg" alt="">
												<i>
													<svg version="1.1" class="play"
														xmlns="http://www.w3.org/2000/svg"
														xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
														y="0px" height="50px" width="50px" viewBox="0 0 100 100"
														enable-background="new 0 0 100 100"
														xml:space="preserve">
														<path class="stroke-solid" fill="none" stroke="" d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7
													C97.3,23.7,75.7,2.3,49.9,2.5" />
														<path class="icon" fill=""
															d="M38,69c-1,0.5-1.8,0-1.8-1.1V32.1c0-1.1,0.8-1.6,1.8-1.1l34,18c1,0.5,1,1.4,0,1.9L38,69z" />
													</svg>
												</i>
											</a>
											<div class="over-photo">
												<div class="likes heart" title="Like/Dislike">❤ <span>15</span>
												</div>
												<span>20 hours ago</span>
											</div>
										</div>
									</li>
									<li>
										<div class="item-box">
											<a href="https://www.youtube.com/watch?v=fF382gwEnG8&amp;t=1s"
												title="" data-strip-group="mygroup" class="strip"
												data-strip-options="width: 700,height: 450,youtube: { autoplay: 1 }"><img
													src="/user/images/resources/vid-12.jpg" alt="">
												<i>
													<svg version="1.1" class="play"
														xmlns="http://www.w3.org/2000/svg"
														xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
														y="0px" height="50px" width="50px" viewBox="0 0 100 100"
														enable-background="new 0 0 100 100"
														xml:space="preserve">
														<path class="stroke-solid" fill="none" stroke="" d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7
														C97.3,23.7,75.7,2.3,49.9,2.5" />
														<path class="icon" fill=""
															d="M38,69c-1,0.5-1.8,0-1.8-1.1V32.1c0-1.1,0.8-1.6,1.8-1.1l34,18c1,0.5,1,1.4,0,1.9L38,69z" />
													</svg>
												</i>
											</a>
											<div class="over-photo">
												<div class="likes heart" title="Like/Dislike">❤ <span>20</span>
												</div>
												<span>20 hours ago</span>
											</div>
										</div>
									</li>
									<li>
										<div class="item-box">
											<a href="https://www.youtube.com/watch?v=fF382gwEnG8&amp;t=1s"
												title="" data-strip-group="mygroup" class="strip"
												data-strip-options="width: 700,height: 450,youtube: { autoplay: 1 }"><img
													src="/user/images/resources/vid-10.jpg" alt="">
												<i>
													<svg version="1.1" class="play"
														xmlns="http://www.w3.org/2000/svg"
														xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
														y="0px" height="50px" width="50px" viewBox="0 0 100 100"
														enable-background="new 0 0 100 100"
														xml:space="preserve">
														<path class="stroke-solid" fill="none" stroke="" d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7
														C97.3,23.7,75.7,2.3,49.9,2.5" />
														<path class="icon" fill=""
															d="M38,69c-1,0.5-1.8,0-1.8-1.1V32.1c0-1.1,0.8-1.6,1.8-1.1l34,18c1,0.5,1,1.4,0,1.9L38,69z" />
													</svg>
												</i>
											</a>
											<div class="over-photo">
												<div class="likes heart" title="Like/Dislike">❤ <span>49</span>
												</div>
												<span>20 days ago</span>
											</div>
										</div>
									</li>
									<li>
										<div class="item-box">
											<a href="https://www.youtube.com/watch?v=fF382gwEnG8&amp;t=1s"
												title="" data-strip-group="mygroup" class="strip"
												data-strip-options="width: 700,height: 450,youtube: { autoplay: 1 }"><img
													src="/user/images/resources/vid-9.jpg" alt="">
												<i>
													<svg version="1.1" class="play"
														xmlns="http://www.w3.org/2000/svg"
														xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
														y="0px" height="50px" width="50px" viewBox="0 0 100 100"
														enable-background="new 0 0 100 100"
														xml:space="preserve">
														<path class="stroke-solid" fill="none" stroke="" d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7
														C97.3,23.7,75.7,2.3,49.9,2.5" />
														<path class="icon" fill=""
															d="M38,69c-1,0.5-1.8,0-1.8-1.1V32.1c0-1.1,0.8-1.6,1.8-1.1l34,18c1,0.5,1,1.4,0,1.9L38,69z" />
													</svg>
												</i>
											</a>
											<div class="over-photo">
												<div class="likes heart" title="Like/Dislike">❤ <span>156</span>
												</div>
												<span>Yesterday</span>
											</div>
										</div>
									</li>
									<li>
										<div class="item-box">
											<a href="https://www.youtube.com/watch?v=fF382gwEnG8&amp;t=1s"
												title="" data-strip-group="mygroup" class="strip"
												data-strip-options="width: 700,height: 450,youtube: { autoplay: 1 }"><img
													src="/user/images/resources/vid-6.jpg" alt="">
												<i>
													<svg version="1.1" class="play"
														xmlns="http://www.w3.org/2000/svg"
														xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
														y="0px" height="50px" width="50px" viewBox="0 0 100 100"
														enable-background="new 0 0 100 100"
														xml:space="preserve">
														<path class="stroke-solid" fill="none" stroke="" d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7
														C97.3,23.7,75.7,2.3,49.9,2.5" />
														<path class="icon" fill=""
															d="M38,69c-1,0.5-1.8,0-1.8-1.1V32.1c0-1.1,0.8-1.6,1.8-1.1l34,18c1,0.5,1,1.4,0,1.9L38,69z" />
													</svg>
												</i>
											</a>
											<div class="over-photo">
												<div class="likes heart" title="Like/Dislike">❤ <span>202</span>
												</div>
												<span>3 weeks ago</span>
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section><!-- content -->

<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

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
            if (response.response === true) {
                $this.closest('.add-tofrndlist').html(`
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
</script>
@endsection