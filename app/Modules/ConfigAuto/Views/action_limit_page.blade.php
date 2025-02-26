@extends ('admin.layouts.master')

@section('title', 'Config Auto')

@section('head.scripts')
<link rel="stylesheet" href="{{ asset('modules/config_auto/css/auto_config.css') }}">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection

@section('content')

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Thành công',
        text: "{{ session('success') }}",
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif
<div class="content-wrapper d-flex justify-content-center align-items-center" style="background: #191c24; min-height: 100vh;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <!-- Bảng danh sách quyền -->
            <div class="col-md-10">
                <div class="card wrapper">
                    <div class="card-body">
                        <form action="{{ route('facebook.action.limit.save') }}" method="post">
                            @csrf
                            <h4 class="card-title title">Cấu hình giới hạn tương tác</h4>
                            <hr>
                            <div class="configurations">
                                @foreach($configLimit as $key => $value)
                                <div class="form-group config-details">
                                    <div class="feature">
                                        <h5>{{ ucfirst(str_replace('_', ' ', $key)) }}</h5>
                                    </div>
                                    <div class="inputs">
                                        <div class="input-group">
                                            <label for="{{ $key }}">Số lượng</label>
                                            <input type="number" id="{{ $key }}" class="edit-form-control" name="{{ $key }}" value="{{ $value }}" style="width: 30%;">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Lưu cấu hình</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection