<?php
require_once('getImages.php');
class SpriteCreator
{
    private $width, $height, $filename, $cssFilename, $htmlFilename = "index.html";
    private $isPadding, $isOverride, $isColumn, $maxHeight;
    protected $arrayOfImages = array();
    public function __construct(array $images, $filename = "sprite.png", $cssFilename = "style.css", $isOverride = false, $isPadding = 0, $isColumn = 0)
    {
        list($width, $height) = $this->getDimension($images, $isPadding);

        if ($isOverride) {
            $height = $isOverride;
        }
        $this->maxHeight = $height;
        if ($isColumn != 0) {
            $height = $height * (int) count($images) / $isColumn;
            // $width = $width / (int) count($images) / $isColumn;
        }
        $this->width = $width;
        $this->height = $height;
        $this->arrayOfImages = $images;
        $this->filename = $filename;
        $this->cssFilename = $cssFilename;
        $this->isOverride = $isOverride;
        $this->isPadding = $isPadding;
        $this->isColumn = $isColumn;
    }
    public function maxWidth()
    { }

    /**
     * @return array index 0 => sum of all widths or (null if an error occurs) | index 1 => height of the highest image (null if an error occurs)
     * @return false
     */
    private function getDimension(array $images, $isPadding)
    {
        $arrayOfHeights = array();
        $width = 0;
        $height = 0;
        if (is_array($images)) {
            try {
                // Il faudrait checker si le fichier existe & Que c'est bien une image;
                foreach ($images as $image) {
                    if (file_exists($image)) {
                        array_push($arrayOfHeights, getimagesize($image)[1]);
                        $width += getimagesize($image)[0] + $isPadding;
                    }
                }
            } catch (\Throwable $th) {
                $th->getMessage();
                $dimensions = [null, null];
                return $dimensions;
            }
        }
        $height = max($arrayOfHeights);
        // La hauteur de l'image sera la hauteur maximale des images données
        $dimensions = [$width, $height];
        return $dimensions;
    }

    /**
     * get the sprite width
     */
    public function getWidth()
    {
        return $this->width;
    }
    /**
     * get the sprite height
     */
    public function getHeight()
    {
        return $this->height;
    }
    /**
     * create a sprite
     * uses array of images passed in the controller
     * @return true if the sprite has been created
     * @return false if an error occured
     */
    public function createSpriteAndCSS()
    {
        try {
            $mySprite = imagecreatetruecolor($this->width, $this->height);
            // TRANSPARENCY
            imagesavealpha($mySprite, true);
            $alpha = imagecolorallocatealpha($mySprite, 0, 0, 0, 127);
            imagefill($mySprite, 0, 0, $alpha);
            $pointer = 0; // The actual position of the pointer is when the next image should start.
            $pointerY = 0;
            $images = $this->arrayOfImages;

            $cssToWrite = "";
            $i = 0; //for imagename, csspropriety and htmlclassname

            $columCopiedImageCount = 1; // column
            $allDivs = "";
            foreach ($images as $image) {
                list($width, $height) = getimagesize($image);
                $pngImage = imagecreatefrompng($image);
                if ($this->isOverride) {
                    imagecopyresized($mySprite, $pngImage, $pointer, $pointerY, 0, 0, $this->isOverride, $this->isOverride, $width, $height);
                } else {
                    imagecopy($mySprite, $pngImage, $pointer, $pointerY, 0, 0, $width, $height);
                }
                $htmlClassName = "image" . ++$i; //html class identifiers
                $cssToWrite .= $this->createCSS($htmlClassName, $width, $height, $pointer);
                $allDivs .= $this->createDiv($htmlClassName);
                if ($columCopiedImageCount == $this->isColumn) {
                    $pointerY += $this->maxHeight; // pointer
                    $pointer = 0;
                    $columCopiedImageCount = 0;
                } else {
                    $pointer += $width + $this->isPadding;
                }
                $columCopiedImageCount++;
            }
            $htmlToWrite = $this->createHTML($allDivs);
            /**
             * CSS & HTML BUILDER
             */
            $cssFile = fopen($this->cssFilename, 'w'); //the CSS File
            $htmlFile = fopen($this->htmlFilename, 'w'); //html file
            fwrite($cssFile, $cssToWrite);
            fwrite($htmlFile, $htmlToWrite);
            //END CSS  HTML BUILDER
            imagepng($mySprite, $this->filename);
            imagedestroy($mySprite);
            fclose($cssFile);
            fclose($htmlFile);

            return true;
        } catch (\Throwable $th) {
            $th->getMessage();
            return false;
        }
    }
    /**
     * @return CSS propriety with given name, height, and background start.
     */
    public function createCSS($name, $width, $height, $backgroundStart)
    {
        $name = "." . $name;
        $width = "width : $width" . "px;";
        $height = "height : $height" . "px;";
        $backgroundImage = "background: url(" . $this->filename . ") -$backgroundStart" . "px 0;";
        $cssProprieties = "$name{
                            $width
                            $height
                            $backgroundImage
        }";

        return $cssProprieties . PHP_EOL;
    }

    /**
     * put the "divClass" on the div class
     * @return HTML div
     */
    public function createDiv($divClass)
    {
        return "<div class=\"$divClass\"></div><br>" . PHP_EOL;
    }

    /**
     * "allDivs" will be put in the body
     * @return HTML page
     */
    public function createHTML($allDivs)
    {
        $html = "<!DOCTYPE html>
        <html lang=\"en\">
        <head>
            <meta charset=\"UTF-8\">
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
            <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
            <title>Document</title>
            <link rel=\"stylesheet\" href=\"$this->cssFilename\">
        </head>
        <body>
            $allDivs
        </body>
        </html>
        ";
        return $html;
    }
}
