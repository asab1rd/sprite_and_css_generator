<?php
require_once('getImages.php');
class SpriteCreator
{
    private $width, $height, $filename, $cssFilename;
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
            /**
             * CSS BUILDER
             */
            $cssFile = fopen($this->cssFilename, 'w'); //the CSS File
            $cssToWrite = "";
            $i = 0;
            foreach ($images as $image) {
                $cssClassName = "image" . ++$i; //Css class identifiers
                list($width, $height) = getimagesize($image);
                $pngImage = imagecreatefrompng($image);
                imagecopy($mySprite, $pngImage, $pointer, 0, 0, 0, $width, $height);
                $cssToWrite .= $this->createCSS($cssClassName, $width, $height, $image, $pointer);
                $pointer += $width;
            }
            fwrite($cssFile, $cssToWrite);
            //END
            imagepng($mySprite, $this->filename);
            imagedestroy($mySprite);
            fclose($cssFile);

            return true;
        } catch (\Throwable $th) {
            $th->getMessage();
            return false;
        }
    }
    public function createCSS($name, $width, $height, $imgUrl, $backgroundStart)
    {
        print("$name, $width, $height, $imgUrl, $backgroundStart");
        $name = "." . $name;
        $width = "width : $width" . "px;";
        $height = "height : $height" . "px;";
        $backgroundImage = "background-image: url(" . $this->filename . ") -$backgroundStart px;";
        $cssProprieties = "$name{
                            $width
                            $height
                            $backgroundImage
        }";

        return $cssProprieties . PHP_EOL;
    }
}

// $images = getAllFiles("test", true);
// $sprite = new SpriteCreator($images);
// var_dump($sprite->createSpriteAndCSS());
