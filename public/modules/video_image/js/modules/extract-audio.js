// modules/extract-audio.js
import { showLoading, hideLoading } from './common.js';

export function initExtractAudio() {
    $('#extractAudioForm').submit(function (e) {
        e.preventDefault();
        showLoading();
        const formData = new FormData(this);

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
                Swal.fire("Lỗi!", 'Có lỗi xảy ra: ' + (xhr.responseJSON.message || 'Vui lòng kiểm tra lại.'), "error");
            }
        });
    });
}