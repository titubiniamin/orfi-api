<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annotation extends Model
{
    use HasFactory;

    protected $connection = 'mysql_second';
    protected $table = 'annotations';
}
