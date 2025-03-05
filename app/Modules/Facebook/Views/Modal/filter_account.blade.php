<style>
    input::placeholder {
        color: black !important;
    }
</style>

<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 500px;">
        <div class="modal-content" style="height: 825px; overflow-y: auto; background-color: white;">
            <div class="modal-header btn btn-success btn-fw" style="background-color: #28a745; border-color: #28a745;">
                <h5 class="modal-title" id="filterModalLabel">Lọc tài khoản</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="color: black;">
                 <!-- Danh sách UID => uid_list -->
                 <div class="form-group">
                    <label for="uid_list">Danh sách UID</label>
                    <textarea class="form-control" id="uid_list" name="uid_list" rows="5" placeholder="Nhập danh sách UID (mỗi UID một dòng)" style="color: black !important; overflow-y: auto;">{{ session('fb_filters_input.uid_list') }}</textarea>
                    <small class="form-text text-muted">Nhập mỗi UID trên một dòng riêng biệt.</small>
                </div>
                <!-- Giới tính => gender -->
                <div class="form-group">
                    <label for="gender">Giới tính</label>
                    <select class="form-control" id="gender" name="gender" style="color: black !important;">
                        <option value="" {{ session('fb_filters_input.gender') == '' ? 'selected' : '' }}>Tất cả</option>
                        <option value="male" {{ session('fb_filters_input.gender') == 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ session('fb_filters_input.gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                    </select>
                </div>
                <!-- Tuổi => birthday (from -> to) -->
                <div class="form-group">
                    <label>Tuổi</label>
                    <div class="d-flex">
                        <input type="number" class="form-control mr-2" id="birthday_from" name="birthday_from" placeholder="Từ" min="0" style="color: black !important;" value="{{ session('fb_filters_input.birthday_from') }}">
                        <input type="number" class="form-control" id="birthday_to" name="birthday_to" placeholder="Đến" min="0" style="color: black !important;" value="{{ session('fb_filters_input.birthday_to') }}">
                    </div>
                </div>
                <!-- Bạn bè => friends (from -> to) -->
                <div class="form-group">
                    <label>Bạn bè</label>
                    <div class="d-flex">
                        <input type="number" class="form-control mr-2" id="friends_from" name="friends_from" placeholder="Từ" min="0" style="color: black !important;" value="{{ session('fb_filters_input.friends_from') }}">
                        <input type="number" class="form-control" id="friends_to" name="friends_to" placeholder="Đến" min="0" style="color: black !important;" value="{{ session('fb_filters_input.friends_to') }}">
                    </div>
                </div>
                <!-- Nhóm => groups (from -> to) -->
                <div class="form-group">
                    <label>Nhóm đã tham gia</label>
                    <div class="d-flex">
                        <input type="number" class="form-control mr-2" id="groups_from" name="groups_from" placeholder="Từ" min="0" style="color: black !important;" value="{{ session('fb_filters_input.groups_from') }}">
                        <input type="number" class="form-control" id="groups_to" name="groups_to" placeholder="Đến" min="0" style="color: black !important;" value="{{ session('fb_filters_input.groups_to') }}">
                    </div>
                </div>
                <!-- Tương tác cuối => last_seeding -->
                <div class="form-group">
                    <label>Tương tác cuối (giờ)</label>
                    <input type="number" class="form-control" id="last_seeding" name="last_seeding" placeholder="Số giờ" min="0" style="color: black !important;" value="{{ session('fb_filters_input.last_seeding') }}">
                </div>
                <!-- Nhóm tài khoản => groups_account -->
                <div class="form-group">
                    <label for="groups_account">Nhóm tài khoản</label>
                    <select class="form-control" id="groups_account" name="groups_account">
                        <option value="" {{ session('fb_filters_input.groups_account') == '' ? 'selected' : '' }}>Chọn nhóm tài khoản</option>
                        @foreach($accountGroups as $group)
                        <option value="{{ $group }}" {{ session('fb_filters_input.groups_account') == $group ? 'selected' : '' }}>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Email => email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Nhập từ khóa email (ví dụ: @gmail)" style="color: black !important;" value="{{ session('fb_filters_input.email') }}">
                </div>
                <!-- Status => status -->
                <div class="form-group">
                    <label for="status">Trạng thái</label>
                    <select class="form-control" id="status" name="status">
                        <option value="" {{ session('fb_filters_input.status') == '' ? 'selected' : '' }}>Tất cả trạng thái</option>
                        @foreach($allStatus as $status)
                        <option value="{{ $status }}" {{ session('fb_filters_input.status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Proxy => networkuse[port] -->
                <div class="form-group">
                    <label for="networkuse_port">Proxy (Port)</label>
                    <select class="form-control" id="networkuse_port" name="networkuse_port" style="color: black !important;">
                        <option value="" {{ session('fb_filters_input.networkuse_port') == '' ? 'selected' : '' }}>Chọn trạng thái</option>
                        <option value="has_proxy" {{ session('fb_filters_input.networkuse_port') == 'has_proxy' ? 'selected' : '' }}>Có</option>
                        <option value="no_proxy" {{ session('fb_filters_input.networkuse_port') == 'no_proxy' ? 'selected' : '' }}>Chưa có</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary BtnFilter">Áp dụng bộ lọc</button>
                <button type="button" class="btn btn-danger BtnClearFilter">Xóa bộ lọc</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    $(document).ready(function() {
        // Hiển thị modal khi nhấn vào biểu tượng filter
        $('#filterToolbarIcon').on('click', function(e) {
            e.preventDefault();
            $('#filterModal').modal('show');
        });

        // Xử lý sự kiện khi nhấn nút áp dụng bộ lọc
        $('.BtnFilter').on('click', function(e) {
            e.preventDefault();

            // Lấy CSRF token
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Thu thập dữ liệu từ các input
            const filterData = {
                uid_list: $('#uid_list').val(),
                gender: $('#gender').val(),
                birthday_from: $('#birthday_from').val(),
                birthday_to: $('#birthday_to').val(),
                friends_from: $('#friends_from').val(),
                friends_to: $('#friends_to').val(),
                groups_from: $('#groups_from').val(),
                groups_to: $('#groups_to').val(),
                last_seeding: $('#last_seeding').val(),
                groups_account: $('#groups_account').val(),
                email: $('#email').val(),
                status: $('#status').val(),
                networkuse_port: $('#networkuse_port').val(),
            };

            // Gửi AJAX request
            $.ajax({
                url: "{{ route('facebook.filter') }}", // URL của Laravel route
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken // Thêm CSRF token vào header
                },
                data: filterData, // Dữ liệu gửi lên
                success: function(response) {
                    console.log('Bộ lọc đã áp dụng thành công:', response);
                    $('#filterModal').modal('hide'); // Ẩn modal
                    location.reload(); // Reload lại trang để áp dụng bộ lọc
                },
                error: function(xhr) {
                    console.error('Lỗi khi áp dụng bộ lọc:', xhr.responseText);
                    // Hiển thị thông báo lỗi
                }
            });
        });

        // Xử lý sự kiện khi nhấn nút Xóa bộ lọc
        $('.BtnClearFilter').on('click', function(e) {
            e.preventDefault();

            // Lấy CSRF token
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Gửi AJAX request để xóa session bộ lọc
            $.ajax({
                url: "{{ route('facebook.clearFilter') }}", // URL của Laravel route để xóa session
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken // Thêm CSRF token vào header
                },
                success: function(response) {
                    console.log('Bộ lọc đã được xóa:', response);
                    location.reload(); // Reload lại trang để xóa bộ lọc
                },
                error: function(xhr) {
                    console.error('Lỗi khi xóa bộ lọc:', xhr.responseText);
                    // Hiển thị thông báo lỗi
                }
            });
        });
    });
</script>