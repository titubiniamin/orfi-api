<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookmarkStoreRequest;
use App\Http\Resources\BookmarkResource;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{

    public function getBookmarks(Request $request)
    {
        $searchQuestion = $request->searchQuestion ?? null;

        $bookMark = Bookmark::query()
            ->where('user_id', Auth::id())
            ->with(['answer' => function ($query) use ($searchQuestion) {
                    $query->select(['id', 'index_id', 'question_id', 'answer', 'annotation_id'])
                    ->whereHas('question', function ($q) use ($searchQuestion) {
                        $q->where('question', 'like', '%' . $searchQuestion . '%');
                    })
                    ->with('annotation:id,cropped_image')
                    ->with(['question'=> function ($query) {
                        $query->select(['id', 'question', 'annotation_id'])
                            ->with('annotation:id,cropped_image');
                    }])
                   ;
            }])
           ->latest()->paginate(20)
           ->filter(fn ($bookmark) => $bookmark->answer != null);

        return response()->json([
            'bookmarks' => BookmarkResource::collection($bookMark),
            'status' => 200
        ]);
    }

    /**
     * @param BookmarkStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function saveBookmark(BookmarkStoreRequest $request)
    {
        $bookMark = Bookmark::query();

        if ($bookMark->where(['user_id' => Auth::id(), 'index_id' => $request->index_id])->count())
            return response()->json(['message' => 'You have already added in Bookmark.', 'status' => 400]);

        $bookMark->create(['user_id' => Auth::id(), 'index_id' => $request->index_id]);

        return response()->json(['message' => 'Bookmark added Successfully.', 'status' => 200]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteBookmark($index_id)
    {
        Bookmark::query()->where('user_id', Auth::id())->where('index_id', $index_id)->delete();
        return response()->json(['message' => 'Bookmark removed Successfully.', 'status' => 200]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function userBookmark()
    {
        $bookMark = Bookmark::query()->where('user_id', Auth::id())->get(['index_id']);
        return response()->json(['bookMark' => $bookMark, 'status' => 200]);
    }
}
