$(document).ready(function () {
	// Mở/đóng panel
	$(document).on('click', '#toggle-panel', function () {
		_that = $(this);
		$('#theme-panel').toggleClass('open');
		return false;
	});

	$(document).on('input', '#custom-theme-color', function () {
		_that = $(this);
		_selectedColor = _that.val();
		$('.content-wrapper').css('background-color', _selectedColor);
		$('.content-wrapper').css('background-image', 'none');
		if (typeof Storage !== 'undefined') {
			localStorage.setItem('content-background-color', _selectedColor);
			localStorage.removeItem('content-background-image');
		}
		return false;
	});

	$(document).on('change', '#background-image', function (event) {
		_that = $(this);
		_file = event.target.files[0];
		if (_file) {
			_reader = new FileReader();
			_reader.onload = function (e) {
				$('.content-wrapper').css({
					'background-image': `url(${e.target.result})`,
					'background-size': 'cover',
					'background-repeat': 'no-repeat',
					'background-position': 'center'
				});
				if (typeof Storage !== 'undefined') {
					localStorage.setItem('content-background-image', e.target.result);
					localStorage.removeItem('content-background-color');
				}
			};
			_reader.readAsDataURL(_file);
		}
		return false;
	});

	$(document).on('input', '#shared-color', function () {
		_that = $(this);
		_selectedColor = _that.val();
		$('#sidebar, .sidebar, #navbar, .navbar, #footer, .footer').css('background-color', _selectedColor);
		if (typeof Storage !== 'undefined') {
			localStorage.setItem('sidebar-navbar-background-color', _selectedColor);
		}
		return false;
	});

	$(document).on('input', '#title-color', function () {
		_that = $(this);
		_selectedColor = _that.val();
		$('h1, h2, h3, h4, h5, h6').css('color', _selectedColor);
		if (typeof Storage !== 'undefined') {
			localStorage.setItem('title-color', _selectedColor);
		}
		return false;
	});

	$(document).on('input', '#card-color', function () {
		_that = $(this);
		_selectedColor = _that.val();
		$('.card').css('background-color', _selectedColor);
		if (typeof Storage !== 'undefined') {
			localStorage.setItem('card-background-color', _selectedColor);
		}
		return false;
	});

	$(document).on('input', '#text-color', function () {
		_that = $(this);
		_selectedColor = _that.val();
		$('body, .content-wrapper, table, th, td').css('color', _selectedColor);
		if (typeof Storage !== 'undefined') {
			localStorage.setItem('text-color', _selectedColor);
		}
		return false;
	});

	$(document).on('click', '#reset-theme', function () {
		_that = $(this);
		$('.content-wrapper, #sidebar, .sidebar, #navbar, .navbar, #footer, .footer, .card, h1, h2, h3, h4, h5, h6').css({
			'background-color': '',
			'background-image': 'none',
			'color': ''
		});
		if (typeof Storage !== 'undefined') {
			localStorage.clear();
		}
		return false;
	});


	// $('#card-setting').on('input', function () {
	//     const selectedColor = $(this).val();
	//     $('.card-setting').css('background-color', selectedColor);
	//     if (typeof Storage !== 'undefined') {
	//         localStorage.setItem('card-setting-color', selectedColor);
	//     }
	// });

	if (typeof Storage !== 'undefined') {
		const savedContentColor = localStorage.getItem('content-background-color');
		const savedContentImage = localStorage.getItem('content-background-image');
		const savedSharedColor = localStorage.getItem('sidebar-navbar-background-color');
		const savedCardColor = localStorage.getItem('card-background-color');
		const savedTitleColor = localStorage.getItem('title-color');
		const savedTextColor = localStorage.getItem('text-color');

		if (savedContentColor) $('.content-wrapper').css('background-color', savedContentColor);
		if (savedContentImage) {
			$('.content-wrapper').css({
				'background-image': `url(${savedContentImage})`,
				'background-size': 'cover',
				'background-repeat': 'no-repeat',
				'background-position': 'center',
			});
		}
		if (savedSharedColor) {
			$('#sidebar, .sidebar, #navbar, .navbar, #footer, .footer').css('background-color', savedSharedColor);
		}
		if (savedTitleColor) {
			$('h1, h2, h3, h4, h5, h6').css('color', savedTitleColor);
		}
		if (savedCardColor) $('.card').css('background-color', savedCardColor);

		if (savedTextColor) {
			$('body, .content-wrapper, table, th, td').css('color', savedTextColor);
		}
	}



});


$(document).ready(function () {
	// Loại bỏ script xử lý cũ
	$('#extraControls').remove();

	// Lấy các phần tử cần thiết
	var $themePanel = $('#theme-panel');
	var $sssSidebar = $('#sss-sidebar');
	var $docSidebar = $('#doc-sidebar');

	// Xử lý các liên kết trong dropdown
	$('#settingLink').on('click', function (e) {
		e.preventDefault();

		// Đóng các sidebar khác
		$sssSidebar.removeClass('open');
		if ($docSidebar.length) {
			$docSidebar.hide();
		}

		// Mở/đóng theme panel
		$themePanel.toggleClass('open');
	});

	$('#todoLink').on('click', function (e) {
		e.preventDefault();

		// Đóng các sidebar khác
		$themePanel.removeClass('open');
		if ($docSidebar.length) {
			$docSidebar.hide();
		}

		// Mở/đóng todo list sidebar
		$sssSidebar.toggleClass('open');
	});

	$('#docLink').on('click', function (e) {
		e.preventDefault();
	
		// Đóng các sidebar khác
		$themePanel.removeClass('open');
		$sssSidebar.removeClass('open');
	
		// Mở/đóng documentation sidebar đơn giản bằng cách toggle class
		if ($docSidebar.length) {
			if ($docSidebar.hasClass('open')) {
				// Đóng sidebar
				$docSidebar.removeClass('open');
				setTimeout(function() {
					// Sau khi animation hoàn tất, ẩn sidebar
					if (!$docSidebar.hasClass('open')) {
						$docSidebar.hide();
					}
				}, 500); // 500ms = thời gian transition trong CSS
			} else {
				// Hiển thị sidebar
				$docSidebar.show();
				
				// Đợi một chút để hiệu ứng CSS hoạt động tốt
				setTimeout(function() {
					$docSidebar.addClass('open');
					
					// Tải nội dung
					const currentPath = window.location.pathname;
					const pageId = currentPath.startsWith('/') ? currentPath.substring(1) : currentPath || 'index';
					
					// Tạo sự kiện để tải nội dung
					const docEvent = new Event('loadDocumentation');
					document.dispatchEvent(docEvent);
				}, 10);
			}
		}
	});

	// Xử lý đóng tất cả khi click ra ngoài
	$(document).on('click', function (event) {
		if (!$(event.target).closest('.dropdown, #theme-panel, #sss-sidebar, #doc-sidebar').length) {
			$themePanel.removeClass('open');
			$sssSidebar.removeClass('open');
			if ($docSidebar.length) {
				$docSidebar.hide().removeClass('doc-visible');
			}
		}
	});

	
});
