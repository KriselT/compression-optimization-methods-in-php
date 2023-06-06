<?php
// Example usage:
$inputImage = 'input.jpg';
$outputImage = 'output.jpg';

// Load the input image and convert it to grayscale
$input = imagecreatefromjpeg($inputImage);
$width = imagesx($input);
$height = imagesy($input);
$grayscale = imagecreatetruecolor($width, $height);
imagecopy($grayscale, $input, 0, 0, 0, 0, $width, $height);
imagefilter($grayscale, IMG_FILTER_GRAYSCALE);

// Convert the grayscale image into a matrix
$inputMatrix = [];
for ($y = 0; $y < $height; $y++) {
    for ($x = 0; $x < $width; $x++) {
        $rgb = imagecolorat($grayscale, $x, $y);
        $gray = ($rgb >> 16) & 0xFF;
        $inputMatrix[$y][$x] = $gray;
    }
}

// Apply the DCT to the input matrix
$dct = new DiscreteCosineTransform();
$dctMatrix = $dct->applyDCT($inputMatrix);

// Apply the inverse DCT to reconstruct the image
$reconstructedMatrix = $dct->applyInverseDCT($dctMatrix);

// Create the output image from the reconstructed matrix
$output = imagecreatetruecolor($width, $height);
for ($y = 0; $y < $height; $y++) {
    for ($x = 0; $x < $width; $x++) {
        $gray = $reconstructedMatrix[$y][$x];
        $color = imagecolorallocate($output, $gray, $gray, $gray);
        imagesetpixel($output, $x, $y, $color);
    }
}

// Save the output image
imagejpeg($output, $outputImage, 100);

// Free up memory
imagedestroy($input);
imagedestroy($grayscale);
imagedestroy($output);
