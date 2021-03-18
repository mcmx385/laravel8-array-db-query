<?php

namespace App\Libraries\Larocket\CoreHandlers;

use Endroid\QrCode\QrCode;

class StringHandler
{
    protected $str; // String
    protected $char; // Character
    protected $rand; // Random

    public static function rand($len = 10)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLen = strlen($chars);
        $randStr = '';
        for ($i = 0; $i < $len; $i++) {
            $randStr .= $chars[rand(0, $charsLen - 1)];
        }
        return $randStr;
    }
    public static function rmExt($str)
    {
        return substr($str, 0, strrpos($str, '.'));
    }

    // JSON
    public static function isJson($str)
    {
        $result = json_decode($str);
        if (json_last_error() === JSON_ERROR_NONE) {
            return true;
        }
        return false;
    }
    public static function enJson($str)
    {
        if (self::isJson($str)) :
            return $str;
        else :
            return json_encode($str);
        endif;
    }
    public static function deJson($str)
    {
        if (!self::isJson($str)) :
            return $str;
        else :
            return json_decode($str);
        endif;
    }

    // Serialize
    public static function isSerial($str)
    {
        $result = @unserialize($str);
        if ($result !== false) {
            return true;
        } else {
            return false;
        }
    }
    public static function serial($str)
    {
        if (self::isSerial($str)) :
            return $str;
        else :
            return serialize($str);
        endif;
    }
    public static function unserial($str)
    {
        if (!self::isSerial($str)) :
            return $str;
        else :
            return unserialize($str);
        endif;
    }

    public static function qrcode($str)
    {
        $qrCode = new QrCode($str);
        header('Content-Type: ' . $qrCode->getContentType());
        $dataUri = $qrCode->writeDataUri();
        return $dataUri;
    }
    public function slugify($str)
    {
        // replace non letter or digits by -
        $str = preg_replace('~[^\pL\d]+~u', '-', $str);

        // transliterate
        $str = iconv('utf-8', 'us-ascii//TRANSLIT', $str);

        // remove unwanted chars
        $str = preg_replace('~[^-\w]+~', '', $str);

        // trim
        $str = trim($str, '-');

        // remove duplicate -
        $str = preg_replace('~-+~', '-', $str);

        // lowercase
        $str = strtolower($str);

        if (empty($str)) {
            return 'n-a';
        }

        return $str;
    }

    public function haveChi($str)
    {
        return preg_match("/\p{Han}+/u", $str);
    }
    public function onlyCharDigit($str)
    {
        return !preg_match('/[^A-Za-z0-9]/', $str);
    }
}
