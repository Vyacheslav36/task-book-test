<?php


namespace App\helpers;


class RouterHelper
{
    public static function getResponseByHandler($handler, $request)
    {
        if (is_array($handler)) {
            $class = new $handler[0]();
            $action = $handler[1];
            return $class->$action($request);
        }

        if (is_string($handler)) {
            return (new $handler())($request);
        }

        return $handler($request);
    }

    public static function getUrl($path = '/')
    {
        return str_replace('//', '/', BASE_URL . $path);
    }
}