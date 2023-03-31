<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileService
{
    public static function upload ($file, $folder = null): string
    {
        $path = '/images/';

        if ($folder) {
            $path = "/$folder/";
        }

        $name = uniqid() . date('dmy') . '.' . $file->getClientOriginalExtension();

        Storage::put($path . $name, file_get_contents($file));

        return $path.$name;
    }
}
