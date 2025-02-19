<div class="row">
    <div class="card mt-5 wrapper" style=" backdrop-filter: blur(100px); ">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">Danh Sách {{ isset($accounts->groups->count) ? $accounts->groups->count : ($accounts->groups ?? 0) }} Nhóm </h6>
            <div class="input-group" style="max-width: 300px;">
                <input type="text" class="face-edit-form-control form-control search-input" placeholder="Tìm kiếm bạn bè..." style="border-radius: 20px;">
            </div>
        </div>
        <div class="card-body">
            @if(isset($accounts->groups->groups_list) && count($accounts->groups->groups_list) > 0)
            <div class="friends-grid">
                @foreach($accounts->groups->groups_list as $group)
                <div class="friend" data-name="{{ $group->name }}">
                    <!-- Sử dụng ảnh mặc định nếu avatar là null -->
                    <img src="{{ $group->avatar ?? asset('assets/admin/img/2043173.png') }}" alt="Avatar">
                    <h4>{{ $group->name }}</h4>
                </div>
                @endforeach
            </div>
            @else
            null
            @endif
        </div>
    </div>
</div>
