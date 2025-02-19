@extends('user.layouts.master')

@section('title', 'Fanpage')

@section('head.scripts')
@endsection

@section('content')

<section>
    <div class="gap2 gray-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row merged20" id="page-contents">
                        @include('user.pages.Profile.Profile_baner')
                        <div class="col-lg-12">
                            <div class="central-meta">
                                <div class="title-block">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="align-left">
                                                <h5>Fanpage <span>{{count($data['account']->MultiAccount)}}</span></h5>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row merged20">
                                                <div class="col-lg-7 col-md-7 col-sm-7">
                                                    <form method="post">
                                                        <input type="text" placeholder="Search Group">
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
                                                            <li><a title="" href="#">Show Friends Public</a></li>
                                                            <li><a title="" href="#">Show Friends Private</a></li>
                                                            <li><a title="" href="#">Mute Notifications</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- title block -->
                            <div class="central-meta padding30">
                                <div class="row">
                                    <div class="col-lg-2 col-md-4 col-sm-4">
                                        <div class="addgroup">
                                            <div class="item-upload">
                                                <i class="fa fa-plus-circle"></i>
                                                <div class="upload-meta">
                                                    <h5>Create Fanpage</h5>
                                                    <span>its only take a few minutes!</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @foreach($data['account']->MultiAccount as $account)
                                    <div class="col-lg-2 col-md-4 col-sm-4">
                                        <div class="group-box">
                                            <figure><img src="{{$account->profile->profile_picture['uri']}}" alt=""></figure>
                                            <a href="#" title="{{$account->profile->name}}" style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{$account->profile->name}}</a>
                                            <span>125M Follow</span>
                                            <button>join Fanpage</button>
                                        </div>
                                    </div>
                                    @endforeach
                                    
                                </div>
                                <div class="lodmore">
                                    <span>Viewing 1-11 of 302 Fanpage</span>
                                    <button class="btn-view btn-load-more"></button>
                                </div>
                            </div>
                            <!-- <div class="central-meta">
                                <div class="author-info">
                                    <h4>Suggested Groups</h4>
                                    <p>You May join more these relevant groups.</p>
                                </div>
                                <ul class="related-groups">
                                    <li>
                                        <div class="group-box">
                                            <figure><img src="/user/images/resources/group12.jpg" alt=""></figure>
                                            <a href="#" title="">Big Biker</a>
                                            <span>15K Members</span>
                                            <button>join group</button>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="group-box">
                                            <figure><img src="/user/images/resources/group13.jpg" alt=""></figure>
                                            <a href="#" title="">Blue Tech</a>
                                            <span>12k Members</span>
                                            <button>join group</button>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="group-box">
                                            <figure><img src="/user/images/resources/group14.jpg" alt=""></figure>
                                            <a href="#" title="">Gold Movies</a>
                                            <span>125M Members</span>
                                            <button>join group</button>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="group-box">
                                            <figure><img src="/user/images/resources/group15.jpg" alt=""></figure>
                                            <a href="#" title="">Musicly Friends</a>
                                            <span>22M Members</span>
                                            <button>join group</button>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="group-box">
                                            <figure><img src="/user/images/resources/group16.jpg" alt=""></figure>
                                            <a href="#" title="">AFC Cafe</a>
                                            <span>5k Members</span>
                                            <button>join group</button>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="group-box">
                                            <figure><img src="/user/images/resources/group17.jpg" alt=""></figure>
                                            <a href="#" title="">Volunteers</a>
                                            <span>12k Members</span>
                                            <button>join group</button>
                                        </div>
                                    </li>
                                </ul>
                            </div> -->
                        </div>
                    </div><!-- row merged20 -->
                </div><!-- col-lg-12 -->
            </div><!-- row -->
        </div><!-- container -->
    </div><!-- gap2 gray-bg -->
</section><!-- content -->

@endsection