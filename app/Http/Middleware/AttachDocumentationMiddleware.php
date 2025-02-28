<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AttachDocumentationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Chỉ xử lý các phản hồi HTML
        if ($this->shouldAttachDocumentation($request, $response)) {
            // Lấy nội dung hiện tại
            $content = $response->getContent();

            // Đọc template documentation
            $docView = view('Document::documentation')->render();

            // Chèn vào cuối thẻ body
            $content = str_replace('</body>', $docView . '</body>', $content);

            // Cập nhật nội dung
            $response->setContent($content);
        }

        return $response;
    }

    private function shouldAttachDocumentation($request, $response)
    {
        // Kiểm tra điều kiện để thêm documentation
        return !$request->ajax()
            && $response->headers->get('content-type')
            && strpos($response->headers->get('content-type'), 'text/html') !== false;
    }
}
