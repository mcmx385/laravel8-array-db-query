<?php

namespace App\Libraries\Larocket\CoreHandlers;

class ArrayHandler
{
    public static function isAsso(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
    public static function is2D(array $arr)
    {
        return is_array(reset($arr));
    }
    public static function onlyCol($arr, string $column)
    {
        $list = [];
        foreach ($arr as $item) :
            $item = (array) $item;
            array_push($list, $item[$column]);
        endforeach;
        return $list;
    }
    public static function filterValue(array $arr, array $filter)
    {
        foreach ($filter as $item) :
            if (in_array($item, $arr)) :
                unset($arr[array_search($item, $arr)]);
            endif;
        endforeach;
        return $arr;
    }
    public static function filterKey(array $arr, array $filter)
    {
        foreach ($filter as $item) :
            unset($arr[$item]);
        endforeach;
        return $arr;
    }
    public static function prefix(array $arr, string $prefix)
    {
        foreach ($arr as &$item) :
            $item = $prefix . $item;
        endforeach;
        return $arr;
    }
    public static function arrObjToArrArr($arrObj, array $list = [])
    {
        $arrVal = [];
        if (count($list) > 0) :
            foreach ($arrObj as $Obj) :
                $Val = [];
                foreach ($list as $column) :
                    array_push($Val, $Obj->$column);
                endforeach;
                array_push($arrVal, $Val);
            endforeach;
        else :
            foreach ($arrObj as $Obj) :
                $Val = [];
                foreach ($Obj as $prop => $value) :
                    array_push($Val, $value);
                endforeach;
                array_push($arrVal, $Val);
            endforeach;
        endif;
        return $arrVal;
    }
    public static function translate(array $arr = [], $topic = '')
    {
        if ($topic == '') :
            foreach ($arr as &$item) :
                $item = __($item);
            endforeach;
        else :
            foreach ($arr as &$item) :
                $raw = $topic . '.' . $item;
                $translated = __($raw);
                if ($translated !== $raw) :
                    $item = $translated;
                endif;
            endforeach;
        endif;
        return $arr;
    }
    public static function jsonDecArrObjCol($arr = [], $column = [])
    {
        if (is_string($column)) :
            for ($count = 0; $count < count($arr); $count++) :
                if (isset($arr[$count]->$column)) :
                    $arr[$count]->$column = json_decode($arr[$count]->$column);
                endif;
            endfor;
        elseif (is_array($column)) :
            for ($count = 0; $count < count($arr); $count++) :
                foreach ($column as $thing) :
                    if (isset($arr[$count]->$thing)) :
                        $arr[$count]->$thing = json_decode($arr[$count]->$thing);
                    endif;
                endforeach;
            endfor;
        endif;
        return $arr;
    }
    public static function assoSwap($key1, $key2, $array)
    {
        $newArray = [];
        foreach ($array as $key => $value) :
            if ($key == $key1) :
                $newArray[$key2] = $array[$key2];
            elseif ($key == $key2) :
                $newArray[$key1] = $array[$key1];
            else :
                $newArray[$key] = $value;
            endif;
        endforeach;
        return $newArray;
    }
    public static function assoKeyValMoveFront($arr, $key)
    {
        return [$key => $arr[$key]] + $arr;
    }
    private static function assoSplice(&$input, $offset, $length, $replacement)
    {
        $replacement = (array) $replacement;
        $key_indices = array_flip(array_keys($input));
        if (isset($input[$offset]) && is_string($offset)) {
            $offset = $key_indices[$offset];
        }
        if (isset($input[$length]) && is_string($length)) {
            $length = $key_indices[$length] - $offset;
        }

        $input = array_slice($input, 0, $offset, TRUE)
            + $replacement
            + array_slice($input, $offset + $length, NULL, TRUE);
    }
    public static function assoMove($which, $where, $array)
    {
        $tmpWhich = $which;
        $j = 0;
        $keys = array_keys($array);

        for ($i = 0; $i < count($array); $i++) {
            if ($keys[$i] == $tmpWhich)
                $tmpWhich = $j;
            else
                $j++;
        }
        $tmp  = array_splice($array, $tmpWhich, 1);
        self::assoSplice($array, $where, 0, $tmp);
        return $array;
    }

    // Array merge every array in array
    public static function flat($array)
    {
        return call_user_func_array('array_merge', $array);
    }

    public static function flatten($array)
    {
        $new_array = [];
        foreach ($array as $key => $item) :
            if (is_int($key)) :
                array_push($new_array, $item);
            elseif (is_string($key)) :
                $new_array = array_merge($new_array, $item);
            endif;
        endforeach;
        return $new_array;
    }

    public static function arrArrFirstValEmpty($arrArr)
    {
        $newArrArr = [];
        foreach ($arrArr as $arr) :
            $tmpArr = [''];
            $tmpArr = array_merge($tmpArr, $arr);
            array_push($newArrArr, $tmpArr);
        endforeach;
        return $newArrArr;
    }
}
