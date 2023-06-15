<?php
namespace Classes;

class Authentication{

    private $pdo;
    private $usersTable;


    public function __construct(DatabaseTable $userTable, private string $userColumn, private string $passwordColumn ){
        $this->usersTable = $userTable;            
        session_start();
    }

    private  $joins = [
        [ 
            'table' => 'users',
            'field' => 'id',
            'value' => 'user'
        ]
    ];

    
    private function getUser($method){


        // SELECT * FROM team LEFT JOIN `users` ON team.id = `users`.`user` WHERE 
        $user = $this->usersTable->find('email', strtolower($method),$this->joins);

        return $user;
    }

    public function findUser($method){
        $user = $this->usersTable->find('email', strtolower($method),$this->joins);
        return $user;
    }


    public function login (string $username, string $password) : bool {

    $user = $this->getUser($username);

    if(!empty($user) && password_verify($password, $user['password']) ){

        session_regenerate_id();

        $_SESSION['username'] = $user['email'];
        $_SESSION['password'] = $user['password'];

            return true;
    }else{
        return false;
    }

    }

    public function isLoggedIn(): bool {
        if (empty($_SESSION['username'])) {
        return false;
        }
        $user = $this->usersTable->find($this->userColumn,$_SESSION['username'], $this->joins);
        if (!empty($user) && $user[$this->passwordColumn] === $_SESSION['password']) {
        return true;
        } else {
        return false;
        }
    }

    public function logout(){

        unset($_SESSION['username']);
        unset($_SESSION['password']);
        session_regenerate_id();
    }

}