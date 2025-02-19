<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);

require '/var/www/FacebookService/vendor/Phphelper/autoload.php';

use MongoDB\BSON\ObjectID;
use HoangquyIT\FacebookAccount;
use MongoDB\BSON\UTCDateTime;
use HoangquyIT\ProxyV6;
use HoangquyIT\ModelFacebook\Android\CheckConnect;
use HoangquyIT\ModelFacebook\Android\Profile\ProfileManager;
use HoangquyIT\ModelFacebook\Android\Groups\GroupsManager;
use HoangquyIT\NetworkControler\NetworkControler;
use HoangquyIT\OpenCv\ImageDetectFace;



$database = new HoangquyIT\MongoDB\Client("mongodb://localhost:27017");
$collections = $database->NetworkControler->Setting;
$FindNetworkConfig = $collections->findOne(['pppoe' => ['$exists' => true]]);
if(empty($FindNetworkConfig->pppoe))
{
	die('Khong lay dc cau hinh chinh network');
}
$collections = $database->DataService->FollowUser;
// Sử dụng aggregation pipeline với $sample để lấy ngẫu nhiên 1 dữ liệu
$DataSubSelect = $collections->aggregate([
    ['$sample' => ['size' => 1]] // Lấy ngẫu nhiên 1 dữ liệu
]);
if(!empty($DataSubSelect))
{
	// chưa xử lý ghi log
	foreach($DataSubSelect as $_selectUserSub)
	{
		// lấy thông tin chi tiết việc ra
		$uid_sub = $_selectUserSub->uid_sub;// đối tượng cần chạy
		$quantity = $_selectUserSub->quantity;// tổng số lượng sẽ chạy
		$daily_run = $_selectUserSub->daily_run;// so luong se chay trong 1 ngay
		$follow_start = $_selectUserSub->follow_start ?? 0;// số lượng follow ban đầu của đối tượng đang có
		$follow_stop = $_selectUserSub->follow_stop ?? 0;// số lượng follow cần đạt
		$last_run = $_selectUserSub->last_run;// lần chạy cuối của đối tượng sẽ có định dạng 2025/01/06 07:38:19
		$delay = $_selectUserSub->delay ?? 200;// delay mỗi lần chạy được tính bằng giây
		$today_run = $_selectUserSub->today_run ?? 0;
		//
		// Kiểm tra số lượng đã chạy trong ngày
		if ($today_run >= $daily_run) {
			echo "Đã đủ số lượng chạy trong ngày\n";
			continue; // Bỏ qua đối tượng này
		}
		$now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh')); 
		// Chuyển đổi $last_run thành đối tượng DateTime
		$lastRunDate = DateTime::createFromFormat('Y/m/d H:i:s', $last_run, new DateTimeZone('Asia/Ho_Chi_Minh'));

		// Nếu không chuyển đổi được, báo lỗi
		if (!$lastRunDate) {
			echo "Không thể chuyển đổi $last_run thành thời gian\n";
			continue; // Bỏ qua đối tượng này
		}

		// Tính khoảng cách thời gian giữa hiện tại và lần chạy cuối
		$interval = $now->getTimestamp() - $lastRunDate->getTimestamp();

		// So sánh khoảng cách với $delay
		if ($interval >= $delay) {
			echo "Đủ điều kiện chạy tiếp tục, khoảng cách thời gian: {$interval} giây\n";
			// Xử lý logic chạy tiếp theo tại đây
			// chọn từng uid can sub de xử lý sub tung cai 1
			$collections = $database->FacebookData->Account;
			// 
			//$uid = "100016771842286";
			//$FindAccount = $collections->find(["uid" => $uid]);
			$FindAccount = $collections->aggregate([
				['$sample' => ['size' => 1]] // Lấy ngẫu nhiên 1 dữ liệu
			]);
			if(!empty($FindAccount))
			{
				$_Network = new NetworkControler();
				foreach ($FindAccount as $Account) {
					$CheckConnect = $_Network->TestConnect($Account->networkuse);
					if(!$CheckConnect)
					{
						$collections = $database->History->FacebookHistory;
						$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'kiểm tra kết nối interface die, tiến hành khởi tạo lại interface', 'status' => true, 'time' => date("Y/m/d H:i:s"));
						$collections->insertOne($ArrayHistory);
						
						// trường hợp không kết nối được thử gen ip lai phát ko dc nữa thì hủy
						$resultCreate = $_Network->CreateInterface($FindNetworkConfig->pppoe->interfaces);
						if(!$resultCreate->status)
						{
							// hủy làm viec do tạo cổng mạng thất bại
							// khởi tạo array chứa dữ liệu thông tin của History
							$collections = $database->History->FacebookHistory;
							$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'lỗi khởi tạo lại proxy network, hủy làm việc', 'status' => true, 'time' => date("Y/m/d H:i:s"));
							$collections->insertOne($ArrayHistory);
							return;
						}
						$Account->networkuse->ip = $resultCreate->ip;
						// cập nhật lại dữ liệu vào db
						$collections = $database->FacebookData->Account;
						$collections->updateOne(
							["_id" => $Account->_id],
							['$set' => array('networkuse' => iterator_to_array($Account->networkuse))]
						);
						//
						$collections = $database->History->FacebookHistory;
						$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Khởi tạo proxy network thành công !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
						$collections->insertOne($ArrayHistory);
					}
					else{
						$collections = $database->History->FacebookHistory;
						$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Kết nối đến Proxy thành công', 'status' => true, 'time' => date("Y/m/d H:i:s"));
						$collections->insertOne($ArrayHistory);
					}
					$collections = $database->History->FacebookHistory;
					$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Hoàn tất quá trình kiểm tra kết nối tiến hành làm việc, tiến hành kết nối giả lập Device', 'status' => true, 'time' => date("Y/m/d H:i:s"));
					$collections->insertOne($ArrayHistory);
					// chuyển đổi thông tin nick sang array kết nối vào giả lập
					$FacebookUse = iterator_to_array($Account);
					$Accountuse = new FacebookAccount($FacebookUse);
					if ($Accountuse->Connect) {
						$collections = $database->History->FacebookHistory;
						$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Thiết lập kết nối hệ thống thành công', 'status' => true, 'time' => date("Y/m/d H:i:s"));
						$collections->insertOne($ArrayHistory);
						//
						$CheckConnect = new CheckConnect($Accountuse); // kết nối facebook
						if ($CheckConnect->ConnectAccount) {
							$collections = $database->History->FacebookHistory;
							$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Thiết lập Giả lập Device thành công !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
							$collections->insertOne($ArrayHistory);
							//
							// tiến hành kiểm tra thông tin để lấy live Avatar đang có để bảo đảm avatar luôn có cho từng nick 
							$profile = new ProfileManager($Accountuse);
							$_account = $profile->MyProfile();
							if (!empty($_account->avatar)) {
								$collections = $database->History->FacebookHistory;
								$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Lấy thông tin account từ facebook thành công, tiến hành kiểm tra avatar LIVE !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
								$collections->insertOne($ArrayHistory);
								//
								$DetectAvatar = new ImageDetectFace();
								if(!$DetectAvatar->checkHumanFace($_account->avatar))
								{
									$collections = $database->History->FacebookHistory;
									$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Phân tích thông tin avatar LIVE đang cho kết quả false nick chưa có avatar, tiến hành upload Avatar', 'status' => true, 'time' => date("Y/m/d H:i:s"));
									$collections->insertOne($ArrayHistory);
									// xử lý upload Avatar
									// Đường dẫn tới thư mục cần quét
									$directory = "/var/www/Images";
									$allImages = getAllImages($directory);
									if (!empty($allImages)) {
										$randomImage = $allImages[array_rand($allImages)];
										$collections = $database->History->FacebookHistory;
										$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Tiến hành dùng ảnh '.$randomImage.' để làm avatar', 'status' => true, 'time' => date("Y/m/d H:i:s"));
										$collections->insertOne($ArrayHistory);
										//
										$resultIdPhoto = $profile->UploadPhoto($randomImage);
										if (!is_numeric($resultIdPhoto->message)) {
											$collections = $database->History->FacebookHistory;
											$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Upload File '.$randomImage.' thất bại', 'status' => true, 'time' => date("Y/m/d H:i:s"));
											$collections->insertOne($ArrayHistory);
										}
										
										$resultSetAvatar = $profile->SetAvatarAccount($resultIdPhoto->message);
										if($resultSetAvatar)
										{
											$collections = $database->History->FacebookHistory;
											$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Upload File '.$randomImage.' thành công với id là '.$resultIdPhoto->message.' đã thiết lập Avatar xong', 'status' => true, 'time' => date("Y/m/d H:i:s"));
											$collections->insertOne($ArrayHistory);
											//
											$profile = new ProfileManager($Accountuse);
											$ProfileInfo = $profile->MyProfile();
											if(!empty($ProfileInfo->avatar))
											{
												$collections = $database->FacebookData->Account;
												$collections->updateOne(
													["uid" => $Account->uid],
													['$set' => array('avatar' => $ProfileInfo->avatar)]
												);
											}
										}
									}
									else{
										$collections = $database->History->FacebookHistory;
										$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Không có dữ liệu ảnh để upload làm avatar', 'status' => true, 'time' => date("Y/m/d H:i:s"));
										$collections->insertOne($ArrayHistory);
									}
								}
								else{
									$collections = $database->History->FacebookHistory;
									$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Tài khoản đã có avatar không cần cập nhật', 'status' => true, 'time' => date("Y/m/d H:i:s"));
									$collections->insertOne($ArrayHistory);
								}
							}
							//
							$collections = $database->History->FacebookHistory;
							$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Nick có đủ thông tin avatar phù hợp cho việc Sub user tiến hành đi Sub User theo chỉ định !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
							$collections->insertOne($ArrayHistory);
							//tiến hành xử lý chay sub theo uid chỉ định
							// kiểm tra dữ liệu đối tượng cần follow 
							if($follow_start < 1 || $follow_stop < 1)
							{
								// kết nối facebook xin thông tin đối tượng
								$resultUser = $profile->LoadInfoUserByUID($uid_sub);
								if($resultUser->status)
								{
									$follow_start = intval($resultUser->follow);
									$follow_stop = intval($follow_start) + intval($quantity);
									$collections = $database->DataService->FollowUser;
									$collections->updateOne(
										["uid_sub" => $uid_sub],
										['$set' => array('follow_start' => $follow_start, 'follow_stop' => $follow_stop)]
									);
									//
									$collections = $database->History->FacebookHistory;
									$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Vừa tiến hành cập nhật thông tin đối tượng cần sub cho hệ thống', 'status' => true, 'time' => date("Y/m/d H:i:s"));
									$collections->insertOne($ArrayHistory);
								}
							}
							if($resultUser = $profile->FollowUser($uid_sub))
							{
								$collections = $database->History->FacebookHistory;
								$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Vừa thực hiện Follow User theo Kịch Bản Khai Thác Nick thành công !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
								$collections->insertOne($ArrayHistory);
								//
								$collections = $database->DataService->FollowUser;
								$collections->updateOne(
									["uid_sub" => $uid_sub],
									['$set' => array('last_run' => date("Y/m/d H:i:s"), 'today_run' => $today_run + 1)]
								);
							}
							else{
								$collections = $database->History->FacebookHistory;
								$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Vừa thực hiện Follow User theo Kịch Bản Khai Thác Nick thất bại !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
								$collections->insertOne($ArrayHistory);
							}
						}
						else{
							$collections = $database->History->FacebookHistory;
							$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Thiết lập Giả lập Device thất bại có vẻ nick CHECKPOINT !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
							$collections->insertOne($ArrayHistory);
						}
					}
					else{
						$collections = $database->History->FacebookHistory;
						$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Thiết lập kết nối hệ thống thất bại', 'status' => true, 'time' => date("Y/m/d H:i:s"));
						$collections->insertOne($ArrayHistory);
					}
					// remove ket noi mang
					$collections = $database->History->FacebookHistory;
					$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Hoàn tất công việc tiến hành gỡ bỏ ip khỏi cổng mạng', 'status' => true, 'time' => date("Y/m/d H:i:s"));
					$collections->insertOne($ArrayHistory);
					//
					$_Network->RemoveInterface($FindNetworkConfig->pppoe->interfaces, $Account->networkuse->ip);
				}
			}
			else{
				die('ssssssssssss');
			}
			
			
		} else {
			echo "Chưa đủ điều kiện chạy, phải chờ thêm " . ($delay - $interval) . " giây\n";
		}
		
	}
}
else{
	die('không lấy dc dữ lieu khi chay sub');
}


function getAllImages($directory)
{
    $images = [];

    // Sử dụng hàm RecursiveIteratorIterator để duyệt qua tất cả các tệp trong thư mục và thư mục con
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

    foreach ($iterator as $file) {
        if ($file->isFile()) {
            // Kiểm tra xem tệp có phải là ảnh dựa trên phần mở rộng không
            $extension = strtolower($file->getExtension());
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
                $images[] = $file->getPathname();
            }
        }
    }

    return $images;
}


?>
