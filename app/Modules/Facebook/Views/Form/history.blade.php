<!-- filepath: /var/www/FacebookService/app/Modules/Facebook/Views/Form/history.blade.php -->
<style>
    .truncate-message {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
    }

    .truncate-message:hover {
        white-space: normal;
        overflow: visible;
    }

    .pagination-controls {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 15px;
    }

    .pagination-controls button {
        margin: 0 5px;
    }

    .pagination-info {
        margin: 0 10px;
    }

    /* Added CSS to make all text in the table white */
    #historyTable {
        color: white;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <!-- Danh sách nội dung -->
        <div class="col-12">
            <div class="card wrapper" style="padding: 0.2rem 0.2rem !important;">
                <div class="card-body" style="padding: 0.2rem 0.2rem;">
                    <h4 class="card-title">Lịch sử</h4>
                    <hr>
                    <div class="table-responsive" style="padding-top: 20px;">
                        <table id="historyTable" class="table table table-bordered">
                            <thead>
                                <tr>
                                    <th>Facebook ID</th>
                                    <th>Action</th>
                                    <th class="truncate-message">Message</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody id="historyBody_{{ $uid }}">
                                <!-- Dữ liệu sẽ được tải qua AJAX -->
                            </tbody>
                        </table>
                        <!-- Pagination Controls -->
                        <div class="pagination-controls">
                            <button id="prevButton_{{ $uid }}" class="btn btn-secondary">Previous</button>
                            <span class="pagination-info">Page <span id="currentPage_{{ $uid }}">1</span> of <span id="lastPage_{{ $uid }}">1</span></span>
                            <button id="nextButton_{{ $uid }}" class="btn btn-secondary">Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const uid = '{{ $uid }}';
        let currentPage = 1;
        let lastPage = 1;
        const perPage = 10;

        function loadHistory(uid, page) {
            fetch(`/facebook/history/${uid}?page=${page}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById(`historyBody_${uid}`);
                    // Nếu là trang đầu tiên, xóa dữ liệu cũ
                    if (page === 1) {
                        tbody.innerHTML = '';
                    }

                    data.data.forEach(history => {
                        const time = history.time.$date ? new Date(history.time.$date).toLocaleString() : new Date(history.time).toLocaleString();
                        const row = `<tr>
                            <td>${history.uid}</td>
                            <td>${history.action}</td>
                            <td class="truncate-message">${history.message}</td>
                            <td>${history.status}</td>
                            <td>${time}</td>
                        </tr>`;
                        tbody.innerHTML += row;
                    });

                    currentPage = data.currentPage;
                    lastPage = data.lastPage;

                    // Cập nhật thông tin phân trang
                    document.getElementById(`currentPage_${uid}`).innerText = currentPage;
                    document.getElementById(`lastPage_${uid}`).innerText = lastPage;

                    // Cập nhật trạng thái nút
                    document.getElementById(`prevButton_${uid}`).disabled = currentPage <= 1;
                    document.getElementById(`nextButton_${uid}`).disabled = currentPage >= lastPage;
                })
                .catch(error => console.error('Error:', error));
        }

        document.getElementById(`prevButton_${uid}`).addEventListener('click', function() {
            if (currentPage > 1) {
                loadHistory(uid, currentPage - 1);
            }
        });

        document.getElementById(`nextButton_${uid}`).addEventListener('click', function() {
            if (currentPage < lastPage) {
                loadHistory(uid, currentPage + 1);
            }
        });

        // Tải trang đầu tiên khi tải xong
        document.addEventListener('DOMContentLoaded', function() {
            loadHistory(uid, currentPage);
        });
    })();
</script>