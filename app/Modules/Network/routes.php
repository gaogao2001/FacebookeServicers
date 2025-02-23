<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Network\Controllers\NetworkControler;
use App\Modules\Network\Controllers\ProxyV6\ProxyV6Controller;
use App\Modules\Network\Controllers\ProxyV4\ProxyV4Controller;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SessionTimeout;


Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {

    Route::get('/network_config', [NetworkControler::class, 'index'])->name('NetworkConfig');
});

Route::post('/UpdateSettingsNetwork', [NetworkControler::class, 'updateSettings'])->name('UpdateNetworkConfig.index');


Route::middleware([AdminMiddleware::class, SessionTimeout::class])->group(function () {

    Route::get('/proxy_v6_page', [ProxyV6Controller::class, 'proxySystemPage'])->name('proxyv6.index');
    Route::get('/proxy_v4_page', [ProxyV4Controller::class, 'proxyV4SystemPage']);

});
Route::get('/proxy-system', [ProxyV6Controller::class, 'index']);
Route::get('/proxy-system/search', [ProxyV6Controller::class, 'searchProxies']);
Route::get('/proxyv6/delete-all', [ProxyV6Controller::class, 'deleteAllProxies'])->name('proxyv6.deleteAll');
Route::post('/proxyv6/create', [ProxyV6Controller::class, 'create'])->name('proxyv6.create');
Route::post('/proxyv6/reload-ip/{id}', [ProxyV6Controller::class, 'ReloadIpv6'])->name('proxyv6.reloadIp');
Route::get('/proxyv6/check-ip/{id}', [ProxyV6Controller::class, 'CheckProxy'])->name('proxyv6.checkIp');
Route::get('/proxyv6/delete/{id}', [ProxyV6Controller::class, 'DeleteProxyv6'])->name('proxyv6.delete');
Route::post('/InterfaceControler', [NetworkControler::class, 'InterfaceControler']);
Route::post('/CreateProxyV4', [ProxyV4Controller::class, 'CreateProxyV4'])->name('proxyv4.create');
Route::post('/check-proxy-v6-status', [ProxyV6Controller::class, 'checkProxyV6Status'])->name('proxyv6.check_proxy_status');

//
Route::post('/proxyv4/delete', [ProxyV4Controller::class, 'DeleteProxyv4'])->name('proxyv4.delete');
Route::post('/proxyv4/connect', [ProxyV4Controller::class, 'ConnectProxyv4'])->name('proxyv4.connect');
Route::post('/proxyv4/disconnect', [ProxyV4Controller::class, 'DisConnectProxyv4'])->name('proxyv4.disconnect');
Route::post('/proxyv4/CheckTimeConnect', [ProxyV4Controller::class, 'CheckTimeConnect'])->name('proxyv4.checktimeconnect');
Route::post('/proxyv4/BtnReloadIp', [ProxyV4Controller::class, 'BtnReloadIp'])->name('proxyv4.reloadip');