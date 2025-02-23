$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // Function to display alerts using SweetAlert
    function showAlert(type, message) {
        Swal.fire({
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000
        });
    }

    function loadUsers() {
        $.ajax({
            url: '/users',
            method: 'GET',
            success: function (response) {
                $('#adminAcountList').empty();

                response.forEach(user => {
                    $('#adminAcountList').append(`
                        <tr>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>
                                <label class="btn btn-outline-success btn-fw">${user.role_name || user.role}</label>
                            </td>
                            <td>
                                <button class="btn btn-inverse-light edit-user" 
                                        data-id="${user._id.$oid}" 
                                        data-name="${user.name}" 
                                        data-email="${user.email}" 
                                        data-role="${user.role}">
                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                </button>
                                <button class="btn btn-inverse-danger delete-user" data-id="${user._id.$oid}">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    `);
                });

                // Edit event
                $('.edit-user').on('click', function () {
                    const userId = $(this).data('id');
                    const userName = $(this).data('name');
                    const userEmail = $(this).data('email');
                    const userRole = $(this).data('role');

                    // Fill user info into modal
                    $('#editUserModalLabel').text('Chỉnh sửa người dùng');
                    $('#editUsername').val(userName);
                    $('#editEmail').val(userEmail);
                    $('#editRole').val(userRole);
                    $('#editUserForm').attr('data-id', userId);
                    $('#editUserForm').attr('data-action', 'edit');

                    loadRoles();
                    $('#editUserModal').modal('show');
                });

                // Delete event
                $('.delete-user').on('click', function () {
                    const userId = $(this).data('id');

                    Swal.fire({
                        title: 'Bạn có chắc muốn xóa người dùng này không?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Có, xóa nó!',
                        cancelButtonText: 'Không, giữ lại'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/users/${userId}`,
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken 
                                },
                                success: function () {
                                    showAlert('success', 'Xóa người dùng thành công.');
                                    loadUsers();
                                },
                                error: function (xhr) {
                                    showAlert('error', 'Lỗi khi xóa người dùng: ' + xhr.responseText);
                                }
                            });
                        }
                    });
                });
            },
            error: function (xhr) {
                console.error('Lỗi khi tải danh sách người dùng:', xhr.responseText);
            }
        });
    }

    function loadRoles() {
        $.ajax({
            url: '/roles',
            method: 'GET',
            success: function (roles) {
                const roleSelect = $('#editRole');
                roleSelect.empty();

                roles.forEach(role => {
                    roleSelect.append(`<option value="${role._id.$oid}">${role.name}</option>`);
                });
            },
            error: function (xhr) {
                console.error('Lỗi khi tải danh sách quyền:', xhr.responseText);
            }
        });
    }

    $('#addUser').on('click', function () {
        $('#editUserModalLabel').text('Thêm người dùng');
        $('#editUsername').val('');
        $('#editEmail').val('');
        $('#editPassword').val('');
        $('#editRole').val('');
        $('#editUserForm').removeAttr('data-id');
        $('#editUserForm').attr('data-action', 'add');

        loadRoles();
        $('#editUserModal').modal('show');
    });
    $('#editUserForm').on('submit', function (e) {
        e.preventDefault();

        const action = $(this).attr('data-action');
        const userId = $(this).attr('data-id');
        const data = {
            name: $('#editUsername').val(),
            email: $('#editEmail').val(),
            password: $('#editPassword').val(),
            role: $('#editRole').val()
        };

        if (!data.password) {
            showAlert('error', 'Mật khẩu là bắt buộc.');
            return;
        }

        const url = action === 'edit' ? `/users/${userId}` : '/users';
        const method = action === 'edit' ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: data,
            headers: {
                'X-CSRF-TOKEN': csrfToken // Đảm bảo header được thiết lập
            },
            success: function (response) {
                showAlert('success', 'Thao tác thành công.');
                $('#editUserModal').modal('hide');
                loadUsers();
            },
            error: function (xhr) {
                showAlert('error', 'Lỗi: ' + xhr.responseText);
            }
        });
    });

    loadUsers();
});