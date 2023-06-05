<?php

class HuffmanNode {
    public $char;
    public $frequency;
    public $left;
    public $right;

    public function __construct($char, $frequency, $left = null, $right = null) {
        $this->char = $char;
        $this->frequency = $frequency;
        $this->left = $left;
        $this->right = $right;
    }

    public function isLeaf() {
        return ($this->left === null && $this->right === null);
    }
}

function buildFrequencyTable($input) {
    $frequencyTable = [];
    for ($i = 0; $i < strlen($input); $i++) {
        $char = $input[$i];
        if (isset($frequencyTable[$char])) {
            $frequencyTable[$char]++;
        } else {
            $frequencyTable[$char] = 1;
        }
    }
    return $frequencyTable;
}

function buildHuffmanTree($frequencyTable) {
    $priorityQueue = new SplPriorityQueue();
    foreach ($frequencyTable as $char => $frequency) {
        $node = new HuffmanNode($char, $frequency);
        $priorityQueue->insert($node, -$frequency);
    }

    while ($priorityQueue->count() > 1) {
        $leftNode = $priorityQueue->extract();
        $rightNode = $priorityQueue->extract();

        $parentFrequency = $leftNode->frequency + $rightNode->frequency;
        $parentNode = new HuffmanNode(null, $parentFrequency, $leftNode, $rightNode);

        $priorityQueue->insert($parentNode, -$parentFrequency);
    }

    return $priorityQueue->top();
}

function buildCodeTable($root) {
    $codeTable = [];
    buildCodeTableRecursive($root, '', $codeTable);
    return $codeTable;
}

function buildCodeTableRecursive($node, $currentCode, &$codeTable) {
    if ($node->isLeaf()) {
        $codeTable[$node->char] = $currentCode;
        return;
    }

    buildCodeTableRecursive($node->left, $currentCode . '0', $codeTable);
    buildCodeTableRecursive($node->right, $currentCode . '1', $codeTable);
}

function huffmanCompress($inputFile, $outputFile) {
    $input = file_get_contents($inputFile);
    $frequencyTable = buildFrequencyTable($input);
    $huffmanTree = buildHuffmanTree($frequencyTable);
    $codeTable = buildCodeTable($huffmanTree);

    $compressedData = '';
    for ($i = 0; $i < strlen($input); $i++) {
        $char = $input[$i];
        $compressedData .= $codeTable[$char];
    }

    $padding = 8 - (strlen($compressedData) % 8);
    $compressedData .= str_repeat('0', $padding);

    $byteArray = [];
    for ($i = 0; $i < strlen($compressedData); $i += 8) {
        $byte = substr($compressedData, $i, 8);
        $byteArray[] = bindec($byte);
    }

    file_put_contents($outputFile, implode(',', $byteArray));
}

function huffmanDecompress($inputFile, $outputFile) {
    $compressedData = file_get_contents($inputFile);
    $compressedArray = explode(',', $compressedData);

    $binaryData = '';
    foreach ($compressedArray as $byte) {
        $binaryData .= str_pad(decbin($byte), 8, '0', STR_PAD_LEFT);
    }

    $padding = substr($binaryData, -8);
    $binaryData = substr($binaryData, 0, -8);
    $binaryData = substr($binaryData, 0, -bindec($padding));

    $codeTable = buildCodeTable(buildHuffmanTree(buildFrequencyTable($binaryData)));
    $currentCode = '';
    $output = '';

    for ($i = 0; $i < strlen($binaryData); $i++) {
        $currentCode .= $binaryData[$i];
        if (isset($codeTable[$currentCode])) {
            $output .= $codeTable[$currentCode];
            $currentCode = '';
        }
    }

    file_put_contents($outputFile, $output);
}
