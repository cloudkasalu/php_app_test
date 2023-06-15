<?php
namespace Classes\Controllers;
use Classes\DatabaseTable;

class AdminController{

    private $pdo;
    private $portfolioTable;
    private $servicesTable;
    private $blogTable;

    private $usersTable;
    private $teamTable;
    private $authentication;

    public function __construct($pdo,$auth){
        $this->pdo = $pdo;
        $this->authentication = $auth;
        $this->portfolioTable = new DatabaseTable($this->pdo,'portfolio','id');
        $this->servicesTable =  new DatabaseTable($this->pdo,'services','id');
        $this->blogTable =  new DatabaseTable($this->pdo,'blog','id');
        $this->usersTable = new DatabaseTable($this->pdo, 'users', 'id');
        $this->teamTable = new DatabaseTable($this->pdo, 'team', 'id');
    }


    public function home(){
        $title = "Admin";
        return ['template'=> '/admin/home.html.php', 'title'=> $title];

    }

    public function aboutSection(){

            $aboutSectionTable = new DatabaseTable($this->pdo,'about_us','id');
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
                $values= [
                    'title' => $_POST['title'],
                    'paragraph' => $_POST['paragraph'],
                    'image' => file_get_contents($_FILES['image']['tmp_name']),
                    'contact_title' => $_POST['contactTitle'],
                    'contact_details' => $_POST['contactNumber'],
                    'id' => 1
                ];
    
                $aboutSection = $aboutSectionTable->update($values);
    
                http_response_code(301);
                header('Location: /dashboard');
    
            }else {
    
                $title = "Edit About Us Section";
                
                $aboutSection = $aboutSectionTable->findAll();
        
                return ['template'=> '/admin/aboutSection.html.php', 'title'=> $title, 'variables'=>[
                    'aboutSection'=> $aboutSection
                ]];
    
            }

    }


    public function team($variables = null){

            $aboutTeamTable = new DatabaseTable($this->pdo,'team','id');

            if($_SERVER['REQUEST_METHOD'] === 'POST'){

                $values= [
                    'firstname' => $_POST['firstname'],
                    'lastname' => $_POST['lastname'],
                    'position' => $_POST['position'],
                    'profile_picture' =>  file_get_contents($_FILES['image']['tmp_name']) ?? $_POST['profile_picture'],
                    'id' => $_POST['id']
                ];

                $aboutTeamTable->update($values);
    
                http_response_code(301);
                header('Location: /dashboard/about/team');

            
            }else{

                $id = array_shift($variables);
                if($id){
                    $title = "Edit Member";
                    $member = $this->teamTable->find('id',$id);
            
                    return ['template'=> '/admin/editTeam.html.php', 'title'=> $title, 'variables'=>[
                        'member'=> $member
                    ]];
                }

                $title = "View Team Members";

                $currentUser = $_SESSION['username'];
                $permission = $this->authentication->findUser($currentUser)['access'];
                
                $aboutTeam = $aboutTeamTable->findAll();
        
                return ['template'=> '/admin/viewTeam.html.php', 'title'=> $title, 'variables'=>[
                    'aboutTeam'=> $aboutTeam,
                    'permission'=> $permission
                ]];

            }

    }

    public function servicesSection(){

        $serviceSectionTable = new DatabaseTable($this->pdo,'services_section','id');
        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $values= [
                'section_title' => $_POST['title'], 
                'section_paragraph' => $_POST['paragraph'],
                'section_image' => file_get_contents($_FILES['image']['tmp_name']),
                'section_image_caption' => $_POST['caption'],
                'vision' => $_POST['vision'],
                'goal' => $_POST['goal'],
                'id'=> 1
            ];

            $serviceSection = $serviceSectionTable->update($values);

            http_response_code(301);
            header('Location: /dashboard');
        }else{
                $title = "Edit Services Section";
                    
                $serviceSection = $serviceSectionTable->findAll();
        
                return ['template'=> '/admin/serviceSection.html.php', 'title'=> $title, 'variables'=>[
                    'serviceSection'=> $serviceSection
                ]];

        }
    }

    private function editService($id){
        $title = "Edit Service";
        $service = $this->servicesTable->find('id',$id);

        return ['template'=> '/admin/editService.html.php', 'title'=> $title, 'variables'=>[
            'service'=> $service
        ]];

    }

    public function servicesList ($variables){

            if($_SERVER['REQUEST_METHOD'] === 'POST'){

                if(isset($_POST['id'])){

                    $date = new \DateTime();
                    $values= [
                        'id' => $_POST['id'],
                        'service_title' => $_POST['name'],
                        'service_description' => $_POST['description'],
                        'service_icon' => file_get_contents($_FILES['image']['tmp_name']),
                        'service_icon_caption' => $_POST['caption'],
                        'date' => $date->format('Y-m-d H:i:s')

                    ];
    
                    $this->servicesTable->update($values);
                    http_response_code(301);
                    header('Location: /dashboard/services/list');
                }else{
                    $date = new \DateTime();
                    $values= [
                        'section_id' => 4,
                        'service_title' => $_POST['name'],
                        'service_description' => $_POST['description'],
                        'service_icon' => file_get_contents($_FILES['image']['tmp_name']),
                        'service_icon_caption' => $_POST['caption'],
                        'date' => $date->format('Y-m-d H:i:s')
                    ];
    
                    $this->servicesTable->insert($values);
                    http_response_code(301);
                    header('Location: /dashboard/services/list');

                }

            }else{

                $route = array_shift($variables);
                if($route === "edit"){

                    $id = array_shift($variables);
                    return $this->editService($id);

                }else if($route === "create"){
                    $title = "Create Service";
                    return ['template'=> '/admin/postService.html.php', 'title'=> $title];
                }

                $title = "View Services";
                
                $services = $this->servicesTable->findAll();
        
                return ['template'=> '/admin/viewServices.html.php', 'title'=> $title, 'variables'=>[
                    'services'=> $services
                ]];

            }
    }

    public function portfolio($variables){

        $title = "View Projects";
            
        $projects = $this->portfolioTable->findAll();

        return ['template'=> '/admin/viewProjects.html.php', 'title'=> $title, 'variables'=>[
            'projects'=> $projects
        ]];

    }

    public function portfolioEdit($variables){
            if($_SERVER['REQUEST_METHOD'] === 'POST'){

                if(isset($_POST['id'])){

                    $values= [
                        'id' => $_POST['id'],
                        'project_name' => $_POST['name'],
                        'project_description' => $_POST['description'],
                        'project_image' => file_get_contents($_FILES['image']['tmp_name']),
                        'project_image_caption' => $_POST['caption'],

                    ];
    
                    $this->portfolioTable->update($values);
                    http_response_code(301);
                    header('Location: /dashboard/portfolio');
                }

            }else{

                    $id = array_shift($variables);

                    echo $id;
                    $title = "Edit Project";
                    $project = $this->portfolioTable->find('id',$id);
            
                    return ['template'=> '/admin/editProject.html.php', 'title'=> $title, 'variables'=>[
                        'project'=> $project
                    ]];
            }
    }

    public function portfolioPost($variables){

            if($_SERVER['REQUEST_METHOD'] === 'POST'){

                    $date = new \DateTime();
                    $values= [
                        'section_id' => 5,
                        'project_name' => $_POST['name'],
                        'project_description' => $_POST['description'],
                        'project_image' => file_get_contents($_FILES['image']['tmp_name']),
                        'project_image_caption' => $_POST['caption'],
                        'project_date' => $date->format('Y-m-d H:i:s')
                    ];
    
                    $this->portfolioTable->insert($values);
                    http_response_code(301);
                    header('Location: /dashboard/portfolio');

                }else{
                    $title = "Create Service";
                    return ['template'=> '/admin/postProject.html.php', 'title'=> $title];
                }
    }

    public function blog($variables){

            $title = "View Article";

            $query = 'SELECT * FROM `blog` INNER JOIN `users` ON `blog`.`author_id` = `users`.`id`';
                
            $articles = $this->blogTable->findAll($query);
    
            return ['template'=> '/admin/viewArticles.html.php', 'title'=> $title, 'variables'=>[
                'articles'=> $articles
            ]];

    }

    public function blogPost(){
  

            if($_SERVER['REQUEST_METHOD'] === 'POST'){

                $author = $this->authentication->findUser($_SESSION['username']);
                $date = new \DateTime();

                var_dump($author);

                    $values= [
                        'section_id' => 6,
                        'author_id' => $author['id'],
                        'post_title' => $_POST['title'],
                        'post_paragraph' => $_POST['paragraph'],
                        'post_image' => file_get_contents($_FILES['image']['tmp_name']),
                        'post_image_caption' => $_POST['caption'],
                        'post_date' => $date->format('Y-m-d H:i:s')
                    ];
    
                    $this->blogTable->insert($values);
                    http_response_code(301);
                    header('Location: /dashboard/blog');

                }else{

                    $title = "Create Service";
                    return ['template'=> '/admin/postArticle.html.php', 'title'=> $title];
 


            }


    }
    public function blogEdit($variables){

            if($_SERVER['REQUEST_METHOD'] === 'POST'){

                    $values= [
                        'id' => $_POST['id'],
                        'post_title' => $_POST['title'],
                        'post_paragraph' => $_POST['paragraph'],
                        'post_image' => file_get_contents($_FILES['image']['tmp_name']),
                        'post_image_caption' => $_POST['caption'],

                    ];
    
                    $this->blogTable->update($values);
                    http_response_code(301);
                    header('Location: /dashboard/blog');
                }else{

                    $id = array_shift($variables);

                    $title = "Edit Article";
                    $article = $this->blogTable->find('id',$id);
            
                    return ['template'=> '/admin/editArticle.html.php', 'title'=> $title, 'variables'=>[
                        'article'=> $article
                    ]];


            }

    }

    // public function teamEdit($id){

    //     $member = [];
    //     $result = $this->teamTable->find('id',$id);

    //     foreach($result as $row){
    //         $member[] = array(
    //             'id' => $row['id'],
    //             'image'=> array(
    //                 'src'=>  base64_encode($row['profile_picture']),
    //                 'alt'=> $row['position']
    //             ),
    //             'description' => $row['project_description']
    //         ); 
    //     }
    //     header('Content-Type: application/json');

    //     exit (json_encode($member));
    // }


    public function teamRegister($args){
        if(count($args)){
            return $this->registerUser($args);
        }else{
            return $this->registerMember();
        }

    }
    private function registerMember(){

        $title = "Register Member";

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $errors = [];

            $email = $_POST['email'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $position = $_POST['position'];
            $display = $_POST['display'];

            if(empty($firstname)){
                $errors[] = "Enter  Firstname";
            }
            if(empty($lastname)){
                $errors[] = "Enter  Lastname";
            }
            if(empty($email)){
                $errors[] = "Enter  Email";
            }
                else if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
                    $errors[] = 'Enter Valid Email';
                }else if ($this->teamTable->find('email', $email) !== false) {
                    $errors[] = 'That email address is already registered';
                }
                    

            if(empty($errors)){
                
                $values = [
                    'section_id' => 1,
                    'firstname' => $_POST['firstname'],
                    'lastname' => $_POST['lastname'],
                    'email' => $_POST['email'],
                    'position' => $_POST['position'],
                    'display' => $_POST['display'] ? 1 : 0,
                ];

                $this->teamTable->insert($values);

                header('Location: /dashboard');
            }

            return ['template' => '/admin/registerMember.html.php', 'title' => $title, 'variables'=>[
                'errors' => $errors,
            ]];

        }else{

    
            return ['template' => '/admin/registerMember.html.php', 'title' => $title];
        }

    }
    private function registerUser($id){

        $title = "Register User";

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $errors = [];

            $password = $_POST['password'];
                    
            if(empty($password)){
                $errors[] = "Enter  Password";
            }

            if(empty($errors)){
                
                $values = [
                    'user' => $_POST['id'],
                    'access' => $_POST['access'],
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
                ];

                $this->usersTable->insert($values);

                $update = [
                    'id'=>$_POST['id'],
                    'registered'=>1
                ];
                $this->teamTable->update($update);

                header('Location: /dashboard');
            }

            return ['template' => '/admin/registerUser.html.php', 'title' => $title, 'variables'=>[
                'errors' => $errors,
            ]];

        }else{

            $member = $this->teamTable->find('id',$id[0]);

            return ['template' => '/admin/registerUser.html.php', 'title' => $title, 'variables'=>[
                'member' => $member
            ]];
        }

    }
    private function updateUser($id){

        $title = "update User";

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $errors = [];

            $password = $_POST['password'];
                    
            if(empty($password)){
                $errors[] = "Enter  Password";
            }

            if(empty($errors)){
                
                $values = [
                    'user' => $_POST['id'],
                    'access' => $_POST['access'],
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
                ];

                $update = [
                    'id'=>$_POST['id'],
                    'registered'=>1
                ];

                $this->usersTable->insert($values);

                $update = [
                    'id'=>$_POST['id'],
                    'registered'=>1
                ];
                $this->teamTable->update($update);

                header('Location: /dashboard');
            }

            return ['template' => '/admin/registerUser.html.php', 'title' => $title, 'variables'=>[
                'errors' => $errors,
            ]];

        }else{

            $member = $this->teamTable->find('id',$id[0]);

            return ['template' => '/admin/registerUser.html.php', 'title' => $title, 'variables'=>[
                'member' => $member
            ]];
        }

    }


}