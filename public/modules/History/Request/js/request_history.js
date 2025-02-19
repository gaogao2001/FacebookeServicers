document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    const perPage = 100;
    let lastPage = 1;
    let currentSearch = '';
    let isLoading = false;

    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    const requestList = document.getElementById('requestList');
    const selectAllCheckbox = document.getElementById('selectAll');
    const deleteAllButton = document.getElementById('deleteAllButton');
    const loading = document.getElementById('loading');
    const searchInput = document.getElementById('searchInput');
    const paginationContainer = document.getElementById('pagination');

    // Hàm tải dữ liệu yêu cầu
    function loadRequests(page = 1, search = '') {
        if (isLoading) return;
        isLoading = true;
        loading.style.display = 'block';

        $.ajax({
            url: '/request-history/search', // Đã cập nhật URL
            method: 'GET',
            dataType: 'json',
            data: {
                page: page,
                search: search
            },
            success: function (data) {
                requestList.innerHTML = '';
                data.data.forEach(item => {
                    const row = document.createElement('tr');

                    row.innerHTML = `
                        <td><input type="checkbox" class="selectHistory" data-id="${item._id.$oid}"></td>
                        <td>${item.uid}</td>
                        <td>${item.httpStatusCode}</td>
                        <td>${item.errorCode}</td>
                        <td class="truncate-message">${item.errorMessage}</td>
                        <td>${new Date(item.created_at).toLocaleString()}</td>
                        <td>
                            <button class="btn btn-inverse-danger btn-fw deleteBtn" data-id="${item._id.$oid}"><i class="fa fa-trash"></i> Xóa</button>
                            <button class="btn btn-inverse-light btn-fw showBtn" data-id="${item._id.$oid}"><i class="fa fa-info-circle"></i> Xem JSON</button>
                        </td>
                    `;
                    requestList.appendChild(row);
                });

                // Cập nhật phân trang
                currentPage = data.currentPage;
                lastPage = data.lastPage;
                currentSearch = search;

                renderPagination(paginationContainer, currentPage, lastPage, (newPage) => {
                    loadRequests(newPage, currentSearch);
                });
                document.getElementById('requestCountTitle').innerText = `Request History : Số lượng (${data.data.length})`;

                // Đặt lại vị trí cuộn về đầu bảng
                paginationContainer.scrollIntoView({ behavior: 'smooth' });

                isLoading = false;
                loadingIndicator.style.display = 'none';

                toggleDeleteAllButton();
            },
            error: function (error) {
                console.error('Lỗi khi tải dữ liệu:', error);
                loading.style.display = 'none';
                isLoading = false;
            }
        });
    }

    // Gọi hàm tải dữ liệu lần đầu
    loadRequests(currentPage, currentSearch);

    // Xử lý sự kiện tìm kiếm với debounce
    let debounceTimeout = null;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            currentSearch = searchInput.value.trim();
            currentPage = 1;
            loadRequests(currentPage, currentSearch);
        }, 100); // Delay 300ms để ngăn nhiều yêu cầu
    });


   


  

    // Xóa một mục đơn lẻ
    requestList.addEventListener('click', function (e) {
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
                        url: `/request-history/${id}`,
                        method: 'DELETE',
                        success: function () {
                            Swal.fire('Đã xóa!', 'Yêu cầu đã được xóa.', 'success');
                            loadRequests(currentPage, currentSearch);
                        },
                        error: function (error) {
                            Swal.fire('Lỗi!', 'Không thể xóa yêu cầu.', 'error');
                            console.error('Lỗi khi xóa:', error);
                        }
                    });
                }
            });
        }
    });

    // Redirect to route when clicking "Xem JSON"
    requestList.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('showBtn')) {
            const id = e.target.getAttribute('data-id');
            window.open(`/request-history/${id}`, '_blank');
        }
    });
    // Xóa tất cả các mục
    deleteAllButton.addEventListener('click', function () {
        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: 'Thao tác này sẽ xóa tất cả dữ liệu và không thể hoàn tác!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đúng, xóa tất cả!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/request-history/delete_all', // Đảm bảo URL này khớp với route
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (data) {
                        if (data.message) {
                            Swal.fire(
                                'Đã xóa!',
                                data.message,
                                'success'
                            ).then(() => {
                                loadRequests(1, ''); // Tải lại dữ liệu trang đầu tiên
                                selectAllCheckbox.checked = false;
                            });
                        } else if (data.error) {
                            Swal.fire(
                                'Lỗi!',
                                data.error,
                                'error'
                            );
                        }
                    },
                    error: function (error) {
                        Swal.fire(
                            'Lỗi!',
                            'Có lỗi xảy ra khi xóa dữ liệu.',
                            'error'
                        );
                        console.error('Error:', error);
                    }
                });
            }
        });
    });
});