<?php

/**
 * @return array of all files in current directory
 */
function getFiles($dir)
{
    $array = array();
    try {
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if (is_file($entry)) {
                    array_push($array);
                }
            }
        }
    } catch (\Throwable $th) {
        $th->getMessage();
    }
    return $array;
}




/**
 * @return array of all files in current directory & sub-directories
 */
function getAllFiles($dir, $array = array())
{
    try {
        $handle = opendir($dir);
        while ($entry = readdir($handle)) {
            if ($entry !== "." && $entry !== "..") {
                if (is_dir($dir . "/" . $entry)) {
                    print "Hello" . PHP_EOL;
                    $array = getAllFiles($dir . "/" . $entry, $array);
                    # code...
                } else {
                    array_push($array, $dir . "/" . $entry);
                }
            }
        }
        return $array;
    } catch (\Throwable $th) {
        $th->getMessage();
    }
}

var_dump(getAllFiles("test"));


/**
 * @return true if a file got the given extention given
 *
 */
function isImage(string $filename, string $ext = ".png")
{
    if (strpos($filename, $ext) !== false) {
        return true;
    } else {
        return false;
    }
}
