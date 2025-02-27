document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.querySelector('.doc-toggle-btn');
    const closeBtn = document.getElementById('doc-close-btn');
    const sidebar = document.getElementById('doc-sidebar');
    const menuList = document.getElementById('doc-menu-list');
    const contentArea = document.getElementById('doc-content');
    const documentationPath = '/assets/documentation';
    // Toggle sidebar open/close
    toggleBtn.addEventListener('click', (event) => {
        event.stopPropagation();
        sidebar.classList.toggle('open');
        // Nếu sidebar vừa được mở thì load danh sách hướng dẫn
        if (sidebar.classList.contains('open')) {
            loadDocumentationIndex();
        }
    });

    // Đảm bảo nút đóng cũng không bị event bubbling
    if (closeBtn) {
        closeBtn.addEventListener('click', (event) => {
            event.stopPropagation();
            sidebar.classList.remove('open');
        });
    } else {
        console.error("Không tìm thấy nút đóng. Kiểm tra ID 'doc-close-btn' trong HTML");
    }

    // Đóng sidebar khi click ra ngoài khu vực sidebar
    document.addEventListener('click', (event) => {
        if (sidebar.classList.contains('open') &&
            !sidebar.contains(event.target) &&
            event.target !== toggleBtn &&
            !toggleBtn.contains(event.target)) {
            sidebar.classList.remove('open');
        }
    });

    // Phần còn lại của code load dữ liệu hướng dẫn...
    function loadDocumentationIndex() {
        fetch(`${documentationPath}/index.json`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Không thể tải danh sách hướng dẫn');
                }
                return response.json();
            })
            .then(data => {
                renderMenu(data);
            })
            .catch(error => {
                menuList.innerHTML = `
                <li class="doc-menu-item text-danger">
                    Lỗi: ${error.message}. 
                    Vui lòng kiểm tra thư mục "/public/assets/documentation/index.json"
                </li>`;
            });
    }

    // Rest of your existing code...
    // Render menu items
    function renderMenu(data) {
        if (!data || !data.pages || data.pages.length === 0) {
            menuList.innerHTML = '<li class="doc-menu-item">Không có hướng dẫn nào</li>';
            return;
        }

        menuList.innerHTML = '';
        data.pages.forEach(page => {
            const menuItem = document.createElement('li');
            menuItem.className = 'doc-menu-item';
            menuItem.textContent = page.title;
            menuItem.dataset.id = page.id;
            menuItem.addEventListener('click', () => loadDocumentationContent(page.id));
            menuList.appendChild(menuItem);
        });

        // Nếu có page đầu tiên, mặc định load nó
        if (data.pages.length > 0) {
            loadDocumentationContent(data.pages[0].id);
        }
    }

    // Load documentation content from static JSON file
    function loadDocumentationContent(pageId) {
        // Highlight active menu item
        const menuItems = menuList.querySelectorAll('.doc-menu-item');
        menuItems.forEach(item => {
            item.classList.remove('active');
            if (item.dataset.id === pageId) {
                item.classList.add('active');
            }
        });

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
                        Vui lòng kiểm tra thư mục "/public/assets/documentation/pages/${pageId}/content.json"
                    </div>`;
            });
    }

    // Render content
    function renderContent(data, pageId) {
        if (!data) {
            contentArea.innerHTML = '<div class="alert alert-warning">Không có nội dung</div>';
            return;
        }

        let html = `
            <div class="doc-content-title">${data.title}</div>
            <div class="doc-content-body">
        `;

        // Add sections
        if (data.sections && data.sections.length > 0) {
            data.sections.forEach(section => {
                html += `<div class="doc-section">`;

                if (section.title) {
                    html += `<h3>${section.title}</h3>`;
                }

                if (section.content) {
                    html += `<div>${section.content}</div>`;
                }

                // Add images
                if (section.images && section.images.length > 0) {
                    section.images.forEach(image => {
                        html += `<img src="${documentationPath}/pages/${pageId}/images/${image}" class="doc-image" alt="${section.title || 'Hình ảnh hướng dẫn'}">`;
                    });
                }

                // Add videos
                if (section.videos && section.videos.length > 0) {
                    section.videos.forEach(video => {
                        html += `
                            <video class="doc-video" controls>
                                <source src="${documentationPath}/pages/${pageId}/videos/${video}" type="video/mp4">
                                Trình duyệt của bạn không hỗ trợ video.
                            </video>
                        `;
                    });
                }

                html += `</div>`;
            });
        }

        html += `</div>`;
        contentArea.innerHTML = html;
    }
});