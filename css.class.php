<?php

class SpriteCSS
{
    private $name, $width, $height, $backgroundImage;
    public function __construct($name, $width, $height, $imgUrl, $backgroundStart)
    {
        $this->name = "." . $name;
        $this->width = "width : $width" . "px;";
        $this->height = "height : $height" . "px;";
        $this->backgroundImage = "background-image: url($imgUrl) -$backgroundStart px;";
    }


    public function createCSS($name, $width, $height, $imgUrl, $backgroundStart)
    {
        $name = "." . $name;
        $width = "width : $width" . "px;";
        $height = "height : $height" . "px;";
        $backgroundImage = "background-image: url($imgUrl) -$backgroundStart px;";
        $cssProprieties = "$name{
                            $width
                            $height
                            $backgroundImage
        }\n";
        return $cssProprieties;
    }
    public function __toString()
    {
        return "Je genere du css Ouais Ouais";
    }
}
