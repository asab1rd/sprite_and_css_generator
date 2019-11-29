<?php

function getArgs($folderPath)
{
    $options = getOptions();
    $isRecursive = false;
    $styleName = "style.css";
    $imageName = "sprite.png";
    $isPadding = 0;
    $isOverride = false;
    $columnNumbers = false;
    try {
        if (isset($options['h']) or isset($options['help'])) {
            $errorMessage = getDoc();
            throw new Exception($errorMessage, 1);
        }
        if (!is_dir($folderPath)) {
            $errorMessage = "Veuillez donner un dossier qui existe svp " . PHP_EOL;
            throw new Exception($errorMessage, 1);
        }
        if (isset($options['r']) or isset($options['recursive'])) {
            $isRecursive = true;
        }
        if (isset($options['i']) or isset($options['output-image'])) {
            $imageName = isset($options['i']) ? $options['i'] : $options['output-image'];
        }
        if (isset($options['s']) or isset($options['output-style'])) {
            $styleName = isset($options['s']) ? $options['s'] : $options['output-style'];
        }
        if (isset($options['p']) or isset($options['padding'])) {
            $isPadding = isset($options['p']) ? $options['p'] : $options['padding'];
        }
        if (isset($options['o']) or isset($options['override-size'])) {
            $isOverride = isset($options['o']) ? $options['o'] : $options['override-size'];
        }
        if (isset($options['c']) or isset($options['columns_number'])) {
            $columnNumbers = isset($options['c']) ? $options['c'] : $options['columns_number'];
        }
        return array($isRecursive, $imageName . '.png', $styleName . '.css', $isPadding, $isOverride, $columnNumbers);
    } catch (\Throwable $th) {
        print $th->getMessage();
        print  "Pour la documentation veuillez utiliser -h ou --help." . PHP_EOL;
        die;
    }
}

function getOptions()
{
    $shortopts = "";
    $shortopts .= "i:";
    $shortopts .= "s:";
    $shortopts .= "p:";
    $shortopts .= "o:";
    $shortopts .= "c:";
    $shortopts .= "rh";

    $longopts = array(
        "output-image:",
        "output-style:",
        "padding:",
        "override-size:",
        "columns_number:",
        "recursive",
        "help"
    );
    return getopt($shortopts, $longopts);
}
