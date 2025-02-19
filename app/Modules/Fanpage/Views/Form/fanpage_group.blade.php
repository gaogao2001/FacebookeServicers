@foreach($groupsData as $uid => $data)
<div class="row">
    <div class="card mt-5 wrapper" style="backdrop-filter: blur(100px);">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">Danh Sách Nhóm </h6>
            <div class="input-group" style="max-width: 300px;">
                <input type="text" class="face-edit-form-control form-control search-input" placeholder="Tìm kiếm nhóm..." style="border-radius: 20px;">
            </div>
        </div>
        <div class="card-body">
            @if(isset($data->groups_list) && count($data->groups_list) > 0)
            <div class="row">
                @foreach($data->groups_list as $group)
                <div class="col-md-4 group" data-name="{{ $group->name }}">
                    <div >
                        <img src="{{ $group->avatar }}" class="card-img-top" alt="Avatar">

                        <h4>{{ $group->name }}</h4>

                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p>Không có nhóm nào.</p>
            @endif
        </div>
    </div>
</div>
@endforeach