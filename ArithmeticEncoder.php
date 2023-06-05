<?php

class ArithmeticEncoder {
    public function encode($input, $outputFile) {
        $low = 0.0;
        $high = 1.0;
        $range = 1.0;
        $code = '';

        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];
            $symbolLow = $this->getSymbolLow($char);
            $symbolHigh = $this->getSymbolHigh($char);

            $newLow = $low + $range * $symbolLow;
            $newHigh = $low + $range * $symbolHigh;
            $range = $newHigh - $newLow;
            $low = $newLow;

            while ($this->needsRenormalization($low, $high)) {
                $code .= $this->getFractionalPart($low);
                $low = $this->removeFractionalPart($low);
                $high = $this->removeFractionalPart($high);

                if ($low === 0) {
                    $code .= '0';
                }
            }
        }

        $code .= $this->getFractionalPart(($low + $high) / 2);

        $padding = 8 - (strlen($code) % 8);
        $code .= str_repeat('0', $padding);

        $byteArray = [];
        for ($i = 0; $i < strlen($code); $i += 8) {
            $byte = substr($code, $i, 8);
            $byteArray[] = bindec($byte);
        }

        file_put_contents($outputFile, implode(',', $byteArray));
    }

    private function getSymbolLow($symbol) {
        // Define your own logic for getting the low value for a given symbol
        // Return a float between 0.0 and 1.0
    }

    private function getSymbolHigh($symbol) {
        // Define your own logic for getting the high value for a given symbol
        // Return a float between 0.0 and 1.0
    }

    private function needsRenormalization($low, $high) {
        return (($low < 0.5) && ($high >= 0.5));
    }

    private function getFractionalPart($value) {
        $integerPart = intval($value);
        $fractionalPart = $value - $integerPart;
        $fractionalBinary = decbin($integerPart);
        return substr($fractionalBinary, 2) . str_pad(decbin($fractionalPart * 256), 8, '0', STR_PAD_LEFT);
    }

    private function removeFractionalPart($value) {
        return intval($value);
    }
}

class ArithmeticDecoder {
    public function decode($inputFile, $outputFile) {
        $compressedData = file_get_contents($inputFile);
        $compressedArray = explode(',', $compressedData);

        $binaryData = '';
        foreach ($compressedArray as $byte) {
            $binaryData .= str_pad(decbin($byte), 8, '0', STR_PAD_LEFT);
        }

        $padding = substr($binaryData, -8);
        $binaryData = substr($binaryData, 0, -8);
        $binaryData = substr($binaryData, 0, -bindec($padding));

        $low = 0.0;
        $high = 1.0;
        $range = 1.0;
        $decodedData = '';
        $value = 0;

        for ($i = 0; $i < strlen($binaryData); $i++) {
            $value <<= 1;
            $value |= intval($binaryData[$i]);

            $symbol = $this->getSymbol($low, $high, $range, $value);
            $decodedData .= $symbol;

            $symbolLow = $this->getSymbolLow($symbol);
            $symbolHigh = $this->getSymbolHigh($symbol);

            $newLow = $low + $range * $symbolLow;
            $newHigh = $low + $range * $symbolHigh;
            $range = $newHigh - $newLow;
            $low = $newLow;

            while ($this->needsRenormalization($low, $high)) {
                $low = $this->removeFractionalPart($low) << 1;
                $high = ($this->removeFractionalPart($high) << 1) + 1;
                $value = ($value << 1) & 0xFF;

                if ($low === 0) {
                    $value &= 0xFE;
                }

                $decodedData .= $this->getSymbol($low, $high, $range, $value);
            }
        }

        file_put_contents($outputFile, $decodedData);
    }

    private function getSymbol($low, $high, $range, $value) {
        $point = (($value - $low + 1) / $range) - 1;
        // Define your own logic for getting the symbol based on the point value
        // Return the symbol corresponding to the point value
    }

    private function getSymbolLow($symbol) {
        // Define your own logic for getting the low value for a given symbol
        // Return a float between 0.0 and 1.0
    }

    private function getSymbolHigh($symbol) {
        // Define your own logic for getting the high value for a given symbol
        // Return a float between 0.0 and 1.0
    }

    private function needsRenormalization($low, $high) {
        return (($low < 0.5) && ($high >= 0.5));
    }

    private function removeFractionalPart($value) {
        return intval($value);
    }
}
