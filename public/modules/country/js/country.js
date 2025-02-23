document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    const perPage = 300;
    let lastPage = null;
    let currentSearch = '';
    let loading = false;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const countryList = document.getElementById('countryList');
    const loadingIndicator = document.getElementById('loading');
    const searchInput = document.getElementById('searchInput');
    const tableResponsive = document.querySelector('.table-responsive');
    const deleteAllButton = document.getElementById('deleteAllButton');
    const selectAllCheckbox = document.getElementById('selectAll');
    const paginationContainer = document.getElementById('pagination');

    const loadCountries = (page = 1, search = '', append = false) => {
        if (loading) return;
        loading = true;
        loadingIndicator.style.display = 'block';

        fetch(`/admin/facebook/countries/data?page=${page}&search=${encodeURIComponent(search)}`, {
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
            .then(response => response.json())
            .then(data => {
                if (!append) {
                    countryList.innerHTML = ''; // Xóa dữ liệu hiện tại nếu không phải append
                }

                data.data.forEach(country => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><input type="checkbox" class="selectItem" value="${country._id.$oid}"></td>
                        <td>${country.id_country}</td>
                        <td>${country.City}</td>
                        <td>${country.location_country}</td>
                        <td>${country.language}</td>
                        <td>
                            <button class="btn btn-inverse-danger btn-fw deleteBtn" data-id="${country._id.$oid}">
                                <i class="mdi mdi-delete"></i> Xóa
                            </button>
                        </td>
                    `;
                    countryList.appendChild(row);
                });

                // Cập nhật trạng thái phân trang
                currentPage = data.currentPage;
                lastPage = data.lastPage;
                currentSearch = search;

                renderPagination(paginationContainer, currentPage, lastPage, (newPage) => {
                    loadCountries(newPage, currentSearch);
                });

                document.getElementById('countryCountTitle').innerText = `Quản lý Country/Location : Số lượng (${data.data.length})`;

                // Đặt lại vị trí cuộn về đầu bảng
                tableResponsive.scrollTop = 0;

                loading = false;
                loadingIndicator.style.display = 'none';
            })
            .catch(error => {
                console.error('Error fetching countries:', error);
                loading = false;
                loadingIndicator.style.display = 'none';
            });
    };

    loadCountries(currentPage, currentSearch);



    // Xử lý sự kiện tìm kiếm trực tiếp (live search)
    let debounceTimeout = null;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            currentSearch = searchInput.value.trim();
            currentPage = 1;
            loadCountries(currentPage, currentSearch);
        }, 300); // Delay 300ms để ngăn nhiều yêu cầu
    });

    // Tải trang đầu tiên khi tải xong
    loadCountries(currentPage, currentSearch);


    selectAllCheckbox.addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.selectItem');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        toggleDeleteAllButton();
    });

    // Xử lý sự kiện click trên bảng
    countryList.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('selectItem')) {
            const allCheckboxes = document.querySelectorAll('.selectItem');
            const checkedCheckboxes = document.querySelectorAll('.selectItem:checked');

            // Update "Select All" state
            selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;

            toggleDeleteAllButton();
        }
    });

    // Toggle visibility of "Delete All" button
    function toggleDeleteAllButton() {
        const selectedItems = document.querySelectorAll('.selectItem:checked');
        deleteAllButton.style.display = selectedItems.length > 0 ? 'inline-block' : 'none';
    }


    deleteAllButton.addEventListener('click', function () {
        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: 'Thao tác này sẽ xóa toàn bộ dữ liệu và không thể hoàn tác!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Có, xóa tất cả!',
            cancelButtonText: 'Không'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/admin/facebook/country/all-delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ deleteAll: true })
                })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire('Đã xóa!', data.message || 'Tất cả dữ liệu đã được xóa thành công.', 'success');
                        loadCountries(1, ''); // Tải lại dữ liệu
                    })
                    .catch(error => {
                        console.error('Error deleting entries:', error);
                        Swal.fire('Lỗi!', 'Không thể xóa dữ liệu.', 'error');
                    });
            }
        });
    });

    countryList.addEventListener('click', function (e) {
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
                    fetch(`/admin/facebook/country/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire('Đã xóa!', data.message || 'Mục đã được xóa thành công.', 'success');
                            loadCountries(currentPage, currentSearch);
                        })
                        .catch(error => {
                            console.error('Error deleting country entry:', error);
                            Swal.fire('Lỗi!', 'Không thể xóa mục này.', 'error');
                        });
                }
            });
        }
    });

});