document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    const perPage = 10;
    let lastPage = null;
    let currentSearch = '';
    let loading = false;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const adsList = document.getElementById('adsList');
    const loadingIndicator = document.getElementById('loading');
    const searchInput = document.getElementById('searchInput');
    const tableResponsive = document.querySelector('.table-responsive');
    const selectAllCheckbox = document.getElementById('selectAll');
    const deleteAllButton = document.getElementById('deleteAllButton');
    const paginationContainer = document.getElementById('pagination');

    const exportSelectedButton = document.getElementById('exportSelectedButton');

    function toggleSelectionButtons() {
        const selectedItems = document.querySelectorAll('.selectItem:checked');
        if (selectedItems.length > 0) {
            deleteAllButton.style.display = 'inline-block';
            $('#exportSelectedButton').show();
        } else {
            deleteAllButton.style.display = 'none';
            $('#exportSelectedButton').hide();
        }
    }

    document.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('selectItem')) {
            toggleSelectionButtons();
        }
    });



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
                           <td><input type="checkbox" class="selectItem" data-insights="${ad.insights}" value="${ad._id.$oid}"></td>
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

                // Cập nhật tiêu đề với số lượng
                $('#adsCountTitle').text(`Quản lý Ads : Số lượng (${data.data.length})`);

                // Sử dụng hàm renderPagination để hiển thị phân trang
                renderPagination(paginationContainer, currentPage, lastPage, function (page) {
                    loadAds(page, currentSearch);
                });

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

    // Thêm hàm loadFilteredAds để tải lại dữ liệu sau khi áp dụng bộ lọc
    window.loadFilteredAds = function () {
        currentPage = 1; // Reset về trang đầu tiên khi lọc
        currentSearch = searchInput.value || ''; // Giữ nguyên giá trị tìm kiếm hiện tại
        loadAds(currentPage, currentSearch);
    };

    // Khi có sự kiện chọn checkbox
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('selectItem')) {
            toggleDeleteAllButton();
        }
    });

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
        // Gọi hàm toggleSelectionButtons thay vì toggleDeleteAllButton
        toggleSelectionButtons();
    });

    if (exportSelectedButton) {
        exportSelectedButton.addEventListener('click', function (e) {
            e.preventDefault();
            // Lấy các checkbox được chọn
            const selectedItems = document.querySelectorAll('.selectItem:checked');
            if (selectedItems.length === 0) {
                Swal.fire('Lỗi!', 'Vui lòng chọn ít nhất một Ads để xuất account.', 'error');
            } else {
                // Lấy danh sách insights từ các checkbox (giá trị từ thuộc tính data-insights)
                const insightsList = Array.from(selectedItems).map(item => item.getAttribute('data-insights'));
                // Đưa danh sách vào textarea (các giá trị nối bằng dấu xuống dòng)
                document.getElementById('account_list').value = insightsList.join('\n');
                // Hiển thị modal xuất account
                $('#modal-lg').modal('show');
            }
        });
    }

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

    $('.sync-ads').click(function () {
        showLoading();
        $.ajax({
            url: window.syncAllAdsUrl,
            type: "GET",
            dataType: "json",
            success: function (data) {
                hideLoading();
                let results = data.data;
                let successList = [];
                let errorList = [];
                $.each(results, function (uid, result) {
                    if (result.status === 'success') {
                        successList.push(uid);
                    } else {
                        errorList.push(uid);
                    }
                });

                let msg = "";
                if (successList.length > 0) {
                    msg += "Đã đồng bộ ADS cho các tài khoản: " + successList.join(", ") + ".";
                }
                if (errorList.length > 0) {
                    msg += " Các tài khoản không đồng bộ được (lỗi bỏ qua): " + errorList.join(", ") + ".";
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Kết quả đồng bộ ADS',
                    text: msg,
                });
            },
            error: function (xhr, status, error) {
                hideLoading();
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Có lỗi xảy ra khi đồng bộ ADS.',
                });
                console.error('Error syncing ADS:', error);
            }
        });
    });


    function showLoading() {
        console.log("Hiển thị loading...");
        if ($('#loadingOverlay').length === 0) {
            $('body').append(`
                    <div id="loadingOverlay">
                        <div class="loader"></div>
                    </div>
                `);
        }
    }

    function hideLoading() {
        console.log("Ẩn loading...");
        $('#loadingOverlay').remove();
    }

    $(document).on('click', '.BtnExportAccount', function (e) {
        e.preventDefault();

        // Lấy giá trị từ các input trong modal
        var chosenStructure = $('#chosen_structure').val().trim();
        var accountList = $('#account_list').val().trim();

        if (!chosenStructure) {
            Swal.fire('Lỗi!', 'Bạn chưa chọn cấu trúc xuất file!', 'error');
            return;
        }

        if (!accountList) {
            Swal.fire('Lỗi!', 'Bạn chưa nhập danh sách tài khoản!', 'error');
            return;
        }

        // Tạo form ẩn và submit để tải file
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var form = $('<form></form>')
            .attr({
                method: 'POST',
                action: '/export_account'
            });

        // Thêm CSRF token
        form.append($('<input type="hidden" name="_token">').val(csrfToken));
        // Thêm các input cần gửi
        form.append($('<input type="hidden" name="chosen_structure">').val(chosenStructure));
        form.append($('<input type="hidden" name="account_list">').val(accountList));

        // Thêm form vào body và submit
        $('body').append(form);
        form.submit();
    })
});