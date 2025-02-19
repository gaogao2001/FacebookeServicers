<?php
namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;

class SystemHistory extends Controller
{
	protected $history;

    public function __construct()
    {
        //$this->history = $historyRepository;
    }
	
	public function index()
    {
        die('ssssssssss');
    }
}