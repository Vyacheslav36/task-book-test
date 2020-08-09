<?php


namespace App\controllers;

use App\base\Controller;
use App\helpers\FlashHelper;
use App\helpers\RouterHelper;
use App\models\LoginModel;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequest;

class AuthController extends Controller
{
    /**
     * @return HtmlResponse|RedirectResponse
     */
    public function auth()
    {
        if (LoginModel::isAuthorized()) {
            return new RedirectResponse(RouterHelper::getUrl('/'));
        }
        return new HtmlResponse($this->view->render('auth/login_form'));
    }

    /**
     * @param ServerRequest $request
     * @return RedirectResponse
     */
    public function login(ServerRequest $request)
    {
        ['login' => $login, 'password' => $password] = $request->getParsedBody();

        $authModel = new LoginModel();

        if (!$authModel->login($login, $password)) {
            FlashHelper::setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'Login or password is incorrect.'
            ]);
            $url = $_SERVER['HTTP_REFERER'] ?? RouterHelper::getUrl('/');
            return new RedirectResponse($url);
        }

        FlashHelper::setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => 'You have successfully logged in.'
        ]);

        return new RedirectResponse(RouterHelper::getUrl('/'));
    }

    /**
     * @return RedirectResponse
     */
    public function logout()
    {
        $authModel = new LoginModel();
        $authModel->logout();
        return new RedirectResponse(RouterHelper::getUrl('/'));
    }

}