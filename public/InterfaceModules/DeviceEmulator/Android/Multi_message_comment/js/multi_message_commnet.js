$(document).ready(function () {
    function loadMessages(messages) {
        var html = '';
        messages.forEach(function (msg) {
            html += '<li>';
            html += '  <figure><img src="' + msg.avatar_send_by + '" alt="Avatar"></figure>';
            html += '  <div class="text-box">';
            // Hiển thị tên người gửi trong thẻ h6 unread
            html += '    <h6 class="unread">' + msg.send_by_name + '</h6>';
            // Hiển thị nội dung tin nhắn (nếu có)
            html += '    <p>' + (msg.message ? msg.message : '') + '</p>';
            // Hiển thị tên người gửi trong thẻ strong
            html += '    <strong>' + msg.send_by_name + '</strong>';
            html += '  </div>';
            // Nếu có attachments thì hiển thị ảnh đính kèm
            if (msg.attachments) {
                html += '<div class="attachment">';
                html += '  <img src="' + msg.attachments + '" alt="Attachment">';
                html += '</div>';
            }
            html += '</li>';
        });
        $('.conversations').html(html);
    }

    function getMessages(accountUid, messageUid) {
        // Hiển thị loading khi bắt đầu lấy dữ liệu
        showLoading();
        $.ajax({
            url: '/Android/Show-more-message/' + accountUid,
            method: 'GET',
            data: { uid_message: messageUid },
            dataType: 'json',
            success: function (response) {
                // Ẩn loading khi nhận được phản hồi
                hideLoading();
                console.log(response);
                if (response.status === 'success' && response.data) {
                    loadMessages(response.data);
                    // Cập nhật active-user dựa trên tin nhắn đầu tiên
                    if (response.data.length > 0) {
                        var activeUser = response.data[0];
                        $('.active-user figure img')
                            .attr('src', activeUser.avatar_send_by)
                            .attr('alt', 'Avatar');
                        $('.active-user h6.unread').text(activeUser.send_by_name);

                        $('.chater-info figure img')
                            .attr('src', activeUser.avatar_send_by)
                            .attr('alt', 'Avatar');
                        $('.chater-info h6').text(activeUser.send_by_name);
                    }
                } else {
                    console.error(response.message || 'Không thể lấy dữ liệu');
                    $('.conversations').html('<li class="error">Không thể lấy dữ liệu</li>');
                }
            },
            error: function (err) {
                // Ẩn loading ngay cả khi xảy ra lỗi
                hideLoading();
                console.error(err);
                $('.conversations').html('<li class="error">Lỗi hệ thống, vui lòng thử lại sau.</li>');
            }
        });
    }

    $('.msg-pepl-list').on('click', 'li a', function (e) {
        e.preventDefault();
        var accountUid = $(this).data('uid');
        var messageUid = $(this).siblings('.hidden-message-uid').val();
        console.log('AccountUid:', accountUid, 'MessageUid:', messageUid);
        getMessages(accountUid, messageUid);
    });

    // Trigger tự động sự kiện click cho tin nhắn đầu tiên khi load trang
    var firstConversation = $('.msg-pepl-list li a:first');
    if (firstConversation.length) {
        firstConversation.trigger('click');
    }

    // Định nghĩa hàm showLoading và hideLoading
    function showLoading() {
        console.log("Hiển thị loading...");
        $('body').append(`
        <div id="loadingOverlay">
            <div class="loader"></div>
        </div>
    `);
    }

    function hideLoading() {
        console.log("Ẩn loading...");
        $('#loadingOverlay').remove();
    }
});