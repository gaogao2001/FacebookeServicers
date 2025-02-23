@extends ('user.layouts.master')

@section('title', 'admin')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>


<style>
    /* Bao quanh mỗi cột sản phẩm nếu đang dùng Bootstrap .col-lg-6, v.v... */
    .col-lg-6,
    .col-md-6 {
        display: flex;
        /* biến cột thành flex container */
        flex-direction: column;
    }

    /* Mỗi card tự giãn chiếm hết chiều cao của cột */
    .dig-pro {
        display: flex;
        flex-direction: column;
        flex: 1;
        /* rất quan trọng để card kéo dài hết cột */
        margin-bottom: 20px;
        border: 1px solid #eee;
        border-radius: 6px;
        overflow: hidden;
        transition: all 0.3s ease-in-out;
    }

    /* Phần ảnh nằm ở đầu, không co giãn (cố định chiều cao) */
    .dig-pro figure {
        width: 100%;
        height: 200px;
        /* chiều cao mong muốn */
        overflow: hidden;
        margin: 0;
        position: relative;
    }

    .dig-pro figure img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* tránh méo ảnh */
    }

    /* Phần nội dung, cho phép chiếm hết không gian còn lại trong card */
    .digi-meta {
        flex: 1;
        /* tự giãn nốt phần còn lại của card */
        padding: 10px;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    /* Tiêu đề, nội dung, v.v... */
    .digi-meta h4 {
        margin-bottom: 5px;
    }

    /* Mô tả, giới hạn 3 dòng. Khi hover thì bung ra ở bên dưới */
    .digi-meta p {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        /* số dòng muốn hiển thị trước khi cắt */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: all 0.3s ease;
        margin-bottom: auto;
        /* đẩy các phần khác xuống cuối (nếu muốn) */
    }

    /* Khi hover, bỏ giới hạn */
    .dig-pro:hover .digi-meta p {
        -webkit-line-clamp: unset;
        max-height: none;
        overflow: visible;
    }

    /* Phần location + giá. Đặt dưới cùng digi-meta */
    .rate {
        margin-top: auto;
        /* đẩy nó xuống cuối digi-meta nếu muốn */
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Avatar người đăng, có thể đặt ngay trên ảnh hoặc trong phần meta tuỳ ý */
    .user-avatr {
        display: flex;
        align-items: center;
        border-radius: 20px;
        padding: 0px;
        width: 50px;
        height: 50px;
        position: absolute;
        bottom: 10px;
        left: 10px;
        /* nếu muốn đặt trên ảnh */
        background: rgba(255, 255, 255, 0.7);
    }

    .user-avatr img {
        width: 30px;
        height: 30px;
        object-fit: cover;
        border-radius: 50%;
        margin-right: 6px;
    }

    /* Điều chỉnh khoảng cách giữa các input */
    #createPostForm .form-group {
        margin-bottom: 8px;
        /* Giảm khoảng cách giữa các form-group */
    }

    #createPostForm .form-control {
        padding: 5px 10px;
        /* Giảm padding bên trong các input */
        font-size: 14px;
        /* Giảm kích thước font chữ */
    }

    /* Điều chỉnh modal tổng thể */
    .modal-content {
        padding: 10px;
        /* Giảm padding tổng thể trong modal */
        max-width: 500px;
        /* Giới hạn chiều rộng modal */
        margin: auto;
        /* Căn giữa modal */
    }

    /* Hiển thị ảnh preview đẹp hơn */
    #image-preview-container img {
        width: 80px;
        /* Giảm kích thước ảnh */
        height: 80px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }


    .popup form label {
        font-size: 13px;
        margin-bottom: 0px;
        margin-top: 0px;
        text-transform: capitalize;
        width: 100%;
    }

    .add-location-post {
        position: relative;
        z-index: 9999;
        /* tránh bị che */
        display: none;
        /* ẩn mặc định */
    }

    #us3,
    #us3-inline {
        width: 100%;
        height: 400px;
        /* hoặc chiều cao tuỳ chỉnh */
        z-index: 10000;
        pointer-events: auto;
    }

    .popup-wraper {

        overflow: auto;

    }

    /* Popup nội dung */
    .popup {

        overflow-y: auto;

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

    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 100000;
        /* Đảm bảo z-index rất cao */
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
                                    <li><a class="send-mesg" href="#" title="Send Message" data-toggle="tooltip"><i
                                                class="fa fa-comment"></i></a></li>
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
                                                <a class="h4 author-name" href="about.html">Digital Market</a>
                                                <div class="country">Ontario, CA</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-10 col-md-9">
                                        <ul class="profile-menu">
                                            <li>
                                                <a class="" href="{{ route('profile.showMyPost', ['uid' => $uid]) }}">Shop</a>
                                            </li>
                                            <li>
                                                <a class="active" href="shop2.html">Market Place</a>
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
                                <div class="central-meta no-margin job-search-form" style="height: 80px !important">
                                    <ul class="align-right user-ben">
                                        <li class="search-for">
                                            <a data-ripple="" class="circle-btn search-data " title="" href="#"><i class="ti-search"></i></a>
                                            <form class="searchees c-form" id="searchForm" method="POST" style="padding-top:30px ;" style="display: none;">
                                                <span class="cancel-search"><i class="ti-close"></i></span>
                                                <div class="row merged10">
                                                    <div class="col-lg-2 col-md-3">
                                                        <span class="add-loc">
                                                            <input type="text" placeholder="location">
                                                        </span>
                                                        <div class="searchbylocation">

                                                            <div class="add-location-post fa fa-map-marker">
                                                                <button class="setLocationBtn" type="button">Chọn Vị Trí này</button>
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
                                                                <div id="us3" style="height: 360px;  width: 100%;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-9">
                                                        <input type="text" name="description" placeholder="Description">
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
                                                    <div class="col-lg-2 col-md-6">
                                                        <input type="text" placeholder="Price">
                                                    </div>
                                                    <div class="col-lg-1 col-md-12">
                                                        <button type="button" id="findBtn" class="main-btn">Find</button>
                                                    </div>
                                                </div>

                                            </form>
                                        </li>

                                        <li><a href="#" title="" class="main-btn create-pst" data-ripple="">Add New Post</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div><!-- nave list -->
                        <div class="col-lg-3">
                            <aside class="sidebar static left">
                                <div class="widget">
                                    <h4 class="widget-title">Bộ lọc tìm kiếm</h4>
                                    <form class="c-form search" method="post">
                                        <div>
                                            <label>Danh mục sản phẩm</label>
                                            <div class="form-radio">
                                                <div class="radio">
                                                    @foreach($categories->mainCategories as $category)
                                                    <label>
                                                        <input type="radio" checked="checked" name="category" value="{{ $category->id }}"><i class="check-box"></i>{{ $category->name }}
                                                    </label>
                                                    @endforeach
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
                                        <div>
                                            <label>Price Range</label>
                                            <div class="filter-meta">
                                                <span>price</span>
                                                <div id="slider-range"></div>
                                                <input type="text" id="amount" readonly>
                                            </div>
                                        </div><!-- range slider -->
                                    </form>
                                </div>
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
                                <div class="advertisment-box">
                                    <h4 class="">advertisment</h4>
                                    <figure>
                                        <a href="#" title="Advertisment"><img src="/user/images/resources/ad-widget.gif" alt=""></a>
                                    </figure>
                                </div><!-- ad banner -->
                            </aside>
                        </div><!-- sidebar -->
                        <div class="col-lg-9">
                            <div class="central-meta">
                                <h4 class="create-post">Latest Items</h4>
                                <div class="row">
                                    @foreach($products->reponse as $product)
                                    <div class="col-lg-6 col-md-6">
                                        <div class="dig-pro">
                                            <figure>
                                                @if(!empty($product->product->images) && is_array($product->product->images))
                                                <img src="{{ $product->product->images[0] }}" alt="">
                                                @else
                                                <img src="/path/to/default-image.jpg" alt="Default Image">
                                                @endif
                                                <div class="user-avatr">
                                                    <img alt="" src="{{$product->postBy->avatar}}">
                                                    <div>
                                                        <span>Posted by</span>
                                                        <ins>{{$product->postBy->name}}</ins>
                                                    </div>
                                                </div>
                                            </figure>
                                            <div class="digi-meta">
                                                <h4><a href="product-detail.html" title=""> {{$product->product->title}} </a></h4>
                                                <p> {{$product->product->content}}</p>
                                                <div class="rate" style="display: flex; align-items: center; justify-content: space-between;">
                                                    <ol class="location">
                                                        <li><i class="fa fa-map-marker"></i> {{$product->product->location}}</li>
                                                    </ol>
                                                    <span class="qeemat"><del></del>{{$product->product->amount}} VND</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach

                                    <div class="col-lg-12">
                                        <div class="lodmore">
                                            <span>View More Oldest Posts</span>
                                            <div class="auto-load">
                                                <div class="wave">
                                                    <span class="dot"></span>
                                                    <span class="dot"></span>
                                                    <span class="dot"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section><!-- content -->

<div class="popup-wraper">
    <div class="popup">
        <span class="popup-closed"><i class="ti-close"></i></span>
        <div class="popup-meta">
            <div class="popup-head">
                <h5>Create New Post</h5>
            </div>
            <div class="postbox">
                <div class="new-postbox">
                    <figure>
                        <img src="/user/images/resources/admin.jpg" alt="User Avatar">
                    </figure>
                    <div class="newpst-input">
                        <form id="createPostForm" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title" style="font-weight: bold;">Tiêu đề:</label>
                                <input type="text" id="title" name="title" placeholder="Nhập tiêu đề bài viết" class="form-control" style="border: 1px solid black;">
                            </div>
                            <div class="form-group">
                                <label for="content" style="font-weight: bold;">Nội dung:</label>
                                <textarea id="content" name="content" rows="4" placeholder="Nhập nội dung bài viết" class="form-control" style="border: 1px solid black;"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="price" style="font-weight: bold;">Giá tiền:</label>
                                <input type="number" id="price" name="price" placeholder="Nhập giá tiền" class="form-control" style="border: 1px solid black;">
                            </div>
                            <div class="form-group">
                                <label for="post_group" style="font-weight: bold;">Post Groups:</label>
                                <select name="post_group" class="form-control" style="border: 1px solid black;">
                                    <option value="1">Có</option>
                                    <option value="0">Không</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="images" style="font-weight: bold;">Danh sách hình ảnh:</label>
                                <input type="file" id="images" name="images[]" accept="image/*" multiple class="form-control" style="border: 1px solid black;">
                                <div id="image-preview-container"></div>
                            </div>
                        </form>
                    </div>
                    <div class="attachments">
                        <ul>
                            <li><span class="add-loc"><i class="fa fa-map-marker"></i></span></li>
                            <li><i class="fa fa-music"></i><label class="fileContainer"><input type="file" name="music"></label></li>
                            <li><i class="fa fa-image"></i><label class="fileContainer"><input type="file" name="image"></label></li>
                            <li><i class="fa fa-video-camera"></i><label class="fileContainer"><input type="file" name="video"></label></li>
                            <li><i class="fa fa-camera"></i><label class="fileContainer"><input type="file" name="camera"></label></li>
                        </ul>
                        <div class="add-location-post" style="display: none;">
                            <span>Drag map point to selected area</span>
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="control-label">Lat :</label>
                                    <input type="text" id="us3-inline-lat" readonly />
                                </div>
                                <div class="col-lg-6">
                                    <label>Long :</label>
                                    <input type="text" id="us3-inline-lon" readonly />
                                </div>
                            </div>
                            <!-- Đổi id thành duy nhất, ví dụ: us3-inline -->
                            <div id="us3-inline" style="height: 300px;"></div>
                        </div>
                        <button class="post-btn" type="submit">Post</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('footer.scripts')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script src="{{ asset('assets/admin/js/map.js') }}"></script>

<script>
    document.getElementById('images').addEventListener('change', function(event) {
        const imagePreviewContainer = document.getElementById('image-preview-container');
        const files = Array.from(event.target.files); // Chuyển FileList thành mảng
        const pathsArray = []; // Lưu các path (tên file) vào đây

        files.forEach((file) => {
            // Lấy tên file (không lấy được đường dẫn tuyệt đối do lý do bảo mật)
            let path = file.webkitRelativePath ? file.webkitRelativePath : file.name;
            pathsArray.push(path);

            const fileReader = new FileReader();
            fileReader.onload = function(e) {
                // Tạo container hiển thị hình ảnh
                const imageContainer = document.createElement('div');
                imageContainer.style.position = 'relative';
                imageContainer.style.display = 'inline-block';
                imageContainer.style.margin = '5px';

                // Tạo thẻ img để hiển thị hình ảnh
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = file.name;
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.style.border = '1px solid #ddd';
                img.style.borderRadius = '6px';

                // Tạo nút xóa
                const removeButton = document.createElement('button');
                removeButton.textContent = '×';
                removeButton.style.position = 'absolute';
                removeButton.style.top = '5px';
                removeButton.style.right = '5px';
                removeButton.style.backgroundColor = '#1c1b1b';
                removeButton.style.color = 'white';
                removeButton.style.border = 'none';
                removeButton.style.borderRadius = '50%';
                removeButton.style.width = '20px';
                removeButton.style.height = '20px';
                removeButton.style.display = 'flex';
                removeButton.style.alignItems = 'center';
                removeButton.style.justifyContent = 'center';
                removeButton.style.cursor = 'pointer';

                // Xử lý sự kiện xóa file
                removeButton.addEventListener('click', function() {
                    imageContainer.remove();

                    // Cập nhật lại FileList
                    const dt = new DataTransfer();
                    Array.from(document.getElementById('images').files).forEach((f) => {
                        if (f.name !== file.name || f.lastModified !== file.lastModified) {
                            dt.items.add(f);
                        }
                    });
                    document.getElementById('images').files = dt.files;
                });

                // Thêm img và nút xóa vào container
                imageContainer.appendChild(img);
                imageContainer.appendChild(removeButton);

                // Thêm container vào vùng preview
                imagePreviewContainer.appendChild(imageContainer);
            };

            fileReader.readAsDataURL(file);
        });

        // Kiểm tra mảng path
        console.log('Mảng chứa tên file:', pathsArray);
    });
</script>
<script>
    $(document).ready(function() {


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

        // Thiết lập CSRF Token cho tất cả các yêu cầu AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Xử lý sự kiện submit của form
        // Handle the Find button click
        $('#findBtn').on('click', function(e) {
            e.preventDefault(); // Ngăn chặn hành động mặc định của form

            // Thu thập dữ liệu từ form
            const uid = '{{ $uid }}'; // Đảm bảo biến $uid được truyền từ Blade
            const location = $('input[name="location"]').val();
            const description = $('input[name="description"]').val();
            const category = $('select[name="category"]').val();
            const price = $('input[name="price"]').val();

            // Thu thập dữ liệu latitude và longitude (nếu có)
            const lat = $('#us3-lat').val();
            const lon = $('#us3-lon').val();

            // AJAX request
            $.ajax({
                url: `/Android/SearchProduct/${uid}`, // Đường dẫn đến route xử lý
                method: 'POST',
                data: {
                    location,
                    description,
                    category,
                    price,
                    lat,
                    lon
                },
                success: function(response) {
                    // Kiểm tra kết quả trả về
                    if (response.status && response.reponse.length === 0) {
                        // Không có kết quả phù hợp
                        toastr.info('Không có kết quả phù hợp với từ khoá tìm kiếm.', 'Thông báo');
                    } else if (response.status) {
                        // Hiển thị kết quả thành công
                        toastr.success('Tìm kiếm hoàn tất!', 'Thông báo');
                        console.log('Kết quả:', response.reponse);
                    } else {
                        // Xử lý nếu có lỗi khác
                        toastr.error(response.message || 'Đã xảy ra lỗi.', 'Thông báo');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    toastr.error('Đã xảy ra lỗi khi tìm kiếm sản phẩm.', 'Thông báo');
                }
            });
        });

        // Hiển thị/Ẩn form tìm kiếm khi nhấn nút tìm kiếm
        $('.search-data').on('click', function(e) {
            e.preventDefault();
            $('.searchees').toggle();
        });

        // Hủy tìm kiếm khi nhấn nút đóng
        $('.cancel-search').on('click', function() {
            $('.searchees').hide();
        });

        $('.setLocationBtn').on('click', function() {
            // Lấy dữ liệu latitude, longitude và uid từ giao diện
            let lat = $('#us3-lat').val();
            let lon = $('#us3-lon').val();
            let uid = '{{ $uid }}'; // Biến uid được truyền từ Blade

            // Gửi AJAX đến route SetLocation
            $.ajax({
                url: `/Android/SetLocation/${uid}`,
                method: 'GET',
                data: {
                    latitude: lat,
                    longitude: lon
                },
                success: function(response) {
                    console.log(response);
                    toastr.success('Vị trí đã được cập nhật thành công!', 'Thông báo');
                    setTimeout(function() {
                        location.reload(); // Reload toàn bộ trang
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    toastr.error('Không thể cập nhật vị trí. Vui lòng thử lại sau.', 'Thông báo');
                }
            });
        });

        $('.post-btn').on('click', function(e) {
            e.preventDefault();

            let postButton = $(this);
            postButton.prop('disabled', true).text('Đang tải lên bài đăng...'); // Vô hiệu hóa nút và đổi text

            showLoading();

            let lat = $('#us3-inline-lat').val();
            let lon = $('#us3-inline-lon').val();
            let uid = '{{ $uid }}';

            let formData = new FormData($('#createPostForm')[0]);
            formData.append('latitude', lat);
            formData.append('longitude', lon);

            $.ajax({
                url: '/Android/PostMarket/' + uid,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    hideLoading();
                    toastr.success('Đăng bài thành công');
                    // Reset form và vùng preview hình ảnh
                    $('#createPostForm')[0].reset();
                    $('#image-preview-container').empty();
                    location.reload(); // Reload toàn bộ trang
                },
                error: function(err) {
                    hideLoading();
                    toastr.error('Có lỗi xảy ra');
                    console.error(err);
                },
            });
        });
    });
</script>
@endsection