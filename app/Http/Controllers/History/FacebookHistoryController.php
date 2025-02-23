<?php

namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\History\FacebookHistory\HistoryRepositoryInterface;
use MongoDB\BSON\ObjectId;

class FacebookHistoryController extends Controller
{
    protected $historyRepository;

    public function __construct(HistoryRepositoryInterface $historyRepository)
    {
        $this->historyRepository = $historyRepository;
    }

    public function facebookHistoryPage()
    {
        return view('admin.pages.History.facebook_history');
    }

    public function index()
    {
        $history = $this->historyRepository->findAll();

        return response()->json($history);
    }

    public function delete($id)
    {
        $this->historyRepository->delete($id);

        return response()->json(['message' => 'Delete success']);
    }

    public function allDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!empty($ids)) {
            $objectIds = array_map(function ($id) {
                return new ObjectId($id);
            }, $ids);
            $this->historyRepository->deleteMany(['_id' => ['$in' => $objectIds]]);
            return response()->json(['message' => 'Deleted successfully']);
        }

        return response()->json(['message' => 'No items selected'], 400);
    }
}
