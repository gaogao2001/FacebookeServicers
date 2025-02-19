document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    const perPage = 100; // Số lượng mục trên mỗi trang
    let lastPage = 1;
    let isLoading = false;

    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Set up CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    const historyList = document.getElementById('historyList');
    const selectAllCheckbox = document.getElementById('selectAll');
    const deleteAllButton = document.getElementById('deleteAllButton');
    const loadingIndicator = document.getElementById('loading');
    const paginationContainer = document.getElementById('pagination');



    function loadHistory(page = 1) {
        if (isLoading) return;
        isLoading = true;
        loadingIndicator.style.display = 'block';

        $.ajax({
            url: '/history',
            method: 'GET',
            dataType: 'json',
            data: { page: page },
            success: function (data) {
                historyList.innerHTML = '';
                data.data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><input type="checkbox" class="selectHistory" data-id="${item._id.$oid}"></td>
                        <td>${item.uid}</td>
                        <td>${item.facebook_id}</td>
                        <td>${item.action}</td>
                        <td>${item.status}</td>
                        <td class="truncate-message">${item.message ? item.message : 'null'}</td>
                        <td>${item.time}</td>
                        <td>
                            <button type="button" class="btn btn-inverse-danger btn-fw deleteBtn" data-id="${item._id.$oid}"><i class="fa fa-trash"></i> Xóa</button>
                        </td>
                    `;
                    historyList.appendChild(row);
                });

                currentPage = data.currentPage;
                lastPage = data.lastPage;

                // Cập nhật các nút phân trang
                renderPagination(paginationContainer, currentPage, lastPage, loadHistory);

                // Cập nhật trạng thái nút "Delete All"
                toggleDeleteAllButton();

                loadingIndicator.style.display = 'none';
                isLoading = false;
            },
            error: function (error) {
                console.error('Error loading history:', error);
                loadingIndicator.style.display = 'none';
                isLoading = false;
            }
        });
    }



    // Load history on page load
    loadHistory(currentPage);

    // Delete single entry
    historyList.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('deleteBtn')) {
            const id = e.target.getAttribute('data-id');
            Swal.fire({
                title: 'Bạn có chắc không?',
                text: 'Thao tác này không thể hoàn tác!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Có, xóa!',
                cancelButtonText: 'Không'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/history/${id}`,
                        method: 'DELETE',
                        success: function (response) {
                            Swal.fire('Đã xóa!', response.message || 'Entry deleted successfully.', 'success');
                            loadHistory(currentPage);
                        },
                        error: function (xhr) {
                            console.error('Error deleting history entry:', xhr);
                            Swal.fire('Lỗi!', 'Không thể xóa mục này.', 'error');
                        }
                    });
                }
            });
        }
    });

    // Handle "Select All" checkbox
    selectAllCheckbox.addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.selectHistory');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        toggleDeleteAllButton();
    });

    // Handle individual checkboxes
    historyList.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('selectHistory')) {
            const allCheckboxes = document.querySelectorAll('.selectHistory');
            const checkedCheckboxes = document.querySelectorAll('.selectHistory:checked');

            // Update "Select All" state
            selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;

            toggleDeleteAllButton();
        }
    });

    // Toggle visibility of "Delete All" button
    function toggleDeleteAllButton() {
        const selectedItems = document.querySelectorAll('.selectHistory:checked');
        deleteAllButton.style.display = selectedItems.length > 0 ? 'inline-block' : 'none';
    }

    // Delete selected entries
    deleteAllButton.addEventListener('click', function () {
        const selectedIds = Array.from(document.querySelectorAll('.selectHistory:checked'))
            .map(checkbox => checkbox.getAttribute('data-id'));

        if (selectedIds.length > 0) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: 'Thao tác này sẽ xóa tất cả các mục đã chọn và không thể hoàn tác!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Có, xóa!',
                cancelButtonText: 'Không'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/history/all-delete',
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ ids: selectedIds }),
                        success: function (response) {
                            Swal.fire('Đã xóa!', response.message || 'Các mục đã được xóa thành công.', 'success');
                            loadHistory(currentPage);
                        },
                        error: function (xhr) {
                            console.error('Error deleting entries:', xhr);
                            Swal.fire('Lỗi!', 'Không thể xóa các mục.', 'error');
                        }
                    });
                }
            });
        }
    });

    const deleteAllHistory = document.getElementById('deleteAllHistory');
    if (deleteAllHistory) {
        deleteAllHistory.addEventListener('click', function () {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Bạn sẽ không thể khôi phục lại dữ liệu đã xóa!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đúng, xóa tất cả!',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: window.routes.deleteAllFacebookHistory,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            if (data.message) {
                                Swal.fire(
                                    'Đã xóa!',
                                    data.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else if (data.error) {
                                Swal.fire(
                                    'Lỗi!',
                                    data.error,
                                    'error'
                                );
                            }
                        },
                        error: function () {
                            Swal.fire(
                                'Lỗi!',
                                'Có lỗi xảy ra khi xóa dữ liệu.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    }
});
