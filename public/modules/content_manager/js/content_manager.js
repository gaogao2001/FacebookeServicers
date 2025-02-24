// FILE: content_manager.js
$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    // Khi user chọn platform = ChoTot, Shopee, FacebookMarketplace => hiển thị nút
    $('#post_platform').on('change', function () {
        const val = $(this).val();
        if (val === 'ChoTot' || val === 'Shopee' || val === 'FacebookMarketplace') {
            $('#locationPicker').show();
        } else {
            $('#locationPicker').hide();
            // Xoá lat/lng
            $('#latitude').val('');
            $('#longitude').val('');
        }
    });

    // Khi bấm "Thêm vị trí" => mở modal
    $('#btnOpenMapModal').click(function () {
        $('#mapModal').modal('show');
    });

    // Initialize map when modal is shown
    $('#mapModal').on('shown.bs.modal', function () {
        if (!mapInited) {
            initMap();   // Gọi hàm khởi tạo
            mapInited = true;
        }
        // Fix size Leaflet map sau khi modal hiển thị
        setTimeout(function () {
            map.invalidateSize();
        }, 200);
    });

    // Khi ấn "Xác nhận toạ độ" trong modal => gán vào hidden main form
    $('#btnConfirmLocation').click(function () {
        $('#latitude').val(selectedLat.toFixed(6));
        $('#longitude').val(selectedLng.toFixed(6));
        console.log('Latitude:', selectedLat);
        console.log('Longitude:', selectedLng);
        $('#mapModal').modal('hide');
    });

    // Khai báo mảng lưu trữ các file hình ảnh được chọn
    let selectedImages = [];
    let existingImages = [];

    // Hàm hiển thị thông báo sử dụng SweetAlert2
    function showAlert(type, message) {
        if (!message || typeof message !== 'string') {
            console.error('Invalid alert message:', message);
            return;
        }
        Swal.fire({
            icon: type, // 'success', 'error', 'warning', 'info', 'question'
            title: message,
            showConfirmButton: false,
            timer: 3000
        });
    }

    // Khởi tạo TinyMCE
    tinymce.init({
        selector: '#contentBody',
        width: 650,
        height: 500,
        plugins: [
            'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
            'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime',
            'media', 'table', 'emoticons', 'help'
        ],
        toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
            'forecolor backcolor emoticons | help',
        menubar: 'favs file edit view insert format tools table help',
        images_upload_url: '/upload-image',
        automatic_uploads: true,
        file_picker_types: 'image',
        images_reuse_filename: true,
        relative_urls: false,
        convert_urls: false,
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr = new XMLHttpRequest();
            var formData = new FormData();

            xhr.open('POST', '/upload-image');
            xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));

            xhr.onload = function () {
                if (xhr.status === 200) {
                    const json = JSON.parse(xhr.responseText);
                    if (json && json.location) {
                        success(json.location);
                    } else {
                        failure('Upload failed');
                    }
                } else {
                    failure(`HTTP Error: ${xhr.status}`);
                }
            };

            formData.append('file', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);
        }
    });

    function loadContents() {
        $.ajax({
            url: '/content-manager',
            method: 'GET',
            success: function (contents) {
                const contentList = $('#contentList');
                contentList.empty();

                contents.forEach(content => {
                    const row = `
                        <tr>
                            <td>${content.title}</td>
                            <td>${content.created_time}</td>
                           
                            <td>
                                <button class="btn btn-inverse-light edit-content" data-id="${content._id.$oid}">
                                    <i class="fa fa-edit"></i> Sửa
                                </button>
                                <button class="btn btn-inverse-danger delete-content" data-id="${content._id.$oid}">
                                    <i class="fa fa-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    `;
                    contentList.append(row);
                });

                // Thêm sự kiện cho các nút Sửa và Xóa
                $('.edit-content').click(function () {
                    const id = $(this).data('id');
                    editContent(id);
                });

                $('.delete-content').click(function () {
                    const id = $(this).data('id');
                    deleteContent(id);
                });
            },
            error: function (xhr) {
                console.error('Lỗi khi tải danh sách nội dung:', xhr.responseText);
                showAlert('error', 'Đã xảy ra lỗi khi tải danh sách nội dung.');
            }
        });
    }

    $('#saveContent').click(function () {
        const contentId = $('#contentId').val();
        const title = $('#contentTitle').val().trim();
        const content = tinymce.get('contentBody').getContent().trim();
        const postPlatform = $('#post_platform').val();
        const price = $('#contentPrice').val().trim();
        const latitude = $('#latitude').val();
        const longitude = $('#longitude').val();

        if (!title || !content) {
            showAlert('error', 'Title và Nội dung không được để trống.');
            return;
        }

        const url = contentId ? `/content-manager/${contentId}` : '/content-manager';
        const method = 'POST';

        const formData = new FormData();
        formData.append('title', title);
        formData.append('content', content);
        formData.append('post_platform', postPlatform);
        formData.append('price', price);
        formData.append('latitude', latitude);
        formData.append('longitude', longitude);

        // Thêm các hình ảnh được chọn vào formData
        selectedImages.forEach((file) => {
            formData.append('img[]', file);
        });

        // Gửi danh sách hình ảnh hiện có (sau khi đã xoá những ảnh người dùng muốn xoá)
        formData.append('existing_imgs', JSON.stringify(existingImages));

        if (contentId) {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                showAlert('success', contentId ? 'Cập nhật nội dung thành công!' : 'Thêm mới nội dung thành công!');
                loadContents();
                $('#editContentForm')[0].reset();
                tinymce.get('contentBody').setContent('');
                $('#contentId').val('');
                $('#currentImages').empty();
                $('#previewImages').empty();
                selectedImages = [];
                existingImages = [];
                // Reset coordinates
                $('#latitude').val('');
                $('#longitude').val('');
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessages = '';
                    for (const field in errors) {
                        errorMessages += errors[field].join('<br>');
                    }
                    showAlert('error', errorMessages || 'Có lỗi xảy ra khi xử lý yêu cầu.');
                } else {
                    console.error(contentId ? 'Lỗi khi cập nhật nội dung:' : 'Lỗi khi thêm nội dung:', xhr.responseText);
                    showAlert('error', contentId ? 'Đã xảy ra lỗi khi cập nhật nội dung.' : 'Đã xảy ra lỗi khi thêm nội dung.');
                }
            }
        });
    });

    function editContent(id) {
        $.ajax({
            url: `/content-manager/${id}`,
            method: 'GET',
            success: function (content) {
                $('#contentTitle').val(content.title);
                tinymce.get('contentBody').setContent(content.content);
                $('#contentId').val(content._id.$oid);
                $('#post_platform').val(content.post_platform || '');
                $('#post_platform').trigger('change');
                $('#contentPrice').val(content.price || '');
                $('#latitude').val(content.latitude || '');
                $('#longitude').val(content.longitude || '');

                // Reset các hình ảnh mới chọn
                selectedImages = [];
                $('#previewImages').empty();

                // Hiển thị các hình ảnh hiện có
                if (content.imgs) {
                    $('#currentImages').empty().show();
                    existingImages = JSON.parse(content.imgs);
                    existingImages.forEach(function (imgUrl) {
                        // Chuyển đổi đường dẫn tuyệt đối thành URL truy cập được bằng cách loại bỏ phần public path
                        const publicPathPrefix = "/var/www/FacebookService/public/";
                        let displayUrl = imgUrl;
                        if(imgUrl.indexOf(publicPathPrefix) === 0){
                            displayUrl = imgUrl.replace(publicPathPrefix, '/');
                        }
                        
                        const imgName = displayUrl.substring(displayUrl.lastIndexOf('/') + 1);
                        const imgElement = $(`
                            <div class="existing-image">
                                <img src="${displayUrl}" alt="Image">
                                <button type="button" class="remove-existing-image">&times;</button>
                            </div>
                        `);
                    
                        // Xử lý sự kiện xoá hình ảnh hiện có
                        imgElement.find('.remove-existing-image').click(function () {
                            const index = existingImages.indexOf(imgUrl);
                            if (index > -1) {
                                existingImages.splice(index, 1);
                            }
                            imgElement.remove();
                        });
                    
                        $('#currentImages').append(imgElement);
                    });
                } else {
                    $('#currentImages').hide();
                }

                // Set map coordinates if available
                if (content.latitude && content.longitude) {
                    selectedLat = parseFloat(content.latitude);
                    selectedLng = parseFloat(content.longitude);
                    if (mapInited && draggableMarker) {
                        draggableMarker.setLatLng([selectedLat, selectedLng]);
                        map.setView([selectedLat, selectedLng], 13);
                    }
                }
            },
            error: function (xhr) {
                console.error('Lỗi khi lấy nội dung cần sửa:', xhr.responseText);
                showAlert('error', 'Đã xảy ra lỗi khi lấy nội dung cần sửa.');
            }
        });
    }

    $('#contentImage').on('change', function () {
        const files = Array.from(this.files);

        files.forEach((file) => {
            const fileName = file.name;

            // Lấy danh sách tên hình ảnh hiện có
            const existingImageNames = existingImages.map(url => url.substring(url.lastIndexOf('/') + 1));
            const selectedImageNames = selectedImages.map(f => f.name);

            // Kiểm tra trùng tên trong existingImages và selectedImages
            if (existingImageNames.includes(fileName) || selectedImageNames.includes(fileName)) {
                showAlert('error', `Hình ảnh "${fileName}" đã tồn tại.`);
                return; // Bỏ qua hình ảnh trùng
            }

            selectedImages.push(file);

            const reader = new FileReader();
            reader.onload = function (e) {
                const imgElement = $(`
                    <div class="preview-image">
                        <img src="${e.target.result}" alt="Image">
                        <button type="button" class="remove-image">&times;</button>
                    </div>
                `);

                // Xử lý sự kiện xoá hình ảnh
                imgElement.find('.remove-image').click(function () {
                    const index = selectedImages.indexOf(file);
                    if (index > -1) {
                        selectedImages.splice(index, 1);
                    }
                    imgElement.remove();
                });

                $('#previewImages').append(imgElement);
            };
            reader.readAsDataURL(file);
        });

        $(this).val('');
    });

    function deleteContent(id) {
        Swal.fire({
            title: 'Bạn có chắc chắn muốn xóa nội dung này?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Có, xóa nó!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/content-manager/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Xóa nội dung thành công!');
                        loadContents();
                    },
                    error: function (xhr) {
                        console.error('Lỗi khi xóa nội dung:', xhr.responseText);
                        showAlert('error', 'Đã xảy ra lỗi khi xóa nội dung.');
                    }
                });
            }
        });
    }

    // Initialize map variables
    let map;
    let draggableMarker;
    let selectedLat = 10.762622;  // Mặc định
    let selectedLng = 106.660172; // Mặc định
    let mapInited = false;

    // Khởi tạo map
    function initMap() {
        map = L.map('mapContainer').setView([selectedLat, selectedLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Marker kéo thả
        draggableMarker = L.marker([selectedLat, selectedLng], { draggable: true })
            .addTo(map)
            .bindPopup("Kéo tôi hoặc click map để chọn vị trí")
            .openPopup();

        // Sự kiện dragend
        draggableMarker.on('dragend', function (e) {
            const latlng = e.target.getLatLng();
            selectedLat = latlng.lat;
            selectedLng = latlng.lng;
            console.log('Marker dragged to:', selectedLat, selectedLng);
        });

        // Click map => di chuyển marker
        map.on('click', function (e) {
            selectedLat = e.latlng.lat;
            selectedLng = e.latlng.lng;
            draggableMarker.setLatLng(e.latlng);
            console.log('Map clicked at:', selectedLat, selectedLng);
        });

        // Thử locate user
        map.locate({ setView: false, maxZoom: 16 });
        map.on('locationfound', function (e) {
            selectedLat = e.latlng.lat;
            selectedLng = e.latlng.lng;
            draggableMarker.setLatLng(e.latlng);
            map.setView(e.latlng, 16);
            console.log('Location found:', selectedLat, selectedLng);
        });
        map.on('locationerror', function () {
            console.warn("Không thể xác định vị trí của bạn.");
        });
    }
    $('#post_platform').on('change', function () {
        const val = $(this).val();
        if (val === 'ChoTot' || val === 'Shopee' || val === 'FacebookMarketplace') {
            $('#locationPicker').show();
            $('#priceInputGroup').show();
        } else {
            $('#locationPicker').hide();
            $('#priceInputGroup').hide();
            // Xoá lat/lng và giá
            $('#latitude').val('');
            $('#longitude').val('');
            $('#contentPrice').val('');
        }
    });

    loadContents();


    // Biến lưu cấu trúc ảnh trả về từ ajax
    let contentImagesTree = {};
    let currentContentImageFolder = null;
    let contentImageFolderStack = [];
    let fileManagerSelectedImages = []; // Các hình được chọn tạm từ modal
    // existingImages là mảng chứa URL hình đã chọn (dùng gửi form và hiển thị preview)
    
    function loadContentImageList() {
        const imageList = document.getElementById('contentImageList');
        imageList.innerHTML = '';
        const folderName = currentContentImageFolder ? currentContentImageFolder : 'root';
        document.getElementById('currentContentImageFolder').innerText = folderName;
        document.getElementById('backContentImageButton').style.display = currentContentImageFolder ? "inline-block" : "none";
    
        if (!currentContentImageFolder) { // Load folder
            for (let folder in contentImagesTree) {
                const colDiv = document.createElement('div');
                colDiv.className = "col-md-3 mb-3";
                const card = document.createElement('div');
                card.className = "card";
                card.style.cursor = "pointer";
                card.onclick = function() {
                    contentImageFolderStack.push(currentContentImageFolder);
                    currentContentImageFolder = folder;
                    loadContentImageList();
                };
                const cardBody = document.createElement('div');
                cardBody.className = "card-body text-center";
                cardBody.innerText = folder;
                card.appendChild(cardBody);
                colDiv.appendChild(card);
                imageList.appendChild(colDiv);
            }
        } else { // Load hình trong folder
            const images = contentImagesTree[currentContentImageFolder];
            images.forEach(image => {
                const colDiv = document.createElement('div');
                colDiv.className = "col-md-3 mb-3";
                const card = document.createElement('div');
                card.className = "card";
                card.style.cursor = "pointer";
                // Ở đây dùng toggle để multi-select
                card.onclick = function() {
                    toggleContentImageSelection(image, card);
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
    
    function goBackContentImage() {
        if (contentImageFolderStack.length) {
            currentContentImageFolder = contentImageFolderStack.pop();
        } else {
            currentContentImageFolder = null;
        }
        loadContentImageList();
    }
    
    // Hàm hiển thị modal chọn ảnh từ FileManager
    function showContentImageSelector() {
        $.ajax({
            url: '/file-manager/images',
            type: 'GET',
            dataType: 'json',
            success: function(treeData) {
                contentImagesTree = treeData;
                currentContentImageFolder = null;
                contentImageFolderStack = [];
                fileManagerSelectedImages = []; // Reset mảng chọn tạm
                loadContentImageList();
                $('#contentImageSelectorModal').modal('show');
            },
            error: function(err) {
                console.error(err);
                showAlert('error', 'Lỗi tải hình ảnh từ FileManager');
            }
        });
    }
    
    // Hàm toggle (chọn/hủy) hình từ FileManager
    function toggleContentImageSelection(image, cardElement) {
        if (!fileManagerSelectedImages.includes(image.url)) {
            fileManagerSelectedImages.push(image.url);
            cardElement.classList.add('selected');
        } else {
            const index = fileManagerSelectedImages.indexOf(image.url);
            if (index > -1) {
                fileManagerSelectedImages.splice(index, 1);
            }
            cardElement.classList.remove('selected');
        }
        console.log("Selected fileManager images:", fileManagerSelectedImages);
    }
    
    // Sự kiện cho nút xác nhận chọn hình trong modal FileManager
    $('#btnConfirmFileManagerSelection').on('click', function() {
        fileManagerSelectedImages.forEach(function(url) {
            if (!existingImages.includes(url)) {
                existingImages.push(url);
                const previewDiv = $(`
                    <div class="preview-image">
                        <img src="${url}" alt="Image">
                        <button type="button" class="remove-image">&times;</button>
                    </div>
                `);
                previewDiv.find('.remove-image').click(function () {
                    const idx = existingImages.indexOf(url);
                    if (idx > -1) {
                        existingImages.splice(idx, 1);
                    }
                    previewDiv.remove();
                });
                $('#previewImages').append(previewDiv);
            }
        });
        // Sau khi xác nhận, reset mảng chọn tạm và ẩn modal
        fileManagerSelectedImages = [];
        $('#contentImageSelectorModal').modal('hide');
    });
    
    // Sự kiện cho các nút chọn nguồn hình ảnh (ở modal imageOptionModal)
    $('#btnSelectFromFileManager').on('click', function() {
        $('#imageOptionModal').modal('hide');
        showContentImageSelector();
    });
    $('#btnUploadFromLocal').on('click', function() {
        $('#imageOptionModal').modal('hide');
        $('#contentImage').trigger('click');
    });
    $('#btnSelectImageOption').on('click', function() {
        $('#imageOptionModal').modal('show');
    });
    window.goBackContentImage = function() {
        if (contentImageFolderStack.length) {
            currentContentImageFolder = contentImageFolderStack.pop();
        } else {
            currentContentImageFolder = null;
        }
        loadContentImageList();
    }
});