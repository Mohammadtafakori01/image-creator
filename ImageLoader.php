<?php
class ImageLoader
{
    public function createImageFromPath($path)
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $path = $this->downloadImage($path);
        }

        $info = @getimagesize($path);
        if (!$info) {
            throw new Exception("Could not get image size for: $path");
        }

        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                return imagecreatefrompng($path);
            case 'image/gif':
                return imagecreatefromgif($path);
            default:
                throw new Exception("Unsupported image format: $mime");
        }
    }

    private function downloadImage($url)
    {
        $imageContents = file_get_contents($url);
        $tempPath = tempnam(sys_get_temp_dir(), 'img');
        file_put_contents($tempPath, $imageContents);
        return $tempPath;
    }
}