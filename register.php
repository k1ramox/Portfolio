<?php
    session_start();

    // check if the user already logined.
    include("functions/check_login_status.php"); 
    if(login_status()) {
        header("Location: index.php");
        exit();
    }

    //if request method kay POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //connect to the database
        include("functions/database.php");

        //variables
        $isvalid = true;
        $username = strtolower(trim($_POST['username']));
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];

        //validation sa inputs
        //check sa length sa username, check if empty, check if naay whitespace, check if empty ang password, check if same ba ang password, check if ang length sa password kay less than 6 ba or greather than 50
        if ($username == '' || $password == '' || $confirm == '') {
            $_SESSION['register-feedback'] = 'All fields are required.';
            $isvalid = false;
        }
        else if (strlen($username) > 25) {
            $_SESSION['register-feedback'] = 'Username must not exceed 25 characters.';
            $_SESSION['username-feedback-bool'] = true;
            $isvalid = false;
        }
        else if (strlen($username) < 3) {
            $_SESSION['register-feedback'] = 'Username must be at least 3 characters long.';
            $_SESSION['username-feedback-bool'] = true;
            $isvalid = false;
        }
        else if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
            $_SESSION['register-feedback'] = 'Username cannot contain special characters.';
            $_SESSION['username-feedback-bool'] = true;
            $isvalid = false;
        }
        else if (strlen($password) < 6) {
            $_SESSION['register-feedback'] = 'Password must be at least 6 characters long.';
            $isvalid = false;
        }
        else if (strlen($password) > 50) {
            $_SESSION['register-feedback'] = 'Password must not exceed 50 characters.';
            $isvalid = false;
        }
        else if ($password != $confirm) {
            $_SESSION['register-feedback'] = 'Password confirmation does not match.';
            $isvalid = false;
        }

        else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        

        if ($isvalid) {
            try {
                $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
                $query = $handler->prepare($sql);
                $query->execute(array($username, $password));

                //login the user after registered
                $sql_login = "SELECT id FROM users WHERE username = ?";
                $query_login = $handler->prepare($sql_login);
                $query_login->execute(array($username));

                $row = $query_login->fetch(PDO::FETCH_ASSOC);

                $_SESSION['id'] = $row['id'];
                header("Location: index.php" );
            }
            catch (PDOException) {
                echo"Username already taken!";
                exit();
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
    <link rel="stylesheet" href="css/register.css">
    <script src="js/register.js" defer></script>

    <title>Register - URLShawty </title>
</head>

<body>
    <?php include("header.php"); ?>

    <main id="main-register">
            <div id="register-container">
                <h2>Register</h2>
                <?php
                if (isset($_SESSION['register-feedback'])) {
                    echo '<p id="register-feedback">' . $_SESSION['register-feedback'] . '</p>';
                    unset($_SESSION['register-feedback']); // Clear the message after displaying it
                }
                ?>
                <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    <input class="register-input-field" <?php if (isset($_SESSION['username-feedback-bool'])) { echo 'class="error"'; unset($_SESSION['username-feedback-bool']); }?> type="text" name="username" placeholder="Username">
                    <input class="register-input-field" type="password" name="password" placeholder="Password">
                    <input class="register-input-field" type="password" name="confirm" placeholder="Confirm Password">
                    <input id="register-submit" type="submit" name="register-submit" placeholder="Password" value="Register">
                </form>
                <a href="login.php">Already have an account?</a>
            </div>
    </main>

    <?php include("footer.php"); ?>
</body>

</html>

