@extends('admin.layouts.master')

@section('title', 'Quản lý Crontab')

@section('head.scripts')
<style>
    .wrapper {
        background: transparent;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 30px 40px;
    }

    .form-control::placeholder {
        color: #888888;
    }

    .form-control:hover,
    .form-control:focus {
        background-color: #FFFFFF;
        color: #000000;
    }

    .form-control select {
        color: #000000;
        background-color: #FFFFFF;
    }

    .is-invalid {
        border-color: red !important;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper " style="background: #191c24;">
    <div class="container-fluid" style="padding-top:20px;">
        <div class="row" style="padding-top:20px;">
            <div class="col-md-12 grid-margin">
                <div class="card wrapper">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Quản lí Crontab</h4>
                        <div class="button-container" style="display: flex; justify-content: flex-end; margin-right:50px;">
                            <button type="button" class="btn btn-outline-primary btn-fw" data-toggle="modal" data-target="#cronModal">
                                <i class="fa fa-plus"></i> Thêm Crontab
                            </button>
                            <!-- New "Xóa hết" button added -->
                            <button type="button" class="btn btn-outline-danger btn-fw delete-all-cron" style="margin-left:10px;">
                                <i class="fa fa-trash-alt"></i> Xóa hết
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card wrapper">
                    <div class="card-body">

                        <div class="table-responsive" style="padding-top: 20px; max-height: 600px; overflow-y: auto;">
                            <table id="cronpathTable" class="table">
                                <thead style="color: black; font-weight: bold !important;">
                                    <tr>
                                        <th style="width:3%;"><input type="checkbox" id="selectAllCron"></th>
                                        <th style="width:3%;">STT</th>
                                        <th>Command</th>

                                        <th style="width:15%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="cronpathList">
                                    @foreach ($CronList as $index => $cron)
                                        <tr>
                                            <td><input type="checkbox" name="selectedCrons[]" value="{{ $index }}"></td>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $cron }}</td>
                                            <td>
                                                <button type="button" class="btn btn-outline-primary btn-fw edit-cron" data-id="{{ $index }}">
                                                    <i class="fa fa-edit"></i> Sửa
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-fw delete-cron" data-id="{{ $index }}">
                                                    <i class="fa fa-trash"></i> Xóa
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Crontab -->
<div class="modal fade" id="cronModal" tabindex="-1" role="dialog" aria-labelledby="cronModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="#" method="post">
            @csrf
            <input type="hidden" id="cronId" name="cronId" value="">
            <div class="modal-content wrapper">
                <div class="modal-header">
                    <h5 class="modal-title" id="cronModalLabel">Thiết lập Crontab</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Các trường nhập cron -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Phút:</label>
                        <div class="col-sm-10">
                            <input type="text" id="minute" name="minute" class="form-control" placeholder="* hoặc 0-59" onkeyup="updateCrontab()">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Giờ:</label>
                        <div class="col-sm-10">
                            <input type="text" id="hour" name="hour" class="form-control" placeholder="* hoặc 0-23" onkeyup="updateCrontab()">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Ngày:</label>
                        <div class="col-sm-10">
                            <input type="text" id="day" name="day" class="form-control" placeholder="* hoặc 1-31" onkeyup="updateCrontab()">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tháng:</label>
                        <div class="col-sm-10">
                            <input type="text" id="month" name="month" class="form-control" placeholder="* hoặc 1-12" onkeyup="updateCrontab()">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Thứ:</label>
                        <div class="col-sm-10">
                            <input type="text" id="weekday" name="weekday" class="form-control" placeholder="* hoặc 0-6 (CN=0)" onkeyup="updateCrontab()">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Lệnh:</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" id="command" name="command" class="form-control" placeholder="Lệnh thực thi" value="php /var/www/FacebookService/CrontabService" onkeyup="updateCrontab()">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="showProjectFileSelector()">Select File</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-primary" role="alert">
                        <strong>Xem trước lệnh Crontab:</strong>
                        <pre id="crontab-preview">* * * * * echo 'Hello World'</pre>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Project File Selector Modal -->
<div class="modal fade" id="fileSelectorModal" tabindex="-1" aria-labelledby="fileSelectorLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content wrapper">
            <div class="modal-header">
                <button id="backButton" type="button" class="btn btn-secondary" style="display:none; margin-right:10px;" onclick="goBack()">Back</button>
                <h5 class="modal-title" id="fileSelectorLabel">Chọn File trong dự án</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <p>Đường dẫn: <span id="currentPath">/var/www/FacebookService/CrontabService</span></p>
                <div id="fileList" class="list-group">
                    <!-- File/folder list loaded via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>


    // Existing functions: loadFileList, showProjectFileSelector, goBack, selectProjectFile, updateCrontab, validateCronInputs...
    const fileStructure = {!! json_encode($phpFilesTree) !!};

    let currentFolder = fileStructure;
    let pathStack = [];

    function loadFileList() {
        const fileListContainer = document.getElementById("fileList");
        fileListContainer.innerHTML = "";
        document.getElementById("currentPath").innerText = currentFolder.name;
        document.getElementById("backButton").style.display = pathStack.length ? "inline-block" : "none";
        if (currentFolder.children) {
            currentFolder.children.forEach(item => {
                let a = document.createElement('a');
                a.href = "javascript:void(0)";
                a.className = "list-group-item form-control list-group-item-action";
                a.innerText = item.name;
                if (item.type === "folder") {
                    a.onclick = function() {
                        pathStack.push(currentFolder);
                        currentFolder = item;
                        loadFileList();
                    };
                } else if (item.type === "file") {
                    a.onclick = function() {
                        selectProjectFile(item.path);
                    };
                }
                fileListContainer.appendChild(a);
            });
        }
    }

    function showProjectFileSelector() {
        currentFolder = fileStructure;
        pathStack = [];
        loadFileList();
        $('#fileSelectorModal').modal('show');
    }

    function goBack() {
        if (pathStack.length) {
            currentFolder = pathStack.pop();
            loadFileList();
        }
    }

    function selectProjectFile(filePath) {
        let commandInput = document.getElementById("command");
        commandInput.value = 'php ' + filePath;
        updateCrontab();
        $('#fileSelectorModal').modal('hide');
    }

    function validateInputField(id, min, max) {
        const input = document.getElementById(id);
        let value = input.value.trim();
        if (value === "*" || value === "") {
            input.classList.remove("is-invalid");
            return true;
        }
        let num = Number(value);
        if (isNaN(num) || num < min || num > max) {  // Corrected condition here
            input.classList.add("is-invalid");
            return false;
        } else {
            input.classList.remove("is-invalid");
            return true;
        }
    }

    function validateCronInputs() {
        let valid = true;
        if (!validateInputField("minute", 0, 59)) {
            valid = false;
        }
        if (!validateInputField("hour", 0, 23)) {
            valid = false;
        }
        if (!validateInputField("day", 1, 31)) {
            valid = false;
        }
        if (!validateInputField("month", 1, 12)) {
            valid = false;
        }
        if (!validateInputField("weekday", 0, 6)) {
            valid = false;
        }
        return valid;
    }

    function updateCrontab() {
        let minute = document.getElementById("minute").value || "*";
        let hour = document.getElementById("hour").value || "*";
        let day = document.getElementById("day").value || "*";
        let month = document.getElementById("month").value || "*";
        let weekday = document.getElementById("weekday").value || "*";
        let command = document.getElementById("command").value || "echo 'Hello World'";
        document.getElementById("crontab-preview").innerText = ` ${command}`;
    }

    document.querySelectorAll('#cronModal input').forEach(input => {
        input.addEventListener('focus', function() {
            input.classList.remove("is-invalid");
        });
    });

    // 1) Click "Sửa" button -> load data -> fill modal -> open modal
    $(document).on('click', '.edit-cron', function() {
        let index = $(this).data('id');
        let cronCommand = $(this).closest('tr').find('td:nth-child(3)').text().trim();
        let tokens = cronCommand.split(/\s+/);
        
        if (tokens.length >= 6) {
            // Populate schedule inputs
            $('#minute').val(tokens[0]);
            $('#hour').val(tokens[1]);
            $('#day').val(tokens[2]);
            $('#month').val(tokens[3]);
            $('#weekday').val(tokens[4]);
            // Set command input with the remaining tokens
            $('#command').val(tokens.slice(5).join(' '));
        } else {
            // Fallback: keep the entire command if not matching expected structure
            $('#command').val(cronCommand);
        }
        
        $('#cronId').val(index);
        $('#cronModal').modal('show');
    });

    function resetCronForm() {
        $('#cronId').val('');
        $('#command').val('php /var/www/FacebookService/CrontabService');
        $('#minute').val('');
        $('#hour').val('');
        $('#day').val('');
        $('#month').val('');
        $('#weekday').val('');
        updateCrontab();
    }

    // 2) Submit form -> update existing record if currentCronId != null
    $('#cronModal form').on('submit', function(e) {
        e.preventDefault();
        
        if (!currentCronId) return; // Or handle 'new' mode separately
        let command = $('#command').val();
        $.ajax({
            url: '/crontab-update/' + currentCronId,
            type: 'PUT',
            data: {
                command: command
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire("", res.message, "success");
                    $('#cronModal').modal('hide');
                    fetchCrontabs(); // Reload table
                    resetCronForm();
                    currentCronId = null;
                } else {
                    Swal.fire("Lỗi", res.message, "error");
                }
            },
            error: function() {
                Swal.fire("Lỗi", "Cập nhật không thành công.", "error");
            }
        });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('click', '.delete-cron', function() {
        let index = $(this).data('id');
        Swal.fire({
            title: "Xác nhận",
            text: "Bạn có chắc muốn xóa?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Hủy"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post({
                    url: '/crontab-delete',
                    data: {
                        _token: '{{ csrf_token() }}',
                        index: index
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire("", res.message, "success").then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire("Lỗi", res.message, "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Lỗi", "Xóa không thành công.", "error");
                    }
                });
            }
        });
    });

    $('#cronModal').on('hidden.bs.modal', function () {
       resetCronForm();
   });

    function fetchCrontabs() {
        $.ajax({
            url: '{{ route("crontab-page") }}',
            type: 'GET',
            success: function(response) {
                const tbody = document.getElementById("cronpathList");
                tbody.innerHTML = "";
                response.CronList.forEach(function(cron, index) {
                    let tr = document.createElement("tr");

                    let tdCheckbox = document.createElement("td");
                    tdCheckbox.innerHTML = `<input type="checkbox" name="selectedCrons[]" value="${index}">`;

                    let tdIndex = document.createElement("td");
                    tdIndex.innerText = index + 1;

                    let tdCommand = document.createElement("td");
                    tdCommand.innerText = cron;

                    let tdActions = document.createElement("td");
                    tdActions.innerHTML = `
                        <button type="button" class="btn btn-outline-primary btn-fw edit-cron" data-id="${index}">
                            <i class="fa fa-edit"></i> Sửa
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-fw delete-cron" data-id="${index}">
                            <i class="fa fa-trash"></i> Xóa
                        </button>
                    `;

                    tr.appendChild(tdCheckbox);
                    tr.appendChild(tdIndex);
                    tr.appendChild(tdCommand);
                    tr.appendChild(tdActions);
                    tbody.appendChild(tr);
                });
            },
            error: function(xhr, status, error) {
                console.error(error);
                Swal.fire("Lỗi", "Có lỗi khi tải dữ liệu.", "error");
            }
        });
    }

    $(document).on('click', '.edit-cron', function() {
        let index = $(this).data('id');
        let cronCommand = $(this).closest('tr').find('td:nth-child(3)').text();

        $('#cronId').val(index);
        $('#command').val(cronCommand);

        $('#cronModal').modal('show');
    });

    $(document).on('click', '.delete-cron', function() {
        let index = $(this).data('id');
        Swal.fire({
            title: "Xác nhận",
            text: "Bạn có chắc muốn xóa?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Hủy"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post({
                    url: '{{ route("crontab-delete") }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        index: index
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire("", res.message, "success").then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire("Lỗi", res.message, "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Lỗi", "Xóa không thành công.", "error");
                    }
                });
            }
        });
    });

    // Remove duplicate form submission blocks so that only one listener is active.
    // Unified event listener for both new and update actions:
    $('#cronModal form').off('submit').on('submit', function(e) {
        e.preventDefault();
        if (!validateCronInputs()) {
            Swal.fire("Có lỗi", "Có lỗi trong định dạng ngày, giờ, ngày tháng hoặc thứ. Vui lòng kiểm tra lại.", "error");
            return;
        }
        let minute = $('#minute').val() || "*";
        let hour = $('#hour').val() || "*";
        let day = $('#day').val() || "*";
        let month = $('#month').val() || "*";
        let weekday = $('#weekday').val() || "*";
        let commandInput = $('#command').val().trim();
        // Nếu command đã có đủ 5 thông số cron, loại bỏ chúng
        if (/^((\*|\d+|\d+-\d+|\d+(,\d+)*|(?:\*\/\d+))\s+){5}/.test(commandInput)) {
            commandInput = commandInput.replace(/^((\*|\d+|\d+-\d+|\d+(,\d+)*|(?:\*\/\d+))\s+){5}/, '');
        }

        let fullCommand = `${minute} ${hour} ${day} ${month} ${weekday} ${commandInput}`;

        
        let index = $('#cronId').val();
        let url = index ? '/crontab-update/' + index : '{{ route("crontab-submit") }}';
        let type = index ? 'PUT' : 'POST';
        let data = {
            _token: '{{ csrf_token() }}',
            command: fullCommand
        };

        $.ajax({
            url: url,
            type: type,
            data: data,
            success: function(response) {
                Swal.fire("", response.message, "success").then(function() {
                    location.reload();
                });
                $('#cronModal').modal('hide');
                $('#cronId').val('');
            },
            error: function(xhr, status, error) {
                console.error(error);
                Swal.fire("Lỗi", "Cập nhật không thành công.", "error");
            }
        });
    });

    // New click event handler for "Xóa hết" button:
    $(document).on('click', '.delete-all-cron', function() {
        Swal.fire({
            title: "Xác nhận",
            text: "Bạn có chắc muốn xóa tất cả các cron jobs?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Hủy"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post({
                    url: '{{ route("crontab-delete-all") }}',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire("", res.message, "success").then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire("Lỗi", res.message, "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Lỗi", "Xóa không thành công.", "error");
                    }
                });
            }
        });
    });
</script>
@endsection