<!-- show_image.blade.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>


<style>
    .image-container {
        width: 100%;
        padding-top: 75%;
        /* Tỷ lệ 4:3 */
        position: relative;
        margin-bottom: 15px;
        max-width: 300px;
        /* Giới hạn chiều rộng */
        overflow: hidden;
        /* Ẩn phần tràn */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        /* Thêm box shadow màu đen */
    }

    .image-container img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* Làm vừa khung cha */
    }

    .remove-existing-image {
        position: absolute;
        top: 2px;
        right: 2px;
        color: black;
        border: none;
        border-radius: 50%;
        font-size: 16px;
        width: 24px;
        height: 24px;
        cursor: pointer;
    }

    .swal-wide {
        max-width: 150%;
        /* Tăng chiều rộng của modal */
        max-height: 90%;
        /* Tăng chiều cao của modal */
        padding: 0;
    }

    .swal-wide img {
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 100%;
        display: block;
        margin: 0 auto;
    }

    @media (max-width: 768px) {
        .image-container {
            max-width: 100%;
            /* Sử dụng toàn bộ chiều rộng màn hình */
            padding-top: 56.25%;
            /* Tỷ lệ 16:9 */
            margin-bottom: 10px;
        }

        .image-container img {
            object-fit: contain;
            /* Đảm bảo hình ảnh không bị cắt */
        }

        .nav-pills-container {
            overflow-x: scroll;
            /* Cho phép cuộn ngang nếu cần */
        }

        .nav-pills .nav-link {
            padding: 5px;
            font-size: 12px;
        }

        #images-list .col-md-4,
        #videos-list .col-md-4 {
            flex: 0 0 100%;
            /* Mỗi ảnh/video chiếm toàn bộ chiều ngang */
            max-width: 100%;
        }

        .nav-pills-container {
            display: flex;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .image-container img,
        .video-container video {
            width: 100%;
            height: auto;
        }

        .image-container {
            width: 100%;
            padding-top: 75%;
            /* Tỷ lệ 4:3 */
            position: relative;
            margin-bottom: 15px;
            max-width: 300px;
            /* Giới hạn chiều rộng */
            overflow: hidden;
            /* Ẩn phần tràn */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            /* Thêm box shadow màu đen */
            display: flex;
            justify-content: center;
            /* Căn giữa */
            align-items: center;
            /* Căn giữa */

        }

    }
</style>

<div class="container" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <h2>Danh Sách Ảnh</h2>
    <div id="images-list" class="row d-flex flex-wrap">
        <!-- Images sẽ được tải ở đây -->
    </div>

    <div id="loading" class="text-center my-3" style="display: none;">
        <p>Đang tải thêm ảnh...</p>
    </div>
    <div class="text-center my-3">
        <button id="load-prev" class="btn btn-secondary" style="display: none;">Previous</button>
        <button id="load-next" class="btn btn-secondary" style="display: none;">Next</button>
    </div>
</div>

<!-- viết js tại đây luôn không cần tạo file js riêng -->
<script>
    $(document).ready(function() {
        @php
        // Kiểm tra xem biến nào tồn tại
        $uid = isset($accounts) ? $accounts -> uid : (isset($fanpage) ? $fanpage -> page_id : null);
        @endphp

        const uid = "{{ $uid }}"; // Gán giá trị uid
        let page = 1; // Trang hiện tại
        const limit = 15; // Số lượng ảnh mỗi trang
        let loading = false;

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });


        function loadImages() {
            if (loading) return;
            loading = true;
            $('#loading').show();
            $('#load-next, #load-prev').hide(); // Ẩn các nút trong khi đang tải

            $.ajax({
                url: `{{ route('getImage', ['id' => 'UID_PLACEHOLDER']) }}`.replace('UID_PLACEHOLDER', uid),
                type: 'GET',
                dataType: 'json',
                data: {
                    page: page,
                    limit: limit
                },
                success: function(data) {
                    if (data.images.length > 0) {
                        // Xóa danh sách hiện tại trước khi tải lại
                        $('#images-list').empty();

                        // Thêm ảnh vào danh sách
                        data.images.forEach(function(image) {
                            $('#images-list').append(`
                            <div class="col-md-4">
                                <div class="image-container">
                                    <img src="${image}" alt="Image" class="img-fluid">
                                    <button class="delete-image remove-existing-image" data-image="${image}" >x</button>
                            </div>
                        `);
                        });

                        // Hiển thị nút "Next" và "Previous" dựa trên dữ liệu
                        $('#load-next').toggle(data.hasMore);
                        $('#load-prev').toggle(page > 1);
                    } else {
                        if (page === 1) {
                            $('#images-list').html('<p class="text-center text-muted">Không có hình ảnh nào để hiển thị.</p>');
                        }
                    }

                    $('#loading').hide();
                    loading = false;
                },
                error: function(xhr) {
                    $('#loading').hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Lỗi khi tải ảnh: ' + xhr.statusText
                    });
                    loading = false;
                }
            });
        }

        loadImages();

        $('#load-next').on('click', function() {
            page++;
            loadImages();
        });

        $('#load-prev').on('click', function() {
            page--;
            loadImages();
        });

        $('#images-list').on('click', '.delete-image', function() {
            const imageUrl = $(this).data('image');
            const parentElement = $(this).closest('.col-md-4');

            $.ajax({
                url: `{{ route('deleteImage', ['id' => 'UID_PLACEHOLDER']) }}`.replace('UID_PLACEHOLDER', uid),
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}",
                    image: imageUrl,
                },
                success: function() {
                    parentElement.remove();
                    if ($('#images-list .col-md-4').length === 0) {
                        if (page > 1) {
                            page--;
                        }
                        loadImages();
                    }
                },
                error: function() {
                    console.error('Lỗi khi xóa ảnh.');
                },
            });
        });

        $('#images-list').on('click', '.image-container img', function() {
            const imageUrl = $(this).attr('src'); // Lấy URL của ảnh từ `src`
            const oldFileName = imageUrl.split('/').pop();
            const cleanOldFileName = oldFileName.replace(/^(Image)/, '');

            let cropperInstance;

            Swal.fire({
                title: 'Chỉnh sửa hình ảnh',
                html: `
                <div style="max-width: 100%; max-height: 400px; overflow: hidden;">
                    <img id="crop-image" src="${imageUrl}" style="max-width: 100%; display: block;" />
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Lưu thay đổi',
                cancelButtonText: 'Hủy',

                didOpen: () => {
                    const imageElement = document.getElementById('crop-image');
                    cropperInstance = new Cropper(imageElement, {
                        aspectRatio: NaN, // Không giới hạn tỷ lệ
                        viewMode: 1,
                        autoCropArea: 1,
                        responsive: true,
                        scalable: true,
                        zoomable: true,
                    });

                    // Tạo nút "Upload Avatar"
                    const uploadButton = document.createElement('button');
                    uploadButton.id = 'upload-avatar';
                    uploadButton.className = 'swal2-styled';
                    uploadButton.style.marginLeft = '10px';
                    uploadButton.innerText = 'Upload Avatar';
                    uploadButton.style.backgroundColor = '#3085d6';
                    uploadButton.style.border = 'none';
                    uploadButton.style.padding = '8px 16px';
                    uploadButton.style.cursor = 'pointer';

                    // Thêm nút "Upload Avatar" trước nút "Hủy"
                    const actions = Swal.getActions();
                    const cancelButton = actions.querySelector('.swal2-cancel');
                    actions.insertBefore(uploadButton, cancelButton);

                    // Bắt sự kiện click "Upload Avatar"
                    document.getElementById('upload-avatar').addEventListener('click', () => {
                        // Hiển thị overlay loading
                        showLoading();

                        if (!uid) {
                            hideLoading();
                            Swal.fire('Lỗi', 'Không tìm thấy UID!', 'error');
                            return;
                        }

                        if (cropperInstance) {
                            cropperInstance.getCroppedCanvas().toBlob((blob) => {
                                if (blob) {
                                    const formData = new FormData();
                                    formData.append('image', blob);
                                    formData.append('oldFileName', cleanOldFileName);
                                    formData.append('_token', "{{ csrf_token() }}");
                                    formData.append('_method', 'PUT');

                                    const updateImageUrl = `/updateImage/${uid}`;

                                    $.ajax({
                                        url: updateImageUrl,
                                        method: 'POST',
                                        data: formData,
                                        contentType: false,
                                        processData: false,
                                        success: function(response) {
                                            // Update thành công => ẩn loading
                                            hideLoading();

                                            const newImageUrl = response.imageUrl;
                                            // Cập nhật ảnh trên giao diện Cropper
                                            $('#crop-image').attr('src', newImageUrl);

                                            // Cập nhật ảnh trên page
                                            $(`img[src="${imageUrl}"]`).attr('src', newImageUrl);

                                            // Sau khi cập nhật xong => upload avatar
                                            uploadAvatar(newImageUrl);
                                        },
                                        error: function(xhr) {
                                            hideLoading();
                                            console.error('Cập nhật hình ảnh lỗi:', xhr);
                                            Swal.fire('Lỗi', 'Không thể cập nhật hình ảnh!', 'error');
                                        },
                                    });
                                } else {
                                    hideLoading();
                                    Swal.fire('Lỗi', 'Không thể cắt hình ảnh!', 'error');
                                }
                            });
                        } else {
                            hideLoading();
                            Swal.fire('Lỗi', 'Không thể khởi tạo Cropper.', 'error');
                        }
                    });
                },

                // Nếu bạn vẫn dùng preConfirm để "Lưu thay đổi" thì ta cũng show/hide loading
                preConfirm: () => {
                    showLoading();

                    if (cropperInstance) {
                        return new Promise((resolve, reject) => {
                                cropperInstance.getCroppedCanvas().toBlob((blob) => {
                                    if (blob) {
                                        const formData = new FormData();
                                        formData.append('image', blob);
                                        formData.append('oldFileName', oldFileName);
                                        formData.append('_token', "{{ csrf_token() }}");
                                        formData.append('_method', 'PUT'); // Giả lập phương thức PUT

                                        const updateImageUrl = `/updateImage/${uid}`;

                                        $.ajax({
                                            url: updateImageUrl,
                                            method: 'POST', // Sử dụng POST
                                            data: formData,
                                            contentType: false,
                                            processData: false,
                                            success: function(response) {
                                                resolve(response.imageUrl);
                                            },
                                            error: function(xhr) {
                                                reject();
                                            },
                                        });
                                    } else {
                                        reject();
                                    }
                                });
                            })
                            .then((updatedImageUrl) => {
                                hideLoading(); // Ẩn loading khi xong

                                Swal.fire('Thành công', 'Hình ảnh đã được cập nhật!', 'success');
                                // Thay đổi ảnh ở giao diện
                                const timestamp = new Date().getTime();
                                const newImageUrl = updatedImageUrl.includes('?') ?
                                    `${updatedImageUrl}&t=${timestamp}` :
                                    `${updatedImageUrl}?t=${timestamp}`;
                                $(`img[src="${imageUrl}"]`).attr('src', newImageUrl);
                            })
                            .catch(() => {
                                hideLoading();
                                Swal.fire('Lỗi', 'Không thể cập nhật hình ảnh!', 'error');
                            });
                    } else {
                        hideLoading();
                        Swal.fire('Lỗi', 'Không thể khởi tạo Cropper.', 'error');
                    }
                },
            });

            // Nếu bạn có cơ chế lưu trữ localStorage
            $(document).ready(function() {
                const updatedImageUrl = localStorage.getItem('updatedImageUrl');
                if (updatedImageUrl) {
                    $('img').each(function() {
                        const src = $(this).attr('src');
                        if (src.includes('Image')) { // Điều kiện phù hợp với tên file
                            $(this).attr('src', updatedImageUrl);
                        }
                    });
                    localStorage.removeItem('updatedImageUrl');
                }
            });

            // Hàm upload Avatar
            function uploadAvatar(currentImageUrl) {
                showLoading(); // Hiển thị loading khi bắt đầu upload avatar

                $.ajax({
                    url: "{{ route('upload.avatar') }}",
                    method: 'POST',
                    data: {
                        url: currentImageUrl,
                        uid: uid,
                        _token: csrfToken,
                    },
                    success: function(response) {
                        hideLoading(); // Ẩn loading sau khi trả về thành công
                        // Nếu server trả về status là error trong nội dung JSON thì hiển thị thông báo lỗi
                        if (response.status && response.status === 'error') {
                            console.error('Upload Avatar lỗi:', response.message);
                            Swal.fire('Lỗi', response.message || 'Không thể tải lên avatar!', 'error');
                        } else {
                            console.log('Upload Avatar thành công:', response);
                            Swal.fire('Thành công', response.message || 'Avatar đã được tải lên!', 'success');
                        }
                    },
                    error: function(xhr) {
                        hideLoading(); // Ẩn loading khi có lỗi
                        console.error('Upload Avatar lỗi:', xhr);
                        Swal.fire('Lỗi', 'Đã có lỗi xảy ra khi tải lên avatar!', 'error');
                    }
                });
}

        });

        function showLoading() {
            console.log("Hiển thị loading...");
            $('body').append(`
                    <div id="loadingOverlay" style="
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        z-index: 9999;
                    ">
                        <div class="loader"></div>
                    </div>
                `);
        }

        // Ẩn loader
        function hideLoading() {
            console.log("Ẩn loading...");
            $('#loadingOverlay').remove();
        }
    });
</script>