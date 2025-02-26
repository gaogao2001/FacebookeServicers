<!-- FILE: content_manager.blade.php -->
@extends('admin.layouts.master')

@section('title', 'Quản lí nội dung')

@section('head.scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.tiny.cloud/1/c0wi91owheqj5xvveg7tdhzx5q3l5ylnapfvs8gqbvn749tl/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script src="{{ asset('modules/content_manager/js/content_manager.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('modules/content_manager/css/ContentManager.css') }}">



@endsection

@section('content')


<div class="content-wrapper " style="background: #191c24;">

    <div class="row" style="padding-top:20px; padding-left:13px;">
        <div class="col-md-12 grid-margin">
            <div class="card wrapper">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Quản lí nội dung</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <!-- Danh sách nội dung -->
            <div class="col-md-6">
                <div class="card wrapper">
                    <div class="card-body">
                        <h4 class="card-title">Content</h4>

                        <hr>
                        <div class="table-responsive" style="padding-top: 20px;">
                            <table id="contentTable" class="table ">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Created Time</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="contentList">
                                    <!-- Danh sách các nội dung sẽ được tải ở đây -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form chỉnh sửa nội dung -->
            <div class="col-md-6">
                <div class="card wrapper">
                    <div class="card-body ">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Chỉnh sửa nội dung</h4>
                            <button type="button" class="btn btn-outline-primary btn-fw" id="saveContent">Lưu</button>
                        </div>
                        <hr>
                        <form id="editContentForm" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="contentTitle">Title</label>
                                <input type="text" class="edit-form-control" id="contentTitle" name="title" required>
                            </div>
                            <div class="form-group" id="priceInputGroup" style="display: none;">
                                <label for="contentPrice">Price</label>
                                <input type="number" step="any" class="edit-form-control" id="contentPrice" name="price" placeholder="Nhập giá">
                            </div>
                            <div class="form-group">
                                <label>Đăng Lên</label>
                                <select class="form-control" id="post_platform" name="post_platform">
                                    <option value="">Chọn nền tảng</option>
                                    <option value="FacebookProfile">Facebook Profile</option>
                                    <option value="FacebookGroup">Facebook Group</option>
                                    <option value="FacebookPages">Facebook Pages</option>
                                    <option value="FacebookMarketplace">Facebook Marketplace</option>
                                    <option value="ZaloGroup">Zalo Group</option>
                                    <option value="ChoTot">Chợ Tốt</option>
                                    <option value="Shopee">Shopee</option>
                                    <option value="TiktokProfile">Tiktok Profile</option>
                                    <option value="FacebookReels">Facebook reels</option>
                                </select>
                                <div id="locationPicker" style="display: none; margin-top: 10px;">
                                    <button type="button" class="btn btn-primary" id="btnOpenMapModal">
                                        Chọn Vị Trí
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" id="latitude" name="latitude" value="">
                            <input type="hidden" id="longitude" name="longitude" value="">
                            <div class="form-group">
                                <label for="contentImage">Hình ảnh</label>
                                <!-- Ẩn input file dùng cho tải ảnh từ máy -->
                                <input type="file" class="form-control" id="contentImage" name="img[]" accept="image/*" multiple style="display: none;">
                                <!-- Button chính để chọn hình ảnh (mở modal option) -->
                                <button type="button" class="btn btn-info mt-2" id="btnSelectImageOption">Chọn hình ảnh</button>
                                <div id="previewImages"></div>
                                <div id="currentImages"></div>
                            </div>
                            <div class="form-group">
                                <label for="contentBody">Nội dung</label>
                                <textarea id="contentBody" name="content"></textarea>
                            </div>
                            <input type="hidden" id="contentId" name="id">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CHỌN VỊ TRÍ - KHÔNG CHỨA FORM NỮA -->
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chọn vị trí trên bản đồ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Bản đồ Leaflet -->
                <div id="mapContainer" style="height: 500px;"></div>
            </div>

            <div class="modal-footer">
                <!-- Nút xác nhận => cập nhật #latitude, #longitude form chính -->
                <button class="btn btn-success" id="btnConfirmLocation">Xác nhận toạ độ</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imageOptionModal" tabindex="-1" role="dialog" aria-labelledby="imageOptionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content wrapper">
            <div class="modal-header">
                <h5 class="modal-title" id="imageOptionModalLabel">Chọn nguồn hình ảnh</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <button type="button" class="btn btn-primary" id="btnUploadFromLocal">Tải ảnh từ máy</button>
                <button type="button" class="btn btn-secondary" id="btnSelectFromFileManager">Chọn ảnh từ FileManager</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal chọn hình ảnh từ FileManager (cho chọn nhiều hình) -->
<!-- Cập nhật modal chọn hình ảnh từ FileManager để hỗ trợ cả video -->
<div class="modal fade" id="contentImageSelectorModal" tabindex="-1" role="dialog" aria-labelledby="contentImageSelectorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width:66.67%;">
        <div class="modal-content wrapper">
            <div class="modal-header">
                <!-- Nút Back để quay lại danh sách folder -->
                <button id="backContentMediaButton" type="button" class="btn btn-secondary mr-2" style="display:none;" onclick="goBackContentMedia()">Back</button>
                <h5 class="modal-title" id="contentImageSelectorLabel">Chọn media</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Thêm tab cho Images và Videos -->
            <ul class="nav nav-tabs" id="mediaTypeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="images-tab" data-bs-toggle="tab" href="#images-content" role="tab">Images</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="videos-tab" data-bs-toggle="tab" href="#videos-content" role="tab">Videos</a>
                </li>
            </ul>

            <div class="tab-content" id="mediaTypeTabsContent">
                <!-- Tab Images -->
                <div class="tab-pane fade show active" id="images-content" role="tabpanel">
                    <div class="modal-body">
                        <p>Thư mục: <span id="currentContentImageFolder">root</span></p>
                        <div id="contentImageList" class="row">
                            <!-- Danh sách folder/hình sẽ được load tại đây -->
                        </div>
                    </div>
                </div>

                <!-- Tab Videos -->
                <div class="tab-pane fade" id="videos-content" role="tabpanel">
                    <div class="modal-body">
                        <p>Thư mục: <span id="currentContentVideoFolder">root</span></p>
                        <div id="contentVideoList" class="row">
                            <!-- Danh sách folder/video sẽ được load tại đây -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btnConfirmFileManagerSelection">Xác nhận chọn media</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>>

@endsection

@section('foot.scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- Tạo map, marker, ... -->
<script>
    // Moved map initialization to content_manager.js
</script>

@endsection