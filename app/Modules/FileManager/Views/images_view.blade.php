@extends('admin.layouts.master')

@section('title', 'Images')

@section('head.scripts')
<style>
    .card-img-top {
        width: 100%;
        height: 400px;
        object-fit: cover;
    }
    .modal-body {
        max-height: 500px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper " style="background: #191c24;">
    <button type="button" class="btn btn-primary" onclick="showImageSelector()">Chọn Hình Ảnh</button>
</div>
<!-- Modal Image Selector -->
<div class="modal fade" id="imageSelectorModal" tabindex="-1" role="dialog" aria-labelledby="imageSelectorLabel" aria-hidden="true">
    <!-- Giới hạn độ rộng modal còn 2/3 của màn hình -->
    <div class="modal-dialog modal-lg" role="document" style="max-width:66.67%;">
        <div class="modal-content wrapper">
            <div class="modal-header">
                <!-- Nút Back hiện ở trước tiêu đề modal -->
                <button id="backImageButton" type="button" class="btn btn-secondary mr-2" style="display:none;" onclick="goBackImage()">Back</button>
                <h5 class="modal-title" id="imageSelectorLabel">Chọn hình ảnh</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Thư mục: <span id="currentImageFolder">root</span></p>
                <div id="imageList" class="row">
                    <!-- Danh sách folder hoặc hình ảnh sẽ được load tại đây -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer.scripts')
<script>
    // Giả sử biến $tree được truyền từ controller chứa cấu trúc images đã group theo thư mục
    // VD: $tree = ["1046151503" => [ {name, path, url}, {...} ], "1046151504" => [ ... ] ];
    const imagesTree = {!! json_encode($tree) !!};
    let currentImageFolder = null;
    let imageFolderStack = [];

    // Hàm load danh sách folder hoặc hình ảnh dựa trên currentImageFolder
    function loadImageList() {
        const imageList = document.getElementById('imageList');
        imageList.innerHTML = '';
        const folderName = currentImageFolder ? currentImageFolder : 'root';
        document.getElementById('currentImageFolder').innerText = folderName;
        // Hiển thị nút back nếu không ở root
        document.getElementById('backImageButton').style.display = currentImageFolder ? "inline-block" : "none";

        if (!currentImageFolder) {
            // Hiển thị danh sách folder (là key của imagesTree)
            for (let folder in imagesTree) {
                const colDiv = document.createElement('div');
                colDiv.className = "col-md-3 mb-3";
                const card = document.createElement('div');
                card.className = "card";
                card.style.cursor = "pointer";
                card.onclick = function() {
                    imageFolderStack.push(currentImageFolder);
                    currentImageFolder = folder;
                    loadImageList();
                };
                const cardBody = document.createElement('div');
                cardBody.className = "card-body text-center";
                cardBody.innerText = folder;
                card.appendChild(cardBody);
                colDiv.appendChild(card);
                imageList.appendChild(colDiv);
            }
        } else {
            // Hiển thị danh sách hình ảnh trong thư mục hiện tại
            const images = imagesTree[currentImageFolder];
            images.forEach(image => {
                const colDiv = document.createElement('div');
                colDiv.className = "col-md-3 mb-3";
                const card = document.createElement('div');
                card.className = "card";
                card.style.cursor = "pointer";
                card.onclick = function() {
                    selectImage(image);
                };
                const imgElem = document.createElement('img');
                imgElem.className = "card-img-top";
                imgElem.src = image.url;
                imgElem.alt = image.name;
                const cardBody = document.createElement('div');
                cardBody.className = "card-body text-center p-2";
                cardBody.innerText = image.name;
                card.appendChild(imgElem);
                card.appendChild(cardBody);
                colDiv.appendChild(card);
                imageList.appendChild(colDiv);
            });
        }
    }

    function goBackImage() {
        if (imageFolderStack.length) {
            currentImageFolder = imageFolderStack.pop();
            loadImageList();
        } else {
            currentImageFolder = null;
            loadImageList();
        }
    }

    // Hàm hiển thị modal chọn hình ảnh
    function showImageSelector() {
        currentImageFolder = null;
        imageFolderStack = [];
        loadImageList();
        $('#imageSelectorModal').modal('show');
    }

    // Hàm xử lý khi người dùng chọn hình ảnh
    function selectImage(image) {
        alert("Đã chọn hình ảnh: " + image.url);
        $('#imageSelectorModal').modal('hide');
    }
</script>
@endsection