<?php
    session_start();

    include("functions/check_login_status.php"); // check if the user already logined.

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
        if (strlen($username) > 25 
        || strlen($username) < 3 
        || $username == '' 
        || preg_match('/\s/', $username) 
        || $password == '' 
        || $confirm == '' 
        || $password != $confirm 
        || strlen($password) < 6
        || strlen($password) > 50) {

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

    <title>Register - URL Shortener </title>
</head>

<body>
    <?php include("header.php"); ?>

    <main>
            <div id="register-container">
                <h1>Register</h1>
                <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    <input type="text" name="username" placeholder="Username">
                    <input type="password" name="password" placeholder="Password">
                    <input type="password" name="confirm" placeholder="Confirm Password">
                    <input type="submit" name="register-submit" placeholder="Password" value="Register">
                </form>

            </div>
    </main>

    <?php include("footer.html"); ?>
</body>

</html>

