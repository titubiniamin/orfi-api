<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    protected $connection = 'mysql_second';
    protected $table = 'answers';

    /**
     * @return BelongsTo
     */
    public function question() : BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * @return BelongsTo
     */
    public function annotation() : BelongsTo
    {
        return $this->belongsTo(Annotation::class);
    }
}
