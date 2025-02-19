<style>
	.dropdowns {
		display: none;
		position: absolute;
		top: 100%;
		right: 0;
		background-color: #fff;
		width: 300px;
		box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
		z-index: 1000;
		padding: 10px;
	}

	.dropdowns.active {
		display: block;
	}

	.setting-area>li {
		position: relative;
	}

	/* Logo in topbar */
	.logo .logo-text {
		display: flex;
		align-items: center;
		justify-content: center;
		height: 48px;
		width: 100%;
		text-decoration: none;
	}

	.logo .logo-text svg {
		max-height: 48px;
		width: auto;
	}
</style>

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="topbar stick">
	<div class="logo">
		<a class="logo-text" href="index.html">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 368.5 48">
				<text x="31%" y="80%" font-family="Arial" font-size="32" font-weight="bold" fill="#FFFFFF" dominant-baseline="middle" text-anchor="middle">
					Mega Bot
				</text>
			</svg>
		</a>
	</div>
	<div class="top-area">
		<div class="main-menu">
			<span>
				<i class="fa fa-braille"></i>
			</span>
		</div>

		<div class="top-search">
			<form method="get" action="{{ route('android.searchResult', ['uid' => $uid]) }}" class="">
				<input type="text" name="key-search" placeholder="Search People, Pages, Groups etc">
				<button data-ripple><i class="ti-search"></i></button>
			</form>
		</div>

		<div class="page-name">
			<span>Newsfeed</span>
		</div>

		<ul class="setting-area">
			<li>
				<a href="{{ route('android.main', ['uid' => $uid]) }}" title="Home" target="_self">
					<i class="fa fa-home"></i>
				</a>
			</li>
			<li>
				<a href="#" title="Friend Requests" data-ripple="">
					@if(empty($data['FriendListAccept']))
						<i class="fa fa-user"></i><em class="bg-red">0</em>
					@else
						<i class="fa fa-user"></i><em class="bg-red">{{ count($data['FriendListAccept']) }}</em>
					@endif
				</a>
				<div class="dropdowns">
					@if(empty($data['FriendListAccept']))
						<span>No new requests</span>
					@else
						<span>{{ count($data['FriendListAccept']) }} New Requests <a href="#" title="">View all Requests</a></span>
					@endif
					<ul class="drops-menu">
						
					@if(!empty($data['FriendListAccept']))
						@foreach($data['FriendListAccept'] as $friendAccept)
						<li>
							<div>
								<div style="display: flex; align-items: center;">
									<figure style="margin: 0;">
										<img src="{{$friendAccept->picture}}" alt="" style="width: 40px; height: 40px;">
									</figure>
									<div class="mesg-meta" style="margin-left: 10px;">
										<h6><a href="#" title="">{{$friendAccept->name}}</a></h6>
										<span><b></b> {{$friendAccept->mutual_friends}}</span>
									</div>
									<div class="add-del-friends" style="margin-left: auto;" data-uid="{{$friendAccept->uid}}">
										<a href="#" class="accept-friend" title=""><i class="fa fa-check"></i></a>
										<a href="#" title=""><i class="fa fa-trash"></i></a>
									</div>
								</div>
							</div>
						</li>
						@endforeach
					@else
						<li>No account data found.</li>
					@endif
						<!-- Các phần tử khác -->
					</ul>
					<a href="friend-requests.html" title="" class="more-mesg">View All</a>
				</div>
			</li>

			<li>
				<a href="#" title="Fanpage" data-ripple="">
					<i class="fa fa-users"></i><em class="bg-red">5</em>
				</a>
				<div class="dropdowns">
					<span> page <a href="#"></a></span>
					<ul class="drops-menu">
						@if(!empty($data['account']->MultiAccount))
						@if(is_iterable($data['account']->MultiAccount))
						@foreach($data['account']->MultiAccount as $page)
						<li>
							<div>
								<figure>
									<img src="{{$page->profile->profile_picture->uri}}" alt="">
								</figure>
								<div class="mesg-meta">
									<h6><a href="{{ route('android.main', ['uid' => $page->profile->id]) }}" title="">{{$page->profile->name}}</a></h6>
									<span><b>Amy</b> is mutual friend</span>
									<i>yesterday</i>
								</div>
								<div class="add-del-friends">
									<a href="#" title=""><i class="fa fa-heart"></i></a>
									<a href="#" title=""><i class="fa fa-trash"></i></a>
								</div>
							</div>
						</li>
						@endforeach
						@else
						<li>No data available to display.</li>
						@endif
						@else
						<li>No account data found.</li>
						@endif
						<!-- Các phần tử khác -->
					</ul>

					<a href="friend-requests.html" title="" class="more-mesg">View All</a>
				</div>
			</li>



			<li>
				<a href="#" title="Notification" data-ripple="">
					<i class="fa fa-bell"></i><em class="bg-purple">7</em>
				</a>
				<div class="dropdowns">
					<span>4 New Notifications <a href="#" title="">Mark all as read</a></span>
					<ul class="drops-menu">
						<li>
							<a href="notifications.html" title="">
								<figure>
									<img src="/user/images/resources/thumb-1.jpg" alt="">
									<span class="status f-online"></span>
								</figure>
								<div class="mesg-meta">
									<h6>sarah Loren</h6>
									<span>commented on your new profile status</span>
									<i>2 min ago</i>
								</div>
							</a>
						</li>
						<li>
							<a href="notifications.html" title="">
								<figure>
									<img src="/user/images/resources/thumb-2.jpg" alt="">
									<span class="status f-online"></span>
								</figure>
								<div class="mesg-meta">
									<h6>Jhon doe</h6>
									<span>Nicholas Grissom just became friends. Write on his wall.</span>
									<i>4 hours ago</i>
									<figure>
										<span>Today is Marina Valentine’s Birthday! wish for celebrating</span>
										<img src="/user/images/birthday.png" alt="">
									</figure>
								</div>
							</a>
						</li>
						<li>
							<a href="notifications.html" title="">
								<figure>
									<img src="/user/images/resources/thumb-3.jpg" alt="">
									<span class="status f-online"></span>
								</figure>
								<div class="mesg-meta">
									<h6>Andrew</h6>
									<span>commented on your photo.</span>
									<i>Sunday</i>
									<figure>
										<span>"Celebrity looks Beautiful in that outfit! We should see each"</span>
										<img src="/user/images/resources/admin.jpg" alt="">
									</figure>
								</div>
							</a>
						</li>
						<li>
							<a href="notifications.html" title="">
								<figure>
									<img src="/user/images/resources/thumb-4.jpg" alt="">
									<span class="status f-online"></span>
								</figure>
								<div class="mesg-meta">
									<h6>Tom cruse</h6>
									<span>nvited you to attend to his event Goo in</span>
									<i>May 19</i>
								</div>
							</a>
							<span class="tag">New</span>
						</li>
						<li>
							<a href="notifications.html" title="">
								<figure>
									<img src="/user/images/resources/thumb-5.jpg" alt="">
									<span class="status f-online"></span>
								</figure>
								<div class="mesg-meta">
									<h6>Amy</h6>
									<span>Andrew Changed his profile picture. </span>
									<i>dec 18</i>
								</div>
							</a>
							<span class="tag">New</span>
						</li>
					</ul>
					<a href="notifications.html" title="" class="more-mesg">View All</a>
				</div>
			</li>
			<li>
				<a href="#" title="Messages" data-ripple=""><i class="fa fa-commenting"></i><em class="bg-blue">9</em></a>
				<div class="dropdowns">
					<span>5 New Messages <a href="#" title="">Mark all as read</a></span>
					<ul class="drops-menu">
						<li>
							<a class="show-mesg" href="#" title="">
								<figure>
									<img src="/user/images/resources/thumb-1.jpg" alt="">
									<span class="status f-online"></span>
								</figure>
								<div class="mesg-meta">
									<h6>sarah Loren</h6>
									<span><i class="ti-check"></i> Hi, how r u dear ...?</span>
									<i>2 min ago</i>
								</div>
							</a>
						</li>
						<li>
							<a class="show-mesg" href="#" title="">
								<figure>
									<img src="/user/images/resources/thumb-2.jpg" alt="">
									<span class="status f-offline"></span>
								</figure>
								<div class="mesg-meta">
									<h6>Jhon doe</h6>
									<span><i class="ti-check"></i> We’ll have to check that at the office and see if the client is on board with</span>
									<i>2 min ago</i>
								</div>
							</a>
						</li>
						<li>
							<a class="show-mesg" href="#" title="">
								<figure>
									<img src="/user/images/resources/thumb-3.jpg" alt="">
									<span class="status f-online"></span>
								</figure>
								<div class="mesg-meta">
									<h6>Andrew</h6>
									<span> <i class="fa fa-paperclip"></i>Hi Jack's! It’s Diana, I just wanted to let you know that we have to reschedule..</span>
									<i>2 min ago</i>
								</div>
							</a>
						</li>
						<li>
							<a class="show-mesg" href="#" title="">
								<figure>
									<img src="/user/images/resources/thumb-4.jpg" alt="">
									<span class="status f-offline"></span>
								</figure>
								<div class="mesg-meta">
									<h6>Tom cruse</h6>
									<span><i class="ti-check"></i> Great, I’ll see you tomorrow!.</span>
									<i>2 min ago</i>
								</div>
							</a>
							<span class="tag">New</span>
						</li>
						<li>
							<a class="show-mesg" href="#" title="">
								<figure>
									<img src="/user/images/resources/thumb-5.jpg" alt="">
									<span class="status f-away"></span>
								</figure>
								<div class="mesg-meta">
									<h6>Amy</h6>
									<span><i class="fa fa-paperclip"></i> Sed ut perspiciatis unde omnis iste natus error sit </span>
									<i>2 min ago</i>
								</div>
							</a>
							<span class="tag">New</span>
						</li>
					</ul>
					<a href="chat-messenger.html" title="" class="more-mesg">View All</a>
				</div>
			</li>
			<li><a href="#" title="Languages" data-ripple=""><i class="fa fa-globe"></i><em>EN</em></a>
				<div class="dropdowns languages">
					<div data-gutter="10" class="row">
						<div class="col-md-3">
							<ul class="dropdown-meganav-select-list-lang">
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/UK.png">English(UK)
									</a>
								</li>
								<li class="active">
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/US.png">English(US)
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/DE.png">Deutsch
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/NED.png">Nederlands
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/FR.png">Français
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/SP.png">Español
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/ARG.png">Español (AR)
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/IT.png">Italiano
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/PT.png">Português (PT)
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/BR.png">Português (BR)
									</a>
								</li>

							</ul>
						</div>
						<div class="col-md-3">
							<ul class="dropdown-meganav-select-list-lang">
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/FIN.png">Suomi
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/SW.png">Svenska
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/DEN.png">Dansk
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/CZ.png">Čeština
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/HUN.png">Magyar
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/ROM.png">Română
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/JP.png">日本語
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/CN.png">简体中文
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/PL.png">Polski
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/GR.png">Ελληνικά
									</a>
								</li>

							</ul>
						</div>
						<div class="col-md-3">
							<ul class="dropdown-meganav-select-list-lang">
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/TUR.png">Türkçe
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/BUL.png">Български
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/ARB.png">العربية
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/KOR.png">한국어
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/ISR.png">עברית
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/LAT.png">Latviski
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/UKR.png">Українська
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/IND.png">Bahasa Indonesia
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/MAL.png">Bahasa Malaysia
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/TAI.png">ภาษาไทย
									</a>
								</li>

							</ul>
						</div>
						<div class="col-md-3">
							<ul class="dropdown-meganav-select-list-lang">
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/CRO.png">Hrvatski
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/LIT.png">Lietuvių
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/SLO.png">Slovenčina
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/SERB.png">Srpski
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/SLOVE.png">Slovenščina
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/NAM.png">Tiếng Việt
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/PHI.png">Filipino
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/ICE.png">Íslenska
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/EST.png">Eesti
									</a>
								</li>
								<li>
									<a href="#">
										<img title="Image Title" alt="Image Alternative text" src="/user/images/flags/RU.png">Русский
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</li>
			<li><a href="#" title="Help" data-ripple=""><i class="fa fa-question-circle"></i></a>
				<div class="dropdowns helps">
					<span>Quick Help</span>
					<form method="post">
						<input type="text" placeholder="How can we help you?">
					</form>
					<span>Help with this page</span>
					<ul class="help-drop">
						<li><a href="forum.html" title=""><i class="fa fa-book"></i>Community & Forum</a></li>
						<li><a href="faq.html" title=""><i class="fa fa-question-circle-o"></i>FAQs</a></li>
						<li><a href="career.html" title=""><i class="fa fa-building-o"></i>Carrers</a></li>
						<li><a href="privacy.html" title=""><i class="fa fa-pencil-square-o"></i>Terms & Policy</a></li>
						<li><a href="#" title=""><i class="fa fa-map-marker"></i>Contact</a></li>
						<li><a href="#" title=""><i class="fa fa-exclamation-triangle"></i>Report a Problem</a></li>
					</ul>
				</div>
			</li>
		</ul>

		<div class="user-img">
			<h5>{{ $data['account']->name ?? $data['account']->fullname ?? 'Tên không có sẵn' }}</h5>
			<img src="{{ $data['account']->avatar ?? null }}" alt="" width="45" height="45">
			<span class="status f-online"></span>
			<div class="user-setting">
				<span class="seting-title">Chat setting <a href="#" title="">see all</a></span>
				<ul class="chat-setting">
					<li><a href="#" title=""><span class="status f-online"></span>online</a></li>
					<li><a href="#" title=""><span class="status f-away"></span>away</a></li>
					<li><a href="#" title=""><span class="status f-off"></span>offline</a></li>
				</ul>

				<span class="seting-title">User setting <a href="#" title="">see all</a></span>
				<ul class="log-out">
					@if(empty($data['account']))
						<li><span>No profile available</span></li>
					@else
						<li><a href="{{ route('profile.view', ['uid' => $uid]) }}" title=""><i class="ti-user"></i> View Profile</a></li>
					@endif
					<li><a href="setting.html" title=""><i class="ti-pencil-alt"></i>edit profile</a></li>
					<li><a href="#" title=""><i class="ti-target"></i>activity log</a></li>
					@if(!empty($data['account']))
						<li><a href="{{ route('profile.Settings', ['uid' => $uid]) }}" title=""><i class="ti-settings"></i>account setting</a></li>
					@endif
					<li><a href="logout.html" title=""><i class="ti-power-off"></i>log out</a></li>
				</ul>
			</div>
		</div>

		<span class="ti-settings main-menu" data-ripple=""></span>
	</div>


	<nav>
		<ul class="nav-list">
			<li><a class="" href="#" title=""><i class="fa fa-home"></i> Home Pages</a>
				<ul>
					<li><a href="index.html" title="">Pitnik Default</a></li>
					<li><a href="company-landing.html" title="">Company Landing</a></li>
					<li><a href="pitrest.html" title="">Pitrest</a></li>
					<li><a href="redpit.html" title="">Redpit</a></li>
					<li><a href="redpit-category.html" title="">Redpit Category</a></li>
					<li><a href="soundnik.html" title="">Soundnik</a></li>
					<li><a href="soundnik-detail.html" title="">Soundnik Single</a></li>
					<li><a href="career.html" title="">Pitjob</a></li>
					<li><a href="shop.html" title="">Shop</a></li>
					<li><a href="classified.html" title="">Classified</a></li>
					<li><a href="pitpoint.html" title="">PitPoint</a></li>
					<li><a href="pittube.html" title="">Pittube</a></li>
					<li><a href="chat-messenger.html" title="">Messenger</a></li>
				</ul>
			</li>
			<li><a class="" href="#" title=""><i class="fa fa-film"></i> Pittube</a>
				<ul>
					<li><a href="pittube.html" title="">Pittube</a></li>
					<li><a href="pittube-detail.html" title="">Pittube single</a></li>
					<li><a href="pittube-category.html" title="">Pittube Category</a></li>
					<li><a href="pittube-channel.html" title="">Pittube Channel</a></li>
					<li><a href="pittube-search-result.html" title="">Pittube Search Result</a></li>
				</ul>
			</li>
			<li><a class="" href="#" title=""><i class="fa fa-female"></i> PitPoint</a>
				<ul>
					<li><a href="pitpoint.html" title="">PitPoint</a></li>
					<li><a href="pitpoint-detail.html" title="">Pitpoint Detail</a></li>
					<li><a href="pitpoint-list.html" title="">Pitpoint List style</a></li>
					<li><a href="pitpoint-without-baner.html" title="">Pitpoint without Banner</a></li>
					<li><a href="pitpoint-search-result.html" title="">Pitpoint Search</a></li>
				</ul>
			</li>
			<li><a class="" href="#" title=""><i class="fa fa-graduation-cap"></i> Pitjob</a>
				<ul>
					<li><a href="career.html" title="">Pitjob</a></li>
					<li><a href="career-detail.html" title="">Pitjob Detail</a></li>
					<li><a href="career-search-result.html" title="">Job seach page</a></li>
					<li><a href="social-post-detail.html" title="">Social Post Detail</a></li>
				</ul>
			</li>
			<li><a class="" href="#" title=""><i class="fa fa-repeat"></i> Timeline</a>
				<ul>
					<li><a href="timeline.html" title="">Timeline</a></li>
					<li><a href="timeline-photos.html" title="">Timeline Photos</a></li>
					<li><a href="timeline-videos.html" title="">Timeline Videos</a></li>
					<li><a href="timeline-groups.html" title="">Timeline Groups</a></li>
					<li><a href="timeline-friends.html" title="">Timeline Friends</a></li>
					<li><a href="timeline-friends2.html" title="">Timeline Friends-2</a></li>
					<li><a href="about.html" title="">Timeline About</a></li>
					<li><a href="blog-posts.html" title="">Timeline Blog</a></li>
					<li><a href="friends-birthday.html" title="">Friends' Birthday</a></li>
					<li><a href="newsfeed.html" title="">Newsfeed</a></li>
					<li><a href="search-result.html" title="">Search Result</a></li>
				</ul>
			</li>
			<li><a class="" href="#" title=""><i class="fa fa-heart"></i> Favourit Page</a>
				<ul>
					<li><a href="fav-page.html" title="">Favourit Page</a></li>
					<li><a href="fav-favers.html" title="">Fav Page Likers</a></li>
					<li><a href="fav-events.html" title="">Fav Events</a></li>
					<li><a href="fav-event-invitations.html" title="">Fav Event Invitations</a></li>
					<li><a href="event-calendar.html" title="">Event Calendar</a></li>
					<li><a href="fav-page-create.html" title="">Create New Page</a></li>
					<li><a href="price-plans.html" title="">Price Plan</a></li>
				</ul>
			</li>
			<li><a class="" href="#" title=""><i class="fa fa-forumbee"></i> Forum</a>
				<ul>
					<li><a href="forum.html" title="">Forum</a></li>
					<li><a href="forum-create-topic.html" title="">Forum Create Topic</a></li>
					<li><a href="forum-open-topic.html" title="">Forum Open Topic</a></li>
					<li><a href="forums-category.html" title="">Forum Category</a></li>
				</ul>
			</li>
			<li><a class="" href="#" title=""><i class="fa fa-star-o"></i> Featured</a>
				<ul>
					<li><a href="chat-messenger.html" title="">Messenger (Chatting)</a></li>
					<li><a href="notifications.html" title="">Notifications</a></li>
					<li><a href="badges.html" title="">Badges</a></li>
					<li><a href="faq.html" title="">Faq's</a></li>
					<li><a href="contribution.html" title="">Contriburion Page</a></li>
					<li><a href="manage-page.html" title="">Manage Page</a></li>
					<li><a href="weather-forecast.html" title="">weather-forecast</a></li>
					<li><a href="statistics.html" title="">Statics/Analytics</a></li>
					<li><a href="shop-cart.html" title="">Shop Cart</a></li>
				</ul>
			</li>
			<li><a class="" href="#" title=""><i class="fa fa-gears"></i> Account Setting</a>
				<ul>
					<li><a href="setting.html" title="">Setting</a></li>
					<li><a href="privacy.html" title="">Privacy</a></li>
					<li><a href="support-and-help.html" title="">Support & Help</a></li>
					<li><a href="support-and-help-detail.html" title="">Support Detail</a></li>
					<li><a href="support-and-help-search-result.html" title="">Support Search</a></li>
				</ul>
			</li>
			<li><a class="" href="#" title=""><i class="fa fa-lock"></i> Authentication</a>
				<ul>
					<li><a href="login.html" title="">Login Page</a></li>
					<li><a href="register.html" title="">Register Page</a></li>
					<li><a href="logout.html" title="">Logout Page</a></li>
					<li><a href="coming-soon.html" title="">Coming Soon</a></li>
					<li><a href="error-404.html" title="">Error 404</a></li>
					<li><a href="error-404-2.html" title="">Error 404-2</a></li>
					<li><a href="error-500.html" title="">Error 500</a></li>
				</ul>
			</li>
			<li><a class="" href="#" title=""><i class="fa fa-wrench"></i> Tools</a>
				<ul>
					<li><a href="typography.html" title="">Typography</a></li>
					<li><a href="popup-modals.html" title="">Popups/Modals</a></li>
					<li><a href="post-versions.html" title="">Post Versions</a></li>
					<li><a href="sliders.html" title="">Sliders / Carousel</a></li>
					<li><a href="google-map.html" title="">Google Maps</a></li>
					<li><a href="widgets.html" title="">Widgets</a></li>
				</ul>
			</li>
		</ul>

	</nav><!-- nav menu -->

<script>

$(document).ready(function() {
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	console.log('DOM is ready!');
	$(document).on('click', '.accept-friend', function(e) {
		console.log('Accept friend clicked');
		e.preventDefault();
		
		const uid_invitation = $(this).closest('.add-del-friends').data('uid');
		const $this = $(this);
		const $icon = $this.find('i');

		$icon.removeClass('fa-check').addClass('fa-spinner fa-spin');
		$.ajax({
			url: '{{ route("profile.acceptFriend", ":uid") }}'.replace(':uid', {{$data['account']->uid}}),
			type: 'POST',
			data: {
				uid_accept: uid_invitation
			},
			success: function(response) {
				if (response.response === true) {
					$this.closest('.add-del-friends').html(`
						<a href="#" title="Friend Accepted" class="friend-accepted">
							<i class="fa fa-check" style="color: green;"></i>
						</a>
					`);
					toastr.success('Chấp nhận lời mời kết bạn thành công', 'Thành công');
				} else {
					toastr.error(response.response || 'Chấp nhận kết bạn thất bại', 'Lỗi');
					$icon.removeClass('fa-spinner fa-spin').addClass('fa-check');
				}
			},
			error: function(xhr) {
				console.error('Lỗi khi chấp nhận lời mời:', xhr.responseText);
				$icon.removeClass('fa-spinner fa-spin').addClass('fa-check');
				toastr.error('Có lỗi xảy ra. Vui lòng thử lại.', 'Lỗi');
			}
		});
	});
});
</script>

</div><!-- topbar -->