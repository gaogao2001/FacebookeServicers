// modules/concat-segments.js
import { showLoading, hideLoading } from './common.js';

let videoSegmentIndex = 1;
let currentSegmentRow = null;

export function initConcatSegments() {
    $('#addVideoSegmentBtn').on('click', () => {
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

    $(document).on('click', '.removeSegmentBtn', function () {
        $(this).closest('.video-segment-row').remove();
    });

    $(document).on('click', '.select-video-btn', function () {
        currentSegmentRow = $(this).closest('.video-segment-row');
        $('#videoOptionModal').modal('show');
    });

    $(document).on('change', '.segment-video-input', function () {
        const file = this.files[0];
        const row = $(this).closest('.video-segment-row');
        if (file) {
            row.find('.selected-video-name').text(file.name);
            row.find('.segment-video-type').val('local');
            row.find('.segment-video-url').val('');
            console.log("Đã chọn file: " + file.name + " cho segment");
        }
    });

    $('#btnUploadVideosFromLocal').on('click', () => {
        if (currentSegmentRow) {
            currentSegmentRow.find('.segment-video-input').trigger('click');
            $('#videoOptionModal').modal('hide');
        }
    });

    $('#btnSelectVideosFromFileManager').on('click', () => {
        $('#videoOptionModal').modal('hide');
        showContentVideoSelector();
    });

    $('#keepSegmentsAudio').change(function () {
        if (!this.checked) $('#audioSegmentsDiv').slideDown();
        else $('#audioSegmentsDiv').slideUp();
    });

    $('#concatVideoSegmentsForm').submit(function (e) {
        e.preventDefault();
        console.log("Total video segments: " + $('.video-segment-row').length);
        $('.video-segment-row').each(function (index) {
            const fileInput = $(this).find('.segment-video-input')[0];
            console.log("Segment " + index + " file count: " + (fileInput.files ? fileInput.files.length : 0));
            if (fileInput.files && fileInput.files[0]) console.log("Segment " + index + " file name: " + fileInput.files[0].name);
        });

        const formData = new FormData();
        formData.append('_token', $('input[name="_token"]').val());
        formData.append('outputFile', $('#outputSegmentsFile').val());
        formData.append('keepVideoAudio', $('#keepSegmentsAudio').is(':checked') ? '1' : '0');
        if ($('#audioSegments')[0].files.length > 0) formData.append('audio', $('#audioSegments')[0].files[0]);

        $('.video-segment-row').each(function (index) {
            const start = $(this).find('input[name^="segments["][name$="[start]"]').val();
            const end = $(this).find('input[name^="segments["][name$="[end]"]').val();
            formData.append(`segments[${index}][start]`, start);
            formData.append(`segments[${index}][end]`, end);
        });

        let videoTypes = [], videoUrls = [], hasVideos = false;
        $('.video-segment-row').each(function () {
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
                videoUrls.push(url || '');
                if (url) hasVideos = true;
            }
        });

        if (!hasVideos) {
            Swal.fire('Lỗi!', 'Vui lòng chọn ít nhất một video để ghép', 'error');
            return;
        }

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
                $('#videoPreviewBtn').off('click').on('click', () => $('#previewModal').modal('show'));
            },
            error: function (xhr) {
                hideLoading();
                Swal.fire('Lỗi!', 'Có lỗi xảy ra: ' + xhr.responseJSON.message, 'error');
            }
        });
    });
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
        if (currentSegmentRow && fileManagerSelectedVideos.length > 0) {
            const videoUrl = fileManagerSelectedVideos[0];
            currentSegmentRow.find('.selected-video-name').text(videoUrl.split('/').pop());
            currentSegmentRow.find('.segment-video-url').val(videoUrl);
            currentSegmentRow.find('.segment-video-type').val('filemanager');
            currentSegmentRow.find('.segment-video-input').val('');
        }
        fileManagerSelectedVideos = [];
        $('#contentImageSelectorModal').modal('hide');
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
                // previewVideo(video.url); // Nếu cần, thêm lại từ common.js
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