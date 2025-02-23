document.addEventListener('DOMContentLoaded', function () {
    const createFolderButton = document.getElementById("createFolderButton");
    const newFolderInput = document.getElementById("newFolderInput");
    const popupContent = document.getElementById("popupContent");
    const currentFolderName = document.getElementById("currentFolderName");
    const selectedPathInput = document.getElementById("selectedPath");
    const selectPathButton = document.getElementById("selectPathButton");
    const backButton = document.getElementById("backButton");
    const resetPathButton = document.getElementById("resetPathButton");
    const csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const folderModalElement = document.getElementById('folderModal');
    const folderModal = new bootstrap.Modal(folderModalElement, {
        backdrop: 'static',
        keyboard: false
    });

    // Đường dẫn mặc định
    const defaultPath = "/var/www/FacebookService/public/FileData/";
    let currentPath = "/var/www/html/FileData/";

    // Hàm chuẩn hóa đường dẫn (loại bỏ dấu gạch chéo dư thừa)
    function normalizePath(path) {
        return path.replace(/\/+/g, '/');
    }

    // Hàm hiển thị thông báo
    function showNotification(message, success = true) {
        const notificationDiv = document.createElement("div");
        notificationDiv.textContent = message;
        notificationDiv.style.padding = "10px";
        notificationDiv.style.margin = "10px 0";
        notificationDiv.style.borderRadius = "5px";
        notificationDiv.style.color = success ? "#155724" : "#721c24";
        notificationDiv.style.backgroundColor = success ? "#d4edda" : "#f8d7da";
        popupContent.prepend(notificationDiv);
        setTimeout(() => {
            notificationDiv.remove();
        }, 3000);
    }

    // Hàm cập nhật tên thư mục hiện tại
    function updateCurrentFolderName(path) {
        currentFolderName.textContent = `Đường dẫn hiện tại: ${path}`;
        if (selectedPathInput) {
            selectedPathInput.value = path; // Đồng bộ với input đã chọn
        }
    }

    // Hàm cập nhật đường dẫn trong backend
    function updatePathInDatabase(path) {
        path = normalizePath(path); // Chuẩn hóa đường dẫn trước khi gửi
        fetch(`/file-manager/update-path`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf_token
            },
            body: JSON.stringify({ selectedPath: path })
        })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    showNotification(data.message, true);
                    updateCurrentFolderName(path); // Cập nhật giao diện ngay lập tức
                } else if (data.error) {
                    showNotification(data.error, false);
                }
            })
            .catch(error => {
                console.error(error);
                showNotification("Có lỗi xảy ra khi cập nhật đường dẫn.", false);
            });
    }

    // Hàm đặt lại đường dẫn
    resetPathButton.addEventListener('click', () => {
        currentPath = normalizePath(defaultPath); // Đặt lại đường dẫn mặc định
        updateCurrentFolderName(currentPath); // Cập nhật tên thư mục hiện tại trên giao diện
        updatePathInDatabase(currentPath); // Gửi đường dẫn mới lên backend
        loadFolder(currentPath); // Tải lại nội dung thư mục
        showNotification("Đường dẫn đã được đặt lại thành công.", true);
    });

    // Hàm lấy đường dẫn thư mục cha
    function getParentPath(path) {
        if (path.endsWith('/')) path = path.slice(0, -1);
        const parts = path.split('/');
        parts.pop();
        const parentPath = parts.join('/') + '/';
        return parentPath.length < "/var/www/html/FileData/".length ? "/var/www/html/FileData/" : parentPath;
    }

    // Hàm tải thư mục
    function loadFolder(path) {
        path = normalizePath(path); // Chuẩn hóa đường dẫn trước khi gửi
        fetch(`/file-manager/get-directories?path=${encodeURIComponent(path)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderFolders(data.success);
                    updateCurrentFolderName(currentPath);
                } else {
                    showNotification(data.error, false);
                }
            })
            .catch(error => {
                showNotification("Có lỗi xảy ra khi kết nối tới máy chủ.", false);
                console.error(error);
            });
    }

    // Hàm hiển thị danh sách thư mục
    function renderFolders(folders) {
        popupContent.innerHTML = '';
        folders.forEach(folder => {
            const folderDiv = document.createElement('div');
            folderDiv.className = 'folder d-flex align-items-center mb-2 p-2';
            // Removed inline styles:
            // folderDiv.style.cursor = "pointer";
            // folderDiv.style.border = "1px solid #ddd";
            // folderDiv.style.borderRadius = "4px";
            // folderDiv.style.position = "relative";

            const icon = document.createElement('i');
            icon.className = 'bi bi-folder me-2 folder-icon'; // Added 'folder-icon' class

            const folderName = document.createElement('span');
            folderName.className = 'folder-name';
            folderName.textContent = folder;

            folderDiv.appendChild(icon);
            folderDiv.appendChild(folderName);

            // Nút xóa
            const deleteButton = document.createElement('button');
            deleteButton.className = 'btn btn-sm ms-2';
            deleteButton.textContent = 'x';
            deleteButton.style.position = 'absolute';
            deleteButton.style.top = '0';
            deleteButton.style.right = '0';
            deleteButton.style.backgroundColor = 'white';
            deleteButton.style.color = 'black';


            deleteButton.addEventListener('click', (e) => {
                e.stopPropagation(); // Ngăn sự kiện click vào folderDiv
                Swal.fire({
                    title: 'Bạn có chắc chắn muốn xóa?',
                    text: `Thư mục: ${folder}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteFolder(folder);
                    }
                });
            });

            folderDiv.addEventListener('click', () => {
                currentPath = normalizePath(currentPath + '/' + folder + '/');
                loadFolder(currentPath);
            });

            folderDiv.appendChild(deleteButton);
            popupContent.appendChild(folderDiv);
        });
    }

    // Hàm xóa folder bằng AJAX
    function deleteFolder(folderName) {
        const deletePath = normalizePath(currentPath + '/' + folderName);
        fetch('/file-manager/delete-folder', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf_token
            },
            body: JSON.stringify({ path: deletePath })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.success, true);
                    loadFolder(currentPath);
                } else {
                    showNotification(data.error || 'Không thể xóa thư mục.', false);
                }
            })
            .catch(error => {
                console.error(error);
                showNotification("Có lỗi xảy ra khi xóa thư mục.", false);
            });
    }

    // Sự kiện tạo thư mục
    createFolderButton.addEventListener('click', () => {
        const newFolderName = newFolderInput.value.trim();
        if (!newFolderName) {
            showNotification("Vui lòng nhập tên thư mục.", false);
            return;
        }

        const updatedPath = normalizePath(currentPath + '/' + newFolderName + '/');

        fetch(`/file-manager/create-folder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf_token
            },
            body: JSON.stringify({
                path: currentPath,
                newFolderName: newFolderName
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.error && data.error.includes("đã tồn tại")) {
                    Swal.fire({
                        title: 'Thư mục đã tồn tại!',
                        text: 'Bạn có muốn tiếp tục thay đổi đường dẫn này không?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Có',
                        cancelButtonText: 'Không'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            currentPath = updatedPath;
                            updateCurrentFolderName(currentPath);
                            updatePathInDatabase(currentPath);
                            loadFolder(currentPath);
                        }
                    });
                } else if (data.error) {
                    showNotification(data.error, false);
                } else {
                    showNotification(data.success, true);
                    loadFolder(currentPath);
                    newFolderInput.value = '';
                }
            })
            .catch(error => {
                console.error(error);
                showNotification("Có lỗi xảy ra khi tạo thư mục.", false);
            });
    });

    // Sự kiện chọn đường dẫn
    selectPathButton.addEventListener("click", () => {
        updatePathInDatabase(currentPath);
        folderModal.hide();
    });

    // Sự kiện quay lại
    backButton.addEventListener("click", function () {
        currentPath = getParentPath(currentPath);
        loadFolder(currentPath);
    });

    // Mở modal
    folderModalElement.addEventListener('shown.bs.modal', () => loadFolder(currentPath));

    // Đóng modal
    folderModalElement.addEventListener('hidden.bs.modal', () => {
        popupContent.innerHTML = '';
        updateCurrentFolderName(currentPath);
    });
});
