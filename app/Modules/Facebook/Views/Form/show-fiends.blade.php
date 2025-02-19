<div class="row">
    <div class="card mt-5 wrapper" style=" backdrop-filter: blur(100px); ">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">Danh Sách {{ isset($accounts->friends->count) ? $accounts->friends->count : ($accounts->friends ?? 0) }} Bạn Bè</h6>
            <div class="input-group" style="max-width: 300px;">
                <input type="text" class="face-edit-form-control form-control search-input" placeholder="Tìm kiếm bạn bè..." style="border-radius: 20px;">
                <div class="input-group-append">
                    
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="friends-grid">
                @if(isset($accounts->friends->friends_list) && count($accounts->friends->friends_list) > 0)
                @foreach($accounts->friends->friends_list as $friend)
                <div class="friend" data-name="{{ $friend->name }}">
                    <img src="{{ $friend->profile_picture }}" alt="Avatar">
                    <h4>{{ $friend->name }}</h4>
                </div>
                @endforeach
                @else
                {{ null }}
                @endif
            </div>
        </div>
    </div>
</div>