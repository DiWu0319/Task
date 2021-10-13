<?php
   
    declare(strict_types = 1);
  
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    $curYear = date('Y');
    $username = $password = $errorMessage = "";
    $phpScript = sanitizeValue($_SERVER['PHP_SELF']);



    function sanitizeValue($value) {
        return htmlspecialchars( stripslashes( trim( $value ) ) );
    }

   
    if ( $_SERVER['REQUEST_METHOD'] == 'POST') {    
        require_once 'inc.db.php';
        $dsn = 'mysql:host=' . HOST . ';dbname=' . DB;
        $pdo = new PDO($dsn, USER, PWD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        session_start();
        $loginUsername = sanitizeValue($_POST['username']);
        $loginPassword = sanitizeValue($_POST['password']);

        try {
            $sql = "
            SELECT username, password
            FROM users
            WHERE username = '$loginUsername'
            ";

            $stm = $pdo->query($sql, PDO::FETCH_ASSOC);

            if ( $stm->rowCount() == 1 ) {
                $pdo = null;
                $userRecord = $stm->fetch();

                if ( password_verify($loginPassword, $userRecord['password']) ) {
                    
                    $_SESSION['username'] = $loginUsername;

                    header('Location: index.php');
                } else {
                    die("Unable to authenticate.");
                }
            } else {
               die("Sorry, could not verify account.");
            }
        } catch (PDOException $e) {
            die ( $e->getMessage() );
        }    
    }
?>


<!DOCTYPE html>

<html>
    <head>
        <title>Login</title>
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    </head>

    <body class="w3-container w3-margin-left">
        <div class="w3-card w3-light-gray">
            <header class="w3-container w3-red w3-margin-top">
                <h1>Login Form</h1>
            </header>

            <form action="<?php echo $phpScript; ?>" method="POST" class="w3-container">
            <p>
                    <label class="w3-text-dark-grey">Username</label>
                    <span class="w3-text-red"> *</span>
                    <input required name="username" placeholder="username" value="<?php echo $username; ?>" class="w3-input w3-border">
                </p>
                <p>
                    <label class="w3-text-dark-grey">Password</label>
                    <span class="w3-text-red"> *</span>
                    <input required type="password" name="password" placeholder="password" value="<?php echo $password; ?>" class="w3-input w3-border">
                </p>
                <p> 
                    <button name="submit" class="w3-btn w3-red">Login</button>
                </p>
            </form>

            <h2 class="w3-container w3-text-red"><?php echo $errorMessage; ?></h2>
        </div>

        <form>
            <p> 
                No account yet? <a href="signUp.php"> Sign Up!</a>
            </p>
        </form>


        <footer text-align: center></br>&copy; <?php echo $curYear; ?> <b> s4576143 </b> </footer>
    </body>
</html>