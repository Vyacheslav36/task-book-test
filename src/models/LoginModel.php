<?php


namespace App\models;


use App\helpers\ValidationHelper;

class LoginModel
{
    private $_login = ADMIN_LOGIN;
    private $_password = ADMIN_PASSWORD;

    public function __construct()
    {
        $this->sessionStart();
    }

    private static function sessionStart()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @return false|mixed
     */
    public static function isAuthorized() {
        self::sessionStart();
        if (isset($_SESSION["isAuthorized"])) {
            return $_SESSION["isAuthorized"];
        }
        return false;
    }

    /**
     * @param $login
     * @param $password
     * @return bool
     */
    public function login($login, $password) {
        $login = ValidationHelper::textFilter($login);
        $password = ValidationHelper::textFilter($password);
        if ($login == $this->_login && $password == $this->_password) {
            $_SESSION["isAuthorized"] = true;
            $_SESSION["login"] = $login;
            return true;
        } else {
            $_SESSION["isAuthorized"] = false;
            return false;
        }
    }

    /**
     * @return mixed|null
     */
    public function getUserLogin() {
        if ($this->isAuthorized()) {
            return $_SESSION["login"];
        }
        return null;
    }

    /**
     * Logout user
     */
    public function logout() {
        $_SESSION = array();
        session_destroy();
    }
}