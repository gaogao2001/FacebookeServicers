document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    const perPage = 10;
    let lastPage = null;
    let currentSearch = '';
    let loading = false;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const fanpageList = document.getElementById('fanpageList');
    const loadingIndicator = document.getElementById('loading');
    const searchInput = document.getElementById('searchInput');
    const tableResponsive = document.querySelector('.table-responsive');
    const selectAllCheckbox = document.getElementById('selectAll');
    const deleteAllButton = document.getElementById('deleteAllButton');
    const paginationContainer = document.getElementById('pagination');


    const loadFanpages = (page = 1, search = '', append = false) => {
        if (loading) return;
        loading = true;
        loadingIndicator.style.display = 'block';


        fetch(`/admin/facebook/fanpages/data?page=${page}&search=${encodeURIComponent(search)}`, {
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
            .then(response => response.json())
            .then(data => {
                if (!append) {
                    fanpageList.innerHTML = ''; // Xóa dữ liệu hiện tại nếu không phải append
                }

                data.data.forEach(fanpage => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><input type="checkbox" class="selectItem" value="${fanpage.page_id}"></td>
                        <td>${fanpage.uid_controler}</td>
                        <td>${fanpage.page_id}</td>
                        <td>${fanpage.page_name}</td>
                        <td>${fanpage.likes}</td>
                        <td>${fanpage.followers}</td>
                        <td>${fanpage.post}</td>
                        <td>${fanpage.admins}</td>
                        <td>${fanpage.SourceControl}</td>
                        <td>
                             <button class="btn btn-inverse-light editBtn" data-id="${fanpage._id.$oid}" onclick="window.location.href='/admin/facebook/fanpages/edit/${fanpage._id.$oid}'">
                                 <i class="fas fa-edit"></i> Sửa
                             </button>
                             <button class="btn btn-inverse-danger deleteBtn" data-id="${fanpage._id.$oid}">
                                 <i class="fas fa-trash-alt"></i> Xóa
                             </button>
                        </td>
                    `;
                    fanpageList.appendChild(row);
                });

                // Cập nhật trạng thái phân trang
                currentPage = data.currentPage;
                lastPage = data.lastPage;
                currentSearch = search;

                renderPagination(paginationContainer, currentPage, lastPage, (newPage) => {
                    loadFanpages(newPage, currentSearch);
                });



                // Đặt lại vị trí cuộn về đầu bảng
                tableResponsive.scrollTop = 0;

                loading = false;
                loadingIndicator.style.display = 'none';

                toggleDeleteAllButton();
            })
            .catch(error => {
                console.error('Error fetching fanpages:', error);
                loading = false;
                loadingIndicator.style.display = 'none';
            });
    };

    loadFanpages(currentPage, currentSearch);


    // Xử lý sự kiện tìm kiếm trực tiếp (live search)
    let debounceTimeout = null;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            currentSearch = searchInput.value.trim();
            currentPage = 1;
            loadFanpages(currentPage, currentSearch);
        }, 300); // Delay 300ms để ngăn nhiều yêu cầu
    });

    // Tải trang đầu tiên khi tải xong
    loadFanpages(currentPage, currentSearch);

    function toggleDeleteAllButton() {
        const selectedItems = document.querySelectorAll('.selectItem:checked');
        deleteAllButton.style.display = selectedItems.length > 0 ? 'inline-block' : 'none';
    }

    // "Select All" functionality
    selectAllCheckbox.addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.selectItem');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        toggleDeleteAllButton();
    });

    // Handle individual checkbox change
    fanpageList.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('selectItem')) {
            const allCheckboxes = document.querySelectorAll('.selectItem');
            const checkedCheckboxes = document.querySelectorAll('.selectItem:checked');

            // Update "Select All" state
            selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;

            toggleDeleteAllButton();
        }
    });


    fanpageList.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('deleteBtn')) {
            const id = e.target.getAttribute('data-id');
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: 'Thao tác này không thể hoàn tác!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Có, xóa!',
                cancelButtonText: 'Không'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/facebook/fanpages/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire('Đã xóa!', data.message || 'Mục đã được xóa thành công.', 'success');
                            loadFanpages(currentPage, currentSearch);
                        })
                        .catch(error => {
                            console.error('Error deleting fanpage:', error);
                            Swal.fire('Lỗi!', 'Không thể xóa mục này.', 'error');
                        });
                }
            });
        }
    });



    const deleteFanpagesButton = document.getElementById('deleteFanpages');
    const deleteFanpagesForm = document.getElementById('deleteFanpagesForm');
    const fanpageTextArea = document.getElementById('fanpageTextArea');
    const hiddenFanpageContainer = document.getElementById('hiddenFanpageContainer');
    const deleteAllFanpagesButton = document.getElementById('deleteAllFanpages');

    if (deleteFanpagesButton) {
        deleteFanpagesButton.addEventListener('click', function (e) {
            e.preventDefault();

            // Xóa hidden inputs cũ (nếu có)
            hiddenFanpageContainer.innerHTML = '';

            // Thu thập checkbox đang check
            const selectedItems = document.querySelectorAll('.selectItem:checked');

            // Lấy sẵn các dòng đang có trong textarea (nếu user đã dán từ trước)
            let lines = fanpageTextArea.value
                .split('\n')
                .map(line => line.trim())
                .filter(line => line !== '');

            // Thêm ID từ checkbox vào `lines`
            selectedItems.forEach(item => {
                const fanpageId = item.value.trim();
                if (fanpageId && !lines.includes(fanpageId)) {
                    lines.push(fanpageId);
                }
            });

            // Cập nhật lại textarea
            fanpageTextArea.value = lines.join('\n');

            // Hiển thị modal
            $('#deleteFanpageModal').modal('show');
        });
    }

    // Khi submit form "Xóa Fanpage"
    if (deleteFanpagesForm) {
        deleteFanpagesForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Ngăn chặn hành vi mặc định của form

            // Xoá mọi input ẩn cũ
            hiddenFanpageContainer.innerHTML = '';

            // Tách các dòng trong textarea
            let lines = fanpageTextArea.value
                .split('\n')
                .map(line => line.trim())
                .filter(line => line !== '');

            // Tạo <input type="hidden" name="selected_fanpages[]"> cho mỗi ID
            lines.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_fanpages[]';
                input.value = id;
                hiddenFanpageContainer.appendChild(input);
            });

            $.post("/admin/facebook/fanpages/select-delete", $('#deleteFanpagesForm').serialize(), function (result) {
                // Giả sử backend trả về { message: 'Xóa thành công' } khi thành công
                Swal.fire(
                    'Đã Xóa!',
                    result.message,
                    'success'
                ).then(() => {
                    // Tải lại trang để cập nhật giao diện
                    location.reload();
                });
            }, 'json').fail(function (xhr, status, error) {
                Swal.fire(
                    'Lỗi!',
                    xhr.responseJSON ? xhr.responseJSON.message : 'Đã xảy ra lỗi khi xóa dữ liệu.',
                    'error'
                );
                console.error('Error deleting selected fanpages:', error);
            });
        });
    }

    if (deleteAllFanpagesButton) {
        deleteAllFanpagesButton.addEventListener('click', function (e) {
            e.preventDefault();

            // Hiển thị thông báo xác nhận
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: 'Thao tác này sẽ xóa toàn bộ các Fanpage và dữ liệu liên quan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Có, xóa tất cả!',
                cancelButtonText: 'Không'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Gửi yêu cầu AJAX để xóa tất cả Fanpages
                    $.ajax({
                        url: '/admin/facebook/fanpages/delete-all',
                        type: 'POST',
                        data: {
                            _token: csrfToken
                        },
                        success: function (response) {
                            Swal.fire(
                                'Đã Xóa!',
                                response.message,
                                'success'
                            ).then(() => {
                                // Tải lại trang để cập nhật giao diện
                                location.reload();
                            });
                        },
                        error: function (xhr, status, error) {
                            Swal.fire(
                                'Lỗi!',
                                'Đã xảy ra lỗi khi xóa dữ liệu.',
                                'error'
                            );
                            console.error('Error deleting all fanpages:', error);
                        }
                    });
                }
            });
        });
    }

    // Tuỳ ý reset modal khi đóng
    $('#deleteFanpageModal').on('hidden.bs.modal', function () {
        // fanpageTextArea.value = '';
        // hiddenFanpageContainer.innerHTML = '';
    });


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

    $('#syncFanpageBtn').on('click', function () {
        showLoading();
        $.ajax({
            url: window.syncAllFanpageRoute,
            method: 'GET',
            success: function (response) {
                hideLoading();
                let countSuccess = 0;
                let countNotSynced = 0;
                if (response && response.data) {
                    $.each(response.data, function (uid, result) {
                        if (result.status === 'success') {
                            countSuccess++;
                        } else {
                            countNotSynced++;
                        }
                    });
                }
                swal.fire({
                    title: "Kết quả Đồng bộ Fanpage",
                    html: `Đồng bộ thành công: ${countSuccess} tài khoản<br>Không đồng bộ: ${countNotSynced} tài khoản`,
                    icon: "info"
                });
            },
            error: function (err) {
                hideLoading();
                swal.fire({
                    title: "Lỗi",
                    text: "Không thể đồng bộ Fanpage.",
                    icon: "error"
                });
            }
        });
    });
});