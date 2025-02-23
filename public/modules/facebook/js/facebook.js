// import { ToastsMessage } from './toast.js';
document.addEventListener('DOMContentLoaded', function () {

    let currentPage = 1; // Trang chính
    const perPage = 1000; // Số bản ghi trên mỗi trang chính
    const facebookAccountList = document.getElementById('facebookAccountList');
    const loading = document.getElementById('loading');
    const endMessage = document.getElementById('endMessage');
    const nextPageButton = document.getElementById('nextPage');
    const prevPageButton = document.getElementById('prevPage');
    let isLoading = false; // Ngăn việc tải dữ liệu lặp lại

    async function loadPage(page) {
        if (isLoading) return;
        isLoading = true;
        loading.style.display = 'block';
        endMessage.style.display = 'none';

        try {
            const response = await fetch(`${window.routes.loadMore}?page=${page}`);
            if (!response.ok) throw new Error('Failed to load data.');

            const data = await response.json();
            facebookAccountList.innerHTML = ''; // Xóa dữ liệu cũ trước khi tải trang mới

            data.data.forEach((item, index) => {
                const globalIndex = (page - 1) * perPage + index + 1; // Tính STT dựa trên trang và chỉ số
                const row = `
                    <tr>
                    <td><input type="checkbox" class="checkItem" value="${item.uid}"></td>
                        <td>${globalIndex}</td>
                        <td><a href="https://facebook.com/${item.uid}" target="_blank">${item.uid}</a></td>
                        <td>${item.fullname ?? 'N/A'}</td>
                        <td>${item.birthday ?? 'N/A'}</td>
                        <td>${item.friends?.count ?? 0}</td>
                        <td>${item.status ?? 'N/A'}</td>
                        <td>${item.post_data?.count ?? 0}</td>
                        <td>${item.created_time ?? 'N/A'}</td>
                        <td>${item.last_seeding ?? 'N/A'}</td>
                        <td>${item.groups_account ?? 'N/A'}</td>
                    <td>

                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-apps"></i> Action
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#">Mở khóa (Android App)</a>
                                    <a class="dropdown-item" href="#">Mở khóa (Android Browser)</a>
                                    <a class="dropdown-item" href="#">Login app Android</a>
                                    <a class="dropdown-item" href="#">Login Webrowser Adroid</a>
                                    <a class="dropdown-item" href="#">Login Webrowser PC</a>
                                    <hr>
                                    <a class="dropdown-item" href="#">Up Web Bán</a>
                                    <a class="dropdown-item" href="#">Up Spam Sell Clone</a>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <a target="_blank" href="/facebook-show-json/${item._id.$oid}" class="btn btn-light btn-sm btn-edit mb-2" style="padding: 3px; width: 82%;">Xem Json</a>
                            </div>
                            <div class="d-flex justify-content-between edit-delete">
                                <a href="/facebook-edit/${item._id.$oid}" class="btn btn-info btn-sm btn-edit" style="padding: 3px; flex: 1; margin-right: 5px;">Edit</a>
                               <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="${item._id.$oid}" data-name="{{ $item->fullname ?? '' }}" style="padding: 3px;">Delete</button>
                            </div>
                        </td>
                    </tr>`;
                facebookAccountList.insertAdjacentHTML('beforeend', row);
            });

            // Cuộn table lên đầu
            const tableContainer = document.getElementById('tableContainer');
            tableContainer.scrollTop = 0;

            currentPage = page;
            updateButtons(data.lastPage);
        } catch (error) {
            console.error(error.message);
        } finally {
            isLoading = false;
            loading.style.display = 'none';
        }
    }

    function updateButtons(lastPage) {
        const pageNumbersContainer = document.getElementById('pageNumbers');
        pageNumbersContainer.innerHTML = '';
    
        // Helper function to create a page button
        const createPageButton = (pageNumber, isActive = false) => {
            const button = document.createElement('button');
            button.textContent = pageNumber;
            button.className = `btn ${isActive ? 'btn-primary' : 'btn-secondary'}`;
            button.style.margin = '0 2px';
            button.disabled = isActive;
            button.addEventListener('click', () => {
                if (!isActive) {
                    loadPage(pageNumber);
                }
            });
            return button;
        };
    
        // Add the page numbers dynamically
        const maxDisplayedPages = 5; // Maximum visible page buttons
        const ellipsis = document.createElement('span');
        ellipsis.textContent = '...';
        ellipsis.style.margin = '0 5px';
    
        if (lastPage <= maxDisplayedPages) {
            // Show all pages if within the max range
            for (let i = 1; i <= lastPage; i++) {
                pageNumbersContainer.appendChild(createPageButton(i, i === currentPage));
            }
        } else {
            // Add the first page
            pageNumbersContainer.appendChild(createPageButton(1, currentPage === 1));
    
            if (currentPage > 3) {
                // Add left ellipsis if needed
                pageNumbersContainer.appendChild(ellipsis.cloneNode(true));
            }
    
            // Display middle pages around the current page
            const startPage = Math.max(2, currentPage - 1);
            const endPage = Math.min(lastPage - 1, currentPage + 1);
    
            for (let i = startPage; i <= endPage; i++) {
                pageNumbersContainer.appendChild(createPageButton(i, i === currentPage));
            }
    
            if (currentPage < lastPage - 2) {
                // Add right ellipsis if needed
                pageNumbersContainer.appendChild(ellipsis.cloneNode(true));
            }
    
            // Add the last page
            pageNumbersContainer.appendChild(createPageButton(lastPage, currentPage === lastPage));
        }
    
        // Update next/prev button states
        prevPageButton.disabled = currentPage === 1;
        nextPageButton.disabled = currentPage >= lastPage;
    }
    
    // Event listeners for next and previous buttons
    nextPageButton.addEventListener('click', () => {
        if (!nextPageButton.disabled) {
            loadPage(currentPage + 1);
        }
    });
    
    prevPageButton.addEventListener('click', () => {
        if (!prevPageButton.disabled) {
            loadPage(currentPage - 1);
        }
    });
    
    // Load the first page when the document is ready
    loadPage(1);
    



    const searchInput = document.getElementById('searchInput');
    const accountListContainer = document.getElementById('facebookAccountList');
    const loadingIndicator = document.getElementById('loading');
    // let currentPage = 1;
    let lastPage = 1;
    let debounceTimeout = null;
    // Hàm gọi API tìm kiếm
    const fetchSearchResults = (query, page = 1) => {
        if (!query) {
            // Nếu không có query, có thể tải lại trang đầu hoặc hiển thị thông báo
            loadPage(1);
            return;
        }

        // Hiển thị loading
        loadingIndicator.style.display = 'block';
        $.ajax({
            url: '/facebooks/search', // Endpoint của bạn
            method: 'GET',
            data: {
                search: query,
                per_page: 50, // Số lượng item mỗi trang
                page: page,
            },
            success: (response) => {
                // Cập nhật danh sách tài khoản
                const accounts = response.data;
                let html = '';
                const globalIndexOffset = (page - 1) * 1000; // Offset STT dựa trên trang

                accounts.forEach((account, index) => {
                    const globalIndex = globalIndexOffset + index + 1; // Tính STT
					const accountId = account._id && typeof account._id === 'object' && account._id.$oid
					? account._id.$oid
					: account._id;
                    html += `
                        <tr>
                         <td><input type="checkbox" name="select[]" value="${account._id.$oid}"></td>
                            <td>${globalIndex}</td>
                            <td><a href="https://facebook.com/${account.uid}" target="_blank">${account.uid}</a></td>
                            <td>${account.fullname || 'N/A'}</td>
                            <td>${account.birthday || 'N/A'}</td>
                            <td>${account.friends?.count || account.friends || 0}</td>
                            <td>
                                ${account.status === 'LIVE'
                            ? '<button class="btn btn-success btn-fw">LIVE</button>'
                            : account.status === 'Checkpoint'
                                ? '<button class="btn btn-danger btn-fw">Checkpoint</button>'
                                : account.status || 'N/A'
                        }
                            </td>
                            <td>${account.post_data?.count || account.post_data || 0}</td>
                            <td>${account.created_time || 'N/A'}</td>
                            <td>${account.last_seeding || 'N/A'}</td>
                            <td>${account.groups_account || 'N/A'}</td>
                           
                        <td>
                            <div class="dropdown mb-2">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-apps"></i> Action
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#">Mở khóa (Android App)</a>
                                    <a class="dropdown-item" href="#">Mở khóa (Android Browser)</a>
                                    <a class="dropdown-item" href="#">Login app Android</a>
                                    <a class="dropdown-item" href="#">Login Webrowser Adroid</a>
                                    <a class="dropdown-item" href="#">Login Webrowser PC</a>
                                    <hr>
                                    <a class="dropdown-item" href="#">Đổi Serve</a>
                                    <a class="dropdown-item" href="#">Up Web Bán</a>
                                    <a class="dropdown-item" href="#">Up Spam Sell Clone</a>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="/facebook-show-json/${accountId}" class="btn btn-light btn-sm btn-edit mb-2" style="padding: 3px; width: 82%;">Xem Json</a>
                            </div>
                            <div class="d-flex justify-content-between edit-delete">
                                <a href="/facebook-edit/${accountId}" class="btn btn-info btn-sm btn-edit" style="padding: 3px; flex: 1; margin-right: 5px;">Edit</a>
                               <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="${accountId}" data-name="{{ $item->fullname ?? '' }}" style="padding: 3px;">Delete</button>
                            </div>
                        </td>
                        </tr>`;
                });

                accountListContainer.innerHTML = html;

                // Cập nhật trạng thái phân trang
                currentPage = response.currentPage;
                lastPage = response.lastPage;

                updateButtons(lastPage);

                // Ẩn loading
                loadingIndicator.style.display = 'none';
            },
            error: (error) => {
                console.error('Error fetching accounts:', error);
                loadingIndicator.style.display = 'none';
            },
        });
    };
    // Xử lý debounce
    const handleSearchInput = (event) => {
        const query = event.target.value.trim();

        // Clear debounce timeout
        clearTimeout(debounceTimeout);

        // Đặt lại debounce timeout
        debounceTimeout = setTimeout(() => {
            fetchSearchResults(query, 1); // Luôn tìm kiếm từ trang 1
        }, 300); // Delay 300ms
    };
    // Gắn sự kiện vào ô input
    searchInput.addEventListener('input', handleSearchInput);

    $('.delete-form').on('submit', function (e) {
        e.preventDefault(); // Prevent default form submission
        var form = this;
        var name = $(this).data('name') || 'tài khoản';

        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: `Bạn sẽ không thể hoàn nguyên việc xóa "${name}"!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đúng, xóa nó!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // If confirmed, submit form
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                const form = this;
                const name = form.getAttribute('data-name');
                Swal.fire({
                    title: 'Bạn có chắc chắn?',
                    text: `Bạn sắp xóa ${name}. Hành động này không thể hoàn tác.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Vâng, xóa nó!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });

    $('.BtnCheckLiveFacebookID').on('click', function () {
        var _that = $(this);
        var originalContent = _that.html();
        var totalRequests = 0;
        var completedRequests = 0;

        _that.html('<i class="fas fa-spinner fa-spin"></i> Đang kiểm tra...');

        var selectedItems = $('.checkItem:checked'); // Lấy tất cả checkbox được chọn
        if (selectedItems.length > 0) {
            totalRequests = selectedItems.length;
            var uids = [];

            selectedItems.each(function () {
                var uid = $(this).val();
                uids.push(uid);
            });

            function processSelectedUids(uids, index) {
                if (index < uids.length) {
                    var uid = uids[index];
                    var row = $('.checkItem[value="' + uid + '"]').closest('tr');
                    row.addClass('loading highlight');

                    $.get(`/CheckLiveUid/${uid}`, function (result) {
                        if (result.response && result.response.status) {
                            let alertClass = result.response.message === 'LIVE' ? 'toast--success'
                                : result.response.message === 'CHECKPOINT' ? 'toast--error'
                                    : 'toast--warning';
                            ToastsMessage({
                                title: "Thông báo",
                                message: `UID ${uid}: ${result.response.message}`,
                                alertClass
                            });
                        } else {
                            ToastsMessage({
                                title: "Lỗi",
                                message: `UID ${uid}: Không thể kiểm tra.`,
                                alertClass: 'toast--warning'
                            });
                        }
                        row.removeClass('loading highlight');
                    }, 'json').fail(function () {
                        ToastsMessage({
                            title: "Lỗi",
                            message: `UID ${uid}: Lỗi kiểm tra.`,
                            alertClass: 'toast--error'
                        });
                        row.removeClass('loading highlight');
                    }).always(function () {
                        completedRequests++;
                        if (completedRequests === totalRequests) {
                            _that.html(originalContent); // Reset lại nút
                        }
                        setTimeout(function () {
                            processSelectedUids(uids, index + 1);
                        }, 100); // Adjust the delay as necessary
                    });
                }
            }
            processSelectedUids(uids, 0);
        } else {
            // Nếu không chọn UID nào, tự động tải tất cả UID
            $.get('/LoadAllFacebook', function (result) {
                if (result.response.status) {
                    if (result.response.uids) {
                        // Nếu số account dưới 1000, sẽ trả về danh sách UID xử lý như bình thường
                        var uids = result.response.uids;
                        totalRequests = uids.length;
    
                        function processUidWithDelay(uids, index) {
                            if (index < uids.length) {
                                var uid = uids[index];
                                $.get(`/CheckLiveUid/${uid}`, function (result) {
                                    if (result.response && result.response.status) {
                                        let alertClass = result.response.message === 'LIVE' ? 'toast--success'
                                            : result.response.message === 'CHECKPOINT' ? 'toast--error'
                                                : 'toast--warning';
                                        ToastsMessage({
                                            title: "Thông báo",
                                            message: `UID ${uid}: ${result.response.message}`,
                                            alertClass
                                        });
                                    } else {
                                        ToastsMessage({
                                            title: "Lỗi",
                                            message: `UID ${uid}: Không thể kiểm tra.`,
                                            alertClass: 'toast--error'
                                        });
                                    }
                                    setTimeout(() => processUidWithDelay(uids, index + 1), 100);
                                }, 'json').fail(function () {
                                    ToastsMessage({
                                        title: "Lỗi",
                                        message: `UID ${uid}: Lỗi kiểm tra.`,
                                        alertClass: 'toast--error'
                                    });
                                    setTimeout(() => processUidWithDelay(uids, index + 1), 100);
                                }).always(function () {
                                    completedRequests++;
                                    if (completedRequests === totalRequests) {
                                        _that.html(originalContent);
                                    }
                                });
                            }
                        }
                        processUidWithDelay(uids, 0);
                    } else {
                        // Nếu số account quá nhiều (> 1000), server trả về thông báo chuyển qua check ngầm
                        Swal.fire({
                            title: "Thông báo",
                            text: result.response.message,
                            icon: "info"
                        });
                        _that.html(originalContent);
                    }
                } else {
                    ToastsMessage({
                        title: "Thông báo",
                        message: result.response.message || 'Không tìm thấy UID nào.',
                        alertClass: 'toast--warning'
                    });
                    _that.html(originalContent);
                }
            }, 'json').fail(function () {
                ToastsMessage({
                    title: "Lỗi",
                    message: 'Đã xảy ra lỗi khi tải dữ liệu.',
                    alertClass: 'toast--error'
                });
                _that.html(originalContent);
            });
        }
    });

    $(document).on('change', '#selectAll', function () {
        $('.checkItem').prop('checked', $(this).is(':checked'));
    });

    $(document).on('change', '.checkItem', function () {
        // Khi một checkbox con được thay đổi
        var allChecked = $('.checkItem').length === $('.checkItem:checked').length;
        $('#selectAll').prop('checked', allChecked);
    });




    const deleteAllData = document.getElementById('deleteAllData');
    if (deleteAllData) {
        deleteAllData.addEventListener('click', function (e) {
            e.preventDefault();
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
                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch(window.routes.deleteAllAccounts, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({})
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                Swal.fire(
                                    'Đã xóa!',
                                    data.message,
                                    'success'
                                ).then(() => {
                                    // Reload the page or update the table
                                    location.reload();
                                });
                            } else if (data.error) {
                                Swal.fire(
                                    'Lỗi!',
                                    data.error,
                                    'error'
                                );
                            }
                        })
                        .catch((error) => {
                            Swal.fire(
                                'Lỗi!',
                                'Có lỗi xảy ra khi xóa dữ liệu.',
                                'error'
                            );
                            console.error('Error:', error);
                        });
                }
            });
        });
    }



    $(document).on('click', '.btn-delete', function () {
        var id = $(this).data('id');
        var name = $(this).data('name') || 'tài khoản';

        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: `Bạn sẽ không thể hoàn nguyên việc xóa `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Đúng, xóa nó!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/facebook-delete/${id}`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        Swal.fire(
                            'Đã xóa!',
                            'Tài khoản đã được xóa.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    },
                    error: function () {
                        Swal.fire(
                            'Lỗi!',
                            'Đã xảy ra lỗi khi xóa tài khoản.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
