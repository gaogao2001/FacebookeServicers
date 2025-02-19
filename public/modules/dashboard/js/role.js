$(document).ready(function () {

    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Thiết lập CSRF token cho các yêu cầu AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const menuMappingElement = document.getElementById('menu-mapping');
    const menuMapping = JSON.parse(menuMappingElement.getAttribute('data-mapping'));

    function normalizeUrl(url) {
        if (!url || typeof url !== 'string') {
            console.error('Invalid URL provided:', url);
            return '/'; // Trả về '/' nếu URL không hợp lệ
        }
        return '/' + url.trim().replace(/^\/+|\/+$/g, '').toLowerCase();
    }

    // Tạo ánh xạ từ URL đến tên menu với URL chuẩn hóa
    function buildUrlToMenuNameMapping(menuMapping) {
        const urlToMenuName = {};
    
        function processMenu(menu) {
            Object.keys(menu).forEach(menuName => {
                const menuItem = menu[menuName];
    
                if (menuItem.url) {
                    // Chuẩn hóa URL và ánh xạ với tên menu
                    const normalizedUrl = normalizeUrl(menuItem.url);
                    urlToMenuName[normalizedUrl] = menuName;
                }
    
                // Xử lý children nếu có
                if (menuItem.children) {
                    processMenu(menuItem.children);
                }
            });
        }
    
        processMenu(menuMapping);
        return urlToMenuName;
    }
    
    // Tạo ánh xạ từ URL đến tên menu
    const urlToMenuName = buildUrlToMenuNameMapping(menuMapping);
    console.log('urlToMenuName:', urlToMenuName);

    function showAlert(type, message) {
        if (!message || typeof message !== 'string') {
            console.error('Invalid alert message:', message);
            return;
        }
        Swal.fire({
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000
        });
    }

    // Hàm tải danh sách các role
    function loadRoles() {
        $.ajax({
            url: '/roles',
            method: 'GET',
            success: function (roles) {
                console.log('Roles received from server:', roles);
                const roleList = $('#roleList');
                roleList.empty();

                roles.forEach(role => {
                    console.log('Processing role:', role.name);
                    console.log('role.menu:', role.menu);

                    const menuNames = role.menu.map(route => {
                        const normalizedRoute = normalizeUrl(route);
                        const menuName = urlToMenuName[normalizedRoute];
                        if (!menuName) {
                            console.warn(`No menu name found for route "${normalizedRoute}"`);
                        }
                        return menuName || 'Không xác định'; // Hiển thị 'Không xác định' nếu không tìm thấy tên menu
                    });

                    // Hiển thị role trong bảng
                    roleList.append(`
                        <tr>
                            <td>${role.name}</td>
                            <td>${role.description}</td>
                            <td>
                                ${menuNames.map((name, index) => {
                        return `<label class="btn btn-success btn-fw">${name}</label>${(index + 1) % 3 === 0 ? '<br>' : ' '}`;
                    }).join('')}
                            </td>
                            <td>
                                <button class="btn btn-inverse-light edit-role" data-id="${role._id.$oid}"><i class="fa fa-edit"></i> Chỉnh sửa</button>
                                <button class="btn btn-inverse-danger delete-role" data-id="${role._id.$oid}"><i class="fa fa-trash"></i> Xóa</button>
                            </td>
                        </tr>
                    `);
                });

                // Thêm sự kiện click cho nút chỉnh sửa
                $('.edit-role').on('click', function () {
                    const roleId = $(this).data('id');
                    const role = roles.find(r => r._id.$oid === roleId);
                    if (role) {
                        $('#role_name').val(role.name);
                        $('#role_description').val(role.description);
                        $('input[name="menu[]"]').each(function () {
                            $(this).prop('checked', role.menu.includes(normalizeUrl($(this).val())));
                        });
                        $('#saveRoleBtn').data('id', roleId);
                    }
                });

                // Thêm sự kiện click cho nút xóa
                $('.delete-role').on('click', function () {
                    const roleId = $(this).data('id');
                    Swal.fire({
                        title: 'Bạn có chắc chắn muốn xóa quyền này?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Có, xóa nó!',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/roles/${roleId}`,
                                method: 'DELETE',
                                success: function () {
                                    showAlert('success', 'Xóa quyền thành công!');
                                    loadRoles();
                                },
                                error: function (xhr) {
                                    console.error(xhr.responseText);
                                    showAlert('error', 'Đã xảy ra lỗi khi xóa quyền');
                                }
                            });
                        }
                    });
                });
            },
            error: function (xhr) {
                console.error('Error while loading roles:', xhr.responseText);
                showAlert('error', 'Lỗi khi tải danh sách quyền');
            }
        });
    }

    // Gọi hàm loadRoles khi trang được tải
    loadRoles();

    // Xử lý sự kiện lưu role
    $('#saveRoleBtn').on('click', function () {
        const roleId = $(this).data('id');
        const formData = {
            name: $('#role_name').val(),
            description: $('#role_description').val(),
            menu: $('input[name="menu[]"]:checked').map(function () {
                return normalizeUrl(this.value);
            }).get()
        };

        console.log('FormData:', formData);

        const method = roleId ? 'PUT' : 'POST';
        const url = roleId ? `/roles/${roleId}` : '/roles';

        $.ajax({
            url: url,
            method: method,
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function (response) {
                showAlert('success', roleId ? 'Cập nhật quyền thành công!' : 'Thêm mới quyền thành công!');
                $('#saveRoleBtn').removeData('id');
                loadRoles();
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                const errors = xhr.responseJSON?.errors || {};
                showAlert('error', 'Đã xảy ra lỗi: ' + JSON.stringify(errors));
            }
        });
    });
});