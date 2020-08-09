<?php
$this->params['title'] = 'Login';
$this->layout = 'layouts/base';
?>

<form method="POST" action="<?= \App\helpers\RouterHelper::getUrl('/auth/login') ?>">
    <div class="form-group">
        <label for="loginFormControl">Login</label>
        <input type="text" class="form-control" name="login" id="loginFormControl" placeholder="Enter your login" required/>
    </div>
    <div class="form-group">
        <label for="passwordFormControl">Password</label>
        <input type="password" class="form-control" name="password" id="passwordFormControl" placeholder="Enter your password" required/>
    </div>
    <button type="submit" class="btn btn-dark mt-3">Login</button>
</form>