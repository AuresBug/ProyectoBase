<?php

namespace App\Http\Controllers;

use Auresbug\Media\MediaUploader;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $file
     * @param $group
     * @param $model
     */
    protected function saveFile($file, $group = 'default', $model, $disk = 'private')
    {
        try {

            $media = MediaUploader::fromFile($file)
                ->useFileName(Str::random(64) . '.' . $file->getClientOriginalExtension())
                ->useName(Str::random(64))
                ->toDisk($disk)
                ->upload();

            $model->attachMedia($media, $group);

            return true;
        } catch (\Throwable $th) {

            logger($th);

            return false;
        }
    }
}
