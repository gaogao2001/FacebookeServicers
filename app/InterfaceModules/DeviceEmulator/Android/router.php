<?php

use Illuminate\Support\Facades\Route;
use App\InterfaceModules\DeviceEmulator\Android\Controllers\Profile\HomeAccountController;
use App\InterfaceModules\DeviceEmulator\Android\Controllers\Profile\AccountInfoController;
use App\InterfaceModules\DeviceEmulator\Android\Controllers\Profile\AndroidController;
use App\InterfaceModules\DeviceEmulator\Android\Controllers\Profile\MarketPlaceController;
use App\InterfaceModules\DeviceEmulator\Android\Controllers\DeviceEmulatorController;
use Illuminate\Routing\Router;

/* Khu vực router dành cho Android */

//Home controller (Thực hiện các tác vụ hiên thị ở trang chủ)
Route::get('/Android/Main/{uid?}', [HomeAccountController::class, 'Home'])
    ->where('uid', '.*')
    ->name('android.main');

Route::get('/Android/Search-result/{uid?}', [HomeAccountController::class, 'searchResult'])
    ->where('uid', '.*')
    ->name('android.searchResult');

Route::get('Android/SyncFanpage/{uid}', [HomeAccountController::class, 'syncFanpage'])
    ->where('uid', '.*')
    ->name('android.syncFanpage');

Route::post('Android/Start-sub', [HomeAccountController::class, 'SubNow'])
    ->name('android.startSub');

Route::get('Android/SyncAllFanpage', [HomeAccountController::class, 'syncAllFanpage'])
    ->name('android.syncAllFanpage');


//AccountInfo Controller  (Thực hiện các tác vụ tương tác với thông tin cá nhân)
Route::get('/Android/Profile/{uid}', [AccountInfoController::class, 'showProfile'])
    ->where('uid', '.*')
    ->name('profile.view');

Route::get('/Android/Getfriend/{uid}', [AccountInfoController::class, 'getFriend'])
    ->where('uid', '.*')
    ->name('profile.getfriend');

Route::get('/Android/GetTimeLine/{uid}', [AccountInfoController::class, 'getTimeLine'])
    ->where('uid', '.*')
    ->name('profile.getTimeline');

Route::post('Android/update_post/{uid}', [AccountInfoController::class, 'updatePost'])
    ->where('uid', '.*')
    ->name('profile.updatePost');

Route::get('/Android/Fanpage/{uid}', [AccountInfoController::class, 'getFanpage'])
    ->where('uid', '.*')
    ->name('profile.getFanpage');

Route::get('/Android/Group/{uid}', [AccountInfoController::class, 'showGroup'])
    ->where('uid', '.*')
    ->name('profile.showGroup');

Route::get('/Android/UpdateGroups/{uid}',  [AccountInfoController::class, 'updateGroups'])
    ->where('uid', '.*')
    ->name('profile.updateGroups');

Route::get('/Android/Settings/{uid}', [AccountInfoController::class, 'settingProfile'])
    ->where('uid', '.*')
    ->name('profile.Settings');

Route::post('/Android/ChangePassword/{uid}', [AccountInfoController::class, 'changePassword'])
    ->where('uid', '.*')
    ->name('profile.changePassword');

Route::get('/Android/Video/{uid}', [AccountInfoController::class, 'showVideo'])
    ->where('uid', '.*')
    ->name('profile.showVideo');


//Android Controller (Thực hiện các tác vụ tương tác với các hoạt động trên facebook như like, comment, share, post ,...)
Route::post('/Android/PostStatus/{uid}', [AndroidController::class, 'postStatus'])
    ->where('uid', '.*')
    ->name('profile.postStatus');

Route::post('Android/getComment/{uid}', [AndroidController::class, 'getComment'])
    ->where('uid', '.*')
    ->name('profile.getComment');

Route::post('Android/addReaction/{uid}', [AndroidController::class, 'addReaction'])
    ->where('uid', '.*')
    ->name('profile.addReaction');

Route::post('Android/add_friend/{uid}', [AndroidController::class, 'addFriend'])
    ->where('uid', '.*')
    ->name('profile.addFriend');

Route::post('Android/remove_friend/{uid}', [AndroidController::class, 'removeFriend'])
    ->where('uid', '.*')
    ->name('profile.removeFriend');

Route::post('/Android/Join-groups/{uid}', [AndroidController::class, 'joinGroup'])
    ->where('uid', '.*')
    ->name('profile.joinGroup');

Route::post('/Android/Accept-friend/{uid}', [AndroidController::class, 'acceptFriend'])
    ->where('uid', '.*')
    ->name('profile.acceptFriend');
    
Route::post('/Android/like-follow/{uid}', [AndroidController::class, 'likeFollow'])
    ->where('uid', '.*')
    ->name('profile.likeFollow');


//Market Place Controller (Thực hiện các tác vụ tương tác với Marketplace)

Route::get('Android/ShowMarket/{uid}', [MarketPlaceController::class, 'showMarket'])
    ->where('uid', '.*')
    ->name('profile.showMarket');

// Route::get('Android/ShowCategories/{uid}', [MarketPlaceController::class, 'showCategories'])
//     ->where('uid', '.*')
//     ->name('profile.showCategories');

Route::post('Android/SearchProduct/{uid}', [MarketPlaceController::class, 'searchProduct'])
    ->where('uid', '.*')
    ->name('profile.searchProduct');

Route::get('Android/SetLocation/{uid}', [MarketPlaceController::class, 'setLocation'])
    ->where('uid', '.*')
    ->name('profile.setLocation');

Route::post('Android/PostMarket/{uid}', [MarketPlaceController::class, 'postMarket'])
    ->where('uid', '.*')
    ->name('profile.postMarket');

Route::get('Android/ShowMyPost/{uid}', [MarketPlaceController::class, 'showMyPost'])
    ->where('uid', '.*')
    ->name('profile.showMyPost');

Route::post('/Android/DeletePost/{uid}', [MarketPlaceController::class, 'deletePost'])
    ->where('uid', '.*')
    ->name('profile.deletePost');


///Device Emulator controller
Route::post('/post-video', [DeviceEmulatorController::class, 'postVideo']);
Route::post('/post-reels', [DeviceEmulatorController::class, 'postReels']);
Route::post('/live-video', [DeviceEmulatorController::class, 'liveVideo']);
Route::post('/stop-live', [DeviceEmulatorController::class, 'stopLive']);
Route::post('/export-video', [DeviceEmulatorController::class, 'ExportVideo']);

Route::post('/upload-avatar', [DeviceEmulatorController::class, 'uploadAvatar'])->name('upload.avatar');
Route::post('/DeviceEmulator/RenewSession', [DeviceEmulatorController::class, 'RenewSession']);
Route::post('/DeviceEmulator/CheckPageNameAvailability', [DeviceEmulatorController::class, 'CheckPageNameAvailability']);
Route::post('/DeviceEmulator/CheckCategoryNameAvailability', [DeviceEmulatorController::class, 'CheckCategoryNameAvailability']);
Route::post('/DeviceEmulator/CreateNewFanpage', [DeviceEmulatorController::class, 'CreateNewFanpage']);
