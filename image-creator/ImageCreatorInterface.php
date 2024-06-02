<?php
interface ImageCreatorInterface
{
    public function createImageObject($width, $height);
    public function addBackground($urlOrPath, $opacity = 100);
    public function addText($text, $fontSize, $fontFile, $textColor, $boxColor, $radius);
    public function addSticker($stickerPath);
    public function returnImage($type = 'jpeg', $filePath = null);
}