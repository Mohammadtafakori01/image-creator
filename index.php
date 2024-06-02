<?php
require 'image-creator/CmdImage.php';
require 'CommandHandler.php';
require 'TelegramBot.php';

$dbPath = 'commands.db';

// Usage example
$bot = new TelegramBot();
// Create an instance of the CommandHandler class
$commandHandler = new CommandHandler($dbPath);
$command = $commandHandler->readAndUpdateCommand();
$text = $command['example'];
$caption = "command: {$command['command']} in linux,
when use it? {$command['case_of_use']}.  
{$command['description']}
which options?  {$command['options']}";
$image = new CmdImage();
$image->createImageObject(1080, 1080);
$image->createBackgroundRandomLight();
$image->addBackground('./linux.jpg', 50);
$image->addText($text, 60, './times new roman.ttf', [0, 200, 0], [0, 0, 0, 63], 20);
$image->addSticker('./sticker.png');
$img = $image->returnImage('jpeg', './image.jpg');

$bot->send($caption, './image.jpg');
?>
