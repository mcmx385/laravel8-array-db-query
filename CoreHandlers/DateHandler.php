<?php

namespace App\Libraries\Larocket\CoreHandlers;

class DateHandler
{
    public static function percent($date)
    {
        $date = str_split($date);
        $new_date = [];
        foreach ($date as $char) :
            if (ctype_alpha($char)) :
                $char = "%$char";
            endif;
            array_push($new_date, $char);
        endforeach;
        $new_date = implode("", $new_date);
        return $new_date;
    }
}
