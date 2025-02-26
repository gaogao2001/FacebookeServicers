<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);

require '/var/www/FacebookService/vendor/Phphelper/autoload.php';

$database = new HoangquyIT\MongoDB\Client("mongodb://localhost:27017");
$collections = $database->SiteManager->SystemNotification;

// Dữ liệu cần insert với thời gian theo PHP thuần
$document = [
    'create_time' => date("Y-m-d H:i:s"), // Thời gian hiện tại theo định dạng chuỗi
    'notifi_type' => 'Hệ Thống Check LIVE', // Ví dụ loại thông báo
    'content_notifi' => 'Đây là nội dung thông báo mẫu ' . date("Y-m-d H:i:s"), // Nội dung thông báo
    'is_read' => false // Mặc định thông báo chưa đọc
];

// Thực hiện insert
$result = $collections->insertOne($document);

// Kiểm tra kết quả
if ($result->getInsertedCount() === 1) {
    echo "Insert thành công với ID: " . $result->getInsertedId();
} else {
    echo "Insert thất bại.";
}
