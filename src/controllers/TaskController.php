<?php


namespace App\controllers;

use App\base\Controller;
use App\helpers\FlashHelper;
use App\helpers\RouterHelper;
use App\models\LoginModel;
use App\models\TaskModel;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\ServerRequest;

class TaskController extends Controller
{
    /**
     * @return HtmlResponse
     */
    public function index(ServerRequest $request)
    {
        $countOnPage = 3;
        $page = null;
        $sort = null;
        $sortType = null;

        $tasksCount = (new TaskModel())
            ->find()
            ->count();

        $tasks = (new TaskModel())->find();

        $query = $request->getQueryParams();
        if (count($query)) {
            $sort = isset($query['sort']) ? $query['sort'] : null;
            if ($sort) {
                $sortType = strpos($sort, '-') === 0 ? 'DESC' : 'ASC';
                $tasks->orderBy($sort, $sortType);
            }
            $page = isset($query['page']) ? $query['page'] : null;
            if ($page && $tasksCount > 0 && (($countOnPage * $page) - $countOnPage) < $tasksCount) {
                $limit = ($countOnPage * $page) - $countOnPage;
                $tasks->limit("$limit, $countOnPage");
            } else {
                $tasks->limit($countOnPage);
            }
        } else {
            $tasks->limit($countOnPage);
        }

        return new HtmlResponse($this->view->render('task/index', [
            'tasks' => $tasks->execute(),
            'tasksCount' => $tasksCount,
            'currentPage' => (int)$page ?? 1,
            'countOnPage' => $countOnPage,
            'currentSort'=> $sort,
            'currentSortType' => $sortType,
            'isAdmin' => LoginModel::isAuthorized()
        ]));
    }

    /**
     * @return HtmlResponse
     */
    public function create()
    {
        $model = new TaskModel();

        return new HtmlResponse($this->view->render('task/_form', [
            'type' => 'Create',
            'model' => $model
        ]));
    }

    /**
     * @param ServerRequest $request
     * @return HtmlResponse|RedirectResponse
     */
    public function add(ServerRequest $request)
    {
        ['name' => $name, 'email' => $email, 'text' => $text] = $request->getParsedBody();

        $model = new TaskModel();
        $model->setName($name);
        $model->setEmail($email);
        $model->setText($text);

        if (!$model->save()) {
            if (count($model->errors)) {
                FlashHelper::setFlash('alert', [
                    'options' => ['class' => 'alert-danger'],
                    'body' => join('<br/>', $model->errors)
                ]);
                $url = $_SERVER['HTTP_REFERER'] ?? RouterHelper::getUrl('/');
                return new RedirectResponse($url);
            }

            FlashHelper::setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'An error occurred while creating the task.'
            ]);

            return new RedirectResponse(RouterHelper::getUrl('/'));
        }

        FlashHelper::setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => 'Task added successfully.'
        ]);

        return new RedirectResponse(RouterHelper::getUrl('/'));
    }

    /**
     * @param ServerRequest $request
     * @return HtmlResponse
     * @throws \Exception
     */
    public function edit(ServerRequest $request)
    {
        $id = (int)$request->getAttribute('id');
        $model = $this->findModel($id);

        if (!$model) {
            return new HtmlResponse("<h1>404 - Not found</h1>");
        }

        return new HtmlResponse($this->view->render('task/_form', [
            'type' => 'Update',
            'model' => $model
        ]));
    }

    /**
     * @param ServerRequest $request
     * @return HtmlResponse|RedirectResponse
     * @throws \Exception
     */
    public function update(ServerRequest $request)
    {
        if (!$this->checkOnAuthorization('To edit, you must log in to the system.')) {
            return new RedirectResponse(RouterHelper::getUrl('/auth'));
        }

        $id = (int)$request->getAttribute('id');
        ['name' => $name, 'email' => $email, 'text' => $text] = $request->getParsedBody();

        $model = $this->findModel($id);

        $model->setName($name);
        $model->setEmail($email);
        $model->setText($text);
        $model->setIsEdited(true);

        if (!$model->save()) {
            if (count($model->errors)) {
                FlashHelper::setFlash('alert', [
                    'options' => ['class' => 'alert-danger'],
                    'body' => join('<br/>', $model->errors)
                ]);
                $url = $_SERVER['HTTP_REFERER'] ?? RouterHelper::getUrl('/');
                return new RedirectResponse($url);
            }

            FlashHelper::setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'An error occurred while updating the task.'
            ]);
            return new RedirectResponse(RouterHelper::getUrl('/'));
        }

        FlashHelper::setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => 'The task was successfully updated.'
        ]);

        return new RedirectResponse(RouterHelper::getUrl('/'));
    }

    /**
     * @param ServerRequest $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function delete(ServerRequest $request)
    {
        $id = (int)$request->getAttribute('id');

        $this->findModel($id)->delete();

        FlashHelper::setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => 'Task deleted successfully.'
        ]);

        return new RedirectResponse(RouterHelper::getUrl('/'));
    }

    /**
     * @param ServerRequest $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function completed(ServerRequest $request)
    {
        $id = (int)$request->getAttribute('id');

        $model = $this->findModel($id);

        $model->setIsCompleted(true);

        if (!$model->save()) {
            FlashHelper::setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'Error completing task.'
            ]);
            return new RedirectResponse(RouterHelper::getUrl('/'));
        }

        FlashHelper::setFlash('alert', [
            'options' => ['class' => 'alert-success'],
            'body' => 'Task completed successfully.'
        ]);

        return new RedirectResponse(RouterHelper::getUrl('/'));
    }

    /**
     * @param $id
     * @return TaskModel|array
     * @throws \Exception
     */
    private function findModel($id)
    {
        $model = (new TaskModel())
            ->findOne("id = $id");

        if (!$model) {
            throw new \Exception('404 - Not found');
        }

        return $model;
    }

    /**
     * @param $text
     * @return bool|RedirectResponse
     */
    private function checkOnAuthorization($text = 'You need to log in to the system.')
    {
        if (!LoginModel::isAuthorized()) {
            FlashHelper::setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => $text
            ]);
            return false;
        }
        return true;
    }
}