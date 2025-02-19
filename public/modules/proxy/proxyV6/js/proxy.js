/**
 * var reloadIpUrl
 */

$(document).ready(function () {

    var ToastsMessage = function ({ title = "", message = "", type = "info", alertClass = "toast--info", duration = 3000 }) {
        const icons = {
            success: "fas fa-check-circle",
            info: "fas fa-info-circle",
            warning: "fas fa-exclamation-circle",
            error: "fas fa-exclamation-circle"
        };
        const icon = icons[type];
        const delay = (duration / 1000).toFixed(2);

        var toast = document.createElement('div');
        toast.classList.add("toast", `toast--${type}`, "slideInLeft", "fadeOut");
        toast.style.animationDelay = `${delay}s`;

        var alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) {
            alertContainer = document.createElement('div');
            alertContainer.id = 'alertContainer';
            alertContainer.style.position = 'fixed';
            alertContainer.style.bottom = '10px';
            alertContainer.style.right = '10px';
            alertContainer.style.zIndex = '9999';
            alertContainer.style.maxHeight = 'calc(100vh - 20px)';
            alertContainer.style.overflowY = 'auto';
            document.body.appendChild(alertContainer);
        }

        var alert = document.createElement('div');
        alert.className = `toast ${alertClass} fade d-flex align-items-center`;
        alert.role = 'alert';
        alert.setAttribute('aria-live', 'assertive');
        alert.setAttribute('aria-atomic', 'true');
        alert.innerHTML = `
            <div class="toast__icon mr-2">
                <i class="${icon}"></i>
            </div>
            <div class="toast__body flex-grow-1">
                <h3 class="toast__title">${title}</h3>
                <p class="toast__msg mb-0">${message}</p>
            </div>
            <div class="toast__close ml-2">
                <i class="fas fa-times"></i>
            </div>
        `;

        alertContainer.appendChild(alert);

        // Limit to 4 toasts
        if (alertContainer.children.length > 3) {
            alertContainer.removeChild(alertContainer.firstChild);
        }

        $(alert).toast({ delay: duration });
        $(alert).toast('show');

        setTimeout(() => {
            $(alert).toast('dispose');
            alert.remove();
        }, duration + 500);
    };

    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Thiết lập CSRF token cho các yêu cầu AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    const proxyList = document.getElementById('proxyList');
    const loading = document.getElementById('loading');
    const searchInput = document.getElementById('searchInput');
    let currentPage = 1;
    let lastPage = 1;
    let searchQuery = '';
    let isLoading = false;
    let debounceTimeout = null;

    function loadProxies(reset = false) {
        if (isLoading) return;
        if (reset) {
            currentPage = 1;
            lastPage = 1;
            proxyList.innerHTML = '';
        }
        if (currentPage > lastPage && !reset) return;

        isLoading = true;
        loading.style.display = 'block';

        fetch(`/proxy-system/search?search=${encodeURIComponent(searchQuery)}&page=${currentPage}`)
            .then(response => response.json())
            .then(data => {
                data.data.forEach(proxy => {
                    const row = document.createElement('tr');
                    row.classList.add('row-proxy-v6');
                    row.setAttribute('data-status', proxy.port_status);
                    row.setAttribute('data-id', proxy.config_name);

                    const statusClass = proxy.status === 'pending' ? 'badge badge-warning' :
                        proxy.status === 'success' ? 'badge badge-success' : '';

                    const portStatusClass = proxy.port_status === 'pending' ? 'badge badge-warning' :
                        proxy.port_status === 'success' ? 'badge badge-success' : '';

                    const newReloadIpUrl = reloadIpUrl.replace('_id', proxy._id.$oid);
                    const newCheckIpUrl = checkIpUrl.replace('_id', proxy._id.$oid);
                    const newDeleteUrl = deleteUrl.replace('_id', proxy._id.$oid);

                    row.innerHTML = `
                        <td><input type="checkbox" class="selectProxy" data-id="${proxy._id.$oid}"></td>
                        <td>${proxy.config_name}</td>
                        <td>${proxy.eth}</td>
                        <td>${proxy.interface}</td>
                        <td>${proxy.port}</td>
                        <td>${proxy.last_time}</td>
                        <td><span class="${statusClass}">${proxy.status}</span></td>
                        <td class="col-port-status"><span class="${portStatusClass}">${proxy.port_status}</span></td>
                        <td>
                           <button type="button" class="btn btn-inverse-primary btn-fw btn-reload-ip" data-url="${newReloadIpUrl}">
                                <i class="fas fa-sync"></i>
                                <i class="fas fa-spinner fa-spin d-none"></i>
                                Reload IP 
                            </button>
                            <button type="button" class="btn btn-inverse-success btn-fw BtnCheckProxy" data-url="${newCheckIpUrl}">Kiểm tra</button>
                            <a href="${newDeleteUrl}" class="btn btn-inverse-danger">Xóa</a>
                        </td>
                    `;
                    proxyList.appendChild(row);
                });

                currentPage = data.currentPage + 1;
                lastPage = data.lastPage;
                loading.style.display = 'none';
                isLoading = false;
            })
            .catch(error => {
                console.error('Error loading proxies:', error);
                loading.style.display = 'none';
                isLoading = false;
            });
    }

    // Initial load
    loadProxies();

    // Search functionality with debounce
    searchInput.addEventListener('keyup', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            searchQuery = this.value.trim();
            loadProxies(true);
        }, 300); // 300ms debounce
    });

    // Infinite scrolling
    const tableContainer = document.getElementById('tableContainer');
    tableContainer.addEventListener('scroll', function () {
        if (tableContainer.scrollTop + tableContainer.clientHeight >= tableContainer.scrollHeight - 10) {
            loadProxies();
        }
    });

    $(document).on('click', '.BtnClearAllProxy', function (event) {
        event.preventDefault(); // Ngăn chặn hành động mặc định của sự kiện
        var _that = $(this);
        var url = _that.attr("data-url");

        // Thêm icon loading (có thể thay bằng icon bạn muốn)
        var loadingIcon = $('<i class="fas fa-spinner fa-spin loading-icon"></i>'); // Biểu tượng loading
        _that.append(loadingIcon); // Thêm icon vào nút

        // Gọi API
        $.get(url, function (result) {
            // Xóa icon loading khi hoàn tất
            _that.find('.loading-icon').remove();

            if (result.status == true) {
                // Làm gì đó khi thành công
                ToastsMessage({ 
                    title: '', 
                    message: result.msg, 
                    type: 'success', 
                    alertClass: 'toast--success' 
                });
                setTimeout(function () {
                    location.reload()
                }, 3000);
            } else {
                ToastsMessage({ 
                    title: '', 
                    message: result.msg || 'Có lỗi xảy ra.', 
                    type: 'error', 
                    alertClass: 'toast--error' 
                });
            }
        }, 'json').fail(function () {
            // Xử lý lỗi nếu yêu cầu thất bại và xóa icon loading
            _that.find('.loading-icon').remove();
            ToastsMessage({ 
                title: '', 
                message: 'Có lỗi xảy ra trong quá trình cập nhật.', 
                type: 'error', 
                alertClass: 'toast--error' 
            });
        });

        return false;
    });

    $(document).on('click', '.BtnCreateProxyV6', function () {
        let _that = $(this);
        let _form = _that.closest("form");
        let _data = _form.serializeArray(); // Chuyển dữ liệu thành mảng đối tượng
        let _action = _form.attr("action");
        let limit = parseInt(_form.find('input[name="limit"]').val()) || 0; // Lấy giá trị limit từ form
        let batchSize = 10; // Kích thước nhóm (số lượng yêu cầu gửi đồng thời)
        let totalBatches = Math.ceil(limit / batchSize); // Tổng số nhóm
        let currentBatch = 0; // Nhóm hiện tại
        let successCount = 0;
        let errorCount = 0;

        // Hiển thị biểu tượng loading
        _that.find('i').removeClass('d-none');

        function sendBatch() {
            if (currentBatch >= totalBatches) {
                // Khi tất cả nhóm đã hoàn tất
                if (errorCount === 0) {
                    setTimeout(() => location.reload(), 30); // Reload nếu không có lỗi
                } else {
                    ToastsMessage(
                        '',
                        `Có lỗi xảy ra trong ${errorCount} yêu cầu. Vui lòng kiểm tra lại!`,
                        'toast--error'
                    );
                    _that.find('i').addClass('d-none');
                }
                return;
            }

            // Tạo nhóm dữ liệu cho batch hiện tại
            let start = currentBatch * batchSize;
            let end = Math.min(start + batchSize, limit); // Không vượt quá giới hạn `limit`
            let requests = [];

            for (let i = start; i < end; i++) {
                let postData = _data.map(item => {
                    if (item.name === "limit") {
                        return { name: item.name, value: 1 }; // Gán limit = 1 cho từng yêu cầu
                    }
                    return item;
                });
                requests.push($.post(_action, $.param(postData), null, 'json'));
            }

            // Xử lý các yêu cầu trong batch hiện tại
            Promise.allSettled(requests)
                .then(results => {
                    results.forEach(result => {
                        if (result.status === "fulfilled" && result.value.status === true) {
                            successCount++;
                        } else {
                            errorCount++;
                        }
                    });

                    // Tiếp tục nhóm tiếp theo
                    currentBatch++;
                    sendBatch();
                })
                .catch(() => {
                    errorCount += requests.length;
                    currentBatch++;
                    sendBatch();
                });
        }

        // Bắt đầu gửi nhóm đầu tiên
        sendBatch();

        return false;
    });

    $(document).on('click', '.btn-reload-ip', function () {
        let url = $(this).attr('data-url');
        let $this = $(this);
        $.ajax({
            url: url,
            type: 'POST',
            beforeSend: function () {
                $this.find('i.fa-spinner').removeClass('d-none');
                $this.find('i.fa-sync').addClass('d-none');
            },
            success: function (response) {
                if (response.status == true) {
                    ToastsMessage(response.msg, response.msg, 'toast--success');
                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                } else {
                    ToastsMessage('', response.msg, 'toast--error');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            },
            complete: function () {
                $this.find('i.fa-spinner').addClass('d-none');
                $this.find('i.fa-sync').removeClass('d-none');
            }
        });
    });

    $(document).on('click', '.BtnCheckProxy', function () {
        _that = $(this);
        _action = _that.attr("data-url");
        var loadingIcon = $('<i class="fas fa-spinner fa-spin loading-icon"></i>'); // Biểu tượng loading
        _that.append(loadingIcon); // Thêm icon vào nút

        $.get(_action, function (result) {
            ToastsMessage({
                title: "IP: " + result.ip,
                message: result.msg,
                type: 'success'
            });
            loadingIcon.remove(); // Xóa biểu tượng loading
        }, 'json').fail(function () {
            ToastsMessage({
                title: "",
                message: 'Có lỗi xảy ra, vui lòng kiểm tra và thực hiện lại',
                type: 'error'
            });
            loadingIcon.remove(); // Xóa biểu tượng loading
        });

        return false;
    });
});
