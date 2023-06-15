<?php
namespace Classes\Controllers;
use Classes\DatabaseTable;

class LoginController{

    private $pdo;
    private $usersTable;

    private $authentication;
    public function __construct($pdo,  \classes\Authentication $authentication){
        $this->pdo = $pdo;
        $this->authentication = $authentication;
        $this->usersTable = new DatabaseTable($this->pdo,'users','id');
                    
    }


    public function login(){

        $title = "login";

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $errors = [];
            $username = $_POST['username'];
            $password = $_POST['password'];

            if(!isset($username)){
                $errors[] = "Enter Email Or Name";
            }
            if(!isset($password)){
                $errors[] = "Enter  Password";
            }

            if(empty($errors)){

                $success = $this->authentication->login($username,$password);

                if($success){
                    header('Location: /dashboard');
                }else{
                    return ['template' => 'login.html.php', 'title' => $title];
                }

            }

            return ['template' => 'login.html.php', 'title' => $title, 'variables'=>[
                'errors' => $errors,
            ]];

        }

        return ['template' => 'login.html.php', 'title' => $title];

    }


    public function logout() {
        $this->authentication->logout();
        header('location: /');
        }
        
}