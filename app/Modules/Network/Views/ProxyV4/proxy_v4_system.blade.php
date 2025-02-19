@extends('admin.layouts.master')

@section('title', 'Proxy System')

@section('head.scripts')

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('modules/proxy/proxyV4/js/proxyv4Controler.js') }}" defer></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

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
                            <h4 class="card-title">Proxy<p>Số lượng: <span id="proxyCount">0</span></p>
                            </h4>
                            <div style="display: flex; gap: 10px; margin-top: 10px;">
                                <div class="input-group" style="max-width: 300px;">
                                    <input type="text" id="searchInput" class="edit-form-control form-control" placeholder="Tìm kiếm..." style=" border-radius: 20px;">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="background: transparent; border: none;"><i class="fas fa-search" style="color: black;"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button data-toggle="modal" data-target="#modal-CreareProxy" class="btn btn-inverse-primary btn-fw">Tạo Proxy</button>
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
                                        <th>Địa Chỉ IPV4</th>
										<th>Địa Chỉ IPV6</th>
                                        <th>Username</th>
                                        <th>MAC</th>
                                        <th>NIC</th>
                                        <th>Ifname</th>
                                        <th>IPv6</th>
                                        <th>Use Peer DNS</th>
                                        <th>Time Connect</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($configs as $config)
                                    <tr>
                                        <td class="ip-address">{{ $config['ip_address'] ?? 'N/A' }}</td>
										<td class="ipv6-address">{{ $config['ipv6_address'] ?? 'N/A' }}</td>
                                        <td>{{ $config['username'] ?? 'N/A' }}</td>
                                        <td>{{ $config['mac_address'] ?? 'N/A' }}</td>
                                        <td>{{ $config['nic'] ?? 'N/A' }}</td>
                                        <td>{{ $config['ifname'] ?? 'N/A' }}</td>
                                        <td>{{ $config['ipv6'] ? 'Yes' : 'No' }}</td>
                                        <td>{{ $config['usepeerdns'] ? 'Yes' : 'No' }}</td>
                                        <td class="time-connect">{{ $config['time_connected'] ?? 'N/A' }}</td>
                                        <td>
                                            <button class="btn btn-success btnConnect" data-name="{{ $config['ifname'] }}">
                                                <i class="fas fa-plug"></i> Connect
                                            </button>
                                            <button class="btn btn-info BtnReloadIp" data-name="{{ $config['ifname'] }}" style="{{ $config['connection_status'] ? '' : 'display:none;' }}">
                                                <i class="fas fa-sync-alt"></i> Reload IP
                                            </button>
                                            <button class="btn btn-warning btnDisconnect" data-name="{{ $config['ifname'] }}" style="{{ $config['connection_status'] ? '' : 'display:none;' }}">
                                                <i class="fas fa-unlink"></i> Disconnect
                                            </button>
                                            <button class="btn btn-danger btnDelete" data-name="{{ $config['ifname'] }}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
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
        <form method="post" action="{{ route('proxyv4.create') }}">
            <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Clone IP V4</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                <label for="limit">Số lượng</label>
                <input type="number" class="form-control" id="limit" name="limit" value="1">
                </div>
                <div class="form-group">
                <label for="selectInterface">Interface</label>
                <select class="form-control" id="selectInterface" name="selectInterface">
                    @foreach($interface as $doc)
                        @php
                            $arr = $doc->getArrayCopy();
                            unset($arr['_id']);
                        @endphp
                        @foreach($arr as $ifKey => $ifData)
                            @if(!empty($ifData['pppoe_username']) && !empty($ifData['pppoe_password']))
                                <option value="{{ $ifKey }}">{{ $ifKey }}</option>
                            @endif
                        @endforeach
                    @endforeach
                </select>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btnCreateProxyV4">
                <i class="fa fa-spinner fa-spin d-none"></i>
                Xác Nhận
                </button>
            </div>
            </div>
        </form>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function checkTimeConnect() {
        $('tbody tr').each(function() {
            var ifname = $(this).find('td').eq(5).text(); // Get the value of the ifname column
            if (ifname) {
                var row = $(this);
                $.ajax({
                    url: '/proxyv4/CheckTimeConnect',
                    method: 'POST',
                    data: { ifname: ifname },
                    success: function(response) {
                        if (response.status) {
                            row.find('td.ip-address').text(response.ipv4_address || 'N/A');
                            row.find('td.ipv6-address').text(response.ipv6_address || 'N/A');
                            //row.find('td.ipv6').text(response.ipv6_address ? 'Yes' : 'No');
                            row.find('td.time-connect').text(response.time_connected || 'N/A');
                        } else {
                            console.error('Failed to fetch time connect');
                        }
                    },
                    error: function() {
                        console.error('Failed to fetch time connect');
                    }
                });
            }
        });
    }

    setInterval(checkTimeConnect, 1000); // Run checkTimeConnect every second
});
</script>
@endsection