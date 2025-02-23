<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    private function getAllUrls($menus)
    {
        $urls = [];

        foreach ($menus as $menu) {
            if (isset($menu['url'])) {
                $urls[] = $menu['url'];
            }

            if (isset($menu['children'])) {
                $urls = array_merge($urls, $this->getAllUrls($menu['children']));
            }
        }

        return $urls;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $account = session('account');

        // Kiểm tra xem người dùng đã đăng nhập hay chưa
        if (!$account) {
            return redirect('/login')->withErrors('Vui lòng đăng nhập.');
        }

        // Lấy danh sách menu được phép từ session
        $accessibleMenus = $account['menu'] ?? [];

        // Lấy route hiện tại
        $currentRoute = '/' . ltrim($request->path(), '/');

        // Kiểm tra nếu route không được phép truy cập
        $allowedRoutes = $this->getAllUrls($accessibleMenus);

        if (!in_array($currentRoute, $allowedRoutes)) {
            return redirect('/admin/dashboard')->withErrors('Bạn không có quyền truy cập vào trang này.');
        }

        return $next($request);
    }
}
