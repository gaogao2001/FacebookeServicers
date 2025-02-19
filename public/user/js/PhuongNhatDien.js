$(document).ready(function () {
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

	function hideLoading() {
		console.log("Ẩn loading...");
		$('#loadingOverlay').remove();
	}

	// Merge AJAX calls into one
	$(document).on('click', '.BtnLoadComment', function () {
		let postId = $(this).data('id');
		let userId = $(this).data('uid');
		let token = $('meta[name="csrf-token"]').attr('content');
		let userPost = $(this).closest('.user-post');
		let commentsArea = userPost.find('.coment-area');
		// Show/hide
		commentsArea.toggle();
		showLoading(); // Gọi loader

		// Load comments
		$.post('/Android/getComment/' + userId, {
			'post_id': postId, '_token': token
		}, function (result) {
			// Clear old comments
			let list = commentsArea.find('ul.we-comet');
			list.empty();

			// If the response key is 'response'
			let comments = result.response || [];
			comments.forEach(function (c) {
				// Simple time format
				let decodedText = decodeURIComponent(c.text.replace(/\+/g, ' '));
				let date = new Date(c.created_time * 1000).toLocaleString();
				// Build HTML
				let li = `
                    <li>
                        <div class="comet-avatar">
                            <img src="${c.picture}" alt="">
                        </div>
                        <div class="we-comment">
                            <h5><a href="javascript:void(0);" title="">${c.name}</a></h5>
                            <p>${decodedText}</p>
                            <div class="inline-itms">
                                <span>${date}</span>
                                <a class="we-reply" href="#" title="Reply">
                                    <i class="fa fa-reply"></i>
                                </a>
                                <a href="#" title="">
                                    <i class="fa fa-heart"></i><span>0</span>
                                </a>
                            </div>
                        </div>
                    </li>`;
				list.append(li);
			});

			// Optional: add "post-comment" input area
			list.append(`
				<li class="post-comment">
					<div class="comet-avatar">
						<img src="/user/images/resources/nearly1.jpg" alt="">
					</div>
					<div class="post-comt-box">
						<form method="post">
							<textarea placeholder="Post your comment"></textarea>
							<div class="add-smiles">
								<div class="uploadimage">
									<i class="fa fa-image"></i>
									<label class="fileContainer">
										<input type="file">
									</label>
								</div>
								<span class="em em-expressionless" title="add icon"></span>
								<div class="smiles-bunch">
									<i class="em em---1"></i>
									<i class="em em-smiley"></i>
									<i class="em em-anguished"></i>
									<i class="em em-laughing"></i>
									<i class="em em-angry"></i>
									<i class="em em-astonished"></i>
									<i class="em em-blush"></i>
									<i class="em em-disappointed"></i>
									<i class="em em-worried"></i>
									<i class="em em-kissing_heart"></i>
									<i class="em em-rage"></i>
									<i class="em em-stuck_out_tongue"></i>
								</div>
							</div>
							<button type="submit"></button>
						</form>
					</div>
				</li>

            `);

		})
			.always(function () {
				hideLoading(); // Tắt loader
			});

		return false;
	});

	



});