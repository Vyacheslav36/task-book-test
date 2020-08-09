<?php


namespace App\helpers;


class ValidationHelper
{
    public static function textFilter($value)
    {
        return htmlspecialchars(stripslashes(trim($value)));
    }

    public static function checkOnRequired($value)
    {
        return !!trim($value);
    }

    public static function checkOnEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}