$(document).ready(function () {
    $('#addVideo').submit(function (e) {
        e.preventDefault();

        const url = $('#addUrl').val();
        const extractFrames = $('#addExtractFrames').is(':checked') ? 1 : 0;
        const uid = $('#addUid').val();
        const csrfToken = $('input[name="_token"]').val();

        console.log('Gửi dữ liệu:', {
            url: url,
            extract_frames: extractFrames,
            uid: uid
        });
        // Hiển thị loading overlay
        showLoading();

        $.ajax({
            url: '/get-video',
            method: 'POST',
            data: {
                _token: csrfToken,
                url: url,
                extract_frames: extractFrames,
                uid: uid
            },
            success: function (response) {
                console.log('Phản hồi thành công:', response);
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: 'Video đã được tải xuống thành công.'
                });
                $('#addVideoModal').modal('hide');
                $('#addVideo')[0].reset();
            },
            error: function (xhr) {
                console.log('Lỗi từ server:', xhr);
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = '';
                    for (let key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            errorMessages += errors[key].join('\n') + '\n';
                        }
                    }
                    alert(errorMessages);
                } else {
                    const error = xhr.responseJSON.error || 'Có lỗi xảy ra.';
                    alert(error);
                }
            },
            complete: function () {
                // Ẩn loading overlay sau khi hoàn tất yêu cầu
                hideLoading();
            }
        });
    });
});

let lastEndCursor = null;
let logText = "";      // Biến lưu toàn nhật ký (các thông báo)
let allVideoIDs = "";  // Biến lưu các ID video (chỉ ID)
var logSpinnerInterval = null;

function appendLog(message) {
    logText += message + "\n";
    $('#videoUIDResult').val(logText);
}

// Hàm hiển thị spinner (dấu "...") nhấp nháy ở cuối log
function startLogSpinner() {
    if (logSpinnerInterval) return;
    logSpinnerInterval = setInterval(() => {
        let dots = '';
        // Duyệt số chấm từ 1 đến 3 dựa trên thời gian
        let count = (new Date().getMilliseconds() % 4);
        for (let i = 0; i < count; i++) {
            dots += '.';
        }
        let spinnerText = "\nĐang xử lý" + dots;
        $('#videoUIDResult').val(logText + spinnerText);
    }, 500);
}

function stopLogSpinner() {
    if (logSpinnerInterval) {
        clearInterval(logSpinnerInterval);
        logSpinnerInterval = null;
        $('#videoUIDResult').val(logText);
    }
}

$('#addVideo2').submit(function (e) {
    e.preventDefault();

    const csrfToken = $('input[name="_token"]').val();
    const uid = $(this).find('button[type="submit"]').data('uid');
    const url = $('#addUrl2').val();
    const extractFrames = $('#addExtractFrames2').is(':checked');

    // Reset nhật ký và danh sách ID
    logText = "";
    allVideoIDs = "";
    appendLog("Tiến hành gửi link " + url + " để lấy UID Profile !");

    // Bắt đầu spinner log
    startLogSpinner();

    $.ajax({
        url: '/get-video-by-url',
        method: 'POST',
        data: {
            _token: csrfToken,
            uid: uid,
            url: url,
            extract_frames: extractFrames
        },
        success: function (response) {
            stopLogSpinner();
            console.log('Response từ getVideoByUrl:', response);
            appendLog("Lấy UID Profile thành công cho link " + url + " => Kết quả: UID: " + uid);
            appendLog("Tiến hành lấy danh sách Video ID cho UID " + uid + "...");

            let countVideo = 0;
            if (response.info && Array.isArray(response.info)) {
                countVideo = response.info.length;
                // Lưu id video
                allVideoIDs += response.info.map(video => video.id).join('\n') + "\n";
            } else if (response.id) {
                countVideo = 1;
                allVideoIDs += response.id + "\n";
            }
            appendLog("Vừa lấy thành công Video ID cho " + uid + " với kết quả là: "
                + countVideo + " ID video" + (response.end_cursor ? " (Có end_cursor, tiến hành tải thêm)" : ""));

            if (response.end_cursor) {
                lastEndCursor = response.end_cursor;
                autoLoadMore(csrfToken, uid, url, extractFrames, lastEndCursor);
            } else {
                lastEndCursor = null;
                appendLog("Đã hết video để tải thêm.");
                let total = allVideoIDs.trim().split('\n').length;
                appendLog("Tổng số Video ID đã lấy là: " + total);
                localStorage.setItem("videoIDs_log", allVideoIDs);
                Swal.fire('Thông báo', 'Đã lấy xong toàn bộ video ID.', 'info');
                // Sau khi lấy danh sách video ID, bắt đầu download video từng ID
                downloadVideos(allVideoIDs, uid, csrfToken);
            }
        },
        error: function (xhr) {
            stopLogSpinner();
            console.error('Lỗi getVideoByUrl:', xhr);
            const errorMsg = xhr.responseJSON.error || 'Không thể lấy video theo URL.';
            Swal.fire('Lỗi', errorMsg, 'error');
        }
    });
});

function autoLoadMore(csrfToken, uid, url, extractFrames, cursor) {
    startLogSpinner();
    $.ajax({
        url: '/get-video-by-url',
        method: 'POST',
        data: {
            _token: csrfToken,
            uid: uid,
            url: url,
            extract_frames: extractFrames,
            end_cursor: cursor
        },
        success: function (response) {
            stopLogSpinner();
            console.log('Response tải thêm từ getVideoByUrl:', response);
            let newCount = 0;
            if (response.info && Array.isArray(response.info)) {
                newCount = response.info.length;
                allVideoIDs += response.info.map(video => video.id).join('\n') + "\n";
            } else if (response.id) {
                newCount = 1;
                allVideoIDs += response.id + "\n";
            }
            appendLog("Vừa load thêm Video ID thành công cho " + uid + " với kết quả là: " + newCount + " ID video");

            if (response.end_cursor) {
                autoLoadMore(csrfToken, uid, url, extractFrames, response.end_cursor);
            } else {
                appendLog("Đã hết video để tải thêm.");
                let total = allVideoIDs.trim().split('\n').length;
                appendLog("Tổng số Video ID đã lấy là: " + total);
                localStorage.setItem("videoIDs_log", allVideoIDs);
                Swal.fire('Thông báo', 'Đã lấy xong toàn bộ video ID.', 'info');
                // Sau khi lấy xong danh sách, gọi hàm download
                downloadVideos(allVideoIDs, uid, csrfToken);
            }
        },
        error: function (xhr) {
            stopLogSpinner();
            console.error('Lỗi tải thêm getVideoByUrl:', xhr);
            const errorMsg = xhr.responseJSON.error || 'Không thể tải thêm video.';
            Swal.fire('Lỗi', errorMsg, 'error');
        }
    });
}

function downloadVideos(videoIDsStr, uid, csrfToken) {
    // Tách chuỗi thành mảng, loại bỏ các dòng trống
    let videoIds = videoIDsStr.trim().split('\n').filter(id => id.trim() !== "");
    let batchSize = 10;
    let batchIndex = 0;

    function downloadBatch() {
        let start = batchIndex * batchSize;
        if (start < videoIds.length) {
            let currentBatch = videoIds.slice(start, start + batchSize);
            appendLog("Tiến hành download batch " + (batchIndex + 1) + " với " + currentBatch.length + " video(s).");
            let completed = 0;

            currentBatch.forEach(function (videoId) {
                appendLog("Tiến hành gửi Video ID: " + videoId + " đi download");

                $.ajax({
                    url: '/get-video',
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        uid: uid,
                        url: "https://www.facebook.com/" + videoId,
                        extract_frames: 0  // truyền dưới dạng boolean false
                    },
                    success: function (response) {
                        appendLog("Download Video ID: " + videoId + " Thành công!");
                    },
                    error: function (xhr) {
                        const errorMsg = xhr.responseJSON.error || "Lỗi download Video ID: " + videoId;
                        appendLog("Download Video ID: " + videoId + " thất bại: " + errorMsg);
                    },
                    complete: function () {
                        completed++;
                        // Khi hoàn thành batch hiện tại
                        if (completed === currentBatch.length) {
                            batchIndex++;
                            setTimeout(downloadBatch, 2000); // Delay 2 giây trước khi download batch kế tiếp
                        }
                    }
                });
            });
        } else {
            appendLog("Đã tải xong toàn bộ video.");
            Swal.fire('Thông báo', 'Đã tải xong toàn bộ video.', 'info');
        }
    }
    downloadBatch();
}

$('#addMediaFile').submit(function (e) {
    e.preventDefault();
    const form = this;
    showLoading();

    // Kiểm tra IP trước khi gửi file
    $.ajax({
        url: '/check-ip',
        method: 'GET',
        success: function (ipResponse) {
            // Nếu IP hợp lệ, mới thực hiện upload file
            const formData = new FormData(form);
            let fileType = $('#uploadFile').val().split('.').pop().toLowerCase();
            let uploadUrl = '';

            if (['mp4', 'mov', 'avi', 'flv'].includes(fileType)) {
                uploadUrl = '/upload-video/' + $('#addUid').val();
            } else if (['jpeg', 'png', 'jpg', 'gif', 'svg'].includes(fileType)) {
                uploadUrl = '/upload-image/' + $('#addUid').val();
            } else {
                hideLoading();
                Swal.fire('Lỗi!', 'Định dạng tệp không hợp lệ.', 'error');
                return;
            }

            $.ajax({
                url: uploadUrl,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    Swal.fire('Thành công', 'Tệp đã được tải lên thành công.', 'success');
                    $('#addVideoModal').modal('hide');
                    console.log('Phản hồi từ server:', response);
                },
                error: function (xhr) {
                    Swal.fire('Lỗi!', 'Không thể tải lên tệp. Vui lòng thử lại.', 'error');
                    console.error('Lỗi từ server:', xhr);
                },
                complete: function () {
                    hideLoading();
                }
            });
        },
        error: function (xhr) {
            hideLoading();
            Swal.fire('Lỗi!', xhr.responseJSON.error || 'IP không hợp lệ, không được phép upload.', 'error');
        }
    });
});



// Hiển thị loader
function showLoading() {
    console.log("Hiển thị loading...");
    $('body').append(`
        <div id="loadingOverlay" style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        ">
            <div class="loader"></div>
        </div>
    `);
}

// Ẩn loader
function hideLoading() {
    console.log("Ẩn loading...");
    $('#loadingOverlay').remove();
}
///tải video lên 
