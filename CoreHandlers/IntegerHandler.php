<?php

namespace App\Libraries\Larocket\CoreHandlers;

class IntegerHandler
{
    public function rand($len = 10)
    {
        $max = "";
        for ($i = 0; $i < $len; $i++) {
            $max .= "9";
        }
        $max = (int) $max;
        return mt_rand(0, $max);
    }
}
