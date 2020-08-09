<?php
/* @var array $options */
/* @var string $body */

$class = $options['class'] ?? 'alert-success';
?>

<div class="alert <?= $class ?> alert-dismissible fade show" role="alert">
    <?= $body ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
