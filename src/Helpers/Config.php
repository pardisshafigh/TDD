<?php
namespace App\Helpers;

use App\Exceptions\ConfigFileNotFoundException;

class Config{
    public static function getFileContents(string $filename){
        $filepath = realpath(__DIR__ . "/../Configs/" . $filename . ".php");
        if (!$filepath) {
            throw new ConfigFileNotFoundException();
        }
        $fileContents = require $filepath;
        return $fileContents;
    }    
    public static function get(string $filename, string $key = null){
        $fileContents = self::getFileContents($filename);
        if(is_null($key)) return $fileContents;
        return $fileContents[$key] ?? null;
    }
}