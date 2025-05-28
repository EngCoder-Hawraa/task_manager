<?php
/*
 *  APP CORE CLASS
 *  Creates URL & Loads Core Controller
 *  URL Format - /controller/method/param1/param2
 */
class Core {

    // Set Defaults
    protected $currentController = 'Auth'; // Default controller
    protected $currentMethod = 'register'; // Default method
    protected $params = []; // Set initial empty params array

    public function __construct(){
        //print_r($this->getUrl());

        $url = $this->getUrl();

        // Look in controllers for first value
        if (!empty($url) && file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
            // If exists, set as controller
            $this->currentController = ucwords($url[0]);
            // Unset 0 Index
            unset($url[0]);
        }

        // Require the controller
        require_once '../app/controllers/' . $this->currentController . '.php';

        // Instantiate controller class
        $this->currentController = new $this->currentController;

        // Check for second part of url
        if (isset($url[1])) {
            // Check to see if method exists in controller
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                unset($url[1]);
            } else {
                // عرض رسالة خطأ أو تعيين دالة افتراضية
                die('Method does not exist: ' . $url[1]);
            }
        }

        // Get params
        $this->params = $url ? array_values($url) : [];

        // Call a callback with array of params
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    // Construct URL From $_GET['url']
    public function getUrl(){

        if(isset($_GET['url'])){
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
    }


}