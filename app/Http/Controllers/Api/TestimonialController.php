<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Testimonial\StoreTestimonialRequest;
use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TestimonialController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $testimonials = Testimonial::active()->with('user:id,first_name,last_name,avatar')->get();

        return response()->json(TestimonialResource::collection($testimonials));
    }

    /**
     * @param StoreTestimonialRequest $request
     * @return JsonResponse
     */
    public function store(StoreTestimonialRequest $request): JsonResponse
    {
        $request = $request->all();
        $request['user_id'] = Auth::id() ?? 1; //Todo this 1 will be remove next time.
        Testimonial::create($request);

        return response()->json(['message' => 'Testimonial saved Successfully'], 200);
    }

    /**
     * @return JsonResponse
     */
    public function getTestimonialByUser(): JsonResponse
    {
        $testimonials = Testimonial::where('user_id', Auth::id())->active()->with('user:id,first_name,last_name,avatar')->get();

        return response()->json(TestimonialResource::collection($testimonials));
    }

    /**
     * @param StoreTestimonialRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(StoreTestimonialRequest $request, $id): JsonResponse
    {
        Testimonial::find($id)->update($request->all());

        return response()->json(['message' => 'Testimonial updated Successfully'], 200);
    }
}
