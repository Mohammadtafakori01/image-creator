<?php
require 'CmdImage.php';

$text = "sudo makefile to use";

$image = new CmdImage();
$image->createImageObject(1080, 1080);
$image->createBackgroundRandomLight();
$image->addBackground('./linux.jpg', 50);
$image->addText($text, 50, 'C:\Windows\Fonts\arial.ttf', [0, 200, 0], [0, 0, 0, 63], 20);
$image->addSticker('./sticker.png');
$image->returnImage('jpeg');
?>
