$(document).ready(function () {
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
                                    Edit
                                </button>
                                <button class="btn btn-inverse-danger delete-user" data-id="${user._id.$oid}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    `);
                });

                // Sự kiện Edit
                $('.edit-user').on('click', function () {
                    const userId = $(this).data('id');
                    const userName = $(this).data('name');
                    const userEmail = $(this).data('email');
                    const userRole = $(this).data('role');

                    // Điền thông tin người dùng vào modal
                    $('#editUserModalLabel').text('Edit User');
                    $('#editUsername').val(userName);
                    $('#editEmail').val(userEmail);
                    $('#editRole').val(userRole);
                    $('#editUserForm').attr('data-id', userId);
                    $('#editUserForm').attr('data-action', 'edit');

                    loadRoles();
                    $('#editUserModal').modal('show');
                });

                // Sự kiện Delete
                $('.delete-user').on('click', function () {
                    const userId = $(this).data('id');

                    if (confirm('Are you sure you want to delete this user?')) {
                        $.ajax({
                            url: `/users/${userId}`,
                            method: 'DELETE',
                            success: function () {
                                alert('User deleted successfully.');
                                loadUsers();
                            },
                            error: function (xhr) {
                                alert('Error deleting user: ' + xhr.responseText);
                            }
                        });
                    }
                });
            },
            error: function (xhr) {
                console.error('Error loading users:', xhr.responseText);
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
                console.error('Error loading roles:', xhr.responseText);
            }
        });
    }

    $('#addUser').on('click', function () {
        $('#editUserModalLabel').text('Thêm User');
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

        const url = action === 'edit' ? `/users/${userId}` : '/users';
        const method = action === 'edit' ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: data,
            success: function (response) {
                alert('Operation successful.');
                $('#editUserModal').modal('hide');
                loadUsers();
            },
            error: function (xhr) {
                alert('Error: ' + xhr.responseText);
            }
        });
    });

    loadUsers();
});