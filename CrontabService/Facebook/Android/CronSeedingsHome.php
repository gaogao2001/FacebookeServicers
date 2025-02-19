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
use HoangquyIT\Encryption\Php7x\AES256\HqitEncryption;
use HoangquyIT\ModelFacebook\Android\MainControler;



// Kết nối tới MongoDB
$database = new HoangquyIT\MongoDB\Client("mongodb://localhost:27017");
$collections = $database->NetworkControler->Setting;

try {
    // Điều kiện MongoDB: kiểm tra bất kỳ trường nào có `pppoe_username` khác null
    $condition = [
        '$expr' => [
            '$gt' => [
                [
                    '$size' => [
                        '$filter' => [
                            'input' => [
                                '$objectToArray' => '$$ROOT'
                            ],
                            'as' => 'field',
                            'cond' => [
                                '$and' => [
                                    [
                                        '$ne' => ['$$field.v.pppoe_username', null]
                                    ],
                                    [
                                        '$type' => ['$$field.v.pppoe_username']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                0
            ]
        ]
    ];

    // Lấy một tài liệu duy nhất
    $network_settings = $collections->findOne($condition);

    $selectedSetting = array(); // Kết quả mong muốn

    if ($network_settings) {
        foreach ($network_settings as $key => $value) {
            // Kiểm tra nếu trường là một đối tượng MongoDB
            if ($value instanceof HoangquyIT\MongoDB\Model\BSONDocument) {
                $value = $value->getArrayCopy(); // Chuyển về mảng

                if (
                    isset($value['status'], $value['pppoe_username']) &&
                    $value['status'] === 'on' &&
                    !empty($value['pppoe_username'])
                ) {
                    $selectedSetting = [
                        'interface' => $key, // Tên của trường (ví dụ: 'eth1')
                        'data' => $value     // Giá trị của trường
                    ];
                    break;
                }
            }
        }
    }
} catch (\Exception $e) {
   
}
if(!empty($selectedSetting['interface']))
{
	$InterfaceUse = $selectedSetting['interface'];
	// Thời gian hiện tại
	$current_time = new DateTime();
	$current_time->modify('-22 minutes'); // Lấy thời gian hiện tại trừ 22 phút
	$formatted_time = $current_time->format(DateTime::ISO8601); // Định dạng ISO 8601

	// Pipeline cho truy vấn MongoDB
	$pipeline = [
		[
			'$match' => [
				'status' => 'LIVE',
				'useAccount' => 'NO',
			]
		],
		['$sample' => ['size' => 500]] // Lấy ngẫu nhiên 500 tài liệu
	];



	// Lấy danh sách tài khoản theo pipeline
	$collections = $database->FacebookData->Account;
	$AccountSelect = $collections->aggregate($pipeline)->toArray();
	$ids = [];
	foreach ($AccountSelect as $document) {
		$ids[] = $document['_id'];
	}
	if(count($ids) > 0)
	{
		// Cập nhật tất cả các tài liệu có useAccount là 'NO'
		$collections = $database->FacebookData->Account;
		$collections->updateMany(
			['_id' => ['$in' => $ids]],             // Điều kiện _id trong danh sách
			['$set' => ['useAccount' => 'YES']]  // Trường cần cập nhật
		);
		foreach ($AccountSelect as $_select) 
		{
			$SelectAccount = (object)$_select;
			$_id = $SelectAccount->_id;
			$uid = $SelectAccount->uid;
			$ArrayUpdate = array('useAccount' => 'NO');// soan dữ lieu de cap nhat
			if($SelectAccount->config_auto->auto)
			{
				if($SelectAccount->config_auto->configurations->seeding_home_config->auto)
				{
					$collections = $database->History->FacebookHistory;
					$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' tiến hành kiểm tra thời gian tương tác cuối trước khi tương tác', 'status' => true, 'time' => date("Y/m/d H:i:s"));
					$collections->insertOne($ArrayHistory);
					// Chuyển đổi định dạng thời gian
					$date_old = DateTime::createFromFormat('d/m/Y H:i:s', $SelectAccount->config_auto->configurations->post_group_config->time_old);
					$date_now = DateTime::createFromFormat('d/m/Y H:i:s', date('d/m/Y H:i:s'));
					$interval = $date_now->diff($date_old);
					// Chuyển đổi khoảng cách thời gian thành số giây
					$seconds = ($interval->days * 24 * 60 * 60) + ($interval->h * 60 * 60) + ($interval->i * 60) + $interval->s;
					// Kiểm tra xem khoảng cách thời gian 
					if ($seconds >= intval($SelectAccount->config_auto->configurations->post_group_config->min_time))
					{
						$collections = $database->History->FacebookHistory;
						$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' đã đủ thời gian delay tương tác Home, tiến hành tương tác', 'status' => true, 'time' => date("Y/m/d H:i:s"));
						$collections->insertOne($ArrayHistory);
						//
						$_Network = new NetworkControler();
						$CheckConnect = $_Network->TestConnect($SelectAccount->networkuse);
						if (!$CheckConnect) {
							if ($SelectAccount->networkuse->type == 'interfaces') {
								$resultCreate = $_Network->CreateInterface($InterfaceUse);
								if (!$resultCreate->status) {
									$collections = $database->History->FacebookHistory;
									$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' lỗi khi tạo mới Interface thất bại dừng làm việc', 'status' => true, 'time' => date("Y/m/d H:i:s"));
									$collections->insertOne($ArrayHistory);
									//
									$ArrayUpdate['useAccount'] = 'NO';
									$ArrayUpdate['status'] = 'LIVE';
									$collections = $database->FacebookData->Account;
									$result = $collections->updateOne(["_id" => new MongoDB\BSON\ObjectID($_id)], ['$set' => $ArrayUpdate]);
									continue;
								}
								$SelectAccount->networkuse->ip = $resultCreate->ip;
								$updatData = array('networkuse' => iterator_to_array($SelectAccount->networkuse));
								$collections = $database->FacebookData->Account;
								$result = $collections->updateOne(["_id" => new MongoDB\BSON\ObjectID($_id)], ['$set' => $updatData]);
							}
						}
						//
						$FacebookUse = iterator_to_array($SelectAccount);
						unset($FacebookUse["_id"]);
						$ArrayUpdate['last_seeding'] = date('d/m/Y H:i:s');
						$Accountuse = new FacebookAccount($FacebookUse);// ket noi curl va proxy
						if($Accountuse->Connect)
						{
							$collections = $database->History->FacebookHistory;
							$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' kết nối proxy thành công, tiến hành xử lý việc', 'status' => true, 'time' => date("Y/m/d H:i:s"));
							$collections->insertOne($ArrayHistory);
							///
							$Main = new MainControler($Accountuse);
							$NewFeedPost = $Main->LoadPostNewFeed();
							if($NewFeedPost->status)
							{
								$collections = $database->History->FacebookHistory;
								$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' lấy dữ liệu bài viết bản tin thành công !', 'status' => true, 'time' => date("Y/m/d H:i:s"));
								$collections->insertOne($ArrayHistory);
								//
								$ArrayUpdate['status'] = 'LIVE';
								foreach($NewFeedPost->post_data as $_selectPost)
								{
									$ResultReactionPost = $Main->reactionPost($_selectPost, '2');
									if($ResultReactionPost)
									{
										//$ArrayHistory = array('uid' => $SelectAccount->uid, 'facebook_id' => $_selectPost->post_id, 'action' => 'Reaction Post New Feed', ' status' => 'success', 'time' => date('d/m/Y H:i:s'));
										//$collections = $database->FacebookData->History;
										//$collections->insertOne($ArrayHistory);
										$collections = $database->History->FacebookHistory;
										$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' vừa tương tác thành công bài viết '.$_selectPost->post_id, 'status' => true, 'time' => date("Y/m/d H:i:s"));
										$collections->insertOne($ArrayHistory);
										//
										break;
									}
								}
								//
								$config_auto = $FacebookUse['config_auto'];
								$config_auto['configurations']['seeding_home_config']['time_old'] = date('d/m/Y H:i:s');
								
								$ArrayUpdate['last_seeding'] = date('d/m/Y H:i:s');
								$ArrayUpdate['config_auto'] = $config_auto;
								//
								$collections = $database->FacebookData->Account;
								$result = $collections->updateOne(["_id" => new MongoDB\BSON\ObjectID($_id)], ['$set' => $ArrayUpdate]);
								//
								$collections = $database->History->FacebookHistory;
								$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' hoàn tất tiến trình tương tác dừng làm việc đổi nick khác làm việc', 'status' => true, 'time' => date("Y/m/d H:i:s"));
								$collections->insertOne($ArrayHistory);
								// hủy kết nối mạng để giảm tải cho hệ thống
								$_Network->RemoveInterface($InterfaceUse, $SelectAccount->networkuse->ip);
								continue;
							}
							else{
								$_Network->RemoveInterface($InterfaceUse, $SelectAccount->networkuse->ip);
								if(strpos(trim($Accountuse->curl->getRawResponse()), 'NewsFeedQueryDepth3') !== false)
								{
									// acc live nhưng không có bài viết ở bản tin
									$ArrayUpdate['status'] = 'LIVE';
									$ArrayUpdate['groups_account'] = 'NewFeedNoPost';
									$ArrayUpdate['last_seeding'] = date('d/m/Y H:i:s');
									$collections = $database->FacebookData->Account;
									$result = $collections->updateOne(["_id" => new MongoDB\BSON\ObjectID($_id)], ['$set' => $ArrayUpdate]);
									//
									$collections = $database->History->FacebookHistory;
									$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' lấy dữ liệu bài viết bản tin thất bại do không có bài viết trên bản tin', 'status' => true, 'time' => date("Y/m/d H:i:s"));
									$collections->insertOne($ArrayHistory);
									//
								}
								else if(!empty($Accountuse->curl->response->error->code))
								{
									if($Accountuse->curl->response->error->code == 190)
									{
										// thông báo acc bị đăng xuất do phiên hết hạn hoặc nick checkpoint 
										$ArrayUpdate['status'] = 'SessionExpires';
										$ArrayUpdate['last_seeding'] = date('d/m/Y H:i:s');
										$collections = $database->FacebookData->Account;
										$result = $collections->updateOne(["_id" => new MongoDB\BSON\ObjectID($_id)], ['$set' => $ArrayUpdate]);
										//
										$collections = $database->History->FacebookHistory;
										$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' lấy dữ liệu bài viết bản tin thất bại do account bị đăng xuất hoặc checkpoint', 'status' => true, 'time' => date("Y/m/d H:i:s"));
										$collections->insertOne($ArrayHistory);
									}
									else if($Accountuse->curl->response->error->code == 104)
									{
										// có vẻ acc chưa có token chưa từng login
										$ArrayUpdate['status'] = 'NotLoggedIn';
										$ArrayUpdate['last_seeding'] = date('d/m/Y H:i:s');
										$collections = $database->FacebookData->Account;
										$result = $collections->updateOne(["_id" => new MongoDB\BSON\ObjectID($_id)], ['$set' => $ArrayUpdate]);
										//
										$collections = $database->History->FacebookHistory;
										$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' lấy dữ liệu bài viết bản tin thất bại khả năng account chưa login', 'status' => true, 'time' => date("Y/m/d H:i:s"));
										$collections->insertOne($ArrayHistory);
									}
									else{
										$collections = $database->History->FacebookHistory;
										$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' lấy dữ liệu bài viết bản tin thất bại nhưng không xác định lỗi', 'status' => true, 'time' => date("Y/m/d H:i:s"));
										$collections->insertOne($ArrayHistory);
									}
								}
								else{
									// tình huống này chưa xác định lỗi
									$collections = $database->FacebookData->Account;
									$result = $collections->updateOne(["_id" => new MongoDB\BSON\ObjectID($_id)], ['$set' => $ArrayUpdate]);
									//
									$collections = $database->History->FacebookHistory;
									$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' lấy dữ liệu bài viết bản tin thất bại nhưng không xác định lỗi', 'status' => true, 'time' => date("Y/m/d H:i:s"));
									$collections->insertOne($ArrayHistory);
									//
								}
							}
						}
						else{
							// lỗi không kết nối được proxy
							$ArrayUpdate['useAccount'] = 'NO';
							$ArrayUpdate['status'] = 'LIVE';
							$collections = $database->FacebookData->Account;
							$result = $collections->updateOne(["_id" => new MongoDB\BSON\ObjectID($_id)], ['$set' => $ArrayUpdate]);
							//
							$collections = $database->History->FacebookHistory;
							$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' kết nối proxy thất bại, dừng làm việc', 'status' => true, 'time' => date("Y/m/d H:i:s"));
							$collections->insertOne($ArrayHistory);
							$_Network->RemoveInterface($InterfaceUse, $SelectAccount->networkuse->ip);
						}
					}
					else if ($seconds < intval($SelectAccount->config_auto->configurations->post_group_config->min_time)) {
						// chưa đến giờ chạy tác vụ của nick
						$time_remaining = intval($SelectAccount->config_auto->configurations->post_group_config->min_time) - $seconds;
						
						// Chuyển đổi thời gian còn lại từ giây sang giờ, phút, giây
						$hours = floor($time_remaining / 3600);
						$minutes = floor(($time_remaining % 3600) / 60);
						$seconds = $time_remaining % 60;
						//
						$collections = $database->FacebookData->Account;
						$ArrayUpdate['useAccount'] = 'NO';
						$result = $collections->updateOne(["_id" => new MongoDB\BSON\ObjectID($_id)], ['$set' => $ArrayUpdate]);
						//
						$collections = $database->History->FacebookHistory;
						$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.'Chưa đến giờ làm việc ( tương tác newFeed )', 'status' => true, 'time' => date("Y/m/d H:i:s"));
						$collections->insertOne($ArrayHistory);
					}
				}else{
					$collections = $database->History->FacebookHistory;
					$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' không có thiết lập auto tương tác home', 'status' => true, 'time' => date("Y/m/d H:i:s"));
					$collections->insertOne($ArrayHistory);
				}
			}
			else{
				$collections = $database->History->FacebookHistory;
				$ArrayHistory = array('uid' => $SelectAccount->uid, 'action' => 'Home Seedings', 'message' => 'Nick '.$SelectAccount->uid.' không có thiết lập tính năng Auto', 'status' => true, 'time' => date("Y/m/d H:i:s"));
				$collections->insertOne($ArrayHistory);
			}
			//
			$collections = $database->FacebookData->Account;
			$result = $collections->updateOne(["_id" => new MongoDB\BSON\ObjectID($_id)], ['$set' => $ArrayUpdate]);
		}
	}
}



die('Het Code');