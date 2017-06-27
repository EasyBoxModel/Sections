<?php

namespace EBM\Utils;

class StringUtil
{
    public static function capitalize($value)
    {
        $val = strtolower($value);
        $val = ucwords($val);
        return ucfirst($val);
    }

    public static function cleanPhone($value)
    {
        $val = preg_replace('/\+|\s|\-|\(|\)/', '', $value);

        return '+' . $val;
    }
}
