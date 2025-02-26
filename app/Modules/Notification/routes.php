<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Notification\Controllers\NotificationController;


Route::get('/notification-page', [NotificationController::class, 'notificationPage']);

Route::get('/notification', [NotificationController::class, 'notification']);
Route::post('/notifications', [NotificationController::class, 'showNotificationsByIds']);

Route::post('/delete-notifications', [NotificationController::class, 'deleteNotificationByIds']);


