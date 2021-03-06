<?php

    declare(strict_types = 1);
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    $curYear = date('Y');
    session_start();

    $userName = $_SESSION['username'];
    if (isset($userName)) { 
        $welcomeMessage = "<h3>Add a Task | <a href='addRecord.php'>Add Project</a> | <a href='index.php'>Home</a></h3>";
    }

    function sanitizeInput($value) {
        return htmlspecialchars( stripslashes( trim( $value) ) );
    }     

    function insertTaskRecord(PDO $pdo, string $name, string $status, string $userName): int {
        $projectID = getProjectID($pdo, $userName);

        
        $sql = "
        INSERT INTO task
        (project\$id, name, status)
        VALUES
        ('$projectID', '$name', '$status')
        ";
        
        $status = $pdo->exec($sql);
        $id = (int)$pdo->lastInsertId();
        
        return $id;
    }

    function saveProjectTask(PDO $pdo, string $name, string $status, string $userName){
        $userID = getUserId($pdo, $userName);
        $projectId = getProjectID($pdo, $userName);
        if ($projectId == '') {
            
            echo "</br><h1 style='color:red'>***ERROR: No project exists, must add project before adding a task***</h1>";
        } else if ($projectId != ''){
            
            insertTaskRecord($pdo, $name, $status, $userName);
            echo "</br><h1 style='color:green'>Task entered successfully</h1>";
        }else {
            
            echo "</br><h1 style='color:red'>***ERROR occured while adding Task***</h1>";
        }
    }

    function getUserID(PDO $pdo, string $userName){
        
        $sql = "
        SELECT id
        FROM users
        WHERE username = '$userName'
        ";
        $stm = $pdo->query($sql, PDO::FETCH_ASSOC);
        if ($stm->rowCount() == 1) { return (int)$stm->fetch()['id']; }
        else { return ''; }
    }

    function getProjectID(PDO $pdo, string $userName){
        
        $userID = getUserID($pdo, $userName);

        $sql = "
        SELECT id
        FROM project
        WHERE user\$id= '$userID'
        ";
        $stm = $pdo->query($sql, PDO::FETCH_ASSOC);
        if ($stm->rowCount() == 1) { return (int)$stm->fetch()['id']; }
        else { return ''; }
    }
    
    

    $phpScript = sanitizeInput($_SERVER['PHP_SELF']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            
            require_once 'inc.db.php';
            $dsn = 'mysql:host=' . HOST . ';dbname=' . DB;
            $pdo = new PDO($dsn, USER, PWD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            
            $name = sanitizeInput($_POST['name']);
            $status = $_POST['status'];

            saveProjectTask($pdo, $name, $status, $userName);

        } catch(PDOEXCEPTION $e) {
            
            die( $e->getMessage() );
        }
        $pdo = null;
    }




?>
<!DOCTYPE html>

<html>
    <head>
        <title>Add Project | TaskList</title>
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    </head>

    <body class="w3-container w3-margin-left">
        <div class="w3-panel">
            <header>
                <h2>
                    <?php 
                        echo $welcomeMessage;
                    ?>
                </h2>
            </header>
            <form action="<?php echo $phpScript; ?>" method="POST">
                </br>
                <h6>Add a Task</h6>
                <input type="text" name="name" placeholder = "Task Name" required></br></br>
                <p>Is this task completed?</p>
                <input type="radio" id="yes" name="status" value="Y">
                <label for="yes">Yes (Y)</label>&emsp;
                <input type="radio" id="no" name="status" value="N" checked="true">
                <label for="yes">No (N)</label></br><br><br>
                <button class="w3-btn w3-green">Submit Project</button>
            </form>
        </div>
    </body>
    <footer id=footer class="w3-container w3-center w3-text-gray">&copy; <?php echo $curYear; ?>  s4576143 </footer>
    <style>
    body{
            background-color: #fcf3cf; 
    }
    footer{
        height:70px;   
        background-color: #f9e79f;
        font:#515a5a;
        position:absolute;
        bottom:0;
        left:0;
        width:100%;
    }
    </style>
</html>
