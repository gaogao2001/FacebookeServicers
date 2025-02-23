@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@elseif(session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif
<form method="POST" action="{{ route('fanpage-manager.update', $fanpage->_id) }}">
    @csrf
    @method('PUT')
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="uid_controler">UID</label>
            <input id="uid_controler" type="text" class="face-edit-form-control form-control" name="uid_controler" value="{{$fanpage->uid_controler}} ">
        </div>
        <div class="form-group col-md-6">
            <label for="page_id">Page ID</label>
            <input type="text" class="face-edit-form-control form-control" name="page_id" value=" {{$fanpage->page_id}}">
        </div>
        <div class="form-group col-md-6">
            <label for="page_name">Tên fanpage</label>
            <input id="page_name" type="text" class="face-edit-form-control form-control" name="page_name" value="{{$fanpage->page_name   }} ">
        </div>
        <div class="form-group col-md-6">
            <label for="phone">Access Token</label>
            <input id="access_token" type="phone" class="face-edit-form-control form-control" name="access_token" value="{{$fanpage->access_token}} ">
        </div>

        <div class="mt-5 text-right">
            <button class="btn btn-primary" type="submit" style="color: #000000;">Lưu dữ liệu</button>
        </div>
    </div>
</form>