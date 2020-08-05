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

function validateInput($rules, $fieldName, $value)
{
    $validator = Validator::make([
        $fieldName => $value
    ], [
        $fieldName => $rules
    ]);

    return $validator->fails()
        ? $validator->errors()->first($fieldName)
        : null;
}
