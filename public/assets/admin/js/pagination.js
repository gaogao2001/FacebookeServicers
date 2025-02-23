/**
 * Lấy danh sách các trang cần hiển thị với dấu "…"
 * @param {number} current - Trang hiện tại
 * @param {number} last - Tổng số trang
 * @returns {Array} - Mảng các trang và dấu "dots"
 */
function getPagesToShow(current, last) {
    const pages = [];
    const delta = 2;

    if (last <= 7) {
        for (let i = 1; i <= last; i++) {
            pages.push(i);
        }
    } else {
        if (current <= 4) {
            for (let i = 1; i <= 5; i++) {
                pages.push(i);
            }
            pages.push('dots');
            pages.push(last);
        } else if (current >= last - 3) {
            pages.push(1);
            pages.push('dots');
            for (let i = last - 4; i <= last; i++) {
                pages.push(i);
            }
        } else {
            pages.push(1);
            pages.push('dots');
            for (let i = current - 1; i <= current + 1; i++) {
                pages.push(i);
            }
            pages.push('dots');
            pages.push(last);
        }
    }

    return pages;
}

/**
 * Render các nút phân trang vào container
 * @param {HTMLElement} container - Phần tử chứa phân trang
 * @param {number} currentPage - Trang hiện tại
 * @param {number} lastPage - Tổng số trang
 * @param {Function} onPageChange - Callback khi thay đổi trang
 */
function renderPagination(container, currentPage, lastPage, onPageChange) {
    container.innerHTML = '';

    // Nút Previous
    const prevButton = document.createElement('li');
    prevButton.innerHTML = `<a class="prev page-numbers"><i class="fa fa-arrow-left"></i></a>`;
    if (currentPage > 1) {
        prevButton.addEventListener('click', () => {
            onPageChange(currentPage - 1);
        });
    } else {
        prevButton.classList.add('disabled');
    }
    container.appendChild(prevButton);

    // Tạo các nút trang
    const pagesToShow = getPagesToShow(currentPage, lastPage);

    pagesToShow.forEach(item => {
        if (item === 'dots') {
            const li = document.createElement('li');
            li.innerHTML = `<span class="page-numbers dots">…</span>`;
            container.appendChild(li);
        } else if (item === currentPage) {
            const li = document.createElement('li');
            li.innerHTML = `<span aria-current="page" class="page-numbers current">${item}</span>`;
            container.appendChild(li);
        } else {
            const li = document.createElement('li');
            li.innerHTML = `<a class="page-numbers" href="#">${item}</a>`;
            li.addEventListener('click', (e) => {
                e.preventDefault();
                onPageChange(item);
            });
            container.appendChild(li);
        }
    });

    // Nút Next
    const nextButton = document.createElement('li');
    nextButton.innerHTML = `<a class="next page-numbers"><i class="fa fa-arrow-right"></i></a>`;
    if (currentPage < lastPage) {
        nextButton.addEventListener('click', () => {
            onPageChange(currentPage + 1);
        });
    } else {
        nextButton.classList.add('disabled');
    }
    container.appendChild(nextButton);
}