<?php

// Example usage:
$inputPath = 'input.jpg';
$outputPath = 'output.txt';
$reconstructedPath = 'reconstructed.jpg';

$codebookSize = 16;
$blockSize = 8;
$numIterations = 10;

$vq = new VectorQuantizer($codebookSize, $blockSize);

// Train the codebook
$vq->train($inputPath, $numIterations);

// Encode the input image
$vq->encode($inputPath, $outputPath);

// Decode the indices and reconstruct the image
$vq->decode($outputPath, $reconstructedPath);
