<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
  return redirect('/login');
});
//all module routes
require_once base_path('app/Modules/Auth/routes.php');
require_once base_path('app/Modules/Dashboard/routes.php');
require_once base_path('app/Modules/Facebook/routes.php');
require_once base_path('app/Modules/ImageVideo/routes.php');
require_once base_path('app/Modules/Google/routes.php');
require_once base_path('app/Modules/Zalo/routes.php');
require_once base_path('app/Modules/Country/routes.php');
require_once base_path('app/Modules/Fanpage/routes.php');
require_once base_path('app/Modules/Role/routes.php');
require_once base_path('app/Modules/User/routes.php');
require_once base_path('app/Modules/Ads/routes.php');
require_once base_path('app/Modules/Link/routes.php');
require_once base_path('app/Modules/EmailScan/routes.php');
require_once base_path('app/Modules/ContentManager/routes.php');
require_once base_path('app/Modules/ConfigAuto/routes.php');
require_once base_path('app/Modules/SiteManager/routes.php');
require_once base_path('app/Modules/BackupData/routes.php');
require_once base_path('app/Modules/History/routes.php');
require_once base_path('app/Modules/Network/routes.php');
require_once base_path('app/Modules/FileManager/routes.php');
require_once base_path('app/Modules/Map/routes.php');
require_once base_path('app/Modules/Exploitation/routes.php');
require_once base_path('app/Modules/ServiceController/routes.php');
require_once base_path('app/InterfaceModules/DeviceEmulator/Android/router.php');
require_once base_path('app/Modules/CronTab/router.php');
require_once base_path('app/Modules/ExploitationNow/routes.php');



///feMoudule
// require_once base_path('app/FeModules/Home/routes.php');


Route::get('/test', function (Request $request) {
  return view('test');
});
