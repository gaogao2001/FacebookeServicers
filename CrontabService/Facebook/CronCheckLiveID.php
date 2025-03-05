<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);

require '/var/www/FacebookService/vendor/Phphelper/autoload.php';

use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use HoangquyIT\ModelFacebook\Android\CheckConnect;
use HoangquyIT\ModelFacebook\FacebookApi;

$database = new HoangquyIT\MongoDB\Client("mongodb://localhost:27017");
$FileData = '/tmp/tempchecklive.txt';


$linesToRead = 500;

if (file_exists($FileData)) {
    // Đọc toàn bộ file vào mảng, bỏ qua dòng trống
    $lines = file($FileData, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $totalLines = count($lines);

    if ($totalLines > 0) {
        // Số dòng cần đọc sẽ là min(500, tổng số dòng hiện có)
        $readCount = min($linesToRead, $totalLines);
        $selectedLines = array_slice($lines, 0, $readCount);
        
        // Ghi lại phần còn lại vào file nếu vẫn còn dòng
        if ($totalLines > $readCount) {
            file_put_contents($FileData, implode(PHP_EOL, array_slice($lines, $readCount)) . PHP_EOL);
        } else {
            unlink($FileData); // Xóa file nếu không còn dữ liệu
        }

        // Hiển thị số dòng đã lấy
        foreach ($selectedLines as $uid) {
            $FbSdk = new FacebookApi();
            $resultCheck = $FbSdk->checkFacebookUID($uid);

            echo 'Nick '.$uid. ' có trạng thái là '.$resultCheck.' tiến hành cap nhật db'. PHP_EOL;
            if (in_array($resultCheck, ['LIVE', 'CHECKPOINT'])) {
                $collections = $database->FacebookData->Account;
                $collections->updateOne(
                    ["uid" => $uid],
                    ['$set' => ['status' => $resultCheck]]
                );
            }
        }
    }
}
