<?php
//die();
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
$collections = $database->DataService->FollowUser;
// Sử dụng aggregation pipeline với $sample để lấy ngẫu nhiên 1 dữ liệu
$DataSubSelect = $collections->aggregate([
    ['$sample' => ['size' => 1]] // Lấy ngẫu nhiên 1 dữ liệu
])->toArray();
if(count($DataSubSelect) > 0)
{
	// chưa xử lý ghi log
	foreach($DataSubSelect as $_selectUserSub)
	{
		$_selectUserSub = json_decode(json_encode($_selectUserSub));
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
			echo "Đối tượng uid ".$uid_sub." Đã đủ số lượng chạy trong ngày\n".PHP_EOL;
			continue; // Bỏ qua đối tượng này
		}
		$now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh')); 
		// Chuyển đổi $last_run thành đối tượng DateTime
		$lastRunDate = DateTime::createFromFormat('Y/m/d H:i:s', $last_run, new DateTimeZone('Asia/Ho_Chi_Minh'));

		// Nếu không chuyển đổi được, báo lỗi
		if (!$lastRunDate) {
			echo "Đối tượng uid ".$uid_sub."Không thể chuyển đổi $last_run thành thời gian\n".PHP_EOL;
			continue; // Bỏ qua đối tượng này
		}
		
		// Kiểm tra nếu $last_run và thời điểm hiện tại là cùng ngày
		if ($lastRunDate->format('Y-m-d') !== $now->format('Y-m-d')) {
			echo "Đã sang ngày mới\n";
			$today_run = 0;
		}

		// Tính khoảng cách thời gian giữa hiện tại và lần chạy cuối
		$interval = $now->getTimestamp() - $lastRunDate->getTimestamp();

		// So sánh khoảng cách với $delay
		if ($interval >= $delay) {
			echo "Đối tượng uid ".$uid_sub."Đủ điều kiện chạy tiếp tục, khoảng cách thời gian: {$interval} giây\n".PHP_EOL;
			// Xử lý logic chạy tiếp theo tại đây
			$limit = ceil($daily_run / $delay); // Số account cần lấy, làm tròn lên
			echo "Số lượng account cần lấy: {$limit}\n";
			// chọn từng uid can sub de xử lý sub tung cai 1
			$collections = $database->FacebookData->Account;
			//$FindAccount = $collections->find(["uid" => $uid]);
			$FindAccount = $collections->aggregate([
				['$sample' => ['size' => $limit]] // Lấy ngẫu nhiên 1 dữ liệu
			]);
			if(!empty($FindAccount))
			{
				echo 'lấy dữ liệu acc thành công tiến hành xử lý việc '.PHP_EOL;
				$_Network = new NetworkControler();
				foreach ($FindAccount as $Account) {
					echo 'Đưa account '.$Account->uid.' và xử lý kiểm tra kết nối mạng'.PHP_EOL;
					$CheckConnect = $_Network->TestConnect($Account->networkuse);
					if(!$CheckConnect)
					{
						$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'kiểm tra kết nối interface die, tiến hành khởi tạo lại interface', 'status' => true, 'time' => date("Y/m/d H:i:s"));
						updateHistory($database, $ArrayHistory);
						// trường hợp không kết nối được thử gen ip lai phát ko dc nữa thì hủy
						$resultCreate = $_Network->CreateInterface($Account->networkuse->interface);
						if(!$resultCreate->status)
						{
							// hủy làm viec do tạo cổng mạng thất bại
							// khởi tạo array chứa dữ liệu thông tin của History
							$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'lỗi khởi tạo lại proxy network, hủy làm việc', 'status' => true, 'time' => date("Y/m/d H:i:s"));
							updateHistory($database, $ArrayHistory);
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
						$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Khởi tạo proxy network thành công !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
						updateHistory($database, $ArrayHistory);
					}
					else{
						$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Kết nối đến Proxy thành công', 'status' => true, 'time' => date("Y/m/d H:i:s"));
						updateHistory($database, $ArrayHistory);
					}
					$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Hoàn tất quá trình kiểm tra kết nối tiến hành làm việc, tiến hành kết nối giả lập Device', 'status' => true, 'time' => date("Y/m/d H:i:s"));
					updateHistory($database, $ArrayHistory);
					// chuyển đổi thông tin nick sang array kết nối vào giả lập
					$FacebookUse = iterator_to_array($Account);
					$Accountuse = new FacebookAccount($FacebookUse);
					if ($Accountuse->Connect) {
						$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Thiết lập kết nối hệ thống thành công', 'status' => true, 'time' => date("Y/m/d H:i:s"));
						updateHistory($database, $ArrayHistory);
						//
						$CheckConnect = new CheckConnect($Accountuse); // kết nối facebook
						if ($CheckConnect->ConnectAccount) {
							$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Thiết lập Giả lập Device thành công !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
							updateHistory($database, $ArrayHistory);
							//
							// tiến hành kiểm tra thông tin để lấy live Avatar đang có để bảo đảm avatar luôn có cho từng nick 
							$profile = new ProfileManager($Accountuse);
							$_account = $profile->MyProfile();
							if (!empty($_account->avatar)) {
								$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Lấy thông tin account từ facebook thành công, tiến hành kiểm tra avatar LIVE !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
								updateHistory($database, $ArrayHistory);
								//
								$DetectAvatar = new ImageDetectFace();
								if(!$DetectAvatar->checkHumanFace($_account->avatar))
								{
									echo 'Nick '.$Account->uid.' chưa có avatar tiến hành upload avatar'.PHP_EOL;
									$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Phân tích thông tin avatar LIVE đang cho kết quả false nick chưa có avatar, tiến hành upload Avatar', 'status' => true, 'time' => date("Y/m/d H:i:s"));
									updateHistory($database, $ArrayHistory);
									// xử lý upload Avatar
									// Đường dẫn tới thư mục cần quét
									$directory = "/var/www/Avavatr";
									$allImages = getAllImages($directory);
									if (!empty($allImages)) {
										$randomImage = $allImages[array_rand($allImages)];
										$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Tiến hành dùng ảnh '.$randomImage.' để làm avatar', 'status' => true, 'time' => date("Y/m/d H:i:s"));
										updateHistory($database, $ArrayHistory);
										//
										$resultIdPhoto = $profile->UploadPhoto($randomImage);
										if (!is_numeric($resultIdPhoto->message)) {
											$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Upload File '.$randomImage.' thất bại', 'status' => true, 'time' => date("Y/m/d H:i:s"));
											updateHistory($database, $ArrayHistory);
										}
										//
										$resultSetAvatar = $profile->SetAvatarAccount($resultIdPhoto->message);
										if($resultSetAvatar)
										{
											$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Upload File '.$randomImage.' thành công với id là '.$resultIdPhoto->message.' đã thiết lập Avatar xong', 'status' => true, 'time' => date("Y/m/d H:i:s"));
											updateHistory($database, $ArrayHistory);
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
										$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Không có dữ liệu ảnh để upload làm avatar', 'status' => true, 'time' => date("Y/m/d H:i:s"));
										updateHistory($database, $ArrayHistory);
										return ;
									}
								}
								else{
									echo 'Nick '.$Account->uid.' đã sẵn có avatar không cần upload'.PHP_EOL;
									$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Tài khoản đã có avatar không cần cập nhật', 'status' => true, 'time' => date("Y/m/d H:i:s"));
									updateHistory($database, $ArrayHistory);
								}
							}
							//
							echo 'Nick '.$Account->uid.' tiến hành đi sub đối tượng'.PHP_EOL;
							$collections = $database->History->FacebookHistory;
							$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Nick có đủ thông tin avatar phù hợp cho việc Sub user tiến hành đi Sub User theo chỉ định !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
							$collections->insertOne($ArrayHistory);
							//tiến hành xử lý chay sub theo uid chỉ định
							// kết nối facebook xin thông tin đối tượng
							$resultUser = $profile->LoadInfoUserByUID($uid_sub);
							if($resultUser->status)
							{
								// kiểm tra dữ liệu đối tượng cần follow 
								if($follow_start < 1 || $follow_stop < 1)
								{
									// trường hop này thì db chưa có dữ liệu do mới chạy nên sẽ update
									$follow_start = intval($resultUser->follow);
									$follow_stop = intval($follow_start) + intval($quantity);
									$collections = $database->DataService->FollowUser;
									$collections->updateOne(
										["uid_sub" => $uid_sub],
										['$set' => array('follow_start' => $follow_start, 'follow_stop' => $follow_stop)]
									);
									//
									$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Vừa tiến hành cập nhật thông tin đối tượng cần sub cho hệ thống', 'status' => true, 'time' => date("Y/m/d H:i:s"));
									updateHistory($database, $ArrayHistory);
								}
								// tiến hành so sánh dữ liệu để quyết định có follow hay không hoac là dừng hẳn
								if(intval($resultUser->follow)>=$follow_start)
								{
									// nghĩa là chưa đủ hoặc vừa đủ cho dư 1 2 cái không sao
									if($resultUser = $profile->FollowUser($uid_sub))
									{
										echo 'Nick '.$Account->uid.' đã thực hiện sub đối tượng thành công !'.PHP_EOL;
										$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Vừa thực hiện Follow User theo Kịch Bản Khai Thác Nick thành công !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
										updateHistory($database, $ArrayHistory);
										//
										$collections = $database->DataService->FollowUser;
										$collections->updateOne(
											["uid_sub" => $uid_sub],
											['$set' => array('last_run' => date("Y/m/d H:i:s"), 'today_run' => $today_run + 1)]
										);
									}
									else{
										echo 'Nick '.$Account->uid.' đã thực hiện sub đối tượng thất bại !'.PHP_EOL;
										$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Vừa thực hiện Follow User theo Kịch Bản Khai Thác Nick thất bại !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
										updateHistory($database, $ArrayHistory);
									}
								}
								else{
									echo 'Nick '.$Account->uid.' dừng việc sub đối tượng và hủy toàn bộ tiến trình sub cho đối tượng do đã đủ số lượng !'.PHP_EOL;
									$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Dừng việc sub đối tượng và hủy toàn bộ tiến trình sub cho đối tượng do đã đủ số lượng !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
									updateHistory($database, $ArrayHistory);
									return;
								}
							} else {
								$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Khong lay duoc thong tin doi tuong ', 'status' => true, 'time' => date("Y/m/d H:i:s"));
								updateHistory($database, $ArrayHistory);
								return ;
							}
						}
						else{
							$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Thiết lập Giả lập Device thất bại có vẻ nick CHECKPOINT !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
							updateHistory($database, $ArrayHistory);
						}
					}
					else{
						$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Thiết lập kết nối hệ thống thất bại, khả năng die phiên hoặc die nick', 'status' => true, 'time' => date("Y/m/d H:i:s"));
						updateHistory($database, $ArrayHistory);
					}
					// remove ket noi mang
					$ArrayHistory = array('uid' => $Account->uid, 'action' => 'sub user', 'message' => 'Hoàn tất công việc tiến hành gỡ bỏ ip khỏi cổng mạng', 'status' => true, 'time' => date("Y/m/d H:i:s"));
					updateHistory($database, $ArrayHistory);
					//
					$_Network->RemoveInterface($Account->networkuse->interface, $Account->networkuse->ip);
				}
			}
			else{
				echo 'không tìm thấy dữ liệu acc để chạy'.PHP_EOL;
			}
			
			
		} else {
			echo "Đối tượng uid ".$uid_sub." Chưa đủ điều kiện chạy, phải chờ thêm " . ($delay - $interval) . " giây\n".PHP_EOL;
		}
		
	}
}
else{
	echo 'không lấy dc dữ lieu khi chay sub'.PHP_EOL;
	die('hccccccccccode');
}

die('het code');
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

function updateHistory($database, $ArrayInput)
{
	$collections = $database->History->FacebookHistory;
	$collections->insertOne($ArrayInput);
}
?>
