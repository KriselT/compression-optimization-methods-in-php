<?php

class WaveletTransform {
    public function applyHaarTransform($input) {
        $size = count($input);
        $output = [];

        while ($size >= 2) {
            $halfSize = $size / 2;
            $temp = [];

            for ($i = 0; $i < $halfSize; $i++) {
                $avg = ($input[2 * $i] + $input[2 * $i + 1]) / 2;
                $diff = $input[2 * $i] - $input[2 * $i + 1];
                $temp[$i] = $avg;
                $temp[$halfSize + $i] = $diff;
            }

            $output = $temp;
            $input = array_slice($output, 0, $size);
            $size = $halfSize;
        }

        return $output;
    }

    public function applyInverseHaarTransform($input) {
        $size = count($input);
        $output = $input;

        while ($size > 1) {
            $halfSize = $size / 2;
            $temp = [];

            for ($i = 0; $i < $halfSize; $i++) {
                $avg = ($output[$i] + $output[$halfSize + $i]) / 2;
                $diff = ($output[$i] - $output[$halfSize + $i]);
                $temp[2 * $i] = $avg;
                $temp[2 * $i + 1] = $diff;
            }

            $output = $temp;
            $size *= 2;
        }

        return $output;
    }
}

