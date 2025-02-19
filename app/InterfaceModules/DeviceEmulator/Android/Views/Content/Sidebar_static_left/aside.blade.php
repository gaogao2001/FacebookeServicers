<style>
    .refresh-content {
        position: relative;
        display: inline-block;
        pointer-events: none;
        /* Ngăn span chặn sự kiện click */
    }

    .refresh-content .aside-btnShowMapModal {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 20px;
        /* Điều chỉnh kích thước theo nhu cầu */
        color: #fff;
        /* Điều chỉnh màu sắc theo thiết kế */
        position: relative;
        z-index: 2;
        /* Đảm bảo nút trên cùng */
        pointer-events: auto;
        /* Cho phép nút nhận sự kiện */
    }

    .refresh-content::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1;
        /* Nếu có overlay, đảm bảo nó không ngăn cản sự kiện click */
        pointer-events: none;
    }
</style>

<aside class="sidebar static left">


    <!-- <button class="fa fa-refresh aside-btnShowMapModal" data-modal-id="{{ $data['account']->_id }}"></button> -->
    <!-- Thời tiết -->
    <div class="widget">

        <div class="weather-widget low-opacity bluesh">
            <div class="bg-image" style="background-image: url(images/resources/weather.jpg)"></div>
            <span class="refresh-content">
                <button class="fa fa-refresh aside-btnShowMapModal" data-modal-id="{{ $data['account']->_id }}"></button>
            </span>
            <div class="weather-week">
                <div class="icon sun-shower">
                    <div class="cloud"></div>
                    <div class="sun">
                        <div class="rays"></div>
                    </div>
                    <div class="rain"></div>
                </div>
            </div>
			@if (!empty($data['Weather']->v3wxobservationscurrent->temperature))
            <div class="weather-infos">
                <span class="weather-tem">{{ $data['Weather']->v3wxobservationscurrent->temperature }}</span>
                <h3>
                    {{ $data['Weather']->v3locationpoint->location->displayName }}
                    <i>{{ $data['Weather']->v3locationpoint->location->city }}, {{ $data['Weather']->v3locationpoint->location->adminDistrict }}</i>
                </h3>
                <h3>
                    <i>
                        @php
                        $date = date_create_from_format('Ymd\TH:i:sO', $data['Weather']->v3wxobservationscurrent->validTimeLocal);
                        @endphp
                        {{ $date ? $date->format('l, d F Y') : 'Ngày không hợp lệ' }}
                    </i>
                </h3>
                <div class="weather-date skyblue-bg">
                    <span>
                        Ngày
                        <strong>
                            {{ $date ? $date->format('d') : 'Ngày không hợp lệ' }}
                        </strong>
                    </span>
                </div>
            </div>
			@endif
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
    </div><!-- weather widget-->

    <div class="widget whitish low-opacity">
        <div style="background-image: url(images/resources/dob2.png)" class="bg-image"></div>
        <div class="dob-head">
            <img src="images/resources/sug-page-5.jpg" alt="">
            <span>22nd Birthday</span>
            <div class="dob">
                <i>19</i>
                <span>September</span>
            </div>
        </div>
        <div class="dob-meta">
            <figure><img src="images/resources/dob-cake.gif" alt=""></figure>
            <h6><a href="#" title="">Lucy Carbel</a> valentine's birthday</h6>
            <p>leave a message with your best wishes on his profile.</p>
        </div>
    </div><!-- birthday widget -->
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
            <a href="#" title="Advertisment"><img src="images/resources/ad-widget.gif" alt=""></a>
        </figure>
    </div><!-- advertisment box -->
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
                <a href="timeline-photos.html" title="">images</a>
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
    <div class="widget">
        <h4 class="widget-title">Recent Activity</h4>
        <ul class="activitiez">
            <li>
                <div class="activity-meta">
                    <i>10 hours Ago</i>
                    <span><a href="#" title="">Commented on Video posted </a></span>
                    <h6>by <a href="time-line.html">black demon.</a></h6>
                </div>
            </li>
            <li>
                <div class="activity-meta">
                    <i>30 Days Ago</i>
                    <span><a href="#" title="">Posted your status. “Hello guys, how are you?”</a></span>
                </div>
            </li>
            <li>
                <div class="activity-meta">
                    <i>2 Years Ago</i>
                    <span><a href="#" title="">Share a video on her timeline.</a></span>
                    <h6>"<a href="#">you are so funny mr.been.</a>"</h6>
                </div>
            </li>
        </ul>
    </div><!-- recent activites -->
    <div class="widget stick-widget">
        <h4 class="widget-title">Who's follownig</h4>
        <ul class="followers">
            <li>
                <figure><img src="images/resources/friend-avatar2.jpg" alt=""></figure>
                <div class="friend-meta">
                    <h4><a href="time-line.html" title="">Kelly Bill</a></h4>
                    <a href="#" title="" class="underline">Add Friend</a>
                </div>
            </li>
            <li>
                <figure><img src="images/resources/friend-avatar4.jpg" alt=""></figure>
                <div class="friend-meta">
                    <h4><a href="time-line.html" title="">Issabel</a></h4>
                    <a href="#" title="" class="underline">Add Friend</a>
                </div>
            </li>
            <li>
                <figure><img src="images/resources/friend-avatar6.jpg" alt=""></figure>
                <div class="friend-meta">
                    <h4><a href="time-line.html" title="">Andrew</a></h4>
                    <a href="#" title="" class="underline">Add Friend</a>
                </div>
            </li>
            <li>
                <figure><img src="images/resources/friend-avatar8.jpg" alt=""></figure>
                <div class="friend-meta">
                    <h4><a href="time-line.html" title="">Sophia</a></h4>
                    <a href="#" title="" class="underline">Add Friend</a>
                </div>
            </li>
            <li>
                <figure><img src="images/resources/friend-avatar3.jpg" alt=""></figure>
                <div class="friend-meta">
                    <h4><a href="time-line.html" title="">Allen</a></h4>
                    <a href="#" title="" class="underline">Add Friend</a>
                </div>
            </li>
        </ul>
    </div><!-- who's following -->
</aside>