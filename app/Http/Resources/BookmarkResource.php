<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'index_id' => $this->index_id,
            'question' => $this->answer->question->question,
            'question_image' =>$this->answer->question->annotation->cropped_image ? env('AWS_CLOUD_FRONT_URL') . $this->answer->question->annotation->cropped_image : null,
            'answer' => $this->answer->answer,
            'answer_image' => $this->answer->annotation->cropped_image ? env('AWS_CLOUD_FRONT_URL') . $this->answer->annotation->cropped_image : null,
            'created_at' => $this->created_at ? $this->created_at->diffForHumans() : null,
        ];
    }
}
