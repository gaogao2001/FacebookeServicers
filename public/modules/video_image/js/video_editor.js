
$(document).ready(function () {
    // --- KHÁI BÁO BIẾN TOÀN CỤC ---
    let selectedImages = [];            // Local file objects
    let existingImages = [];            // URLs từ FileManager
    let selectedOrder = [];             // Mảng lưu thứ tự chọn, mỗi phần tử: { type: 'local'|'filemanager', data: file|url }
    let currentPage = 1;
    const pageSize = 9;

    let videoSegmentIndex = 1;

    // Biến tạm và biến cho modal FileManager
    let fileManagerSelectedImages = [];
    let fileManagerSelectedVideos = [];
    let contentImagesTree = [];
    let contentVideosTree = [];
    let currentContentImageFolder = null;
    let currentContentVideoFolder = null;
    let contentImageFolderStack = [];
    let contentVideoFolderStack = [];

    let concatSelectedVideos = [];    // Local file objects cho concat
    let concatExistingVideos = [];    // URLs từ FileManager cho concat
    let concatSelectedOrder = [];      // Mảng lưu thứ tự chọn video cho concat
    let concatCurrentPage = 1;

    let extractAudioVideoRow = null;


    $('#addVideoSegmentBtn').on('click', function () {
        const rowHtml = `
            <div class="row mb-2 video-segment-row">
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary select-video-btn w-100">
                        <i class="bi bi-file-earmark-play"></i> Chọn Video
                    </button>
                    <input type="file" name="videos[]" class="form-control d-none segment-video-input" accept="video/*">
                    <div class="selected-video-name mt-1 small text-truncate"></div>
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
                <input type="hidden" name="segment_video_urls[]" class="segment-video-url">
                <input type="hidden" name="segment_video_types[]" class="segment-video-type" value="local">
            </div>
        `;
        $('#videoSegmentsContainer').append(rowHtml);
        videoSegmentIndex++;
    });

    let currentSegmentRow = null;

    // Sử dụng event delegation để xóa row khi click vào nút removeSegmentBtn
    $(document).on('click', '.removeSegmentBtn', function () {
        $(this).closest('.video-segment-row').remove();
    });

    $(document).on('click', '.select-video-btn', function () {
        // Lưu trữ tham chiếu đến hàng hiện tại để sử dụng sau
        currentSegmentRow = $(this).closest('.video-segment-row');
        $('#videoOptionModal').modal('show');
    });

    $(document).on('change', '.segment-video-input', function () {
        const file = this.files[0];
        const row = $(this).closest('.video-segment-row');

        if (file) {
            // Hiển thị tên file đã chọn
            row.find('.selected-video-name').text(file.name);
            // Cập nhật loại video là local
            row.find('.segment-video-type').val('local');
            // Xóa URL của FileManager nếu có
            row.find('.segment-video-url').val('');

            console.log("Đã chọn file: " + file.name + " cho segment");
        }
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

        const videoType = $('#extractVideoType').val();

        // Kiểm tra nếu không có video được chọn
        if (videoType === 'local' && $('#extractVideoInput')[0].files.length === 0) {
            Swal.fire('Lỗi!', 'Vui lòng chọn một video để tách âm thanh', 'error');
            return;
        }

        if (videoType === 'filemanager' && !$('#extractVideoUrl').val()) {
            Swal.fire('Lỗi!', 'Vui lòng chọn một video để tách âm thanh', 'error');
            return;
        }

        showLoading();

        var formData = new FormData(this);
        formData.append('video_type', videoType);

        $.ajax({
            url: '/extract-audio',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading();
                Swal.fire("Thành công!", response.message, "success");
            },
            error: function (xhr) {
                hideLoading();
                Swal.fire("Lỗi!", 'Có lỗi xảy ra: ' + (xhr.responseJSON?.message || 'Vui lòng kiểm tra lại.'), "error");
            }
        });
    });

    // Xử lý submit form video segments
    // Xử lý submit form video segments
    $('#concatVideoSegmentsForm').submit(function (e) {
        e.preventDefault();

        // Hiển thị log kiểm tra các input file
        console.log("Total video segments: " + $('.video-segment-row').length);
        $('.video-segment-row').each(function (index) {
            const fileInput = $(this).find('.segment-video-input')[0];
            console.log("Segment " + index + " file count: " + (fileInput.files ? fileInput.files.length : 0));
            if (fileInput.files && fileInput.files[0]) {
                console.log("Segment " + index + " file name: " + fileInput.files[0].name);
            }
        });

        // Tạo FormData thủ công
        var formData = new FormData();

        // Thêm CSRF token
        formData.append('_token', $('input[name="_token"]').val());

        // Thêm tên file output
        formData.append('outputFile', $('#outputSegmentsFile').val());

        // Thêm trạng thái giữ âm thanh gốc
        formData.append('keepVideoAudio', $('#keepSegmentsAudio').is(':checked') ? '1' : '0');

        // Thêm file audio nếu có
        if ($('#audioSegments')[0].files.length > 0) {
            formData.append('audio', $('#audioSegments')[0].files[0]);
        }

        // Thu thập thời gian segments
        let segments = [];
        $('.video-segment-row').each(function (index) {
            const start = $(this).find('input[name^="segments["][name$="[start]"]').val();
            const end = $(this).find('input[name^="segments["][name$="[end]"]').val();

            formData.append(`segments[${index}][start]`, start);
            formData.append(`segments[${index}][end]`, end);
            segments.push({ start: start, end: end });
        });

        // Thu thập video files và types
        let videoTypes = [];
        let videoUrls = [];
        let hasVideos = false;

        $('.video-segment-row').each(function (index) {
            const type = $(this).find('.segment-video-type').val();
            videoTypes.push(type);

            if (type === 'local') {
                const fileInput = $(this).find('.segment-video-input')[0];
                if (fileInput.files.length > 0) {
                    console.log("Adding video file: " + fileInput.files[0].name);
                    formData.append('videos[]', fileInput.files[0]);
                    hasVideos = true;
                }
                videoUrls.push('');
            } else if (type === 'filemanager') {
                const url = $(this).find('.segment-video-url').val();
                if (url) {
                    videoUrls.push(url);
                    hasVideos = true;
                } else {
                    videoUrls.push('');
                }
            }
        });

        if (!hasVideos) {
            Swal.fire('Lỗi!', 'Vui lòng chọn ít nhất một video để ghép', 'error');
            return;
        }

        // Thêm thông tin về nguồn video vào request
        formData.append('video_types', JSON.stringify(videoTypes));
        formData.append('video_urls', JSON.stringify(videoUrls));

        showLoading();

        $.ajax({
            url: '/concat-video-segments-preview',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading();
                $('#video source').attr('src', response.previewUrl);
                $('#video')[0].load();

                if ($('#videoPreviewBtn').length === 0) {
                    $('#video').closest('.col-3').append(`
                <div id="videoPreviewContainer" class="mt-2">
                    <button type="button" id="videoPreviewBtn" class="btn btn-info">Xem demo</button>
                </div>
                `);
                }

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


    $('#previewModal').on('show.bs.modal', function () {
        var demoUrl = $('#video source').attr('src');
        // Thêm timestamp mới vào URL để tránh cache khi mở modal
        var uncachedUrl = getUncachedUrl(demoUrl);
        $('#previewVideo').html('<source src="' + uncachedUrl + '" type="video/mp4">');
        $('#previewVideo')[0].load();
    });


    $('#exportFileBtn').click(function () {
        // Lấy URL gốc đã lưu (không có timestamp)
        var originalUrl = $('#video').data('originalUrl');

        // Nếu không có URL gốc, dùng URL có timestamp
        if (!originalUrl) {
            originalUrl = $('#video source').attr('src').split('?')[0]; // Loại bỏ tham số query
        }

        // Tách lấy tên file (phần sau dấu /)
        var segments = originalUrl.split('/');
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
        // Xác định tab nào đang active
        const activeTabId = $('#editorTabContent .tab-pane.active').attr('id');
        const activeMediaTab = $('#mediaTypeTabs .nav-link.active').attr('id');

        // Xử lý cho tab ghép video
        if (activeTabId === 'concat-videos-form' && activeMediaTab === 'videos-tab') {
            fileManagerSelectedVideos.forEach(function (url) {
                if (!concatExistingVideos.includes(url)) {
                    concatExistingVideos.push(url);
                    concatSelectedOrder.push({ type: 'filemanager', data: url });
                }
            });

            // Reset biến tạm và cập nhật hiển thị
            fileManagerSelectedVideos = [];
            $('#contentImageSelectorModal').modal('hide');
            concatCurrentPage = Math.ceil(concatSelectedOrder.length / pageSize);
            updateConcatVideosPreview();
        }
        // Xử lý cho tab tạo video cơ bản (images)
        else if (activeTabId === 'basic-video-form' && activeMediaTab === 'images-tab') {
            fileManagerSelectedImages.forEach(function (url) {
                if (!existingImages.includes(url)) {
                    existingImages.push(url);
                    selectedOrder.push({ type: 'filemanager', data: url });
                }
            });

            // Reset biến tạm và cập nhật hiển thị
            fileManagerSelectedImages = [];
            $('#contentImageSelectorModal').modal('hide');
            currentPage = Math.ceil(selectedOrder.length / pageSize);
            updatePreviewImages();
        }
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

    $('#btnSelectVideos').on('click', function () {
        $('#videoOptionModal').modal('show');
    });

    $('#btnUploadVideosFromLocal').on('click', function () {
        const activeMainTab = $('#editorTabs .nav-link.active').attr('id');

        if (currentSegmentRow) {
            // Xử lý cho segment videos
            currentSegmentRow.find('.segment-video-input').trigger('click');
        }
        else if (activeMainTab === 'concat-videos-tab') {
            // Xử lý cho concat videos
            $('#concatVideos').trigger('click');
        } else if (activeMainTab === 'extract-audio-tab') {
            // Xử lý cho extract audio
            $('#extractVideoInput').trigger('click');
        } else if (activeMainTab === 'cut-video-tab') {
            // Xử lý cho cut video
            $('#cutVideoInput').trigger('click');
        }

        $('#videoOptionModal').modal('hide');
    });


    $('#concatVideos').on('change', function (e) {
        const newFiles = Array.from(this.files);
        newFiles.forEach(function (file) {
            // Chỉ chấp nhận video
            if (file.type.startsWith('video/')) {
                concatSelectedVideos.push(file);
                concatSelectedOrder.push({ type: 'local', data: file });
            }
        });
        concatCurrentPage = Math.ceil(concatSelectedOrder.length / pageSize);
        $(this).val('');
        updateConcatVideosPreview();
    });

    $('#btnSelectVideosFromFileManager').on('click', function () {
        // Đảm bảo rằng modal videoOptionModal đã được ẩn trước
        $('#videoOptionModal').modal('hide');

        // Đặt tab videos là active
        setTimeout(function () {
            // Đảm bảo DOM đã được cập nhật trước khi chuyển tab
            $('#videos-tab').tab('show');

            // Load dữ liệu video nếu chưa có
            if (Object.keys(contentVideosTree).length === 0) {
                loadVideosData();
            }

            // Hiển thị modal FileManager sau khi đã làm mọi thứ khác
            $('#contentImageSelectorModal').modal('show');
        }, 300);
    });

    // Sửa hàm xử lý nút xác nhận FileManager
    $('#btnConfirmFileManagerSelection').off('click').on('click', function () {
        const activeTab = $('#mediaTypeTabs .nav-link.active').attr('id');
        const activeMainTab = $('#editorTabs .nav-link.active').attr('id');

        // Xử lý cho video segments
        if (activeTab === 'videos-tab' && currentSegmentRow) {
            if (fileManagerSelectedVideos.length > 0) {
                const videoUrl = fileManagerSelectedVideos[0];
                currentSegmentRow.find('.selected-video-name').text(videoUrl.split('/').pop());
                currentSegmentRow.find('.segment-video-url').val(videoUrl);
                currentSegmentRow.find('.segment-video-type').val('filemanager');
                currentSegmentRow.find('.segment-video-input').val('');
            }
        }
        // Xử lý cho tab concat videos
        else if (activeTab === 'videos-tab' && activeMainTab === 'concat-videos-tab') {
            fileManagerSelectedVideos.forEach(function (url) {
                if (!concatExistingVideos.includes(url)) {
                    concatExistingVideos.push(url);
                    concatSelectedOrder.push({ type: 'filemanager', data: url });
                }
            });
            concatCurrentPage = Math.ceil(concatSelectedOrder.length / pageSize);
            updateConcatVideosPreview();
        } else if (activeTab === 'videos-tab' && activeMainTab === 'extract-audio-tab') {
            if (fileManagerSelectedVideos.length > 0) {
                const videoUrl = fileManagerSelectedVideos[0];
                $('#extractVideoName').text(videoUrl.split('/').pop());
                $('#extractVideoUrl').val(videoUrl);
                $('#extractVideoType').val('filemanager');
                $('#extractVideoInput').val('');
            }
        }
        // Xử lý cho tab basic video (hình ảnh)
        else if (activeTab === 'images-tab' && activeMainTab === 'basic-video-tab') {
            fileManagerSelectedImages.forEach(function (url) {
                if (!existingImages.includes(url)) {
                    existingImages.push(url);
                    selectedOrder.push({ type: 'filemanager', data: url });
                }
            });
            currentPage = Math.ceil(selectedOrder.length / pageSize);
            updatePreviewImages();
        } else if (activeTab === 'videos-tab' && activeMainTab === 'cut-video-tab') {
            if (fileManagerSelectedVideos.length > 0) {
                const videoUrl = fileManagerSelectedVideos[0];
                const fileName = videoUrl.split('/').pop();

                $('#cutVideoName').text(fileName);
                $('#cutVideoUrl').val(videoUrl);
                $('#cutVideoType').val('filemanager');
                $('#cutVideoInput').val('');

                // Hiển thị preview
                $('#cutVideoContainer').html(`
                    <div class="video-preview mb-3">
                        <video width="70" height="70" controls style="max-width: 50%; object-fit: cover;">
                            <source src="${videoUrl}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <div class="mt-1 text-center video-name small text-truncate">${fileName}</div>
                    </div>
                `);
            }
        }

        if (activeTab === 'videos-tab' && activeMainTab === 'extract-audio-tab') {
            if (fileManagerSelectedVideos.length > 0) {
                const videoUrl = fileManagerSelectedVideos[0]; // Take the first selected video
                $('#extractVideoUrl').val(videoUrl);
                $('#extractVideoType').val('filemanager');
                $('#extractVideoName').text(videoUrl.split('/').pop());

                // Show video preview from URL
                // Show video preview from URL - Update this part
                $('#extractVideoPreview').html(`
                    <div class="preview-video position-relative mb-3" style="width: 200px;">
                        <div class="video-thumbnail position-relative" style="height: 150px; overflow: hidden; background-color: #000;">
                            <video width="100%" height="100%" style="object-fit: cover;" controls>
                                <source src="${videoUrl}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <div class="video-name position-absolute bottom-0 start-0 end-0 p-1 bg-dark bg-opacity-75 text-white small text-truncate">
                                ${videoUrl.split('/').pop()}
                            </div>
                        </div>
                    </div>
                `);
            }
        }

        fileManagerSelectedVideos = [];
        fileManagerSelectedImages = [];
        $('#contentImageSelectorModal').modal('hide');
    });

    // --- HÀM HIỆN THỊ PREVIEW VIDEO CHO CONCAT VIDEOS ---
    function updateConcatVideosPreview() {
        $('#concatVideosContainer').empty();
        const totalMedia = concatSelectedOrder;
        const startIndex = (concatCurrentPage - 1) * pageSize;
        const endIndex = concatCurrentPage * pageSize;
        const mediaToShow = totalMedia.slice(startIndex, endIndex);

        mediaToShow.forEach((item, index) => {
            if (item.type === 'local') {  // Video từ thiết bị
                const file = item.data;
                const videoName = file.name;

                const previewDiv = $(`
                    <div class="preview-video position-relative mb-3" style="width:200px;" data-index="${startIndex + index}">
                        <div class="video-thumbnail position-relative" style="height: 150px; overflow: hidden; background-color: #000;">
                            <div class="thumbnail-container" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-play-circle" style="font-size: 3rem; color: #fff; position:absolute; z-index:2;"></i>
                                <canvas width="200" height="150" class="thumbnail-canvas" style="width:100%; height:100%; object-fit:cover;"></canvas>
                            </div>
                            <div class="video-name position-absolute bottom-0 start-0 end-0 p-1 bg-dark bg-opacity-75 text-white small text-truncate">
                                ${videoName}
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm remove-video position-absolute top-0 end-0 m-1" style="border-radius:50%; background-color:white; color:black;">&times;</button>
                    </div>
                `);

                // Tạo thumbnail từ file local
                const canvas = previewDiv.find('.thumbnail-canvas')[0];
                const tempVideo = document.createElement('video');
                tempVideo.preload = 'metadata';
                tempVideo.muted = true;
                tempVideo.src = URL.createObjectURL(file);

                tempVideo.onloadedmetadata = function () {
                    tempVideo.currentTime = 1; // Lấy frame tại giây đầu tiên
                };

                tempVideo.oncanplay = function () {
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(tempVideo, 0, 0, canvas.width, canvas.height);
                    URL.revokeObjectURL(tempVideo.src); // Giải phóng bộ nhớ
                };

                // Xử lý click vào video để xem trước
                previewDiv.find('.video-thumbnail').on('click', function () {
                    const videoUrl = URL.createObjectURL(file);
                    previewVideo(videoUrl);
                });

                // Xử lý xóa video
                previewDiv.find('.remove-video').on('click', function () {
                    concatSelectedVideos = concatSelectedVideos.filter(f => f !== file);
                    concatSelectedOrder.splice(startIndex + index, 1);
                    if (Math.ceil(concatSelectedOrder.length / pageSize) < concatCurrentPage) {
                        concatCurrentPage = concatCurrentPage > 1 ? concatCurrentPage - 1 : 1;
                    }
                    updateConcatVideosPreview();
                });

                $('#concatVideosContainer').append(previewDiv);

            } else if (item.type === 'filemanager') { // Video từ FileManager
                const url = item.data;
                const videoName = url.split('/').pop();

                const previewDiv = $(`
                    <div class="preview-video position-relative mb-3" style="width:200px;" data-index="${startIndex + index}">
                        <div class="video-thumbnail position-relative" style="height: 150px; overflow: hidden; background-color: #000; cursor:pointer;">
                            <div class="thumbnail-container" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-play-circle" style="font-size: 3rem; color: #fff; position:absolute; z-index:2;"></i>
                                <canvas width="200" height="150" class="thumbnail-canvas" style="width:100%; height:100%; object-fit:cover;"></canvas>
                            </div>
                            <div class="video-name position-absolute bottom-0 start-0 end-0 p-1 bg-dark bg-opacity-75 text-white small text-truncate">
                                ${videoName}
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm remove-video position-absolute top-0 end-0 m-1" style="border-radius:50%; background-color:white; color:black;">&times;</button>
                    </div>
                `);

                // Tạo thumbnail từ URL
                const canvas = previewDiv.find('.thumbnail-canvas')[0];
                const tempVideo = document.createElement('video');
                tempVideo.crossOrigin = "anonymous";
                tempVideo.preload = 'metadata';
                tempVideo.muted = true;
                tempVideo.src = url;

                tempVideo.onloadedmetadata = function () {
                    tempVideo.currentTime = 1; // Lấy frame tại giây đầu tiên
                };

                tempVideo.oncanplay = function () {
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(tempVideo, 0, 0, canvas.width, canvas.height);
                };

                // Xử lý click để xem trước
                previewDiv.find('.video-thumbnail').on('click', function () {
                    previewVideo(url);
                });

                // Xử lý xóa
                previewDiv.find('.remove-video').on('click', function () {
                    concatExistingVideos = concatExistingVideos.filter(u => u !== url);
                    concatSelectedOrder.splice(startIndex + index, 1);
                    if (Math.ceil(concatSelectedOrder.length / pageSize) < concatCurrentPage) {
                        concatCurrentPage = concatCurrentPage > 1 ? concatCurrentPage - 1 : 1;
                    }
                    updateConcatVideosPreview();
                });

                $('#concatVideosContainer').append(previewDiv);
            }
        });

        const totalPages = Math.ceil(concatSelectedOrder.length / pageSize);
        const paginationContainer = document.getElementById('concatPagination');
        renderPagination(paginationContainer, concatCurrentPage, totalPages, function (newPage) {
            concatCurrentPage = newPage;
            updateConcatVideosPreview();
        });
    }



    // --- UPDATE BUTTON XỬ LÝ CONCATVIDEO ---
    $('#concatVideosBtn').off('click').on('click', function (e) {
        e.preventDefault();

        // Kiểm tra nếu không có video nào được chọn
        if (concatSelectedOrder.length === 0) {
            Swal.fire('Lỗi!', 'Vui lòng chọn ít nhất một video để ghép', 'error');
            return;
        }

        showLoading();
        var form = $('#concatVideosForm')[0];
        var formData = new FormData(form);

        // Xóa videos[] hiện tại khỏi formData (nếu có)
        formData.delete('videos[]');

        // Thêm cả local videos và filemanager videos vào formData
        let localVideos = [];
        let fileManagerVideos = [];

        concatSelectedOrder.forEach(function (item) {
            if (item.type === 'local') {
                formData.append('videos[]', item.data);
                localVideos.push(item.data);
            } else if (item.type === 'filemanager') {
                fileManagerVideos.push(item.data);
            }
        });

        // Thêm URLs của video từ FileManager
        if (fileManagerVideos.length > 0) {
            formData.append('existing_videos', JSON.stringify(fileManagerVideos));
        }

        // Thêm thứ tự video để server xử lý đúng
        formData.append('video_order', JSON.stringify(concatSelectedOrder.map(item => item.type)));

        // Gửi request
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
                    // Thêm timestamp vào URL để tránh cache
                    var uncachedUrl = getUncachedUrl(response.previewUrl);

                    // Reset video player hoàn toàn
                    $('#video').html('<source src="' + uncachedUrl + '" type="video/mp4">');
                    $('#video')[0].load();

                    // Lưu URL gốc để export
                    $('#video').data('originalUrl', response.previewUrl);

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

    function getUncachedUrl(url) {
        let timestamp = new Date().getTime();
        if (url.indexOf('?') !== -1) {
            // Nếu URL đã có tham số query
            return url + '&_nocache=' + timestamp;
        } else {
            // Nếu URL chưa có tham số query
            return url + '?_nocache=' + timestamp;
        }
    }

    // Xử lý nút chọn video cho extract audio
    $(document).on('click', '.extract-video-btn', function () {
        // Lưu trữ tham chiếu đến form
        extractAudioVideoRow = $(this).closest('form');
        $('#videoOptionModal').modal('show');
    });

    $(document).on('click', '.extract-video-btn', function () {
        // Set the current active tab for FileManager
        $('#editorTabs .nav-link').removeClass('active');
        $('#extract-audio-tab').addClass('active');

        // Show the video option modal
        $('#videoOptionModal').modal('show');
    });

    $('#extractVideoInput').on('change', function () {
        const file = this.files[0];
        if (file) {
            // Hiển thị tên file đã chọn
            $('#extractVideoName').text(file.name);
            $('#extractVideoType').val('local');
            $('#extractVideoUrl').val('');

            // Update video preview
            updateExtractVideoPreview(file);

            console.log("Đã chọn file video cho extract audio: " + file.name);
        }
    });




    let cutVideoPreviewUrl = null;
    let currentCutVideoFile = null;

    $(document).on('click', '.cut-video-btn', function () {
        $('#videoOptionModal').modal('show');
    });

    $('#keepCutAudio').change(function () {
        if (!this.checked) {
            $('#audioCutDiv').slideDown();
        } else {
            $('#audioCutDiv').slideUp();
        }
    });

    $('#cutVideoInput').on('change', function () {
        const file = this.files[0];
        if (file) {
            // Hiển thị tên file đã chọn
            $('#cutVideoName').text(file.name);
            $('#cutVideoType').val('local');
            $('#cutVideoUrl').val('');
            currentCutVideoFile = file;

            // Tạo video preview
            const url = URL.createObjectURL(file);
            cutVideoPreviewUrl = url;

            // Hiển thị preview
            $('#cutVideoContainer').html(`
                <div class="video-preview mb-3">
                    <video width="70" height="70" controls style="max-width: 50%; object-fit: cover;">
                        <source src="${url}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="mt-1 text-center video-name small text-truncate">${file.name}</div>
                </div>
            `);

            console.log("Đã chọn file video cho cắt video: " + file.name);
        }
    });

    $('#cutVideoForm').submit(function (e) {
        e.preventDefault();

        const videoType = $('#cutVideoType').val();
        const startTime = parseFloat($('input[name="start_time"]').val());
        const endTime = parseFloat($('input[name="end_time"]').val());

        // Kiểm tra thời gian
        if (endTime <= startTime) {
            Swal.fire('Lỗi!', 'Thời gian kết thúc phải lớn hơn thời gian bắt đầu', 'error');
            return;
        }

        // Kiểm tra nếu không có video được chọn
        if (videoType === 'local' && $('#cutVideoInput')[0].files.length === 0) {
            Swal.fire('Lỗi!', 'Vui lòng chọn một video để cắt', 'error');
            return;
        }

        if (videoType === 'filemanager' && !$('#cutVideoUrl').val()) {
            Swal.fire('Lỗi!', 'Vui lòng chọn một video để cắt', 'error');
            return;
        }

        // Kiểm tra nếu không giữ audio gốc và không chọn audio mới
        const keepAudio = $('#keepCutAudio').is(':checked');
        if (!keepAudio && $('#audioCut')[0].files.length === 0) {
            Swal.fire('Lỗi!', 'Vui lòng chọn file audio mới hoặc giữ audio gốc', 'error');
            return;
        }

        showLoading();

        var formData = new FormData(this);

        $.ajax({
            url: '/cut-video-preview',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading();
                $('#video source').attr('src', response.previewUrl);
                $('#video')[0].load();

                // Hiển thị nút xem demo
                if ($('#videoPreviewBtn').length === 0) {
                    $('#video').closest('.col-3').append(`
                    <div id="videoPreviewContainer" class="mt-2">
                        <button type="button" id="videoPreviewBtn" class="btn btn-info">Xem demo</button>
                    </div>
                    `);
                }

                // Cập nhật modal preview
                $('#previewModal').find('video source').attr('src', response.previewUrl);
                $('#previewModal').find('video')[0].load();

                // Xử lý nút xem demo
                $('#videoPreviewBtn').off('click').on('click', function () {
                    $('#previewModal').modal('show');
                });

                // Xử lý nút xuất file
                $('#exportFileBtn').off('click').on('click', function () {
                    $.ajax({
                        url: '/confirm-export',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            outputFile: $('#outputCutFile').val()
                        },
                        success: function (response) {
                            Swal.fire('Thành công!', 'File đã được xuất thành công!', 'success');
                            $('#previewModal').modal('hide');
                        },
                        error: function () {
                            Swal.fire('Lỗi!', 'Không thể xuất file', 'error');
                        }
                    });
                });
            },
            error: function (xhr) {
                hideLoading();
                Swal.fire('Lỗi!', 'Có lỗi xảy ra: ' + (xhr.responseJSON?.message || 'Vui lòng thử lại.'), 'error');
            }
        });
    });

    $('#btnSelectVideosFromFileManager').on('click', function () {
        activeMediaTab = 'videos-tab';
        $('#mediaTypeTabs a[href="#videos-content"]').tab('show');
        $('#contentImageSelectorModal').modal('show');

        // Đảm bảo load videos khi modal hiển thị
        loadContentVideoList();
    });

    function updateExtractVideoPreview(file) {
        // Clear previous preview
        $('#extractVideoPreview').empty();

        if (file) {
            // Create object URL for the file
            const url = URL.createObjectURL(file);

            // Add video preview
            $('#extractVideoPreview').html(`
                <div class="preview-video position-relative mb-3" style="width: 200px;">
                    <div class="video-thumbnail position-relative" style="height: 150px; overflow: hidden; background-color: #000;">
                        <video width="100%" height="100%" style="object-fit: cover;" controls>
                            <source src="${url}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <div class="video-name position-absolute bottom-0 start-0 end-0 p-1 bg-dark bg-opacity-75 text-white small text-truncate">
                            ${file.name}
                        </div>
                    </div>
                </div>
            `);
        }
    }


});
