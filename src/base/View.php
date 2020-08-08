<?php


namespace App\base;


class View
{
    private $params = [];
    private $layout;

    /**
     * @param $view
     * @param array $params
     * @return string
     */
    public function render($view, array $params = []): string
    {
        $templateFile = __DIR__ . '/../views/' . $view . '.php';
        ob_start();
        extract($params, EXTR_OVERWRITE);
        $this->layout = null;
        require $templateFile;
        $content = ob_get_clean();

        if (!$this->layout) {
            return $content;
        }

        return $this->render($this->layout, [
            'content' => $content,
        ]);
    }
}