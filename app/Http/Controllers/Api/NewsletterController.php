<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Newsletter\StoreNewsletterRequest;
use App\Models\Newsletter;
use Illuminate\Http\JsonResponse;

class NewsletterController extends Controller
{
    /**
     * @param StoreNewsletterRequest $request
     * @return JsonResponse
     */
    public function store(StoreNewsletterRequest $request): JsonResponse
    {
        Newsletter::create(['user_id' => auth()->id() ?? null, 'email' => $request->email]);
        return response()->json(['message' => 'Email store Successfully', 'status' => 200]);
    }
}
