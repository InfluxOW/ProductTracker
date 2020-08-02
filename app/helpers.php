<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

function getFilesInfo($dir)
{
    $files = File::allFiles($dir);

    foreach ($files as $file) {
        $file->classname = str_replace(
            [app_path(), '/', '.php'],
            ['App', '\\', ''],
            $file->getRealPath()
        );
    }

    return $files;
}

function toLowercaseWord(string $string) : string {
    return Str::of($string)->replace(' ', '')->lower();
}
