<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auresbug\Media\MediaUploader;
use Auresbug\Media\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilesController extends Controller
{
    /**
     * Retrieve and download a file by its name and conversion type.
     *
     * @param  Request                                              $request
     * @param  string                                               $fileName
     * @param  string|null                                          $conversion
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function getFile(Request $request, string $fileName, string $conversion = '')
    {
        $media = Media::where('name', $fileName)->firstOrFail();
        $disk  = $media->disk;
        $path  = $media->getPath($conversion);

        // Check if the disk is private and the user is authenticated
        if ($disk === 'private' && !Auth::check()) {
            abort(404);
        }

        // Abort if the file does not exist
        if (!Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        // Return the file for download

        return Storage::disk($disk)->download($path);
    }

    /**
     * Upload and save a file to a specified model and group.
     *
     * @param  \Illuminate\Http\UploadedFile $file
     * @param  mixed                         $model
     * @param  string                        $disk
     * @param  string                        $group
     * @param  string|null                   $name
     * @return bool
     */
    public static function saveFile($file, $model, string $disk = 'private', string $group = 'default', string $name = null)
    {
        try {
            // Generate unique file name if provided
            if ($name) {
                $name = self::generateUniqueFileName($name);
            } else {
                $name = Str::random(64);
            }

            // Upload the file using MediaUploader
            $media = MediaUploader::fromFile($file)
                ->useFileName($name . '.' . $file->getClientOriginalExtension())
                ->useName($name)
                ->toDisk($disk)
                ->upload();

            // Attach the media to the model
            $model->attachMedia($media, $group);

            return true;
        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Generate a unique file name by appending a random string if the name exists.
     *
     * @param  string   $name
     * @return string
     */
    private static function generateUniqueFileName(string $name): string
    {
        $count = 1;

        do {
            $nameToSave = Str::slug($name) . '_' . Str::random($count);
            $count++;
        } while (Media::where('name', $nameToSave)->exists());

        return $nameToSave;
    }
}
