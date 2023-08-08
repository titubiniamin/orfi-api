<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileUploadInS3 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $file;
    private $path_with_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $path_with_name)
    {
        $this->path_with_name = $path_with_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->file = Storage::disk('public')->get($this->path_with_name);
        Storage::disk('s3')->put($this->path_with_name, $this->file);
        if (Storage::disk('s3')->exists($this->path_with_name)) {
            Storage::disk('public')->delete($this->path_with_name);
        }
        Log::info('File Uploaded successfully in S3');
    }
}
