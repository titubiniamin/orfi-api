<?php

namespace App\Models;

use App\Models\CMS\Answer;
use App\Models\CMS\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'index_id'];

    public function answer()
    {
        return $this->belongsTo(Answer::class, 'index_id', 'index_id');
    }

    public function answerQuestion()
    {
        return $this->hasOneThrough(Question::class,Answer::class,'question_id', 'index_id', 'id');
    }
}
