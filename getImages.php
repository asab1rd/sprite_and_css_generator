<?php

/**
 * @return array of all files in current directory & sub-directories
 */
function getAllFiles($dir, $recursive = false, $array = array())
{
    try {
        $handle = opendir($dir);
        while ($entry = readdir($handle)) {
            if ($entry !== "." && $entry !== "..") {
                $path = $dir . "/" . $entry;
                if ($recursive && is_dir($path)) {
                    $array = getAllFiles($path, true, $array);
                } else if (is_file($path)) {
                    array_push($array, $path);
                }
            }
        }
        return $array;
    } catch (\Throwable $th) {
        $th->getMessage();
    }
}


/**
 * @return true if a file got the given extention given
 * This function will probably not be used in this project
 */
function isImage(string $filename, string $extention = ".png")
{
    if (strpos($filename, $extention) !== false) {
        return true;
    } else {
        return false;
    }
}
