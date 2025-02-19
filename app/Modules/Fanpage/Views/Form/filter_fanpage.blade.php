<style>
    .form-control {
        background-color: white;
        color: black;
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
                <hr>
                <h5>Bộ lọc số liệu</h5>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="likes_min">Like từ</label>
                            <input type="number" class="form-control" id="likes_min" placeholder="Từ" value="{{ session('fanpage_filters.likes_min') ?? '' }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="likes_max">Like đến</label>
                            <input type="number" class="form-control" id="likes_max" placeholder="Đến" value="{{ session('fanpage_filters.likes_max') ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="followers_min">Followers từ</label>
                            <input type="number" class="form-control" id="followers_min" placeholder="Từ" value="{{ session('fanpage_filters.followers_min') ?? '' }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="followers_max">Followers đến</label>
                            <input type="number" class="form-control" id="followers_max" placeholder="Đến" value="{{ session('fanpage_filters.followers_max') ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="posts_min">Post từ</label>
                            <input type="number" class="form-control" id="posts_min" placeholder="Từ" value="{{ session('fanpage_filters.posts_min') ?? '' }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="posts_max">Post đến</label>
                            <input type="number" class="form-control" id="posts_max" placeholder="Đến" value="{{ session('fanpage_filters.posts_max') ?? '' }}">
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


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Xử lý áp dụng bộ lọc
        document.querySelector('.BtnFilter').addEventListener('click', function(e) {
            e.preventDefault();

            const filterData = {
                likes_min: document.getElementById('likes_min').value,
                likes_max: document.getElementById('likes_max').value,
                followers_min: document.getElementById('followers_min').value,
                followers_max: document.getElementById('followers_max').value,
                posts_min: document.getElementById('posts_min').value,
                posts_max: document.getElementById('posts_max').value,
            };

            fetch("{{ route('fanpage-manager.filter') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(filterData),
                })
                .then((response) => response.json())
                .then((data) => {
                    // Luôn reload lại trang sau khi áp dụng bộ lọc
                    location.reload();
                })
                .catch((error) => {
                    console.error('Lỗi khi lọc:', error);
                });
        });

        // Xử lý xóa bộ lọc
        document.querySelector('.BtnClearFilter').addEventListener('click', function(e) {
            e.preventDefault();

            fetch("{{ route('fanpage-manager.clearFilter') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                })
                .then((response) => response.json())
                .then((data) => {
                    // Luôn reload lại trang sau khi xóa bộ lọc
                    location.reload();
                })
                .catch((error) => {
                    console.error('Lỗi khi xóa bộ lọc:', error);
                });
        });
    });
</script>