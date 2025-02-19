<?php

namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\History\ZaloHistory\ZaloHistoryRepositoryInterface as ZaloHistoryRepositoryInterface;

class ZaloHistoryController extends Controller
{
    protected $zaloHistoryRepository;

    public function __construct(ZaloHistoryRepositoryInterface $zaloHistoryRepository)
    {
        $this->zaloHistoryRepository = $zaloHistoryRepository;
    }

    public function zaloHistoryPage()
    {
        return view('admin.pages.History.zalo_history');
    }

    public function index()
    {
        $zaloHistory = $this->zaloHistoryRepository->findAll();
        return response()->json($zaloHistory);
    }
}
