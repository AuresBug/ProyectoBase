<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auresbug\Media\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{

    /**
     * @param Request      $request
     * @param $filenName
     * @param $group
     */
    public function __invoke(Request $request, $filenName, $group = '')
    {
        $media = Media::where('name', $filenName)->first();
        $disk  = $media->disk;
        $path  = $media->getURL($group);

        abort_if($disk == 'private' && !Auth::check(), 503);

        abort_if(
            !Storage::disk($disk)->exists($path),
            404,
            "The file doesn't exist. Check the path."
        );

        return Storage::disk($disk)->response($path);

    }
}
