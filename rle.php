<?php
function runLengthEncode($inputFile, $outputFile) {
    $input = file_get_contents($inputFile);
    $encoded = '';
    $length = strlen($input);

    if ($length === 0) {
        return;
    }

    $count = 1;
    for ($i = 1; $i < $length; $i++) {
        if ($input[$i] === $input[$i - 1]) {
            $count++;
        } else {
            $encoded .= $input[$i - 1] . $count;
            $count = 1;
        }
    }

    // Add the last character and its count
    $encoded .= $input[$length - 1] . $count;

    file_put_contents($outputFile, $encoded);
}

function runLengthDecode($inputFile, $outputFile) {
    $input = file_get_contents($inputFile);
    $decoded = '';
    $length = strlen($input);

    if ($length === 0) {
        return;
    }

    $i = 0;
    while ($i < $length) {
        $char = $input[$i];
        $count = '';
        $i++;

        while ($i < $length && is_numeric($input[$i])) {
            $count .= $input[$i];
            $i++;
        }

        $count = intval($count);

        for ($j = 0; $j < $count; $j++) {
            $decoded .= $char;
        }
    }

    file_put_contents($outputFile, $decoded);
}
