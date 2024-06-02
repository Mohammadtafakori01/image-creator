<?php
require "ImageCreatorInterface.php";
require "ColorGenerator.php";

require "ImageLoader.php";

class CmdImage implements ImageCreatorInterface
{
    private $image;
    private $width;
    private $height;
    private $colorGenerator;
    private $imageLoader;

    public function __construct()
    {
        $this->imageLoader = new ImageLoader();
    }

    public function createImageObject($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->image = imagecreatetruecolor($width, $height);
        $this->colorGenerator = new ColorGenerator($this->image);
    }

    public function addBackground($urlOrPath, $opacity = 100)
    {
        $bgImage = $this->imageLoader->createImageFromPath($urlOrPath);
        $bgWidth = imagesx($bgImage);
        $bgHeight = imagesy($bgImage);

        $scale = max($this->width / $bgWidth, $this->height / $bgHeight);
        $newBgWidth = intval($bgWidth * $scale);
        $newBgHeight = intval($bgHeight * $scale);

        $resizedBgImage = imagecreatetruecolor($newBgWidth, $newBgHeight);
        imagealphablending($resizedBgImage, false);
        imagesavealpha($resizedBgImage, true);
        imagecopyresampled($resizedBgImage, $bgImage, 0, 0, 0, 0, $newBgWidth, $newBgHeight, $bgWidth, $bgHeight);

        $x = intval(($this->width - $newBgWidth) / 2);
        $y = intval(($this->height - $newBgHeight) / 2);
        imagecopymerge($this->image, $resizedBgImage, $x, $y, 0, 0, $newBgWidth, $newBgHeight, $opacity);

        imagedestroy($bgImage);
        imagedestroy($resizedBgImage);
    }

    public function createBackgroundRandomLight()
    {
        $color = $this->colorGenerator->getRandomLightColor();
        imagefill($this->image, 0, 0, $color);
    }

    private function validateFontFile($fontFile)
    {
        if (!file_exists($fontFile)) {
            throw new Exception("Font file not found: $fontFile");
        }
    }

    private function drawRoundedRectangle($x1, $y1, $x2, $y2, $radius, $color)
    {
        imagefilledarc($this->image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color, IMG_ARC_PIE);
        imagefilledarc($this->image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $color, IMG_ARC_PIE);
        imagefilledarc($this->image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 0, 90, $color, IMG_ARC_PIE);
        imagefilledarc($this->image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color, IMG_ARC_PIE);

        imagefilledrectangle($this->image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($this->image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
    }

    private function drawTextBox($text, $fontSize, $fontFile, $boxColor, $padding, $radius)
    {
        $lines = explode("\n", wordwrap($text, intval(($this->width - 2 * $padding) / ($fontSize * 0.75)), "\n"));
        $lineHeight = $fontSize * 1.5;

        $textBoxHeight = count($lines) * $lineHeight + 2 * $padding;
        $textBoxWidth = 0;
        foreach ($lines as $line) {
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $line);
            $lineWidth = $bbox[2] - $bbox[0];
            if ($lineWidth > $textBoxWidth) {
                $textBoxWidth = $lineWidth;
            }
        }
        $textBoxWidth += 2 * $padding;

        $xBox = intval(($this->width - $textBoxWidth) / 2);
        $yBox = intval(($this->height - $textBoxHeight) / 2);

        $boxColorAlloc = imagecolorallocatealpha($this->image, $boxColor[0], $boxColor[1], $boxColor[2], $boxColor[3]);
        $this->drawRoundedRectangle($xBox, $yBox, $xBox + $textBoxWidth, $yBox + $textBoxHeight, $radius, $boxColorAlloc);

        return ['xBox' => $xBox, 'yBox' => $yBox, 'textBoxWidth' => $textBoxWidth, 'textBoxHeight' => $textBoxHeight, 'lines' => $lines, 'lineHeight' => $lineHeight];
    }

    public function addText($text, $fontSize = 12, $fontFile = 'arial.ttf', $textColor = [0, 0, 0], $boxColor = [0, 0, 0, 127], $radius = 20)
    {
        $this->validateFontFile($fontFile);

        $padding = 50;
        $boxData = $this->drawTextBox($text, $fontSize, $fontFile, $boxColor, $padding, $radius);

        $color = imagecolorallocate($this->image, $textColor[0], $textColor[1], $textColor[2]);
        $yText = $boxData['yBox'] + $padding + $fontSize;
        foreach ($boxData['lines'] as $line) {
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $line);
            $textWidth = $bbox[2] - $bbox[0];
            $xText = $boxData['xBox'] + intval(($boxData['textBoxWidth'] - $textWidth) / 2);
            imagettftext($this->image, $fontSize, 0, $xText, $yText, $color, $fontFile, $line);
            $yText += $boxData['lineHeight'];
        }
    }

    public function addSticker($stickerPath)
    {
        $sticker = $this->imageLoader->createImageFromPath($stickerPath);
        $stickerWidth = imagesx($sticker);
        $stickerHeight = imagesy($sticker);

        $newStickerHeight = intval($this->height / 10);
        $scale = $newStickerHeight / $stickerHeight;
        $newStickerWidth = intval($stickerWidth * $scale);

        $resizedSticker = imagecreatetruecolor($newStickerWidth, $newStickerHeight);
        imagealphablending($resizedSticker, false);
        imagesavealpha($resizedSticker, true);
        imagecopyresampled($resizedSticker, $sticker, 0, 0, 0, 0, $newStickerWidth, $newStickerHeight, $stickerWidth, $stickerHeight);

        imagecopy($this->image, $resizedSticker, 0, $this->height - $newStickerHeight, 0, 0, $newStickerWidth, $newStickerHeight);

        imagedestroy($sticker);
        imagedestroy($resizedSticker);
    }

    public function returnImage($type = 'jpeg', $filePath = null)
    {
        if ($filePath) {
            switch ($type) {
                case 'jpeg':
                    imagejpeg($this->image, $filePath);
                    break;
                case 'png':
                    imagepng($this->image, $filePath);
                    break;
                case 'gif':
                    imagegif($this->image, $filePath);
                    break;
            }
        } else {
            header('Content-Type: image/' . $type);
            switch ($type) {
                case 'jpeg':
                    imagejpeg($this->image);
                    break;
                case 'png':
                    imagepng($this->image);
                    break;
                case 'gif':
                    imagegif($this->image);
                    break;
            }
            move("./img.png", imagejpeg($this->image));
        }
        imagedestroy($this->image);
    }
}
?>