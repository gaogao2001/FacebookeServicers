<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Modules\Document\Controllers\DocumentController;


Route::post('/document', [DocumentController::class, 'create']);

// Route GET động cho tài liệu dựa trên tên page (pageId)
Route::middleware(['web'])->group(function () {
    // Middleware để đính kèm documentation vào mỗi trang
    Route::middleware('attachDocumentation')->group(function () {
        // Các route khác trong hệ thống
    });
});
