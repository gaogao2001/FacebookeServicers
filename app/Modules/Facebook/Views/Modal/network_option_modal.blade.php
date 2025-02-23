<!-- Modal -->
<style>
    /* Toàn bộ modal */
    #networkOptionsModal .modal-content {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        color: #333333;
    }

    /* Header của modal */
    #networkOptionsModal .modal-header {
        border-bottom: 1px solid #e9ecef;
        padding: 15px 20px;
        background-color: #f8f9fa;
        color: #495057;
        font-size: 18px;
        font-weight: 500;
    }

    /* Body của modal */
    #networkOptionsModal .modal-body {
        padding: 20px;
    }

    /* Form input */
    #networkOptionsModal .form-control {
        background-color: #ffffff;
        color: #333333;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 10px;
        font-size: 14px;
    }

    #networkOptionsModal .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    /* Danh sách các tài khoản đã chọn */
    #networkOptionsModal .list-group-item {
        background-color: #f8f9fa;
        color: #333333;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 5px;
        padding: 10px;
    }

    /* Footer của modal */
    #networkOptionsModal .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
    }

    /* Nút */
    #networkOptionsModal .btn {
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 14px;
    }

    #networkOptionsModal .btn-secondary {
        background-color: #6c757d;
        color: #ffffff;
        border: none;
    }

    #networkOptionsModal .btn-secondary:hover {
        background-color: #5a6268;
    }

    #networkOptionsModal .btn-primary {
        background-color: #007bff;
        color: #ffffff;
        border: none;
    }

    #seleteAccountModal .btn-primary:hover {
        background-color: #0056b3;
    }

    /* Style cho select nhóm */
    #seleteAccountModal .form-group.group-select {
        display: none;
        margin-top: 10px;
    }

    .input-group-text {
        background-color: #0056b3;
        color: #ffffff;
        border: none;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .input-group-text:hover {
        background-color: #ffffff;
        color: #0056b3;
    }

    .uid-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .uid-select {
        width: 30%;
    }

    /* Tạo lưới 3 cột, mỗi cột chiếm 1/3 chiều rộng, khoảng cách giữa các mục là 10px */
    .uid-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .uid-item {
        position: relative;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 10px;
        color: #333333;
    }

    /* Nút xóa nhỏ nằm ở góc, nếu muốn */
    .uid-remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgb(15, 15, 15);
        color: rgb(238, 233, 233);
        border: none;
        border-radius: 50%;
        font-size: 11px;
        padding: 3px 6px;
        cursor: pointer;
    }

    .uid-remove-btn:hover {
        background: #c82333;
    }

    #networkOptionsModal .modal-dialog {
        max-width: 40%;
    }

    #networkOptionsModal .modal-body {
        max-height: 60vh;
        overflow-y: auto;
    }
</style>
<div class="modal fade" id="networkOptionsModal" tabindex="-1" aria-labelledby="networkOptionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Network Option</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <form class="d-flex justify-content-around">
                            @csrf
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="useProxy">
                                <label class="form-check-label" for="useProxy">Dùng Proxy</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="useSSH">
                                <label class="form-check-label" for="useSSH">Dùng SSH</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="useDcom">
                                <label class="form-check-label" for="useDcom">Dùng Dcom</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="useInterfaces">
                                <label class="form-check-label" for="useInterfaces">Dùng Interfaces</label>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Combined Form with Cards -->
                <form id="networkOptionsForm" style="display: none;">
                    @csrf
                    <div class="card mt-3" id="cardProxy" style="display: none;">
                        <div class="card-body">
                            <h5 class="card-title">Form Dùng Proxy</h5>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="proxyOption" id="proxyList" value="list">
                                    <label class="form-check-label" for="proxyList">Dùng Proxy List</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="proxyOption" id="proxySystem" value="system">
                                    <label class="form-check-label" for="proxySystem">Dùng Proxy Hệ Thống</label>
                                </div>

                                <div id="proxyListContent" style="display: none;">
                                    <div class="form-group">
                                        <label for="proxyTextArea">Danh sách Proxy:</label>
                                        <textarea class="form-control" id="proxyTextArea" name="proxyListTextArea" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ...existing code... -->
                    <div class="card mt-3" id="cardSSH" style="display: none;">
                        <div class="card-body">
                            <h5 class="card-title"></h5>
                            <div>
                                <!-- SSH form content -->
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3" id="cardDcom" style="display: none;">
                        <div class="card-body">
                            <h5 class="card-title"></h5>
                            <div>
                                <!-- Dcom form content -->
                            </div>
                        </div>
                    </div>
                    <div class="card mt-3" id="networkInterfacesForm" style="display: none;">
                        <div class="card-body">
                            <h5 class="card-title">Network Interfaces</h5>
                            <div class="form-group">
                                @foreach($interfaces as $key => $interface)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="networkInterfaceOption_{{ $key }}" name="networkInterface" value="{{ $key }}">
                                    <label class="form-check-label" for="networkInterfaceOption_{{ $key }}">
                                        {{ $key }} - {{ $interface->macAddress }}
                                        ({{ $interface->status ? 'Active' : 'Inactive' }})
                                        {{ $interface->isDefault ? '(Default)' : '' }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>          
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary" id="saveButton" style="display: none;">Lưu Thiết Lập</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle checkbox click
        $('.form-check-input[type="checkbox"]').on('change', function() {
            // Uncheck other checkboxes
            $('.form-check-input[type="checkbox"]').not(this).prop('checked', false);

            // Hide all cards
            $('#cardProxy, #cardSSH, #cardDcom, #networkInterfacesForm').hide();

            // Hide all proxy-specific content
            $('#proxyListContent, #proxySystemContent').hide();

            // Show the corresponding card and form
            $('#networkOptionsForm').hide();
            if ($('#useProxy').is(':checked')) {
                $('#networkOptionsForm').show();
                $('#cardProxy').show();
            } else if ($('#useSSH').is(':checked')) {
                $('#networkOptionsForm').show();
                $('#cardSSH').show();
            } else if ($('#useDcom').is(':checked')) {
                $('#networkOptionsForm').show();
                $('#cardDcom').show();
            } else if ($('#useInterfaces').is(':checked')) {
                $('#networkOptionsForm').show();
                $('#networkInterfacesForm').show();
            }

            // Show or hide the save button
            if ($('.form-check-input[type="checkbox"]:checked').length > 0) {
                $('#saveButton').show();
            } else {
                $('#saveButton').hide();
            }
        });

        $('input[name="proxyOption"]').on('change', function() {
            // Display corresponding content
            if ($('#proxySystem').is(':checked')) {
                $('#proxySystemContent').show();
                $('#proxyListContent').hide();
            } else if ($('#proxyList').is(':checked')) {
                $('#proxyListContent').show();
                $('#proxySystemContent').hide();
            } else {
                $('#proxyListContent, #proxySystemContent').hide();
            }
        });

        $('#openNetworkOption').on('click', function(e) {
            e.preventDefault();
            var networkOptionsModal = new bootstrap.Modal(document.getElementById('networkOptionsModal'));
            networkOptionsModal.show();
        });

        $('#networkOptionsForm').on('submit', function(e) {
            e.preventDefault();

            let useInterfaces = $('#useInterfaces').is(':checked');
            let selectedUids = $('#faceAcountTable tbody .checkItem:checked').map(function() {
                return $(this).val();
            }).get();

            if ($('#useInterfaces').is(':checked')) {
                let selectedInterface = $('input[name="networkInterface"]:checked').val();
                if (!selectedInterface) {
                    Swal.fire('Error', 'Vui lòng chọn một Interface.', 'error');
                    return;
                }

                $.ajax({
                    url: window.routes.updateNetworkUse,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        uids: selectedUids,
                        type: 'interfaces',
                        interface: selectedInterface,
                    },
                    success: function(response) {
                        Swal.fire('Success', response.message, 'success');
                        $('#networkOptionsModal').modal('hide');
                    },
                    error: function(xhr) {
                        let errorMsg = 'Đã xảy ra lỗi.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errorMsg, 'error');
                    }
                });
            }

            let useProxy = $('#useProxy').is(':checked');

            if (useProxy) {
                let proxyOption = $('input[name="proxyOption"]:checked').val();
                if (proxyOption === 'system') {
                    $.ajax({
                        url: '/proxySplit',
                        type: 'GET',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            proxy: $('#proxy').val(),
                            number: $('#number').val(),
                        },
                        success: function(data) {
                            console.log(data);
                            Swal.fire({
                                icon: 'success',
                                text: 'Proxy đã được chia thành công.',
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                text: 'Đã xảy ra lỗi khi chia proxy.',
                            });
                        }
                    });
                } else if (proxyOption === 'list') {
                    let proxyList = $('#proxyTextArea').val();
                    let number = $('#number').val();
                    $.ajax({
                        url: window.routes.updateNetworkUseList,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            proxyList: proxyList,
                            number: $('#number').val(),
                        },
                        success: function(data) {
                            console.log(data);
                            Swal.fire({
                                icon: 'success',
                                text: 'Danh sách Proxy đã được xử lý thành công.',
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                text: 'Đã xảy ra lỗi khi xử lý danh sách proxy.',
                            });
                        }
                    });
                }
            }
        });

        // Remove the populateNetworkInterfaces function and its call
    });
</script>