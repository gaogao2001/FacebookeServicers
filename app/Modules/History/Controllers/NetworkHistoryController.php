<?php

namespace App\Modules\History\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NetworkHistoryController extends Controller
{
    public function networkHistoryPage()
    {
        return view('History::network_history');
    }
}
