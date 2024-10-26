<?php
    session_start();

    // check if the user already logined.
    include("functions/check_login_status.php"); 
    if(login_status()) {
        header("Location: index.php");
        exit();
    }

    $feedback = '';

    //if request method kay POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //connect to the database
        include("functions/database.php");

        //variables
        $username = strtolower(trim($_POST['username']));
        $password = $_POST['password'];
        $isvalid = true;

        //validations
        if (!$username || !$password) {
            $isvalid = false;
        }
        else {
            try {
                $sql = "SELECT * FROM users WHERE username = ?";
                $query = $handler->prepare($sql);
                $query->execute(array($username));

                $user = $query->fetch(PDO::FETCH_ASSOC);

                if ($user) {

                    $hashedPassword = $user['password'];

                    if (password_verify($password, $hashedPassword)) {
                        //login the user 
                        $_SESSION['id'] = $user['id'];
                        $_SESSION['feedback'] = 'Succesfully logged in!';
                        header("Location: index.php");
                        exit();
                    }
                    
                }
                else {
                    $feedback = 'Incorrent username or password.';
                }
            } 
            catch(PDOException $e) {
                echo $e->getMessage();
            }
        }
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/login.css">
    <script src="js/login.js" defer></script>

    <title>URLShawty - Log In</title>
</head>

<body>
    <?php include("header.php"); ?>

    <main id="main-login">
        <div id="login-container">
            <?php if(isset($_SESSION['login_required_prompt'])): ?>
                <h2 style="color: red;">Log in required!</h2>
                <?php unset($_SESSION['login_required_prompt']) ?>
            <?php else: ?>
                <h2>Log in</h2>
            <?php endif; ?>
            <p id="loginjs-feedback"></p>
            <p id="login-feedback"><?php echo $feedback ?></p>
            <form id="login-form" action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <input id="username-field" type="text" name="username" placeholder="Username">
                <input id="password-field" type="password" name="password" placeholder="Password">
                <input type="submit" name="login-submit" placeholder="Password" value="Login">
            </form>
            <a href="register.php">Don't have an account?</a>
        </div>
    </main>

    <?php include("footer.php"); ?>
</body>

</html>

