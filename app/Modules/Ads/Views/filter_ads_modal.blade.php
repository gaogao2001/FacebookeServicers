<style>
    /* Đặt nền trắng và chữ đen cho các ô nhập liệu và dropdown */
    .form-control {
        background-color: gainsboro;
        /* Nền trắng */
        color: black;
        /* Chữ màu đen */
        border: 1px solid #ccc;
        /* Viền mỏng màu xám */
    }

    /* Đặt màu chữ cho placeholder */
    .form-control::placeholder {
        color: gray;
        /* Chữ màu xám nhạt cho placeholder */
        opacity: 1;
        /* Hiển thị rõ placeholder */
    }

    /* Tùy chỉnh ô chọn (dropdown) */
    select.form-control {
        -webkit-appearance: none;
        /* Tắt kiểu mặc định của trình duyệt */
        -moz-appearance: none;
        appearance: none;
        padding-right: 30px;
        /* Thêm khoảng trống bên phải để phù hợp icon dropdown */
    }

    /* Tăng độ rõ khi hover */
    .form-control:hover {
        border-color: #28a745;
        /* Đổi viền sang màu xanh lá */
    }

    /* Tăng độ rõ khi được focus */
    .form-control:focus {
        outline: none;
        /* Bỏ viền mặc định */
        border-color: #007bff;
        /* Viền xanh dương khi focus */
        box-shadow: 0 0 3px rgba(0, 123, 255, 0.5);
        /* Hiệu ứng ánh sáng */
    }
</style>

<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 500px;">
        <div class="modal-content" style="height: auto; overflow-y: auto; background-color: white;">
            <div class="modal-header btn btn-success btn-fw" style="background-color: #28a745; border-color: #28a745;">
                <h5 class="modal-title" id="filterModalLabel">Lọc Fanpage</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="color: black;">
                <div class="form-group">
                    <label for="insights_list">Danh sách Insights</label>
                    <textarea class="form-control" id="insights_list" name="insights_list" rows="5" placeholder="Nhập danh sách Insights (mỗi Insights một dòng)" style="color: black !important; background-color: gainsboro;">{{ session('fanpage_filters.insights_list') }}</textarea>
                    <small class="form-text text-muted">Nhập mỗi Insights trên một dòng riêng biệt.</small>
                </div>

                <!-- Danh sách Act ID -->
                <div class="form-group">
                    <label for="act_id_list">Danh sách Act ID</label>
                    <textarea class="form-control" id="act_id_list" name="act_id_list" rows="5" placeholder="Nhập danh sách Act ID (mỗi Act ID một dòng)" style="color: black !important; background-color: gainsboro;">{{ session('fanpage_filters.act_id_list') }}</textarea>
                    <small class="form-text text-muted">Nhập mỗi Act ID trên một dòng riêng biệt.</small>
                </div>
                <hr>
                <h5>Bộ lọc số liệu</h5>
                <div class="row">
                    <div class="col-6">
                        <label for="admin_hidden">Admin Ẩn</label>
                        <select class="form-control" id="admin_hidden">
                            <option value="">Chọn giá trị</option>
                            @foreach(collect($ads)->unique('admin_hidden') as $ad)
                            <option value="{{ $ad['admin_hidden'] }}"
                                @if(!is_null(session('fanpage_filters.admin_hidden')) && session('fanpage_filters.admin_hidden')===(string)$ad['admin_hidden'])
                                selected
                                @endif>
                                {{ $ad['admin_hidden'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <div class="form-edit-group">
                            <label for="timezone">Time Zone</label>
                            <select class="form-control" id="timezone">
                                <option value="">Chọn giá trị</option>
                                @foreach(collect($ads)->unique('timezone') as $ad)
                                <option value="{{ $ad['timezone'] }}" {{ session('fanpage_filters.timezone') == $ad['timezone'] ? 'selected' : '' }}>
                                    {{ $ad['timezone'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-edit-group">
                            <label for="currency">Currency</label>
                            <select class="form-control" id="currency">
                                <option value="">Chọn giá trị</option>
                                @foreach(collect($ads)->unique('currency') as $ad)
                                <option value="{{ $ad['currency'] }}" {{ session('fanpage_filters.currency') == $ad['currency'] ? 'selected' : '' }}>
                                    {{ $ad['currency'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-edit-group">
                            <label for="account_status">Account Status</label>
                            <select class="form-control" id="account_status">
                                <option value="">Chọn giá trị</option>
                                @foreach(collect($ads)->unique('account_status') as $ad)
                                <option value="{{ $ad['account_status'] }}" {{ session('fanpage_filters.account_status') == $ad['account_status'] ? 'selected' : '' }}>
                                    {{ $ad['account_status'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-edit-group">
                            <label for="nguong_tt">Ngưỡng TT</label>
                            <select class="form-control" id="nguong_tt">
                                <option value="">Chọn giá trị</option>
                                @foreach(collect($ads)->unique('nguong_tt') as $ad)
                                <option value="{{ $ad['nguong_tt'] }}" {{ session('fanpage_filters.nguong_tt') == $ad['nguong_tt'] ? 'selected' : '' }}>
                                    {{ $ad['nguong_tt'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-edit-group">
                            <label for="nguong_tt_hientai">Ngưỡng TT Hiện tại</label>
                            <select class="form-control" id="nguong_tt_hientai">
                                <option value="">Chọn giá trị</option>
                                @foreach(collect($ads)->unique('nguong_tt_hientai') as $ad)
                                <option value="{{ $ad['nguong_tt_hientai'] }}" {{ session('fanpage_filters.nguong_tt_hientai') == $ad['nguong_tt_hientai'] ? 'selected' : '' }}>
                                    {{ $ad['nguong_tt_hientai'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary BtnFilter">Áp dụng bộ lọc</button>
                <button type="button" class="btn btn-danger BtnClearFilter">Xóa bộ lọc</button>
            </div>
        </div>
    </div>
</div>

<!-- Bao gồm các thư viện cần thiết -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Thêm SweetAlert nếu chưa có -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('.BtnFilter').on('click', function() {
            const insights_list = $('#insights_list').val();
            const act_id_list = $('#act_id_list').val();
            const admin_hidden = $('#admin_hidden').val();
            const timezone = $('#timezone').val();
            const currency = $('#currency').val();
            const account_status = $('#account_status').val();
            const nguong_tt = $('#nguong_tt').val();
            const nguong_tt_hientai = $('#nguong_tt_hientai').val();

            $.ajax({
                url: '/filter_ads',
                method: 'POST',
                data: {
                    insights_list: insights_list,
                    act_id_list: act_id_list,
                    admin_hidden: admin_hidden,
                    timezone: timezone,
                    currency: currency,
                    account_status: account_status,
                    nguong_tt: nguong_tt,
                    nguong_tt_hientai: nguong_tt_hientai,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                // Đóng modal lọc
                var filterModal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
                filterModal.hide();
                
                // Hiển thị thông báo thành công và sau đó gọi đến hàm loadFilteredAds
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: 'Bộ lọc đã được áp dụng thành công!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Gọi hàm loadFilteredAds để tải lại dữ liệu
                    if (typeof window.loadFilteredAds === 'function') {
                        window.loadFilteredAds();
                    } else {
                        window.location.reload();
                    }
                });
            },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    // Hiển thị thông báo lỗi
                    Swal.fire("Lỗi", "Có lỗi xảy ra khi áp dụng bộ lọc.", "error");
                }
            });
        });

        $('.BtnClearFilter').on('click', function() {
            $.ajax({
                url: '/clear_filter_ads',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Đóng modal lọc
                    var filterModal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
                    filterModal.hide();
                    // Hiển thị thông báo đã xóa bộ lọc và reload trang
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã xóa',
                        text: 'Bộ lọc đã được xóa.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    // Hiển thị thông báo lỗi
                    Swal.fire("Lỗi", "Có lỗi xảy ra khi xóa bộ lọc.", "error");
                }
            });
        });
    });
</script>