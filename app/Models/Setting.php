<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    use CrudTrait;

    protected $fillable = ['title', 'short_description', 'background_image', 'logo', 'header_color'];

    /**
     * @param $value
     */
    public function setLogoAttribute($value)
    {
        $attribute_name = "logo";
        $disk = "public";
        $destination_path = "images/settings";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    /**
     * @param $value
     */
    public function setBackgroundImageAttribute($value)
    {
        $attribute_name = "background_image";
        $disk = "public";
        $destination_path = "images/settings";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }
}
