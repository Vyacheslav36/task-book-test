<?php
/* @var array $tasks */
/* @var int $tasksCount */
/* @var int $currentPage */
/* @var int $countOnPage */

$this->params['title'] = 'Задачи';
$this->layout = 'layouts/base';
?>

<?php
$rowTemplate = (function ($index, \App\models\TaskModel $model) {
    $rowClass = $model->getIsCompleted() ? 'completed' : '';
    $editButton = "<a href='/task/{$model->getId()}/edit'><i class='fas fa-pencil-alt'></i></a>";
    $removeButton = "<a href='/task/{$model->getId()}/delete' data-method='POST' data-confirm='Are you sure you want to delete this entry?'><i class='fas fa-trash-alt'></i></a>";
    return "<tr class='$rowClass'>
            <th scope=\"row\">{$index}</th>
            <td>{$model->getName()}</td>
            <td>{$model->getEmail()}</td>
            <td>{$model->getText()}</td>
            <td class='actions'>$editButton $removeButton</td>
        </tr>";
});
?>

<?php
$previousLink = (function ($currentPage) {
    $isDisabled = $currentPage <= 1;
    $class = $isDisabled ? 'disabled' : '';
    $previousPage = $currentPage > 1 ? --$currentPage : 1;
    return "<li class=\"page-item {$class}\">
                <a class=\"page-link\" href=\"/?page=$previousPage\" aria-label=\"Previous\">
                    <span aria-hidden=\"true\">&laquo;</span>
                </a>
            </li>";
});

$nextLink = (function ($currentPage, $tasksCount) {
    $isDisabled = ($currentPage * 3) >= $tasksCount;
    $class = $isDisabled ? 'disabled' : '';
    $nextPage = $currentPage > 1 ? ++$currentPage : 2;
    return "<li class=\"page-item {$class}\">
                <a class=\"page-link\" href=\"/?page=$nextPage\" aria-label=\"Next\">
                    <span aria-hidden=\"true\">&raquo;</span>
                </a>
            </li>";
});

$paginationItem = (function ($page, $active) {
    $class = $active ? 'active' : '';
    return "<li class=\"page-item {$class}\"><a class=\"page-link\" href=\"/?page=$page\">$page</a></li>";
});

$paginationItems = (function ($currentPage, $tasksCount, $countOnPage) use ($paginationItem) {
    $itemsCount = ceil($tasksCount / $countOnPage);
    $items = '';
    for ($i = 1; $i <= $itemsCount; $i++) {
        $isActive = $i === $currentPage;
        $items .= $paginationItem($i, $isActive);
    }
    return $items;
});
?>

<div>
    <a href="/task/create" class="btn btn-dark mb-3">Create Task</a>
    <div class="overflow-x">
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Text</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $index => $task): ?>
                <?= $rowTemplate(++$index, $task) ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <?= $previousLink($currentPage) ?>
            <?= $paginationItems($currentPage, $tasksCount, $countOnPage) ?>
            <?= $nextLink($currentPage, $tasksCount) ?>
        </ul>
    </nav>
</div>