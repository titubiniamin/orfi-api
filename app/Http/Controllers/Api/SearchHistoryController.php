<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SearchingHistory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SearchHistoryController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getHistories(Request $request): JsonResponse
    {
        $searchQuestion = $request->searchQuestion ?? null;
        $histories = SearchingHistory::query()
            ->where('user_id', Auth::id())
            ->where('question', 'like', '%' . $searchQuestion . '%')
            ->latest()
            ->simplePaginate(20)
            ->groupBy(fn($query) => Carbon::parse($query->created_at)->format('l, F d, Y'));

        return response()->json(['histories' => $histories, 'status' => 200]);
    }

    /**
     * @return JsonResponse
     */
    public function getSearchCount(): JsonResponse
    {
        $noOfSearch = SearchingHistory::query()->where('user_id', Auth::id())->count();
        return response()->json(['noOfSearch' => $noOfSearch, 'status' => 200]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteHistories(Request $request): JsonResponse
    {
        $message = count($request->ids) > 1 ? 'Histories' : 'History';
        SearchingHistory::query()->where('user_id', Auth::id())->whereIn('id', $request->ids)->delete();
        return response()->json(['message' => "{$message} deleted successfully.", 'status' => 200]);
    }

    /**
     * @return JsonResponse
     */
    public function getHistoriesForSuggestion()
    {
        $histories = SearchingHistory::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get('question');
        return response()->json(['histories' => $histories, 'status' => 200]);
    }
}
