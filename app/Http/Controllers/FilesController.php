<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auresbug\Media\MediaUploader;
use Auresbug\Media\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Exists;

class FilesController extends Controller
{

    /**
     * @param Request      $request
     * @param $filenName
     * @param $group
     */
    public static function getFile(Request $request, $filenName, $conversion = '')
    {
        $media = Media::where('name', $filenName)->first();
        $disk  = $media->disk;
        $path  = $media->getPath($conversion);

        abort_if($disk == 'private' && !Auth::check(), 404);

        abort_if(!Storage::disk($disk)->exists($path), 404);

        return Storage::disk($disk)->download($path);

    }

    /**
     * @param $file
     * @param $model
     * @param $disk
     * @param $group
     */
    public static function saveFile($file, $model, $disk = 'private', $group = 'default', $name = null)
    {
        try {

            if (($name)) {

                $count = 1;

                do {
                    $name_to_save = Str::slug($name) . '_' . Str::random($count);
                    $count++;
                } while (Media::where('name', $name_to_save)->exists());

                $name = $name_to_save;

            }

            $media = MediaUploader::fromFile($file)
                ->useFileName(($name ?? Str::random(64)) . '.' . $file->getClientOriginalExtension())
                ->useName(($name ?? Str::random(64)))
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
