<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class FileManagementService
{
    public static function uploadImage($value, $modul): string
    {
        $image  = Image::make($value)->resize(300, 300, fn ($constraint) => $constraint->aspectRatio())->stream('png', 70);
        $name   = Str::random(64) . '.png';
        $path   = "images/$modul/$name";

        Storage::put($path, $image);

        return $path;
    }
}
