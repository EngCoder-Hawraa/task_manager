<?php
/**
 * Core Controller Class
 * Provides methods to load models and views
 */
class Controller
{
    /**
     * Load a model
     *
     * @param string $model - model file name (without .php)
     * @return object - instantiated model class
     */
    public function model($model)
    {
        $modelFile = '../app/models/' . $model . '.php';

        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        } else {
            die("❌ Model file not found: $modelFile");
        }
    }

    /**
     * Load a view
     *
     * @param string $view - view file path (without .php)
     * @param array $data - associative array passed to the view
     */
    public function view($view, $data = [])
    {
        $viewFile = '../app/views/' . $view . '.php';

        if (file_exists($viewFile)) {
            extract($data); // Converts array keys to variables
            require_once $viewFile;
        } else {
            die("❌ View file not found: $viewFile");
        }
    }
}
