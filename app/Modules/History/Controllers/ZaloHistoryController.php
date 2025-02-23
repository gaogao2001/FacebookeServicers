<?php

namespace App\Modules\History\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\History\Repositories\Zalo\ZaloHistoryRepositoryInterface;

class ZaloHistoryController extends Controller
{
    protected $zaloHistoryRepository;

    public function __construct(ZaloHistoryRepositoryInterface $zaloHistoryRepository)
    {
        $this->zaloHistoryRepository = $zaloHistoryRepository;
    }

    public function zaloHistoryPage()
    {
        return view('History::zalo_history');
    }

    public function index()
    {
        $zaloHistory = $this->zaloHistoryRepository->findAll();
        return response()->json($zaloHistory);
    }
}
