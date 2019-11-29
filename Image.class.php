<?php
require_once('getImages.php');
class SpriteCreator
{
    private $width, $height, $filename, $cssFilename, $htmlFilename = "index.html";
    protected $arrayOfImages = array();
    public function __construct(array $images, $filename = "sprite.png", $cssFilename = "style.css")
    {
        list($width, $height) = $this->getDimension($images);
        $this->width = $width;
        $this->height = $height;
        $this->arrayOfImages = $images;
        $this->filename = $filename;
        $this->cssFilename = $cssFilename;
    }

    /**
     * @return array index 0 => sum of all widths or (null if an error occurs) | index 1 => height of the highest image (null if an error occurs)
     * @return false
     */
    private function getDimension(array $images)
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
                        $width += getimagesize($image)[0];
                    }
                }
            } catch (\Throwable $th) {
                $th->getMessage();
                $dimensions = [null, null];
                return $dimensions;
            }
        }

        $height = max($arrayOfHeights); // La hauteur de l'image sera la hauteur maximale des images données
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
            $images = $this->arrayOfImages;

            $cssToWrite = "";
            $i = 0;
            $allDivs = "";
            foreach ($images as $image) {
                list($width, $height) = getimagesize($image);
                $pngImage = imagecreatefrompng($image);
                imagecopy($mySprite, $pngImage, $pointer, 0, 0, 0, $width, $height);
                $htmlClassName = "image" . ++$i; //html class identifiers
                $cssToWrite .= $this->createCSS($htmlClassName, $width, $height, $pointer);
                $allDivs .= $this->createDiv($htmlClassName);
                $pointer += $width;
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

    public function createDiv($divClass)
    {
        return "<div class=\"$divClass\"></div><br>" . PHP_EOL;
    }
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
