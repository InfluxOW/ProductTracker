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

function replaceArrayKeysWithMapper(array $data, array $mapper, $unsetNotFoundKeys = true)
{
    foreach ($data as $key => $value) {
        if ($unsetNotFoundKeys) {
            unset($data[$key]);
        }

        if (array_key_exists($key, $mapper)) {
            $data[$mapper[$key]] = $value;
        }
    }

    return $data;
}

function replaceArrayValuesWithMapper(array $data, array $mapper)
{
    foreach ($data as $key => $value) {
        foreach ($mapper as $option => $attribute) {
            if (! is_null($value) && str_contains($value, $option)) {
                $data[$key] = str_replace($option, $attribute, $data[$key]);
            }
        }
    }

    return $data;
}
