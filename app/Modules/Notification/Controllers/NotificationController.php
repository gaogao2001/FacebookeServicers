<?php

namespace App\Modules\Notification\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\Notification\NotificationRepositoryInterface;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function notificationPage()
    {
        return view('Notification::notification');
    }

    public function notification(Request $request)
    {

        // Nếu có tham số latest, trả về danh sách thông báo mới nhất (ví dụ chỉ lấy 5 thông báo)
        if ($request->has('latest') && $request->input('latest') == 'true') {
            $limit = 100;
            $notifications = $this->notificationRepository->getLatestNotifications($limit);
            return response()->json([
                'data' => $notifications
            ]);
        }
        $perPage = 15;
        $page = (int) $request->input('page', 1);

        // Đảm bảo $page là số hợp lệ
        if ($page < 1) {
            return response()->json(['message' => 'Invalid page number'], 200);
        }

        $notifications = $this->notificationRepository->getAllNotificationData($perPage, $page);

        $total = $this->notificationRepository->countNotificationData();

        $lastPage = max(1, ceil($total / $perPage));

        if ($page > $lastPage) {
            return response()->json(['message' => 'Page not found'], 200);
        }

        return response()->json([
            'data' => $notifications,
            'currentPage' => $page,
            'lastPage' => $lastPage,
            'total' => $total,
        ]);
    }

    public function showNotificationsByIds(Request $request)
    {
        $ids = $request->input('ids'); // $ids là mảng các uid truyền qua request

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'ID không hợp lệ hoặc rỗng'], 200);
        }

        // Nếu truyền "all" thì cập nhật tất cả thông báo trong database
        if (isset($ids[0]) && $ids[0] === 'all') {
            $this->notificationRepository->markAllNotificationsAsRead();
            // Lấy lại toàn bộ thông báo (hoặc chỉ trả về số lượng cập nhật)
            $notifications = $this->notificationRepository->findAll();
            return response()->json([
                'message' => 'Tất cả thông báo đã được đọc',
                'data' => $notifications
            ]);
        } else {
            // Cập nhật trạng thái is_read cho các thông báo có trong mảng $ids thành true
            $this->notificationRepository->markNotificationsAsRead($ids);

            $notifications = $this->notificationRepository->getNotificationsByIds($ids);

            if (empty($notifications)) {
                return response()->json(['message' => 'Không tìm thấy thông báo'], 200);
            }

            return response()->json($notifications);
        }
    }

    public function deleteNotificationByIds(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Danh sách ID không hợp lệ hoặc trống'], 200);
        }

        if (isset($ids[0]) && $ids[0] === 'all') {
            $this->notificationRepository->deleteAllNotifications();

            return response()->json([
                'message' => 'Tất cả thông báo đã được xóa thành công',
            ]);
        } else {
            $this->notificationRepository->deleteNotifications($ids);

            return response()->json(['message' => 'Thông báo đã được xóa thành công']);
        }
    }
}
