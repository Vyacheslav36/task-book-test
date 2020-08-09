<?php


namespace App\helpers;


class ValidationHelper
{
    public static function textFilter($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }
}