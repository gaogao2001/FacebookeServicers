@extends('user.layouts.master')

@section('title', 'Fanpage')

@section('head.scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

@endsection

@section('content')

@if(session('success'))
<script>
    toastr.success("{{ session('success') }}", "Thành công", {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    });
</script>
@endif

@if(session('error'))
<script>
    toastr.error("{{ session('error') }}", "Lỗi", {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-left",
        "timeOut": "3000"
    });
</script>
@endif

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
                                                @php
                                                // Tính tổng số nhóm
                                                $totalGroups = 0;
                                                foreach($data['account']->groups as $groupGroup){
                                                $totalGroups += count($groupGroup);
                                                }
                                                @endphp
                                                <h5>Groups <span>{{ $totalGroups }}</span></h5>
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
                                                            <li><a title="" href="{{ route('profile.updateGroups', ['uid' => $uid]) }}">Reload Groups</a></li>

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
                                                    <h5>Create Group</h5>
                                                    <span>its only take a few minutes!</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @foreach($data['account']->groups as $groupGroup)
                                        @foreach($groupGroup as $group)
                                            <div class="col-lg-2 col-md-4 col-sm-4">
                                                <div class="group-box">
                                                    <figure><img src="/user/images/resources/group12.jpg" alt=""></figure>
                                                    <a href="#" title="" style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                        {{ $group->name }}
                                                    </a>
                                                    <span>125M Follow</span>
                                                    <button>Join Groups</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach

                                </div>
                                <div class="lodmore">
                                    <span>Viewing 1-11 of 302 Fanpage</span>
                                    <button class="btn-view btn-load-more"></button>
                                </div>
                            </div>
                            <div class="central-meta">
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
                            </div>
                        </div>
                    </div><!-- row merged20 -->
                </div><!-- col-lg-12 -->
            </div><!-- row -->
        </div><!-- container -->
    </div><!-- gap2 gray-bg -->
</section><!-- content -->

@endsection