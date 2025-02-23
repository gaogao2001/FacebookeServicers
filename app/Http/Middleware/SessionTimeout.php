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
        $lastActivity = Session::get('lastActivityTime');
        $currentTime = time();
        
        if ($lastActivity && ($currentTime - $lastActivity) > $this->timeout) {
            Session::forget('account');
            Session::forget('lastActivityTime');
            return redirect('/login')->withErrors('Phiên làm việc của bạn đã hết hạn. Vui lòng đăng nhập lại.');
        }

        Session::put('lastActivityTime', $currentTime);
       

        return $next($request);
    }
}
