// modules/common.js
export let selectedOrder = [];
export let concatSelectedOrder = [];
export let currentPage = 1;
export let concatCurrentPage = 1;
export const pageSize = 9;

export function showLoading() {
    console.log("Hiển thị loading...");
    if ($('#loadingOverlay').length === 0) {
        $('body').append(`
            <div id="loadingOverlay">
                <div class="loader"></div>
            </div>
        `);
    }
}

export function hideLoading() {
    console.log("Ẩn loading...");
    $('#loadingOverlay').remove();
}

export function getUncachedUrl(url) {
    let timestamp = new Date().getTime();
    return url.indexOf('?') !== -1 ? url + '&_nocache=' + timestamp : url + '?_nocache=' + timestamp;
}

export function previewVideo(videoUrl) {
    if ($('#tempVideoPreviewModal').length === 0) {
        $('body').append(`
            <div class="modal fade" id="tempVideoPreviewModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
                    <div class="modal-content wrapper">
                        <div class="modal-header py-2">
                            <h5 class="modal-title video-title">Xem thử video</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body p-0">
                            <video id="tempPreviewVideo" controls style="display: block; width: 100%; height: auto; max-height: 80vh;">
                                <source src="" type="video/mp4">
                            </video>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    const videoName = videoUrl.split('/').pop();
    $('.video-title').text(videoName);
    $('#tempPreviewVideo source').attr('src', videoUrl);
    const videoElement = $('#tempPreviewVideo')[0];
    videoElement.load();

    $(videoElement).on('loadedmetadata', function () {
        const viewportHeight = window.innerHeight;
        const viewportWidth = window.innerWidth;
        const videoAspectRatio = this.videoWidth / this.videoHeight;
        let maxWidth = Math.min(viewportWidth * 0.9, this.videoWidth);
        let maxHeight = Math.min(viewportHeight * 0.8, this.videoHeight);
        const modalDialog = $('#tempVideoPreviewModal .modal-dialog');
        if (this.videoWidth < viewportWidth * 0.5 && this.videoHeight < viewportHeight * 0.5) {
            modalDialog.css('max-width', this.videoWidth + 30 + 'px');
        } else {
            modalDialog.css('max-width', Math.min(maxWidth, maxHeight * videoAspectRatio) + 'px');
        }
    });

    $('#tempVideoPreviewModal').modal('show');
    $('#tempVideoPreviewModal').on('shown.bs.modal', () => videoElement.play());
    $('#tempVideoPreviewModal').on('hidden.bs.modal', () => videoElement.pause());
}