<?php
class ColorGenerator
{
    private $image;

    public function __construct($image)
    {
        $this->image = $image;
    }

    public function getRandomLightColor()
    {
        return imagecolorallocate($this->image, rand(200, 255), rand(200, 255), rand(200, 255));
    }
}