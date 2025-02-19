<div class="custom-modal-container ">
	<div class="modal fade" id="modal-lg">
		<div class="modal-dialog modal-lg">
			<div class="modal-content wrapper">
				<div class="card btn btn-success btn-fw" style="background-color: #28a745; border-color: #28a745;">
					<div class="card-header">
						<h3 class="modal-title ">Nạp Nick vào Hệ Thống</h3>
					</div>
				</div>
				<form action="{{ route('facebook.importAccount') }}" method="post">
					@csrf
					<input type="hidden" id="chosen_structure" name="chosen_structure" value="">
					<div class="modal-body">
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-4">
									<!-- text input -->
									<div class="form-group">
										<label>Loại Tài Khoản</label>
										<select class="form-control" id="account_type" name="account_type">
											<option value="Facebook">Facebook</option>
											<option value="Zalo">Zalo</option>
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<!-- text input -->
									<div class="form-group">
										<label>Chọn Nhóm Tài Khoản</label>
										<select class="form-control" id="select_group_account" name="select_group_account">
											<option value="default">default</option>
											<option value="newGroup">Nhóm Mới</option>
											@foreach($accountGroups as $group)
											<option value="{{ $group }}">{{ $group }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Nhóm sẽ được nạp</label>
										<input class="form-control" type="text" id="group_account_name"
											name="group_account_name" value="default" disabled="true">
									</div>
								</div>



							</div>

						</div>
						<div class="row" id="hqitFormImport">
							<div class="col-sm-12">
								<!-- select -->
								<div class="form-group">
									<textarea class="form-control" id="account_list" name="account_list" rows="3"
										placeholder="Vui lòng nhập dữ liệu nick vào đây...."></textarea>
								</div>
								<div class="form-group">
									<label>Chọn cấu trúc dữ liệu ( Theo thứ tự được check )<br> Ngoài ra bạn có thể dùng 1
										số tool thị trường để đồng bộ dữ liệu, vui lòng liên hệ support để được hỗ
										trợ</label>
									<div class="row">
										<div class="col-md-6">
											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-check-input form-check-structure"
														value="uid">UID
												</label>
											</div>
											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-check-input form-check-structure"
														value="qrcode">Qrcode
												</label>
											</div>
											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-check-input form-check-structure"
														value="password_email">Password Email
												</label>
											</div>
											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-check-input form-check-structure"
														value="cookies_pc">Cookies PC
												</label>
											</div>
											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-check-input form-check-structure"
														value="useragent_pc">Useragent PC
												</label>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-check-input form-check-structure"
														value="password">Password
												</label>
											</div>
											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-check-input form-check-structure"
														value="email">Email
												</label>
											</div>
											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-check-input form-check-structure"
														value="birthday"> Sinh nhật
												</label>
											</div>
											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-check-input form-check-structure"
														value="cookies_mobile"> Cookies Moblie
												</label>
											</div>
											<div class="form-check">
												<label class="form-check-label">
													<input type="checkbox" class="form-check-input form-check-structure"
														value="useragent_mobile"> Useragent Mobiles
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label>Cấu trúc đã chọn</label>
									<input type="text" class="form-control" name="chosen_structure" id="chosen_structure" readonly style="color: black;">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer justify-content-between">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button class="btn btn-primary BtnImportAccount">Nạp Nick</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@if(session('message'))
<script>
	document.addEventListener('DOMContentLoaded', function() {
		Swal.fire({
			title: 'Thông báo',
			text: "{{ session('message') }}",
			icon: "{{ session('status') ? 'success' : 'error' }}",
			confirmButtonText: 'OK'
		});
	});
</script>
@endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
	$(document).ready(function() {
		$('#select_group_account').change(function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'newGroup') {
				$('#group_account_name').val('').prop('disabled', false);
			} else {
				$('#group_account_name').val(selectedValue).prop('disabled', true);
			}
		});
	});
</script>

<script>
	$(document).ready(function() {
		let chosenStructureArr = []; // Mảng lưu trữ giá trị các checkbox đã chọn

		// Khi có thay đổi checkbox
		$(document).on('change', '.form-check-structure', function() {
			const value = $(this).val();

			if ($(this).is(':checked')) {
				// Thêm giá trị vào mảng nếu checkbox được chọn
				if (!chosenStructureArr.includes(value)) {
					chosenStructureArr.push(value);
				}
			} else {
				// Loại bỏ giá trị khỏi mảng nếu checkbox bị bỏ chọn
				chosenStructureArr = chosenStructureArr.filter(item => item !== value);
			}

			// Cập nhật giá trị cho thẻ input
			const chosenStructureValue = chosenStructureArr.join('|');
			$('#chosen_structure').val(chosenStructureValue); // Cập nhật thẻ input hidden
			$('input[name="chosen_structure"]').val(chosenStructureValue); // Cập nhật thẻ input text hiển thị

			// Đồng bộ lại trạng thái checkbox theo mảng đã lưu
			syncCheckboxState(chosenStructureArr);

			// Debug để kiểm tra giá trị
			console.log('Cấu trúc đã chọn:', chosenStructureValue);
		});

		// Hàm đồng bộ trạng thái checkbox với mảng `chosenStructureArr`
		function syncCheckboxState(chosenArray) {
			$('.form-check-structure').each(function() {
				const value = $(this).val();
				if (chosenArray.includes(value)) {
					$(this).prop('checked', true);
				} else {
					$(this).prop('checked', false);
				}
			});
		}

		// Khởi tạo đồng bộ trạng thái checkbox khi trang tải
		syncCheckboxState(chosenStructureArr);
	});
</script>