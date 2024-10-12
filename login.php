<?php
    session_start();

    include("functions/check_login_status.php"); // check if the user already logined.

    //if request method kay POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //connect to the database
        include("functions/database.php");

        //some code later
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/login.css">

    <title>Log In - URL Shortener </title>
</head>

<body>
    <?php include("header.php"); ?>

    <main>
            <div id="login-container">
                <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    <input type="text" name="username" placeholder="Username">
                    <input type="password" name="password" placeholder="Password">
                    <input type="submit" name="login-submit" placeholder="Password" value="Login">
                </form>

            </div>
    </main>

    <?php include("footer.html"); ?>
</body>

</html>

