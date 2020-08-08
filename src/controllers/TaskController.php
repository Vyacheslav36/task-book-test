<?php


namespace App\controllers;

use App\base\Controller;
use App\models\TaskModel;
use Zend\Diactoros\Request;
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
        $tasksCount = (new TaskModel())
            ->find()
            ->count();

        $tasks = (new TaskModel())->find();

        $query = $request->getQueryParams();
        if (count($query)) {
           $page = isset($query['page']) ? $query['page'] : null;
           if ($page && $tasksCount > 0 && (($countOnPage * $page) - $countOnPage) < $tasksCount) {
               $limit = ($countOnPage * $page) - $countOnPage;
               $tasks->limit("$limit, $countOnPage");
           }
        } else {
            $tasks->limit($countOnPage);
        }

        return new HtmlResponse($this->view->render('task/index', [
            'tasks' => $tasks->execute(),
            'tasksCount' => $tasksCount,
            'currentPage' => (int) $page ?? 1,
            'countOnPage' => $countOnPage
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
            return new HtmlResponse("<h1>500 - Error</h1>");
        }

        return new RedirectResponse('/');
    }

    /**
     * @param ServerRequest $request
     * @return HtmlResponse
     * @throws \Exception
     */
    public function edit(ServerRequest $request)
    {
        $id = (int) $request->getAttribute('id');
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
        $id = (int) $request->getAttribute('id');
        ['name' => $name, 'email' => $email, 'text' => $text] = $request->getParsedBody();

        $model = $this->findModel($id);

        $model->setName($name);
        $model->setEmail($email);
        $model->setText($text);

        if (!$model->save()) {
            return new HtmlResponse("<h1>500 - Error</h1>");
        }

        return new RedirectResponse('/');
    }

    /**
     * @param ServerRequest $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function delete(ServerRequest $request)
    {
        $id = (int) $request->getAttribute('id');

        $this->findModel($id)->delete();

        return new RedirectResponse('/');
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

}