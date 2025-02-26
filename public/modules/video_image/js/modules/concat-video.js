// modules/concat-video.js
import { concatSelectedOrder, concatCurrentPage, pageSize, showLoading, hideLoading, getUncachedUrl, previewVideo } from './common.js';

let concatSelectedVideos = [];
let concatExistingVideos = [];

export function initConcatVideo() {
    $('#btnSelectVideos').on('click', () => $('#videoOptionModal').modal('show'));
    $('#btnUploadVideosFromLocal').on('click', () => {
        $('#videoOptionModal').modal('hide');
        $('#concatVideos').trigger('click');
    });

    $('#concatVideos').on('change', function (e) {
        const newFiles = Array.from(this.files);
        newFiles.forEach(file => {
            if (file.type.startsWith('video/')) {
                concatSelectedVideos.push(file);
                concatSelectedOrder.push({ type: 'local', data: file });
            }
        });
        concatCurrentPage = Math.ceil(concatSelectedOrder.length / pageSize);
        $(this).val('');
        updateConcatVideosPreview();
    });

    $('#btnSelectVideosFromFileManager').on('click', () => {
        $('#videoOptionModal').modal('hide');
        showContentVideoSelector();
    });

    $('#concatVideosBtn').on('click', function (e) {
        e.preventDefault();
        if (concatSelectedOrder.length === 0) {
            Swal.fire('Lỗi!', 'Vui lòng chọn ít nhất một video để ghép', 'error');
            return;
        }

        showLoading();
        const formData = new FormData($('#concatVideosForm')[0]);
        formData.delete('videos[]');
        let fileManagerVideos = [];

        concatSelectedOrder.forEach(item => {
            if (item.type === 'local') formData.append('videos[]', item.data);
            else if (item.type === 'filemanager') fileManagerVideos.push(item.data);
        });

        if (fileManagerVideos.length > 0) formData.append('existing_videos', JSON.stringify(fileManagerVideos));
        formData.append('video_order', JSON.stringify(concatSelectedOrder.map(item => item.type)));

        $.ajax({
            url: '/create-video-with-audio',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading();
                if (response.previewUrl) {
                    const uncachedUrl = getUncachedUrl(response.previewUrl);
                    $('#video').html('<source src="' + uncachedUrl + '" type="video/mp4">');
                    $('#video')[0].load();
                    $('#video').data('originalUrl', response.previewUrl);
                    if ($('#videoPreviewBtn').length === 0) {
                        $('#video').closest('.col-3').append(`
                            <div id="videoPreviewContainer" class="mt-2">
                                <button type="button" id="videoPreviewBtn" class="btn btn-info">Xem demo</button>
                            </div>
                        `);
                    }
                    $('#videoPreviewBtn').off('click').on('click', () => $('#previewModal').modal('show'));
                }
                Swal.fire("Thành công!", response.message, "success");
            },
            error: function (xhr) {
                hideLoading();
                Swal.fire("Lỗi!", 'Có lỗi xảy ra: ' + (xhr.responseJSON.message || 'Vui lòng kiểm tra lại.'), "error");
            }
        });
    });

    $('#keepVideoAudio').change(function () {
        if (!this.checked) $('#audioConcatDiv').slideDown();
        else $('#audioConcatDiv').slideUp();
    });
}

function updateConcatVideosPreview() {
    $('#concatVideosContainer').empty();
    const startIndex = (concatCurrentPage - 1) * pageSize;
    const endIndex = concatCurrentPage * pageSize;
    const mediaToShow = concatSelectedOrder.slice(startIndex, endIndex);

    mediaToShow.forEach((item, index) => {
        const previewDiv = item.type === 'local' ? createLocalVideoPreview(item.data, startIndex + index) : createFileManagerVideoPreview(item.data, startIndex + index);
        $('#concatVideosContainer').append(previewDiv);
    });

    const totalPages = Math.ceil(concatSelectedOrder.length / pageSize);
    renderPagination(document.getElementById('concatPagination'), concatCurrentPage, totalPages, newPage => {
        concatCurrentPage = newPage;
        updateConcatVideosPreview();
    });
}

function createLocalVideoPreview(file, index) {
    const videoName = file.name;
    const previewDiv = $(`
        <div class="preview-video position-relative mb-3" style="width:200px;" data-index="${index}">
            <div class="video-thumbnail position-relative" style="height: 150px; overflow: hidden; background-color: #000;">
                <div class="thumbnail-container" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-play-circle" style="font-size: 3rem; color: #fff; position:absolute; z-index:2;"></i>
                    <canvas width="200" height="150" class="thumbnail-canvas" style="width:100%; height:100%; object-fit:cover;"></canvas>
                </div>
                <div class="video-name position-absolute bottom-0 start-0 end-0 p-1 bg-dark bg-opacity-75 text-white small text-truncate">${videoName}</div>
            </div>
            <button type="button" class="btn btn-sm remove-video position-absolute top-0 end-0 m-1" style="border-radius:50%; background-color:white; color:black;">×</button>
        </div>
    `);

    const canvas = previewDiv.find('.thumbnail-canvas')[0];
    const tempVideo = document.createElement('video');
    tempVideo.preload = 'metadata';
    tempVideo.muted = true;
    tempVideo.src = URL.createObjectURL(file);
    tempVideo.onloadedmetadata = () => tempVideo.currentTime = 1;
    tempVideo.oncanplay = () => {
        const ctx = canvas.getContext('2d');
        ctx.drawImage(tempVideo, 0, 0, canvas.width, canvas.height);
        URL.revokeObjectURL(tempVideo.src);
    };

    previewDiv.find('.video-thumbnail').on('click', () => previewVideo(URL.createObjectURL(file)));
    previewDiv.find('.remove-video').on('click', () => {
        concatSelectedVideos = concatSelectedVideos.filter(f => f !== file);
        concatSelectedOrder.splice(index, 1);
        if (Math.ceil(concatSelectedOrder.length / pageSize) < concatCurrentPage) concatCurrentPage--;
        updateConcatVideosPreview();
    });

    return previewDiv;
}

function createFileManagerVideoPreview(url, index) {
    const videoName = url.split('/').pop();
    const previewDiv = $(`
        <div class="preview-video position-relative mb-3" style="width:200px;" data-index="${index}">
            <div class="video-thumbnail position-relative" style="height: 150px; overflow: hidden; background-color: #000; cursor:pointer;">
                <div class="thumbnail-container" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-play-circle" style="font-size: 3rem; color: #fff; position:absolute; z-index:2;"></i>
                    <canvas width="200" height="150" class="thumbnail-canvas" style="width:100%; height:100%; object-fit:cover;"></canvas>
                </div>
                <div class="video-name position-absolute bottom-0 start-0 end-0 p-1 bg-dark bg-opacity-75 text-white small text-truncate">${videoName}</div>
            </div>
            <button type="button" class="btn btn-sm remove-video position-absolute top-0 end-0 m-1" style="border-radius:50%; background-color:white; color:black;">×</button>
        </div>
    `);

    const canvas = previewDiv.find('.thumbnail-canvas')[0];
    const tempVideo = document.createElement('video');
    tempVideo.crossOrigin = "anonymous";
    tempVideo.preload = 'metadata';
    tempVideo.muted = true;
    tempVideo.src = url;
    tempVideo.onloadedmetadata = () => tempVideo.currentTime = 1;
    tempVideo.oncanplay = () => {
        const ctx = canvas.getContext('2d');
        ctx.drawImage(tempVideo, 0, 0, canvas.width, canvas.height);
    };

    previewDiv.find('.video-thumbnail').on('click', () => previewVideo(url));
    previewDiv.find('.remove-video').on('click', () => {
        concatExistingVideos = concatExistingVideos.filter(u => u !== url);
        concatSelectedOrder.splice(index, 1);
        if (Math.ceil(concatSelectedOrder.length / pageSize) < concatCurrentPage) concatCurrentPage--;
        updateConcatVideosPreview();
    });

    return previewDiv;
}

function showContentVideoSelector() {
    let fileManagerSelectedVideos = [];
    $.ajax({
        url: '/file-manager/videos',
        type: 'GET',
        dataType: 'json',
        success: function (treeData) {
            const contentVideosTree = treeData;
            let currentContentVideoFolder = null;
            let contentVideoFolderStack = [];
            loadContentVideoList(contentVideosTree, currentContentVideoFolder, contentVideoFolderStack, fileManagerSelectedVideos);
            $('#contentImageSelectorModal').modal('show');
            $('#videos-tab').tab('show');
        },
        error: function (err) {
            console.error(err);
            Swal.fire('Lỗi!', 'Lỗi tải video từ FileManager', 'error');
        }
    });

    $('#btnConfirmFileManagerSelection').off('click').on('click', () => {
        fileManagerSelectedVideos.forEach(url => {
            if (!concatExistingVideos.includes(url)) {
                concatExistingVideos.push(url);
                concatSelectedOrder.push({ type: 'filemanager', data: url });
            }
        });
        fileManagerSelectedVideos = [];
        $('#contentImageSelectorModal').modal('hide');
        concatCurrentPage = Math.ceil(concatSelectedOrder.length / pageSize);
        updateConcatVideosPreview();
    });
}

function loadContentVideoList(tree, folder, stack, selected) {
    $('#contentVideoList').empty();
    if (folder === null) {
        $('#currentContentVideoFolder').text('Danh sách thư mục');
        $('#backContentImageButton').hide();
        const folders = Object.keys(tree);
        folders.forEach(folderKey => {
            const folderCard = $(`
                <div class="col-md-3 mb-2">
                    <div class="card" style="cursor:pointer; background-color:#b7474a;">
                        <div class="card-body text-center text-white">
                            <h5 class="card-title">${folderKey}</h5>
                            <p class="card-text">${tree[folderKey].length} video</p>
                        </div>
                    </div>
                </div>
            `);
            folderCard.on('click', () => {
                stack.push(folder);
                loadContentVideoList(tree, folderKey, stack, selected);
            });
            $('#contentVideoList').append(folderCard);
        });
    } else {
        $('#currentContentVideoFolder').text(folder);
        $('#backContentImageButton').show().off('click').on('click', () => {
            folder = stack.pop();
            loadContentVideoList(tree, folder, stack, selected);
        });
        const videos = tree[folder] || [];
        videos.forEach(video => {
            const isSelected = selected.includes(video.url);
            const cardContainer = $(`
                <div class="col-md-4 mb-3">
                    <div class="card ${isSelected ? 'selected' : ''}" style="cursor:pointer;">
                        <div class="video-thumbnail position-relative" style="height: 150px; overflow: hidden; background-color: #000;">
                            <video src="${video.url}" preload="metadata" style="width: 100%; height: 100%; object-fit: cover;" muted></video>
                            <div class="play-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center;">
                                <div class="play-button" style="width: 50px; height: 50px; border-radius: 50%; background-color: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-play-fill" style="font-size: 2rem; color: #fff;"></i>
                                </div>
                            </div>
                            <span class="badge bg-dark position-absolute bottom-0 end-0 m-2"><i class="bi bi-film me-1"></i>Video</span>
                        </div>
                        <div class="card-body p-2">
                            <p class="card-text mb-0 text-truncate" style="color: #000;" title="${video.name}">${video.name}</p>
                        </div>
                    </div>
                </div>
            `);

            const videoEl = cardContainer.find('video')[0];
            videoEl.onloadedmetadata = () => {
                const duration = Math.round(videoEl.duration);
                const minutes = Math.floor(duration / 60);
                const seconds = duration % 60;
                cardContainer.find('.badge').text(`${minutes}:${seconds < 10 ? '0' + seconds : seconds}`);
            };

            cardContainer.find('.play-overlay, .play-button').on('click', e => {
                e.stopPropagation();
                previewVideo(video.url);
            });

            cardContainer.find('.card').on('click', function (e) {
                if (!$(e.target).closest('.play-overlay, .play-button').length) {
                    if (!selected.includes(video.url)) {
                        selected.push(video.url);
                        $(this).addClass('selected');
                    } else {
                        selected.splice(selected.indexOf(video.url), 1);
                        $(this).removeClass('selected');
                    }
                }
            });

            $('#contentVideoList').append(cardContainer);
        });
    }
}

// Giả lập hàm renderPagination
function renderPagination(container, current, total, callback) {
    // Thêm logic phân trang tại đây nếu cần
}