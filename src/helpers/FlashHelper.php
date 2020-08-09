<?php


namespace App\helpers;


class FlashHelper
{
    private static function sessionStart()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function get($key, $defaultValue = null)
    {
        self::sessionStart();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
    }

    public static function has($key)
    {
        self::sessionStart();
        return isset($_SESSION[$key]);
    }

    public static function setFlash($key, $value = true)
    {
        self::sessionStart();
        $_SESSION[$key] = $value;
    }

    public static function getFlash($key, $defaultValue = null, $delete = true)
    {
        self::sessionStart();
        $value = self::get($key, $defaultValue);
        if ($delete) {
            self::remove($key);
        }

        return $value;
    }

    public static function remove($key)
    {
        $value = isset($_SESSION[$key]) ? $_SESSION[$key] : null;
        unset($_SESSION[$key]);

        return $value;
    }
}