@extends('admin.layouts.master')

@section('title', 'Proxy System')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('assets/admin/js/toast.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('assets/admin/css/toast.css') }}">

<script>
    const reloadIpUrl = "{{ route('proxyv6.reloadIp', ['id' => '_id']) }}";
    const checkIpUrl = "{{ route('proxyv6.checkIp', ['id' => '_id']) }}";
    const deleteUrl = "{{ route('proxyv6.delete', ['id' => '_id']) }}";

    const checkStatusUrl = "{{ route('proxyv6.check_proxy_status') }}";
    $(document).ready(function () {
        function checkProxyStatus() {
            let listProxies = $('.row-proxy-v6[data-status="pending"]');
            let ids = [];
            listProxies.each(function() {
                ids.push($(this).attr('data-id'));
            });

            if (ids.length <= 0) {
                // if all proxy port is success => clear check status interval
                clearInterval(intervalId);
            }

            $.ajax({
                url: checkStatusUrl,
                type: 'POST',
                data: {
                    ids: ids,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function(response) {
                    $('#total_proxy_success').text(response.total_success);
                    $('#total_proxy').text(response.total_proxy);
                    let listProxies = response.list_proxies;
                    listProxies.forEach(function (value, index) {
                        $('.row-proxy-v6[data-id="' + value.config_name + '"]').attr('data-status', value.port_status);
                        let portStatus = '<span class="badge badge-danger">Pending</span>';
                        if (value.port_status == 'success') {
                            portStatus = '<span class="badge badge-success">Success</span>';
                        }
                        $('.row-proxy-v6[data-id="' + value.config_name + '"]').find('.col-port-status').html(portStatus);
                    });

                    $('#proxyCount').text(`(${response.total_proxy})`);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        const intervalId = setInterval(checkProxyStatus, 10000);
    });
</script>
<script src="{{ asset('modules/proxy/proxyV6/js/proxy.js') }}" defer></script>

<style>
    .wrapper {
        background: transparent;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 30px 40px;
    }

    .edit-form-control {
        width: 100%;
        background-color: transparent;
        color: #FFFFFF;
        border: none;
        outline: none;
        border: 2px solid rgba(255, 255, 255, .2);
        font-size: 16px;
        padding: 20px;
        box-shadow: #000000 0 0 10px;
    }

    .edit-form-control:hover,
    .edit-form-control:focus {
        background-color: #FFFFFF;
        color: black;
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
                        <div>
                            <h4 class="card-title">Proxy <p>Số lượng: <span id="proxyCount">0</span></p></h4>
                            <div style="display: flex; gap: 10px; margin-top: 10px;">
                                <div class="input-group" style="max-width: 300px;">
                                    <input type="text" id="searchInput" class="edit-form-control form-control" placeholder="Tìm kiếm..." style=" border-radius: 20px;">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="background: transparent; border: none;"><i class="fas fa-search" style="color: black;"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>

                            <button data-toggle="modal" data-target="#modal-CreareProxy" class="btn btn-inverse-primary btn-fw" id="addProxy">Tạo Proxy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card wrapper">
                    <div class="card-body">
                        <button type="button" data-url="{{ route('proxyv6.deleteAll') }}" class="btn btn-inverse-danger btn-fw BtnClearAllProxy">Xoá tất cả</button>
                        <div class="table-responsive" style="padding-top: 20px; max-height: 600px; overflow-y: auto;" id="tableContainer">
                            <table id="proxyTable" class="table">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Config Name</th>
                                        <th>Eth</th>
                                        <th>Interface</th>
                                        <th>Port</th>
                                        <th>Cập nhật cuối </th>
                                        <th>Trạng thái</th>
                                        <th>Trạng thái port</th>
                                        <th>Tùy chọn</th>
                                    </tr>
                                </thead>
                                <tbody id="proxyList">

                                </tbody>
                            </table>
                            <div id="loading" style="text-align: center; display: none;">Đang tải...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-CreareProxy">
    <div class="modal-dialog">
        <form method="post" action="{{ route('proxyv6.create') }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Clone IP V6</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="interfaceSelect">Chọn Interface</label>
                        <select class="form-control" id="interfaceSelect" name="interface">
                            @if(!empty($ListInterface))
                                @foreach($ListInterface as $interface)
                                    <option value="{{ $interface['name'] }}">
                                        {{ $interface['name'] }} (IPv4: {{ $interface['ipv4'] ?? 'Không có IPv4' }} - IPv6: {{ $interface['ipv6'] ?? 'Không có IPv6' }})
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>Không có interface nào</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="limit">Số lượng</label>
                        <input type="number" class="form-control" id="limit" name="limit" value="100">
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary BtnCreateProxyV6">
                        <i class="fa fa-spinner fa-spin d-none"></i>
                        Xác Nhận
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection