
$(document).ready(function () {
    // --- KHÁI BÁO BIẾN TOÀN CỤC ---
    let selectedImages = [];            // Local file objects
    let existingImages = [];            // URLs từ FileManager
    let selectedOrder = [];             // Mảng lưu thứ tự chọn, mỗi phần tử: { type: 'local'|'filemanager', data: file|url }
    let currentPage = 1;
    const pageSize = 9;

    // Biến tạm và biến cho modal FileManager
    let fileManagerSelectedImages = [];
    let fileManagerSelectedVideos = [];
    let contentImagesTree = [];
    let contentVideosTree = [];
    let currentContentImageFolder = null;
    let currentContentVideoFolder = null;
    let contentImageFolderStack = [];
    let contentVideoFolderStack = [];


    $('#addVideoSegmentBtn').on('click', function () {
        const rowHtml = `
            <div class="row mb-2 video-segment-row">
                <div class="col-md-3">
                    <input type="file" name="videos[]" class="form-control" accept="video/*" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="segments[${videoSegmentIndex}][start]" class="form-control" placeholder="Start (giây)" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="segments[${videoSegmentIndex}][end]" class="form-control" placeholder="End (giây)" required>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-danger btn-sm removeSegmentBtn">Xóa</button>
                </div>
            </div>
        `;
        $('#videoSegmentsContainer').append(rowHtml);
        videoSegmentIndex++;
    });

    // Sử dụng event delegation để xóa row khi click vào nút removeSegmentBtn
    $(document).on('click', '.removeSegmentBtn', function () {
        $(this).closest('.video-segment-row').remove();
    });


    // Các đoạn mã khác...


    $('#keepSegmentsAudio').change(function () {
        if (!this.checked) {
            $('#audioSegmentsDiv').slideDown();
        } else {
            $('#audioSegmentsDiv').slideUp();
        }
    });

    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).data('bsTarget');
        if (target === "#overlay-form") {
            $('#bottomPart').show();
            __setTimelineHeight(); // Tính lại chiều cao timeline khi hiển thị
        } else {
            $('#bottomPart').hide();
        }
    });
    const cursorWidth = $(".cursor").width();
    const rulerWidth = $(".ruler").width();
    const rulerWrapperOffset = parseInt($(".ruler-wrapper").css("padding-left").replace('px', ''));

    var cursorIsDragged = false;
    var videoDuration = 0;


    $('#createVideoBtn').click(function (e) {
        e.preventDefault();
        showLoading();
        var form = $('#createVideoForm')[0];
        var formData = new FormData(form);

        // Mảng để lưu URLs của hình ảnh được chọn theo đúng thứ tự
        let orderedImageUrls = [];

        // Thay vì gửi selectedImages và existingImages riêng rẽ
        // chúng ta sẽ dùng selectedOrder để duy trì thứ tự chính xác
        selectedOrder.forEach(function (item) {
            if (item.type === 'local') {
                // Thêm file vào formData
                formData.append('images[]', item.data);
            } else if (item.type === 'filemanager') {
                // Thêm URL vào mảng URLs
                orderedImageUrls.push(item.data);
            }
            // Không xử lý type 'video' ở đây vì đây là tính năng createBasicVideo
        });

        // Thêm mảng URLs đã được sắp xếp theo thứ tự vào formData
        if (orderedImageUrls.length > 0) {
            formData.append('existing_images', JSON.stringify(orderedImageUrls));
        }

        // Thêm mảng thứ tự để server biết chính xác cách sắp xếp
        formData.append('image_order', JSON.stringify(selectedOrder.map(item => item.type)));

        $.ajax({
            url: '/create-basic-video',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading();
                // Cập nhật nguồn video demo với previewUrl nhận được từ backend
                $('#video source').attr('src', response.previewUrl);
                $('#video')[0].load();
                // Tạo nút "Xem demo" nếu chưa có
                if ($('#videoPreviewBtn').length === 0) {
                    $('#video').closest('.col-3').append(`
                        <div id="videoPreviewContainer" class="mt-2">
                            <button type="button" id="videoPreviewBtn" class="btn btn-info">Xem demo</button>
                        </div>
                    `);
                }
                // Gán sự kiện cho nút "Xem demo" để mở modal preview
                $('#videoPreviewBtn').off('click').on('click', function () {
                    $('#previewModal').modal('show');
                });
            },
            error: function (xhr) {
                hideLoading();
                Swal.fire('Lỗi!', 'Có lỗi xảy ra: ' + xhr.responseJSON.message, 'error');
            }
        });
    });

    // Hiển thị/ẩn phần chọn file audio khi không giữ audio gốc
    $('#keepVideoAudio').change(function () {
        if (!this.checked) {
            $('#audioConcatDiv').slideDown();
        } else {
            $('#audioConcatDiv').slideUp();
        }
    });

    // Hiển thị/ẩn phần tùy chọn chuyển cảnh
    $('#applyTransition').change(function () {
        if (this.checked) {
            $('#transitionOptions').slideDown();
            $('#transitionOptions input').attr('required', true);
        } else {
            $('#transitionOptions').slideUp();
            $('#transitionOptions input').removeAttr('required');
        }
    });

    $('#concatVideosBtn').click(function (e) {
        e.preventDefault();
        showLoading();
        var form = $('#concatVideosForm')[0];
        var formData = new FormData(form);

        $.ajax({
            url: '/create-video-with-audio',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading();

                // Nếu backend trả về previewUrl thì cập nhật video demo
                if (response.previewUrl) {
                    $('#video source').attr('src', response.previewUrl);
                    $('#video')[0].load();

                    // Tạo nút "Xem demo" nếu chưa có
                    if ($('#videoPreviewBtn').length === 0) {
                        $('#video').closest('.col-3').append(`
                            <div id="videoPreviewContainer" class="mt-2">
                                <button type="button" id="videoPreviewBtn" class="btn btn-info">Xem demo</button>
                            </div>
                        `);
                    }
                    // Gán sự kiện cho nút "Xem demo" để mở modal preview
                    $('#videoPreviewBtn').off('click').on('click', function () {
                        $('#previewModal').modal('show');
                    });
                }

                Swal.fire("Thành công!", response.message, "success");
            },
            error: function (xhr) {
                hideLoading();
                Swal.fire("Lỗi!", 'Có lỗi xảy ra: ' + (xhr.responseJSON.message || 'Vui lòng kiểm tra lại.'), "error");
            }
        });
    });

    $('#extractAudioForm').submit(function (e) {
        e.preventDefault();
        showLoading(); // Hàm hiển thị loading (nếu đã định nghĩa)
        var form = $('#extractAudioForm')[0];
        var formData = new FormData(form);

        $.ajax({
            url: '/extract-audio',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading(); // Hàm ẩn loading
                Swal.fire("Thành công!", response.message, "success");
            },
            error: function (xhr) {
                hideLoading();
                Swal.fire("Lỗi!", 'Có lỗi xảy ra: ' + (xhr.responseJSON.message || 'Vui lòng kiểm tra lại.'), "error");
            }
        });
    });

    $('#concatVideoSegmentsForm').submit(function (e) {
        e.preventDefault();
        showLoading();
        var form = $(this)[0];
        var formData = new FormData(form);
        $.ajax({
            url: '/concat-video-segments-preview',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading();
                // Cập nhật video demo
                $('#video source').attr('src', response.previewUrl);
                $('#video')[0].load();

                // Tạo nút "Xem demo" nếu chưa có
                if ($('#videoPreviewBtn').length === 0) {
                    $('#video').closest('.col-3').append(`
                        <div id="videoPreviewContainer" class="mt-2">
                            <button type="button" id="videoPreviewBtn" class="btn btn-info">Xem demo</button>
                        </div>
                    `);
                }

                // Gán sự kiện cho nút "Xem demo" để mở modal preview
                $('#videoPreviewBtn').off('click').on('click', function () {
                    // Mở modal đã được định nghĩa trong Blade (xem bước 2)
                    $('#previewModal').modal('show');
                });
            },
            error: function (xhr) {
                hideLoading();
                Swal.fire('Lỗi!', 'Có lỗi xảy ra: ' + xhr.responseJSON.message, 'error');
            }
        });
    });

    $('#previewModal').on('show.bs.modal', function () {
        var demoUrl = $('#video source').attr('src');
        $('#previewVideo source').attr('src', demoUrl);
        $('#previewVideo')[0].load();
    });

    $('#exportFileBtn').click(function () {
        // Lấy src của video demo từ modal hoặc phần page
        var previewUrl = $('#previewVideo source').attr('src');
        // Tách lấy tên file (phần sau dấu /)
        var segments = previewUrl.split('/');
        var outputFile = segments[segments.length - 1];

        $.ajax({
            url: '/confirm-export',
            type: 'POST',
            data: { outputFile: outputFile, _token: $('input[name=_token]').val() },
            success: function (resp) {
                $('#previewVideo')[0].pause();
                $('#previewModal').modal('hide');
                Swal.fire('Thành công!', resp.message, 'success');
            },
            error: function (xhr) {
                Swal.fire('Lỗi!', 'Có lỗi xảy ra khi xuất file', 'error');
            }
        });
    });


    $('#editor').show();
    $('#editorTabs a#basic-video-tab').tab('show');



    // --- HÀM HIỆN THỊ PREVIEW ẢNH (với phân trang theo thứ tự chọn) ---
    function updatePreviewImages() {
        $('#selectedVideosContainer').empty();
        const totalMedia = selectedOrder;
        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = currentPage * pageSize;
        const mediaToShow = totalMedia.slice(startIndex, endIndex);

        mediaToShow.forEach(item => {
            if (item.type === 'local') {  // Local file object
                const file = item.data;
                const reader = new FileReader();
                reader.onload = function (e) {
                    const previewDiv = $(`
                        <div class="preview-image position-relative" style="width:200px; height:150px;" data-file-name="${file.name}">
                            <img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;" alt="Preview">
                            <button type="button" class="btn btn-sm remove-image" style="position:absolute; top:2px; right:2px; border-radius:50%; background-color:white; color:black; z-index:10;">&times;</button>
                        </div>
                    `);
                    previewDiv.find('.remove-image').on('click', function () {
                        selectedImages = selectedImages.filter(f => f.name !== file.name);
                        selectedOrder = selectedOrder.filter(it => it.type === 'local' ? it.data.name !== file.name : true);
                        if (Math.ceil(selectedOrder.length / pageSize) < currentPage) {
                            currentPage = currentPage > 1 ? currentPage - 1 : 1;
                        }
                        updatePreviewImages();
                    });
                    $('#selectedVideosContainer').append(previewDiv);
                };
                reader.readAsDataURL(file);
            } else if (item.type === 'filemanager') { // FileManager URL (hình ảnh)
                const url = item.data;
                const previewDiv = $(`
                    <div class="preview-image position-relative" data-file-name="${url}">
                        <img src="${url}" style="width:200px; height:150px; object-fit:cover;" alt="Preview">
                        <button type="button" class="btn btn-sm remove-image" style="position:absolute; top:2px; right:2px; border-radius:50%; background-color:white; color:black; z-index:10;">&times;</button>
                    </div>
                `);
                previewDiv.find('.remove-image').on('click', function () {
                    existingImages = existingImages.filter(u => u !== url);
                    selectedOrder = selectedOrder.filter(it => it.type === 'filemanager' ? it.data !== url : true);
                    if (Math.ceil(selectedOrder.length / pageSize) < currentPage) {
                        currentPage = currentPage > 1 ? currentPage - 1 : 1;
                    }
                    updatePreviewImages();
                });
                $('#selectedVideosContainer').append(previewDiv);
            } else if (item.type === 'video') { // FileManager URL (video)
                const url = item.data;
                const videoName = url.split('/').pop();
                const previewDiv = $(`
                    <div class="preview-video position-relative" data-video-url="${url}">
                        <div style="width:200px; height:150px; background-color:#000; position:relative;">
                            <i class="bi bi-film" style="font-size:3rem; color:#fff; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);"></i>
                            <span class="badge bg-danger" style="position:absolute; bottom:10px; left:10px;">VIDEO</span>
                            <button type="button" class="btn btn-sm btn-outline-light preview-btn" style="position:absolute; bottom:10px; right:10px;">
                                <i class="bi bi-play-fill"></i> Xem
                            </button>
                        </div>
                        <div class="text-center mt-1">${videoName}</div>
                        <button type="button" class="btn btn-sm remove-image" style="position:absolute; top:2px; right:2px; border-radius:50%; background-color:white; color:black; z-index:10;">&times;</button>
                    </div>
                `);

                previewDiv.find('.preview-btn').on('click', function () {
                    previewVideo(url);
                });

                previewDiv.find('.remove-image').on('click', function () {
                    existingVideos = existingVideos.filter(u => u !== url);
                    selectedOrder = selectedOrder.filter(it => it.type === 'video' ? it.data !== url : true);
                    if (Math.ceil(selectedOrder.length / pageSize) < currentPage) {
                        currentPage = currentPage > 1 ? currentPage - 1 : 1;
                    }
                    updatePreviewImages();
                });
                $('#selectedVideosContainer').append(previewDiv);
            }
        });

        const totalPages = Math.ceil(selectedOrder.length / pageSize);
        const paginationContainer = document.getElementById('pagination');
        renderPagination(paginationContainer, currentPage, totalPages, function (newPage) {
            currentPage = newPage;
            updatePreviewImages();
        });
    }

    // --- HÀM CHỌN/HỦY ẢNH TỪ FILEMANAGER ---
    function toggleContentImageSelection(image, cardElement) {
        if (!fileManagerSelectedImages.includes(image.url)) {
            fileManagerSelectedImages.push(image.url);
            cardElement.classList.add('selected');
        } else {
            const index = fileManagerSelectedImages.indexOf(image.url);
            if (index > -1) fileManagerSelectedImages.splice(index, 1);
            cardElement.classList.remove('selected');
        }
    }

    // --- HÀM DỰNG DANH SÁCH ẢNH TRONG MODAL FILEMANAGER ---
    function loadContentImageList() {
        $('#contentImageList').empty();
        if (currentContentImageFolder === null) {
            $('#currentContentImageFolder').text('Danh sách thư mục');
            $('#backContentImageButton').hide();
            const folders = Object.keys(contentImagesTree);
            if (folders.length) {
                folders.forEach(function (folderKey) {
                    let folderCard = $(`
                        <div class="col-md-3 mb-2">
                            <div class="card" style="cursor:pointer; background-color:#47a4a7;">
                                <div class="card-body text-center text-white">
                                    <h5 class="card-title">${folderKey}</h5>
                                    <p class="card-text">${contentImagesTree[folderKey].length} hình</p>
                                </div>
                            </div>
                        </div>
                    `);
                    folderCard.on('click', function () {
                        contentImageFolderStack.push(currentContentImageFolder);
                        currentContentImageFolder = folderKey;
                        loadContentImageList();
                    });
                    $('#contentImageList').append(folderCard);
                });
            } else {
                $('#contentImageList').html('<p>Không có thư mục nào.</p>');
            }
        } else {
            $('#currentContentImageFolder').text(currentContentImageFolder);
            $('#backContentImageButton').show();
            const images = contentImagesTree[currentContentImageFolder] || [];
            if (images.length) {
                images.forEach(function (image) {
                    let cardContainer = $(`
                        <div class="col-md-3 mb-2">
                            <div class="card" style="cursor:pointer;">
                                <img src="${image.url}" class="card-img-top" alt="${image.name}">
                            </div>
                        </div>
                    `);
                    cardContainer.find('.card').on('click', function () {
                        toggleContentImageSelection(image, this);
                    });
                    $('#contentImageList').append(cardContainer);
                });
            } else {
                $('#contentImageList').html('<p>Không có hình ảnh nào trong thư mục này.</p>');
            }
        }
    }

    // --- HÀM SHOW MODAL FILEMANAGER (Ajax JSON) ---
    function showContentImageSelector() {
        // Reset lại trạng thái khi mở modal
        fileManagerSelectedImages = [];
        fileManagerSelectedVideos = [];

        // Load dữ liệu hình ảnh trước
        $.ajax({
            url: '/file-manager/images',
            type: 'GET',
            dataType: 'json',
            success: function (treeData) {
                contentImagesTree = treeData;
                currentContentImageFolder = null;
                contentImageFolderStack = [];
                loadContentImageList();

                // Hiển thị modal khi đã tải xong dữ liệu hình ảnh
                $('#contentImageSelectorModal').modal('show');
            },
            error: function (err) {
                console.error(err);
                showAlert('error', 'Lỗi tải hình ảnh từ FileManager');
            }
        });
    }

    function loadVideosData() {
        $.ajax({
            url: '/file-manager/videos',
            type: 'GET',
            dataType: 'json',
            success: function (treeData) {
                contentVideosTree = treeData;
                currentContentVideoFolder = null;
                contentVideoFolderStack = [];
                loadContentVideoList();
            },
            error: function (err) {
                console.error(err);
                showAlert('error', 'Lỗi tải video từ FileManager');
            }
        });
    }
    function loadContentVideoList() {
        $('#contentVideoList').empty();
        if (currentContentVideoFolder === null) {
            // Hiển thị danh sách thư mục (giữ nguyên code cũ)
            $('#currentContentVideoFolder').text('Danh sách thư mục');
            $('#backContentImageButton').hide(); // Dùng chung nút back
            const folders = Object.keys(contentVideosTree);
            if (folders.length) {
                folders.forEach(function (folderKey) {
                    let folderCard = $(`
                        <div class="col-md-3 mb-2">
                            <div class="card" style="cursor:pointer; background-color:#b7474a;">
                                <div class="card-body text-center text-white">
                                    <h5 class="card-title">${folderKey}</h5>
                                    <p class="card-text">${contentVideosTree[folderKey].length} video</p>
                                </div>
                            </div>
                        `);
                    folderCard.on('click', function () {
                        contentVideoFolderStack.push(currentContentVideoFolder);
                        currentContentVideoFolder = folderKey;
                        loadContentVideoList();
                    });
                    $('#contentVideoList').append(folderCard);
                });
            } else {
                $('#contentVideoList').html('<p>Không có thư mục video nào.</p>');
            }
        } else {
            // Hiển thị danh sách video trong thư mục (đã cải tiến)
            $('#currentContentVideoFolder').text(currentContentVideoFolder);
            $('#backContentImageButton').show();
            const videos = contentVideosTree[currentContentVideoFolder] || [];
            if (videos.length) {
                videos.forEach(function (video) {
                    const isSelected = fileManagerSelectedVideos.includes(video.url);
                    let cardContainer = $(`
                                           <div class="col-md-4 mb-3">
                                            <div class="card ${isSelected ? 'selected' : ''}" style="cursor:pointer;">
                                                <div class="video-thumbnail position-relative" style="height: 150px; overflow: hidden; background-color: #000;">
                                                    <!-- Tạo thumbnail từ video bằng cách load frame đầu tiên -->
                                                    <video src="${video.url}" preload="metadata" style="width: 100%; height: 100%; object-fit: cover;" 
                                                        muted></video>
                                                    
                                                    <!-- Play button overlay - đã bỏ viền trắng -->
                                                    <div class="play-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; 
                                                        display: flex; align-items: center; justify-content: center;">
                                                        <div class="play-button" style="width: 50px; height: 50px; border-radius: 50%; background-color: rgba(0,0,0,0.5);
                                                            display: flex; align-items: center; justify-content: center;">
                                                            <i class="bi bi-play-fill" style="font-size: 2rem; color: #fff;"></i>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Video duration badge -->
                                                    <span class="badge bg-dark position-absolute bottom-0 end-0 m-2">
                                                        <i class="bi bi-film me-1"></i>Video
                                                    </span>
                                                </div>
                                                <div class="card-body p-2">
                                                    <p class="card-text mb-0 text-truncate" style="color: #000;" title="${video.name}">${video.name}</p>
                                                </div>
                                            </div>
                                        </div>
                                        `);

                    // Xử lý video không load được
                    cardContainer.find('video').on('error', function () {
                        $(this).hide();
                        $(this).parent().append(`
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
                                 display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-film" style="font-size: 3rem; color: #fff;"></i>
                            </div>
                        `);
                    });

                    // Load thời lượng video
                    let videoEl = cardContainer.find('video')[0];
                    $(videoEl).on('loadedmetadata', function () {
                        if (videoEl.duration) {
                            let duration = Math.round(videoEl.duration);
                            let minutes = Math.floor(duration / 60);
                            let seconds = duration % 60;
                            cardContainer.find('.badge').text(
                                `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`
                            );
                        }
                    });

                    cardContainer.find('.play-overlay, .play-button').on('click', function (e) {
                        e.stopPropagation();
                        previewVideo(video.url);
                    });

                    // Sự kiện cho card video (chọn/hủy chọn)
                    cardContainer.find('.card').on('click', function (e) {
                        if (!$(e.target).closest('.play-overlay, .play-button').length) {
                            toggleVideoSelection(video.url, this);
                        }
                    });

                    $('#contentVideoList').append(cardContainer);
                });
            } else {
                $('#contentVideoList').html('<p>Không có video nào trong thư mục này.</p>');
            }
        }
    }

    function toggleVideoSelection(videoUrl, cardElement) {
        if (!fileManagerSelectedVideos.includes(videoUrl)) {
            fileManagerSelectedVideos.push(videoUrl);
            $(cardElement).addClass('selected');
        } else {
            const index = fileManagerSelectedVideos.indexOf(videoUrl);
            if (index > -1) {
                fileManagerSelectedVideos.splice(index, 1);
            }
            $(cardElement).removeClass('selected');
        }
    }

    // Thay thế hoặc cập nhật function previewVideo
    function previewVideo(videoUrl) {
        // Tạo modal preview tạm thời nếu chưa có
        if ($('#tempVideoPreviewModal').length === 0) {
            $('body').append(`
            <div class="modal fade" id="tempVideoPreviewModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
                    <div class="modal-content wrapper">
                        <div class="modal-header py-2">
                            <h5 class="modal-title video-title" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Xem thử video</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="video-container" style="position: relative; width: 100%;">
                                <video id="tempPreviewVideo" controls style="display: block; width: 100%; height: auto; max-height: 80vh;">
                                    <source src="" type="video/mp4">
                                </video>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);
        }

        // Lấy tên file video từ URL
        const videoName = videoUrl.split('/').pop();
        $('.video-title').text(videoName);

        // Cập nhật nguồn video
        $('#tempPreviewVideo source').attr('src', videoUrl);
        const videoElement = $('#tempPreviewVideo')[0];
        videoElement.load();

        // Điều chỉnh kích thước modal khi video sẵn sàng
        $(videoElement).on('loadedmetadata', function () {
            // Lấy kích thước viewport
            const viewportHeight = window.innerHeight;
            const viewportWidth = window.innerWidth;

            // Lấy tỷ lệ khung hình của video
            const videoAspectRatio = this.videoWidth / this.videoHeight;

            // Tính toán kích thước tối đa cho modal
            let maxWidth = Math.min(viewportWidth * 0.9, this.videoWidth);
            let maxHeight = Math.min(viewportHeight * 0.8, this.videoHeight);

            // Điều chỉnh modal dialog theo tỷ lệ khung hình
            if (this.videoWidth > 0 && this.videoHeight > 0) {
                const modalDialog = $('#tempVideoPreviewModal .modal-dialog');

                // Nếu video quá nhỏ, hiển thị kích thước thực
                if (this.videoWidth < viewportWidth * 0.5 && this.videoHeight < viewportHeight * 0.5) {
                    modalDialog.css('max-width', this.videoWidth + 30 + 'px'); // Thêm padding
                } else {
                    // Nếu video lớn, giữ tỷ lệ khung hình và giới hạn kích thước
                    modalDialog.css('max-width', Math.min(maxWidth, maxHeight * videoAspectRatio) + 'px');
                }
            }
        });

        // Hiển thị modal
        $('#tempVideoPreviewModal').modal('show');

        // Tự động phát video khi modal hiển thị
        $('#tempVideoPreviewModal').on('shown.bs.modal', function () {
            videoElement.play();
        });

        // Dừng video khi đóng modal
        $('#tempVideoPreviewModal').on('hidden.bs.modal', function () {
            videoElement.pause();
        });
    }

    // --- SỰ KIỆN CHO PHẦN CHỌN ẢNH TỪ MÁY & FILEMANAGER ---
    $('#btnSelectImages').on('click', function () {
        $('#imageOptionModal').modal('show');
    });

    $('#btnUploadFromLocal').on('click', function () {
        $('#imageOptionModal').modal('hide');
        $('#images').trigger('click');
    });

    $('#images').on('change', function (e) {
        const newFiles = Array.from(this.files);
        newFiles.forEach(function (file) {
            if (!selectedImages.some(f => f.name === file.name)) {
                selectedImages.push(file);
            }
            selectedOrder.push({ type: 'local', data: file });
        });
        currentPage = Math.ceil(selectedOrder.length / pageSize);
        $(this).val('');
        updatePreviewImages();
    });

    $('#btnSelectFromFileManager').on('click', function () {
        $('#imageOptionModal').modal('hide');
        showContentImageSelector();
    });

    $('#btnConfirmFileManagerSelection').on('click', function () {
        fileManagerSelectedImages.forEach(function (url) {
            if (!existingImages.includes(url)) {
                existingImages.push(url);
                selectedOrder.push({ type: 'filemanager', data: url });
            }
        });
        fileManagerSelectedImages = [];
        $('#contentImageSelectorModal').modal('hide');
        currentPage = Math.ceil(selectedOrder.length / pageSize);
        updatePreviewImages();
    });


    $('#videos-tab').on('click', function () {
        // Nếu chưa tải dữ liệu video, thực hiện tải
        if (Object.keys(contentVideosTree).length === 0) {
            loadVideosData();
        }
    });

    // Cập nhật xử lý nút xác nhận để bao gồm cả video
    $('#btnConfirmFileManagerSelection').off('click').on('click', function () {
        // Thêm hình ảnh đã chọn vào danh sách
        fileManagerSelectedImages.forEach(function (url) {
            if (!existingImages.includes(url)) {
                existingImages.push(url);
                selectedOrder.push({ type: 'filemanager', data: url });
            }
        });

        // Thêm video đã chọn vào danh sách
        fileManagerSelectedVideos.forEach(function (url) {
            if (!existingVideos.includes(url)) {
                existingVideos.push(url);
                selectedOrder.push({ type: 'video', data: url });
            }
        });
        // Reset các biến tạm
        fileManagerSelectedImages = [];
        fileManagerSelectedVideos = [];
        // Đóng modal
        $('#contentImageSelectorModal').modal('hide');

        // Cập nhật hiển thị
        currentPage = Math.ceil(selectedOrder.length / pageSize);
        updatePreviewImages();
    });


    // --- SỰ KIỆN QUAY LẠI THƯ MỤC FILEMANAGER ---
    window.goBackContentImage = function () {
        if (contentImageFolderStack.length) {
            currentContentImageFolder = contentImageFolderStack.pop();
        } else {
            currentContentImageFolder = null;
        }
        loadContentImageList();
    };

    window.goBackContentMedia = function () {
        // Xác định đang ở tab nào để quay lại đúng
        const activeTab = $('#mediaTypeTabs .nav-link.active').attr('id');

        if (activeTab === 'images-tab') {
            if (contentImageFolderStack.length) {
                currentContentImageFolder = contentImageFolderStack.pop();
                loadContentImageList();
            } else {
                currentContentImageFolder = null;
                loadContentImageList();
            }
        } else {
            if (contentVideoFolderStack.length) {
                currentContentVideoFolder = contentVideoFolderStack.pop();
                loadContentVideoList();
            } else {
                currentContentVideoFolder = null;
                loadContentVideoList();
            }
        }
    };

    function showLoading() {
        console.log("Hiển thị loading...");
        if ($('#loadingOverlay').length === 0) {
            $('body').append(`
                    <div id="loadingOverlay">
                        <div class="loader"></div>
                    </div>
                `);
        }
    }

    function hideLoading() {
        console.log("Ẩn loading...");
        $('#loadingOverlay').remove();
    }

});

