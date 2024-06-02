# ImageCreator

ImageCreator is a PHP class that allows you to create and manipulate images. This class can create images, add backgrounds, text, and stickers, and then output the final image in various formats.

## Requirements

- PHP 7.4 or higher
- GD Library with FreeType support enabled

## Installation

1. Clone this repository or download the `CmdImage.php` file.
2. Ensure that the GD library with FreeType support is enabled in your PHP installation. You can check this by running `phpinfo();` and looking for the GD section.

## Usage

1. Create a new PHP file, e.g., `index.php`.
2. Include the `CmdImage.php` file.
3. Use the `CmdImage` class to create and manipulate your image.

### Example

Create an `index.php` file with the following content:

```php
<?php
require 'CmdImage.php';

// Instantiate the CmdImage class
$image = new CmdImage();

// Create a new image object with specified dimensions
$image->createImageObject(1080, 1080);

// Fill the background with a random light color
$image->createBackgroundRandomLight();

// Add a background image with specified opacity
$image->addBackground('./linux.jpg', 50);

// Add text with specified parameters
$image->addText("sudo ls -a make me happy with click on windows", 50, 'C:\Windows\Fonts\arial.ttf', [0, 200, 0], [0, 0, 0, 63], 20);

// Add a sticker image to the main image
$image->addSticker('./sticker.png');

// Output the final image as a JPEG file
$image->returnImage('jpeg');
?>
