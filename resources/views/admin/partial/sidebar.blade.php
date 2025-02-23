<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
        <a class="sidebar-brand brand-logo" href="index.html">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 368.5 48">
                <text x="50%" y="50%" font-family="Arial" font-size="32" font-weight="bold" fill="#FFFFFF" dominant-baseline="middle" text-anchor="middle">
                    Mega Bot
                </text>
            </svg>
        </a>
        <a class="sidebar-brand brand-logo-mini" href="index.html">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 368.5 48">
                <text x="50%" y="50%" font-family="Arial" font-size="32" font-weight="bold" fill="#FFFFFF" dominant-baseline="middle" text-anchor="middle">
                    Mega Bot
                </text>
            </svg>
        </a>
    </div>
    @php
    $accessibleMenus = session('account.menu')  ?? [];
    @endphp
    <!-- Phần đầu giữ nguyên -->
    <ul class="nav">
        @foreach($accessibleMenus as $menuName => $menuData)
        @if(isset($menuData['children']))
        <li class="nav-item menu-items">
            <a class="nav-link" data-bs-toggle="collapse" href="#{{ Str::slug($menuName) }}" aria-expanded="false" aria-controls="{{ Str::slug($menuName) }}">
                <span class="menu-icon">
                    <i class="mdi {{ $menuData['icon'] }}"></i>
                </span>
                <span class="menu-title">{{ $menuName }}</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="{{ Str::slug($menuName) }}">
                <ul class="nav flex-column sub-menu">
                    @foreach($menuData['children'] as $childName => $childData)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ $childData['url'] }}">
                            {{ $childName }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </li>
        @else
        <li class="nav-item menu-items {{ request()->is(ltrim($menuData['url'], '/')) ? 'active' : '' }}">
            <a class="nav-link" href="{{ $menuData['url'] }}">
                <span class="menu-icon">
                    <i class="mdi {{ $menuData['icon'] }}"></i>
                </span>
                <span class="menu-title">{{ $menuName }}</span>
            </a>
        </li>
        @endif
        @endforeach
    </ul>
</nav>