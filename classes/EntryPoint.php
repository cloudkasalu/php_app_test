<?php
namespace Classes;
use Classes\Authentication;
use Classes\Controllers\AdminController;
use Classes\Controllers\LoginController;
use Classes\Controllers\VisitorController;
class EntryPoint{
    public function __construct(){

    }

    private function loadTemplate($templateFileName, $variables = []) {
        extract($variables);
        ob_start();
        include __DIR__ . '/../templates/' . $templateFileName;
        return ob_get_clean();

    }

    private function checkUri($url) {
        if ($url != strtolower($url)) {
        http_response_code(301);
        header('location: ' . strtolower($url));
        }
    }
        

    public function run($url){



        try {

            include __DIR__ . '/../includes/DatabaseConnection.php';
            // include __DIR__ . '/../controllers/AdminController.php';
            // include __DIR__ . '/../controllers/VisitorController.php';
            // include __DIR__ . '/../includes/autoload.php';

            $auth = new Authentication(new DatabaseTable ($pdo,'team','id'),'email','password');

            $this->checkUri($url);

            $url == ''? $url = 'home' : $url;
    
            $url_request = explode('/', $url);
        
            $route = array_shift($url_request);
            $action = null;
            $controller = null;

            switch ($route) {
                case 'dashboard':

                    if($auth->isLoggedIn()){
                        $req = array_shift($url_request);
                        !$req ? $action = 'home' :  $action = $req . ucfirst(array_shift($url_request)?? '') ;
                        $controller = new AdminController($pdo,$auth);

                    }else{
                        header('Location: /login');
                        exit();
                    }
                    break;
                case 'login':
                case 'logout':
                    $action = $route;
                    $controller = new LoginController($pdo, $auth);
                    break;
                default:
                    $action = $route;
                    $controller = new VisitorController($pdo);

            }


            if(is_callable([$controller, $action])){
                    $page = $controller->$action($url_request);
                    $title = $page['title'];
                    $variables = $page['variables'] ?? [];
                    $output = $this->loadTemplate($page['template'], $variables);
            }else{
                http_response_code(404);

                $route = "error";
                $title = 'Not found';

                ob_start();
                include __DIR__ . '/../templates/404.html.php';
                $output = ob_get_clean();
        
            }
        
            
        } catch (\PDOException $e) {
            $title = 'An error has occurred';
            $output = 'Database error: ' . $e->getMessage() . ' in ' .
            $e->getFile() . ':' . $e->getLine();
        }

        if($route === "dashboard"){
            include __DIR__ . '/../templates/admin/layout.php';
        }else if($route === "login" || $route === "error"){
            include __DIR__ . '/../templates/blank_layout.php';
        }else{
            include __DIR__ . '/../templates/visitor/layout.php';
        }


    }
}