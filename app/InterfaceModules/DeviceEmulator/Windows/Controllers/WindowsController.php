<?php
namespace App\InterfaceModules\DeviceEmulator\Windows\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Facebook\Repositories\Account\AccountRepositoryInterface;
use MongoDB\BSON\ObjectId;
use HoangquyIT\FacebookAccount;// ok
use HoangquyIT\FacebookAds\Web\AdsManager;

class WindowsController extends Controller
{
    protected $accountRepository;

    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }
	
	public function LoadAdsAccount(Request $request, $uid = null)
	{
		
		if (is_null($uid) || empty($uid) || !is_string($uid)) {
			return redirect()->route('facebook.pages')->with('error', 'UID không hợp lệ.');
		}

		$accounts = $this->accountRepository->searchAccounts(['uid' => $uid]);
		if (empty($accounts) || empty($accounts['data'])) {
			return redirect()->route('facebook.pages')->with('error', 'Không tìm thấy tài khoản.');
		}
		$FacebookUse = iterator_to_array($accounts["data"][0]);
		unset($FacebookUse["_id"]);
		//
		$Qrcode = $FacebookUse["qrcode"];
		$Accountuse = new FacebookAccount($FacebookUse);
		$this->curl = $Accountuse->curl;
		$this->curl->setHttpVersion(CURL_HTTP_VERSION_NONE);
		$this->curl->setHeader('Accept', '*/*');
		$this->curl->setHeader('Content-Type', 'application/x-www-form-urlencoded');
		//
		$this->curl->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36');
		$this->curl->setOpt(CURLOPT_COOKIE , 'datr=IR8nZ8yK1eCbjCcir5TglPDY; sb=IR8nZ1NLY3sbwr0o7ggVfplv; c_user=100010323180721; ps_l=1; ps_n=1; ar_debug=1; presence=C%7B%22t3%22%3A%5B%5D%2C%22utc3%22%3A1736727242379%2C%22v%22%3A1%7D; fr=10fQto5SnXjcX8Jmc.AWX0bMr4R11AsVp73XsU9v3vrTg.BnhFyK..AAA.0.0.BnhFyK.AWWwrHEh60k; xs=11%3ASZHTvaYGF4KrCw%3A2%3A1730617146%3A-1%3A6188%3A%3AAcUR_m-tHSC7qzUafCM4AUAPMBYB0JOmkJjaNyr32MQA; wd=565x876');
		$ads = new AdsManager($this->curl, $uid, $Qrcode);
		$AccessToken = 'EAAI4BG12pyIBO1TzxIr9r3llTy2ZCZCflXeSZCOXu4LZBg6O9cnlbs0ZAjuojUz7tG99hqFV9QnIZA8CWT5FcJbz6VGgZAAQjm61xYP8CmHLrrs5OPhPaEoPIXKOQZBcyBAr7Tt8UuRVPPcmRJQjGZAjMvKIX8O8ABtw5hYzHZA471v6NZBv03ZBydxqVxKZAZAAZDZD';//$ads->LoadToken();
		//
		$collection = app('mongo')->FacebookData->Adsmanager;
		//
		$DataAds = $ads->ListAdsAccount($AccessToken);
		if(count($DataAds) > 0)
		{
			foreach($DataAds as $SlAds)
			{
				$Act_id = $SlAds->act_id;
				
				$adsAccount = json_decode(json_encode($SlAds), true);
				$FindAds = $collection->findOne(["act_id" => $Act_id]);

				if(!empty($FindAds))
				{
					$resultUpdate = $collection->updateOne(["_id" => new MongoDB\BSON\ObjectID($FindAds->_id)], ['$set' => $adsAccount]);
					$reponse['status'] = true;
					$reponse['message'] = 'cập nhật thành công';
					$reponse['ads'] = $adsAccount;
				}
				else{
					$resultInsert = $collection->insertOne($adsAccount);
					$reponse['status'] = true;
					$reponse['message'] = 'thêm mới thành công';
					$reponse['ads'] = $adsAccount;
				}
			
			}
		}
		var_dump($Accountuse->Connect);
		var_dump($AccessToken);
		die('sssssss');
	}
	
	
    public function unlock(Request $request, $uid = null)
	{
		if (is_null($uid) || empty($uid) || !is_string($uid)) {
			return redirect()->route('facebook.pages')->with('error', 'UID không hợp lệ.');
		}

		$accounts = $this->accountRepository->searchAccounts(['uid' => $uid]);
		if (empty($accounts) || empty($accounts['data'])) {
			return redirect()->route('facebook.pages')->with('error', 'Không tìm thấy tài khoản.');
		}

		if ($request->isMethod('get')) {
			// Logic cho GET request
			die('sssssssss');
		} elseif ($request->isMethod('post')) {
			// Logic cho POST request
			die('vvvvvvvvv');
		}
	}
}
