<?php

namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NetworkHistoryController extends Controller
{
    public function networkHistoryPage()
    {
        return view('admin.pages.History.network_history');
    }
}
