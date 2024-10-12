<?php
    session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/indexloginfalse.css">

    <title>
        <?php
            if (isset($_SESSION['username'])) {
                echo "Home - URL Shortener";
            }
            else {
                echo "URL Shortener";
            }
        ?>
    </title>
</head>

<body>
    <?php include("header.php"); ?>

    <main>
            <?php if (isset($_SESSION['username'])): ?>

                <h1>Logined</h1>

            <?php else: ?>

                <div id="welcome-container">
                    <h1>Welcome to URL Shortener</h1>
                    <p>Simplify your links and make sharing easier with our URL shortener service. Designed for registered users, our platform allows you to effortlessly shorten long URLs, making them more manageable and user-friendly. Whether you're sharing links on social media, in emails, or on your website, our service ensures that your URLs are concise and easy to remember.</p>
                    <a href="test.php">Shorten Your URL Now</a>
                </div>

            <?php endif; ?>
    </main>

    <?php include("footer.html"); ?>
</body>

</html>

