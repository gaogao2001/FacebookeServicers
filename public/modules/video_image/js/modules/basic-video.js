// modules/basic-video.js
import { selectedOrder, currentPage, pageSize, showLoading, hideLoading } from './common.js';

let selectedImages = [];
let existingImages = [];

export function initBasicVideo() {
    $('#btnSelectImages').on('click', () => $('#imageOptionModal').modal('show'));
    $('#btnUploadFromLocal').on('click', () => {
        $('#imageOptionModal').modal('hide');
        $('#images').trigger('click');
    });

    $('#images').on('change', function (e) {
        const newFiles = Array.from(this.files);
        newFiles.forEach(file => {
            if (!selectedImages.some(f => f.name === file.name)) {
                selectedImages.push(file);
                selectedOrder.push({ type: 'local', data: file });
            }
        });
        currentPage = Math.ceil(selectedOrder.length / pageSize);
        $(this).val('');
        updatePreviewImages();
    });

    $('#btnSelectFromFileManager').on('click', () => {
        $('#imageOptionModal').modal('hide');
        showContentImageSelector();
    });

    $('#createVideoBtn').click(function (e) {
        e.preventDefault();
        showLoading();
        const formData = new FormData($('#createVideoForm')[0]);
        let orderedImageUrls = [];

        selectedOrder.forEach(item => {
            if (item.type === 'local') formData.append('images[]', item.data);
            else if (item.type === 'filemanager') orderedImageUrls.push(item.data);
        });

        if (orderedImageUrls.length > 0) formData.append('existing_images', JSON.stringify(orderedImageUrls));
        formData.append('image_order', JSON.stringify(selectedOrder.map(item => item.type)));

        $.ajax({
            url: '/create-basic-video',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading();
                $('#video source').attr('src', response.previewUrl);
                $('#video')[0].load();
                if ($('#videoPreviewBtn').length === 0) {
                    $('#video').closest('.col-3').append(`
                        <div id="videoPreviewContainer" class="mt-2">
                            <button type="button" id="videoPreviewBtn" class="btn btn-info">Xem demo</button>
                        </div>
                    `);
                }
                $('#videoPreviewBtn').off('click').on('click', () => $('#previewModal').modal('show'));
            },
            error: function (xhr) {
                hideLoading();
                Swal.fire('Lỗi!', 'Có lỗi xảy ra: ' + xhr.responseJSON.message, 'error');
            }
        });
    });

    $('#applyTransition').change(function () {
        if (this.checked) {
            $('#transitionOptions').slideDown();
            $('#transitionOptions input').attr('required', true);
        } else {
            $('#transitionOptions').slideUp();
            $('#transitionOptions input').removeAttr('required');
        }
    });
}

function updatePreviewImages() {
    $('#selectedVideosContainer').empty();
    const startIndex = (currentPage - 1) * pageSize;
    const endIndex = currentPage * pageSize;
    const mediaToShow = selectedOrder.slice(startIndex, endIndex);

    mediaToShow.forEach(item => {
        const previewDiv = item.type === 'local' ? createLocalImagePreview(item.data) : createFileManagerImagePreview(item.data);
        $('#selectedVideosContainer').append(previewDiv);
    });

    const totalPages = Math.ceil(selectedOrder.length / pageSize);
    renderPagination(document.getElementById('pagination'), currentPage, totalPages, newPage => {
        currentPage = newPage;
        updatePreviewImages();
    });
}

function createLocalImagePreview(file) {
    const previewDiv = $(`<div class="preview-image position-relative" style="width:200px; height:150px;" data-file-name="${file.name}"></div>`);
    const reader = new FileReader();
    reader.onload = e => {
        previewDiv.append(`
            <img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;" alt="Preview">
            <button type="button" class="btn btn-sm remove-image" style="position:absolute; top:2px; right:2px; border-radius:50%; background-color:white; color:black; z-index:10;">×</button>
        `);
        previewDiv.find('.remove-image').on('click', () => {
            selectedImages = selectedImages.filter(f => f.name !== file.name);
            selectedOrder = selectedOrder.filter(it => it.type === 'local' ? it.data.name !== file.name : true);
            if (Math.ceil(selectedOrder.length / pageSize) < currentPage) currentPage--;
            updatePreviewImages();
        });
    };
    reader.readAsDataURL(file);
    return previewDiv;
}

function createFileManagerImagePreview(url) {
    const previewDiv = $(`
        <div class="preview-image position-relative" data-file-name="${url}">
            <img src="${url}" style="width:200px; height:150px; object-fit:cover;" alt="Preview">
            <button type="button" class="btn btn-sm remove-image" style="position:absolute; top:2px; right:2px; border-radius:50%; background-color:white; color:black; z-index:10;">×</button>
        </div>
    `);
    previewDiv.find('.remove-image').on('click', () => {
        existingImages = existingImages.filter(u => u !== url);
        selectedOrder = selectedOrder.filter(it => it.type === 'filemanager' ? it.data !== url : true);
        if (Math.ceil(selectedOrder.length / pageSize) < currentPage) currentPage--;
        updatePreviewImages();
    });
    return previewDiv;
}

function showContentImageSelector() {
    let fileManagerSelectedImages = [];
    $.ajax({
        url: '/file-manager/images',
        type: 'GET',
        dataType: 'json',
        success: function (treeData) {
            const contentImagesTree = treeData;
            let currentContentImageFolder = null;
            let contentImageFolderStack = [];
            loadContentImageList(contentImagesTree, currentContentImageFolder, contentImageFolderStack, fileManagerSelectedImages);
            $('#contentImageSelectorModal').modal('show');
        },
        error: function (err) {
            console.error(err);
            Swal.fire('Lỗi!', 'Lỗi tải hình ảnh từ FileManager', 'error');
        }
    });

    $('#btnConfirmFileManagerSelection').off('click').on('click', () => {
        fileManagerSelectedImages.forEach(url => {
            if (!existingImages.includes(url)) {
                existingImages.push(url);
                selectedOrder.push({ type: 'filemanager', data: url });
            }
        });
        fileManagerSelectedImages = [];
        $('#contentImageSelectorModal').modal('hide');
        currentPage = Math.ceil(selectedOrder.length / pageSize);
        updatePreviewImages();
    });
}

function loadContentImageList(tree, folder, stack, selected) {
    $('#contentImageList').empty();
    if (folder === null) {
        $('#currentContentImageFolder').text('Danh sách thư mục');
        $('#backContentImageButton').hide();
        const folders = Object.keys(tree);
        folders.forEach(folderKey => {
            const folderCard = $(`
                <div class="col-md-3 mb-2">
                    <div class="card" style="cursor:pointer; background-color:#47a4a7;">
                        <div class="card-body text-center text-white">
                            <h5 class="card-title">${folderKey}</h5>
                            <p class="card-text">${tree[folderKey].length} hình</p>
                        </div>
                    </div>
                </div>
            `);
            folderCard.on('click', () => {
                stack.push(folder);
                loadContentImageList(tree, folderKey, stack, selected);
            });
            $('#contentImageList').append(folderCard);
        });
    } else {
        $('#currentContentImageFolder').text(folder);
        $('#backContentImageButton').show().off('click').on('click', () => {
            folder = stack.pop();
            loadContentImageList(tree, folder, stack, selected);
        });
        const images = tree[folder] || [];
        images.forEach(image => {
            const cardContainer = $(`
                <div class="col-md-3 mb-2">
                    <div class="card" style="cursor:pointer;">
                        <img src="${image.url}" class="card-img-top" alt="${image.name}">
                    </div>
                </div>
            `);
            cardContainer.find('.card').on('click', function () {
                if (!selected.includes(image.url)) {
                    selected.push(image.url);
                    $(this).addClass('selected');
                } else {
                    selected.splice(selected.indexOf(image.url), 1);
                    $(this).removeClass('selected');
                }
            });
            $('#contentImageList').append(cardContainer);
        });
    }
}

// Giả lập hàm renderPagination (cần được định nghĩa trong project thực tế)
function renderPagination(container, current, total, callback) {
    // Thêm logic phân trang tại đây nếu cần
}