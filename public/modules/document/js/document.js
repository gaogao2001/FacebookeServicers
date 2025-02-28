document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.querySelector('.doc-toggle-btn');
    const closeBtn = document.getElementById('doc-close-btn');
    const sidebar = document.getElementById('doc-sidebar');
    const contentArea = document.getElementById('doc-content');
    const documentationPath = '/assets/documentation';

    // Lấy đường dẫn URL hiện tại (trừ domain)
    const currentPath = window.location.pathname;
    // Loại bỏ dấu "/" đầu tiên nếu có
    const pageId = currentPath.startsWith('/') ? currentPath.substring(1) : currentPath;

    // Toggle sidebar open/close
    toggleBtn.addEventListener('click', (event) => {
        event.stopPropagation();
        sidebar.classList.toggle('open');

        // Khi sidebar được mở, tải tài liệu dựa trên URL hiện tại
        if (sidebar.classList.contains('open')) {
            loadDocumentationContent(pageId);
        }
    });

    // Xử lý đóng sidebar
    if (closeBtn) {
        closeBtn.addEventListener('click', (event) => {
            event.stopPropagation();
            sidebar.classList.remove('open');
        });
    }

    // Đóng sidebar khi click ra ngoài
    document.addEventListener('click', (event) => {
        if (sidebar.classList.contains('open') &&
            !sidebar.contains(event.target) &&
            event.target !== toggleBtn &&
            !toggleBtn.contains(event.target)) {
            sidebar.classList.remove('open');
        }
    });

    // Hàm load nội dung tài liệu dựa trên pageId (URL hiện tại)
    function loadDocumentationContent(pageId) {
        contentArea.innerHTML = '<div class="doc-loading">Đang tải nội dung...</div>';

        fetch(`${documentationPath}/pages/${pageId}/content.json`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Không thể tải nội dung hướng dẫn');
                }
                return response.json();
            })
            .then(data => {
                renderContent(data, pageId);
            })
            .catch(error => {
                contentArea.innerHTML = `
                    <div class="alert alert-danger">
                        Lỗi: ${error.message}<br>
                        Không tìm thấy tài liệu cho trang này. Vui lòng thêm tài liệu mới.
                    </div>`;
            });
    }

    function renderContent(data, pageId) {
        if (!data) {
            contentArea.innerHTML = '<div class="alert alert-warning">Không có nội dung để hiển thị.</div>';
            return;
        }

        let html = `<div class="doc-content-title">${data.title || ''}</div>`;
        html += `<div class="doc-content-body">`;

        // Phần nội dung hướng dẫn
        if (data.content) {
            html += `<div class="doc-guide-content" style="margin-bottom: 20px;">`;
            html += `<h3>Nội dung hướng dẫn</h3>`;
            html += `<div>${data.content}</div>`;
            html += `</div>`;
        }

        // Phần hình ảnh & video
        if ((data.images && data.images.length > 0) || (data.videos && data.videos.length > 0)) {
            html += `<div class="doc-media-section" style="margin-top: 20px;">`;
            html += `<h3>Hình ảnh & Video hướng dẫn</h3>`;
            // Hiển thị hình ảnh với kích thước nhỏ (50% width)
            if (data.images && data.images.length > 0) {
                data.images.forEach(image => {
                    html += `<img src="${documentationPath}/pages/${pageId}/images/${image}" class="doc-image" alt="Hình ảnh hướng dẫn" style="width:28%; margin: 5px;">`;
                });
            }
            // Hiển thị video với kích thước nhỏ (50% width)
            if (data.videos && data.videos.length > 0) {
                data.videos.forEach(video => {
                    html += `
                        <video class="doc-video" controls style="width:28%; margin: 5px;">
                            <source src="${documentationPath}/pages/${pageId}/videos/${video}" type="video/mp4">
                            Trình duyệt của bạn không hỗ trợ video.
                        </video>
                    `;
                });
            }
            html += `</div>`;
        }

        html += `</div>`;
        contentArea.innerHTML = html;
    }

    // Khi có sự kiện load documentation, tải tài liệu theo URL hiện tại
    document.addEventListener('loadDocumentation', function () {
        if (sidebar) {
            loadDocumentationContent(pageId);
        }
    });

    $('#addPageForm').on('submit', function (e) {
        e.preventDefault();

        if ($('#pageId').val() === '') {
            $('#pageId').val(pageId);
        }
        // Sử dụng FormData để bao gồm cả file upload
        var formData = new FormData(this);

        $.ajax({
            url: '/document',
            type: 'POST',
            data: formData,
            processData: false,  // Không xử lý dữ liệu thành chuỗi query
            contentType: false,  // Không set contentType mặc định
            dataType: 'json',
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: response.message || "Tạo tài liệu thành công!"
                });
                $('#addPageForm')[0].reset();
                $('#addPageModal').modal('hide');

                if (typeof window.loadDocumentationIndex === 'function') {
                    window.loadDocumentationIndex();
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: "Lỗi: " + error
                });
            }
        });
    });
});

// Xuất hàm loadDocumentationIndex ra ngoài để có thể gọi từ bên ngoài
window.loadDocumentationIndex = loadDocumentationIndex;


