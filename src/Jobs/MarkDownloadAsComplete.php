<?php

namespace AeroCrossSelling\Jobs;

use AeroCrossSelling\Models\CrossProductDownload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class MarkDownloadAsComplete implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public $model;
    
    public function __construct(CrossProductDownload $model)
    {
        $this->model = $model;
    }

    public function handle()
    {
        $this->model->complete = true;
        $this->model->save();
    }
}