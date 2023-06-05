<?php
function lzwCompress($inputFile, $outputFile) {
    $input = file_get_contents($inputFile);
    $dictionary = [];
    $output = [];
    $currentPhrase = $input[0];
    $nextCode = 256;

    for ($i = 1; $i < strlen($input); $i++) {
        $char = $input[$i];
        $phrase = $currentPhrase . $char;

        if (isset($dictionary[$phrase])) {
            $currentPhrase = $phrase;
        } else {
            $output[] = $dictionary[$currentPhrase];
            $dictionary[$phrase] = $nextCode;
            $nextCode++;
            $currentPhrase = $char;
        }
    }

    $output[] = $dictionary[$currentPhrase];

    $compressedData = implode(',', $output);
    file_put_contents($outputFile, $compressedData);
}

function lzwDecompress($inputFile, $outputFile) {
    $compressedData = file_get_contents($inputFile);
    $compressedArray = explode(',', $compressedData);
    $dictionary = [];
    $nextCode = 256;
    $output = '';

    $currentCode = intval(array_shift($compressedArray));
    $output .= chr($currentCode);

    foreach ($compressedArray as $code) {
        if (isset($dictionary[$code])) {
            $phrase = $dictionary[$code];
        } elseif ($code === $nextCode) {
            $phrase = $currentCode . $currentCode[0];
        } else {
            throw new Exception('Invalid compressed data');
        }

        $output .= $phrase;
        $dictionary[$nextCode] = $currentCode . $phrase[0];
        $nextCode++;
        $currentCode = $code;
    }

    file_put_contents($outputFile, $output);
}
