<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLocalIp
{
    /**
     * Xử lý request và kiểm tra IP
     */
    public function handle(Request $request, Closure $next)
    {
        $clientIp = $request->ip();

        $isLocal = 
            $clientIp === '127.0.0.1' ||
            $clientIp === '::1' ||
            substr($clientIp, 0, 8) === '192.168.' ||
            substr($clientIp, 0, 7) === '10.' ||
            (substr($clientIp, 0, 3) === '172' &&
             intval(substr($clientIp, 4, 2)) >= 16 &&
             intval(substr($clientIp, 4, 2)) <= 31);

        if (!$isLocal) {
            return response()->json(['error' => 'Không được phép upload từ IP không hợp lệ.'], 403);
        }

        return $next($request);
    }
}