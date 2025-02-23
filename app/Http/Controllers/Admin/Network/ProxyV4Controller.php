<?php

namespace App\Http\Controllers\Admin\Network;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProxyV4Controller extends Controller
{
    public function proxyV4SystemPage()
    {
        return view('admin.pages.Netwrork.proxy_v4_system');
    }
}
