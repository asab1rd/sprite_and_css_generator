<?php
require_once('getImages.php');
class SpriteCreator
{
    private $width, $height, $filename;
    protected $arrayOfImages = array();
    public function __construct(array $images)
    {
        list($width, $height) = $this->getDimension($images);
        $this->width = $width;
        $this->height = $height;
        $this->arrayOfImages = $images;
    }

    /**
     * @return array index 0 => sum of all widths or (null if an error occurs) | index 1 => height of the highest image (null if an error occurs)
     * @return false
     */
    private function getDimension(array $images, $filename = "sprite.png")
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
    public function createSprite()
    {
        try {
            $mySprite = imagecreatetruecolor($this->width, $this->height);
            $pointer = 0; // The actual position of the pointer is when the next image should start.
            $images = $this->arrayOfImages;
            foreach ($images as $image) {
                list($width, $height) = getimagesize($image);
                $pngImage = imagecreatefrompng($image);
                imagecopy($mySprite, $pngImage, $pointer, 0, 0, 0, $width, $height);
                $pointer += $width;
            }
            header('Content-Type: image/png');
            imagepng($mySprite, $this->filename);
            imagedestroy($mySprite);
            return true;
        } catch (\Throwable $th) {
            $th->getMessage();
            return false;
        }
    }
}

$images = ["test/no.png", "test/oui.png"];
$image = new SpriteCreator($images);
$image->createSprite();
