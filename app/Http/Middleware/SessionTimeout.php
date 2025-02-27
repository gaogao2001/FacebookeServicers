<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class SessionTimeout
{
    protected $timeout = 7200; // 2 tiếng (7200 giây)

    public function handle(Request $request, Closure $next)
    {
        // Chỉ kiểm tra timeout khi người dùng đã đăng nhập
        if (Session::has('account')) {
            $lastActivity = Session::get('lastActivityTime');
            $currentTime = time();

            // Nếu chưa có lastActivityTime, khởi tạo nó
            if (!$lastActivity) {
                Session::put('lastActivityTime', $currentTime);
            }
            // Nếu đã có và vượt quá thời gian timeout
            else if (($currentTime - $lastActivity) > $this->timeout) {
                Session::forget('account');
                Session::forget('lastActivityTime');
                return redirect('/login')->withErrors('Phiên làm việc của bạn đã hết hạn. Vui lòng đăng nhập lại.');
            }
            // Cập nhật thời gian hoạt động mới nhất
            else {
                Session::put('lastActivityTime', $currentTime);
            }
        }

        return $next($request);
    }
}
