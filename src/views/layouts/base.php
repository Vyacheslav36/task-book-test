<?php

use \App\base\View;
use \App\helpers\FlashHelper;
use \App\helpers\RouterHelper;
use App\models\LoginModel;

/* @var array $params */
/* @var string $content */

$isAdmin = LoginModel::isAuthorized();
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $this->params['title'] ?? 'App' ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link href="<?= RouterHelper::getUrl('/css/style.css') ?>" rel="stylesheet">
    <script src="<?= RouterHelper::getUrl('/js/index.js') ?>"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">Test project</span>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="<?= RouterHelper::getUrl('/') ?>">Home</span></a>
                </li>
                <?php if (!$isAdmin): ?>
                    <li class="nav-item active">
                        <a class="nav-link" href="<?= RouterHelper::getUrl('/auth') ?>">Login</span></a>
                    </li>
                <?php else: ?>
                    <li class="nav-item active">
                        <a class="nav-link" data-method="POST" data-confirm="Do you really want to leave?" href="<?= RouterHelper::getUrl('/auth/logout') ?>">Logout</span></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <?php if (FlashHelper::has('alert')): ?>
        <?php
            $value = FlashHelper::getFlash('alert');
            $options = $value['options'] ?? [];
            $body = $value['body'] ?? '';
        ?>
        <?= (new View())->render('alert/alert', [
            'options' => $options,
            'body' => $body
        ]) ?>
    <?php endif; ?>
    <?= $content ?>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>