<?php

namespace App\Services;

use App\Jobs\FileUploadInS3;
use Illuminate\Support\Facades\Storage;

class FileUploadInCloud
{
    /**
     * @param $path_with_name
     * @param $file
     * @return mixed
     */
    public static function uploadBase64File($file, $path_with_name)
    {
        $file = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $file));
        Storage::disk('public')->put($path_with_name, $file);
        FileUploadInS3::dispatch($path_with_name);
        return $path_with_name;
    }


    public static function uploadFile($file, $path_with_name)
    {
        $path = Storage::disk('public')->put($path_with_name, $file);
        FileUploadInS3::dispatch($path);
        return $path;
    }
}
