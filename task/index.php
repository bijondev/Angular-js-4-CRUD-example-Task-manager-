<?php
require 'config.php';
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->post('/createtask','createTask');
$app->post('/get_task_by_id','get_task_by_id');
$app->post('/deletetask','DeleteTask');
$app->get('/alltask','AllTask');
$app->post('/updatetask','updatetask');


$app->run();

function createTask(){
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $taskname=$data->taskname;
    $taskdesc=$data->taskdesc;
    $status=$data->status;
   
    try {
      
        
        if (strlen(trim($taskname)) !="" && strlen(trim($taskdesc))!="" && strlen(trim($status))!="" )
        {
            $db = getDB();
            $userData = '';
            $sql = "INSERT INTO task (taskname,description,status)VALUES(:taskname,:description,:status)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("taskname", $taskname,PDO::PARAM_STR);
            $stmt->bindParam("description", $taskdesc,PDO::PARAM_STR);
            $stmt->bindParam("status", $status,PDO::PARAM_STR);
            $stmt->execute();
            $db = null;

           echo '{"error":{"text":"Sucess Task Added"}}';
        }
        else{
            echo '{"error":{"text":"Enter valid data"}}';
        }
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
function updatetask(){
       $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $taskname=$data->taskname;
    $taskdesc=$data->description;
    $status=$data->status;
    $id=$data->id;
   
    try {
      
        if (strlen(trim($taskname)) !="" && strlen(trim($taskdesc))!="" && strlen(trim($status))!="" )
        {
            $db = getDB();
            $userData = '';
            // $sql = "INSERT INTO task (taskname,description,status)VALUES(:taskname,:description,:status)";
            $sql="UPDATE task
                    SET 
                    taskname=:taskname,
                    description=:description,
                    status=:status
                    WHERE id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("taskname", $taskname,PDO::PARAM_STR);
            $stmt->bindParam("description", $taskdesc,PDO::PARAM_STR);
            $stmt->bindParam("status", $status,PDO::PARAM_STR);
            $stmt->bindParam("id", $id,PDO::PARAM_INT);
            $stmt->execute();
            $db = null;

           echo '{"error":{"text":"Sucess Task Added"}}';
        }
        else{
            echo '{"error":{"text":"Enter valid data"}}';
        }
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    } 
}
function DeleteTask(){
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $id=$data->id;

        try {
         
            $feedData = '';
            $db = getDB();
            $sql = "Delete FROM task WHERE id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $id, PDO::PARAM_INT);
            $stmt->execute();
           
            $db = null;
            echo '{"success":{"text":"Feed deleted"}}';
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}
function get_task_by_id(){
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $id=$data->id;
            try {
         
            $feedData = '';
            $db = getDB();
            $sql = "Select * FROM task WHERE id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $taskData = $stmt->fetchAll(PDO::FETCH_OBJ);
           
            $db = null;
            echo '{"taskData": ' . json_encode($taskData) . '}';
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
function AllTask(){
        try {
         

            $taskData = '';
            $db = getDB();
            $sql = "SELECT * FROM task ORDER BY id ASC";
            $stmt = $db->prepare($sql);
            // $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $taskData = $stmt->fetchAll(PDO::FETCH_OBJ);
           
            $db = null;
            echo '{"taskData": ' . json_encode($taskData) . '}';
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
/************************* USER LOGIN *************************************/
/* ### User login ### */
function login() {
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    
    try {
        
        $db = getDB();
        $userData ='';
        $sql = "SELECT user_id, name, email, username FROM users WHERE (username=:username or email=:username) and password=:password ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam("username", $data->username, PDO::PARAM_STR);
        $password=hash('sha256',$data->password);
        $stmt->bindParam("password", $password, PDO::PARAM_STR);
        $stmt->execute();
        $mainCount=$stmt->rowCount();
        $userData = $stmt->fetch(PDO::FETCH_OBJ);
        
        if(!empty($userData))
        {
            $user_id=$userData->user_id;
            $userData->token = apiToken($user_id);
        }
        
        $db = null;
         if($userData){
               $userData = json_encode($userData);
                echo '{"userData": ' .$userData . '}';
            } else {
               echo '{"error":{"text":"Bad request wrong username and password"}}';
            }

           
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}


/* ### User registration ### */
function signup() {
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $email=$data->email;
    $name=$data->name;
    $username=$data->username;
    $password=$data->password;
    
    try {
        
        $username_check = preg_match('~^[A-Za-z0-9_]{3,20}$~i', $username);
        $emain_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);
        $password_check = preg_match('~^[A-Za-z0-9!@#$%^&*()_]{6,20}$~i', $password);
        
        
        if (strlen(trim($username))>0 && strlen(trim($password))>0 && strlen(trim($email))>0 && $emain_check>0 && $username_check>0 && $password_check>0)
        {
            $db = getDB();
            $userData = '';
            $sql = "SELECT user_id FROM users WHERE username=:username or email=:email";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("username", $username,PDO::PARAM_STR);
            $stmt->bindParam("email", $email,PDO::PARAM_STR);
            $stmt->execute();
            $mainCount=$stmt->rowCount();
            $created=time();
            if($mainCount==0)
            {
                
                /*Inserting user values*/
                $sql1="INSERT INTO users(username,password,email,name)VALUES(:username,:password,:email,:name)";
                $stmt1 = $db->prepare($sql1);
                $stmt1->bindParam("username", $username,PDO::PARAM_STR);
                $password=hash('sha256',$data->password);
                $stmt1->bindParam("password", $password,PDO::PARAM_STR);
                $stmt1->bindParam("email", $email,PDO::PARAM_STR);
                $stmt1->bindParam("name", $name,PDO::PARAM_STR);
                $stmt1->execute();
                
                $userData=internalUserDetails($email);
                
            }
            
            $db = null;
         

            if($userData){
               $userData = json_encode($userData);
                echo '{"userData": ' .$userData . '}';
            } else {
               echo '{"error":{"text":"Enter valid data"}}';
            }

           
        }
        else{
            echo '{"error":{"text":"Enter valid data"}}';
        }
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}


/* ### internal Username Details ### */
function internalUserDetails($input) {
    
    try {
        $db = getDB();
        $sql = "SELECT user_id, name, email, username FROM users WHERE username=:input or email=:input";
        $stmt = $db->prepare($sql);
        $stmt->bindParam("input", $input,PDO::PARAM_STR);
        $stmt->execute();
        $usernameDetails = $stmt->fetch(PDO::FETCH_OBJ);
        $usernameDetails->token = apiToken($usernameDetails->user_id);
        $db = null;
        return $usernameDetails;
        
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
    
}

function feed(){
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    
    $systemToken=apiToken($user_id);
   
    try {
         
        if($systemToken == $token){
            $feedData = '';
            $db = getDB();
            $sql = "SELECT * FROM feed WHERE user_id_fk=:user_id ORDER BY feed_id DESC";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $feedData = $stmt->fetchAll(PDO::FETCH_OBJ);
           
            $db = null;
            echo '{"feedData": ' . json_encode($feedData) . '}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
    
}
function getRateList(){
       $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    
    $systemToken=apiToken($user_id);
   
    try {
         
        if($systemToken == $token){
            $rateData = '';
            $db = getDB();
            $sql = "SELECT er.id, er.rate, LOWER(country_code) as country_code, tc.country_name AS to_country FROM `exchange_rate` er
                    LEFT JOIN `apps_countries` tc ON
                    er.`to_country`=tc.`id` ORDER BY id ASC";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $rateData = $stmt->fetchAll(PDO::FETCH_OBJ);
           
            $db = null;
            echo '{"rateData": ' . json_encode($rateData) . '}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    } 
}
function editratelist(){
       $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    $id=(int) $data->rateID;
    $systemToken=apiToken($user_id);
   
    try {
         
        if($systemToken == $token){
            $rateData = '';
            $db = getDB();
            $sql = "SELECT er.id, er.rate, LOWER(country_code) as country_code, tc.country_name AS to_country FROM `exchange_rate` er
                    LEFT JOIN `apps_countries` tc ON
                    er.`to_country`=tc.`id`
                    where er.id=:id ORDER BY id ASC";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $rateData = $stmt->fetchAll(PDO::FETCH_OBJ);
           
            $db = null;
            echo '{"rateEditData": ' . json_encode($rateData) . '}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    } 
}
function rateUpdate(){
       $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    $id=$data->id;
    $rate=$data->rate;
    $systemToken=apiToken($user_id);
   
    try {
         
        if($systemToken == $token){
            $rateData = '';
            $db = getDB();
            /*$sql = "SELECT er.id, er.rate, LOWER(country_code) as country_code, tc.country_name AS to_country FROM `exchange_rate` er
                    LEFT JOIN `apps_countries` tc ON
                    er.`to_country`=tc.`id`
                    where er.id=:id ORDER BY id ASC";*/
            $sql="UPDATE exchange_rate
                    SET rate=:rate
                    WHERE id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("rate", $rate);
            $stmt->bindParam("id", $id, PDO::PARAM_INT);
            $stmt->execute();
            // $rateData = $stmt->fetchAll(PDO::FETCH_OBJ);

            $sql1 = "SELECT * FROM exchange_rate Order By id ASC";
            $stmt1 = $db->prepare($sql1);
            //print_r($stmt->errorInfo()); die();
            $stmt1->execute();
            $rateData = $stmt1->fetch(PDO::FETCH_OBJ);

            $db = null;
            echo '{"rateEditData": ' . json_encode($rateData) . '}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    } 
}
function get_countryes(){
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    
    $systemToken=apiToken($user_id);
   
    try {
         
        if($systemToken == $token){
            $countryData = '';
            $db = getDB();
            $sql = "SELECT * FROM apps_countries ORDER BY id ASC";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $countryData = $stmt->fetchAll(PDO::FETCH_OBJ);
           
            $db = null;
            echo '{"countryData": ' . json_encode($countryData) . '}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
    
    
}
function rateCreate(){

    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    $rate=$data->exchange_rate;
    // $from_country=$data->from_country;
    $to_country=$data->to_country;
    
    $systemToken=apiToken($user_id);
  
    try {
          $db = getDB();
        if($systemToken == $token){

            // $sqle = "SELECT * FROM exchange_rate Order By id ASC";
            $sqle = "SELECT * FROM exchange_rate WHERE to_country=:to_country";
           // echo $sqle; die();
            $stmt = $db->prepare($sqle);

            $stmt->bindParam("to_country", $to_country,PDO::PARAM_STR);
            $stmt->execute();

            $mainCount=$stmt->rowCount();
            // echo $mainCount; die();
            if($mainCount<=0){
            $rateData = '';
            $sql = "INSERT INTO exchange_rate ( to_country , rate) VALUES (:to_country,:rate)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("to_country", $to_country, PDO::PARAM_STR);

            // $created = time();
            $stmt->bindParam("rate", $rate);
            $stmt->execute() or die($stmt->errorInfo());
            
            $sql1 = "SELECT * FROM exchange_rate Order By id ASC";
            $stmt1 = $db->prepare($sql1);
            //print_r($stmt->errorInfo()); die();
            $stmt1->execute();
            $rateData = $stmt1->fetch(PDO::FETCH_OBJ);


            $db = null;
            echo '{"rateData": ' . json_encode($rateData) . '}';
        }else{
            echo '{"error":{"text":"This rate already exist"}}';
        }
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text": '. $e->getMessage() .'}}';
    }

}

function feedUpdate(){

    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    $feed=$data->feed;
    
    $systemToken=apiToken($user_id);
   
    try {
         
        if($systemToken == $token){
         
            
            $feedData = '';
            $db = getDB();
            $sql = "INSERT INTO feed ( feed, created, user_id_fk) VALUES (:feed,:created,:user_id)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("feed", $feed, PDO::PARAM_STR);
            $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $created = time();
            $stmt->bindParam("created", $created, PDO::PARAM_INT);
            $stmt->execute();
            


            $sql1 = "SELECT * FROM feed WHERE user_id_fk=:user_id ORDER BY feed_id DESC LIMIT 1";
            $stmt1 = $db->prepare($sql1);
            $stmt1->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $stmt1->execute();
            $feedData = $stmt1->fetch(PDO::FETCH_OBJ);


            $db = null;
            echo '{"feedData": ' . json_encode($feedData) . '}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}

function feedDelete(){
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $user_id=$data->user_id;
    $token=$data->token;
    $feed_id=$data->feed_id;
    
    $systemToken=apiToken($user_id);
   
    try {
         
        if($systemToken == $token){
            $feedData = '';
            $db = getDB();
            $sql = "Delete FROM feed WHERE user_id_fk=:user_id AND feed_id=:feed_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
            $stmt->bindParam("feed_id", $feed_id, PDO::PARAM_INT);
            $stmt->execute();
            
           
            $db = null;
            echo '{"success":{"text":"Feed deleted"}}';
        } else{
            echo '{"error":{"text":"No access"}}';
        }
       
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
    
    
    
}






?>
