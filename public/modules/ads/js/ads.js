document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    const perPage = 10;
    let lastPage = null;
    let currentSearch = '';
    let loading = false;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const adsList = document.getElementById('adsList');
    const loadingIndicator = document.getElementById('loading');
    const currentPageSpan = document.getElementById('currentPage');
    const lastPageSpan = document.getElementById('lastPage');
    const searchInput = document.getElementById('searchInput');
    const tableResponsive = document.querySelector('.table-responsive');
    const selectAllCheckbox = document.getElementById('selectAll');
    const deleteAllButton = document.getElementById('deleteAllButton');


    const loadAds = (page = 1, search = '', append = false) => {
        if (loading) return;
        loading = true;
        loadingIndicator.style.display = 'block';

        fetch(`/admin/facebook/ads-manager/data?page=${page}&search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(data => {
                if (!append) {
                    adsList.innerHTML = ''; // Xóa dữ liệu hiện tại nếu không phải append
                }

                data.data.forEach(ad => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><input type="checkbox" class="selectItem" value="${ad._id.$oid}"></td>
                        <td>${ad.insights}</td>
                        <td>${ad.account_type}</td>
                        <td>${ad.total_spending}</td>
                        <td>${ad.act_id}</td>
                        <td>${ad.name}</td>
                        <td>${ad.currency}</td>
                        <td>${new Date(ad.created_time).toLocaleDateString()}</td>
                        <td>${new Date(ad.next_bill_date).toLocaleDateString()}</td>
                        <td>${ad.timezone}</td>
                        <td>${ad.timezone_name}</td>
                        <td>${ad.account_status}</td>
                        <td>${ad.admin_list.length}</td>
                        <td>${ad.admin_hidden}</td>
                        <td>${ad.user_roles}</td>
                        <td>
                            
                            <button class="btn btn-inverse-danger deleteBtn" data-id="${ad._id.$oid}">Xóa</button>
                        </td>
                    `;
                    adsList.appendChild(row);
                });

                // Cập nhật trạng thái phân trang
                currentPage = data.currentPage;
                lastPage = data.lastPage;
                currentPageSpan.innerText = data.currentPage;
                lastPageSpan.innerText = data.lastPage;

                // Cập nhật trạng thái nút
                document.getElementById('prevButton').disabled = currentPage <= 1;
                document.getElementById('nextButton').disabled = currentPage >= lastPage;

                $('#adsCountTitle').text(`Quản lý Ads : Số lượng (${data.data.length})`);
                // Đặt lại vị trí cuộn về đầu bảng
                tableResponsive.scrollTop = 0;

                loading = false;
                loadingIndicator.style.display = 'none';
                toggleDeleteAllButton();
            })
            .catch(error => {
                console.error('Error fetching ads:', error);
                loading = false;
                loadingIndicator.style.display = 'none';
            });
    };

    adsList.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('deleteBtn')) {
            const adId = e.target.getAttribute('data-id');

            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: 'Thao tác này sẽ xóa Ads và không thể hoàn tác!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Có, xóa!',
                cancelButtonText: 'Không'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/facebook/adsmanager/${adId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                Swal.fire('Thành công!', data.message, 'success');
                                // Remove the deleted row from the table
                                const row = e.target.closest('tr');
                                if (row) {
                                    row.remove();
                                }
                                toggleDeleteAllButton();
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting ad:', error);
                            Swal.fire('Lỗi!', 'Có lỗi xảy ra khi xóa Ads.', 'error');
                        });
                }
            });
        }
    });


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

    // Xử lý sự kiện nút Previous
    document.getElementById('prevButton').addEventListener('click', () => {
        if (currentPage > 1) {
            loadAds(currentPage - 1, currentSearch);
        }
    });

    // Xử lý sự kiện nút Next
    document.getElementById('nextButton').addEventListener('click', () => {
        if (currentPage < lastPage) {
            loadAds(currentPage + 1, currentSearch);
        }
    });

    // Xử lý sự kiện tìm kiếm trực tiếp (live search)
    let debounceTimeout = null;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            currentSearch = searchInput.value.trim();
            currentPage = 1;
            loadAds(currentPage, currentSearch);
        }, 300); // Delay 300ms để ngăn nhiều yêu cầu
    });

    // Tải trang đầu tiên khi tải xong
    loadAds(currentPage, currentSearch);


    deleteAllButton.addEventListener('click', function () {
        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: 'Thao tác này sẽ xóa toàn bộ Ads và không thể hoàn tác!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Có, xóa tất cả!',
            cancelButtonText: 'Không'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('/admin/facebook/adsmanager/all-delete', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ deleteAll: true })
                })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire('Đã xóa!', data.message || 'Toàn bộ Ads đã được xóa.', 'success');
                        loadAds();
                    })
                    .catch(error => {
                        console.error('Error deleting all ads:', error);
                        Swal.fire('Lỗi!', 'Không thể xóa toàn bộ Ads.', 'error');
                    });
            }
        });
    });


    const deleteSelectedButton = document.getElementById('deleteSelectedButton');

    if (deleteSelectedButton) {
        deleteSelectedButton.addEventListener('click', function (e) {
            e.preventDefault();

            // Collect selected ads
            const selectedItems = document.querySelectorAll('.selectItem:checked');
            const selectedIds = Array.from(selectedItems).map(item => item.value);

            if (selectedIds.length === 0) {
                Swal.fire('Lỗi!', 'Vui lòng chọn ít nhất một Ads để xóa.', 'error');
                return;
            }

            // Add selected IDs to the textarea
            adsTextArea.value = selectedIds.join('\n');

            // Show delete modal
            $('#deleteAdsModal').modal('show');
        });
    }

    // Xóa Ads theo input
    deleteAdsForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const ids = adsTextArea.value
            .split('\n')
            .map(id => id.trim())
            .filter(id => id !== '');

        if (ids.length === 0) {
            Swal.fire('Lỗi!', 'Vui lòng nhập ít nhất một ID để xóa.', 'error');
            return;
        }

        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: 'Thao tác này sẽ xóa các Ads đã nhập và không thể hoàn tác!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Có, xóa!',
            cancelButtonText: 'Không'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('/admin/facebook/adsmanager/all-delete', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ ids })
                })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire('Đã xóa!', data.message || 'Các Ads đã được xóa.', 'success');
                        loadAds();
                    })
                    .catch(error => {
                        console.error('Error deleting selected ads:', error);
                        Swal.fire('Lỗi!', 'Không thể xóa các Ads.', 'error');
                    });
            }
        });
    });
});