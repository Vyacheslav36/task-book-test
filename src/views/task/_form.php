<?php
/* @var \App\models\TaskModel $model */
/* @var string $type */

$this->params['title'] = "$type Task";
$this->layout = 'layouts/base';

$action = $type === 'Create' ? '/task/add' : "/task/{$model->getId()}/update";
?>

<form method="POST" action="<?= \App\helpers\RouterHelper::getUrl($action) ?>">
    <div class="form-group">
        <label for="nameFormControl">Name</label>
        <input type="text" class="form-control" name="name" id="nameFormControl" placeholder="Enter your name"
               value="<?= $model->getName() ?>" required/>
    </div>
    <div class="form-group">
        <label for="emailFormControl">Email address</label>
        <input class="form-control" pattern="\S+@[a-z]+.[a-z]+" name="email" id="emailFormControl" placeholder="name@example.com"
               value="<?= $model->getEmail() ?>" required/>
    </div>
    <div class="form-group">
        <label for="textFormControl">Text</label>
        <textarea class="form-control" name="text" id="textFormControl" placeholder="Enter task text" rows="3"
                  required><?= $model->getText() ?></textarea>
    </div>
    <button type="submit" class="btn btn-dark mt-3"><?= $type ?> Task</button>
</form>