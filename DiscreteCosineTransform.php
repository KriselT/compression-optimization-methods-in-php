<?php

class DiscreteCosineTransform
{
    public function applyDCT($input)
    {
        $size = count($input);
        $output = [];

        for ($u = 0; $u < $size; $u++) {
            for ($v = 0; $v < $size; $v++) {
                $sum = 0.0;

                for ($x = 0; $x < $size; $x++) {
                    $cosine1 = cos((2 * $x + 1) * $u * M_PI / (2 * $size));
                    for ($y = 0; $y < $size; $y++) {
                        $cosine2 = cos((2 * $y + 1) * $v * M_PI / (2 * $size));
                        $sum += $input[$x][$y] * $cosine1 * $cosine2;
                    }
                }

                $output[$u][$v] = $sum;
            }
        }

        return $output;
    }

    public function applyInverseDCT($input)
    {
        $size = count($input);
        $output = [];

        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $sum = 0.0;

                for ($u = 0; $u < $size; $u++) {
                    $cosine1 = cos((2 * $x + 1) * $u * M_PI / (2 * $size));
                    for ($v = 0; $v < $size; $v++) {
                        $cosine2 = cos((2 * $y + 1) * $v * M_PI / (2 * $size));
                        $sum += $input[$u][$v] * $cosine1 * $cosine2;
                    }
                }

                $output[$x][$y] = round($sum / ($size * $size));
            }
        }

        return $output;
    }
}
