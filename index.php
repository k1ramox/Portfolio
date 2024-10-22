<?php
    session_start();
    include("functions/check_login_status.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/index.css">

    <title>URLShawty - Get Started</title>
</head>

<body>
    <?php include("header.php"); ?>

    <main id="main-index">
        <div id="welcome-container">
            <h1>Welcome!</h1>   
            <br>
            <br>
            <p>Simplify your links and make sharing easier with our URL shortener service. Designed for registered users, our platform allows you to effortlessly shorten long URLs, making them more manageable and user-friendly. Whether you're sharing links on social media, in emails, or on your website, our service ensures that your URLs are concise and easy to remember.</p>
            <br>
            <br>
            <br>
            <a href="shorten-random.php">SHORTEN YOUR URL NOW</a>
        </div>

        <div id="features-container">
            <div class="feature-box">
                <div class="feature-image-container">
                    <img class="feature-image" src="images/features-stats.png">
                </div>
                <div class="feature-title">
                    Stats
                </div>
                <div class="feature-description">
                    Quickly monitor visit counts for your links, allowing you to assess engagement and make informed decisions.
                </div>
            </div>
            <div class="feature-box">
                <div class="feature-image-container">
                    <img class="feature-image" src="images/features-easy.png">
                </div>
                <div class="feature-title">
                    Easy
                </div>
                <div class="feature-description">
                    URLShawty is easy and fast, you just have to create an account to use our available tools for your link.
                </div>
            </div>
            <div class="feature-box">
                <div class="feature-image-container">
                    <img class="feature-image" src="images/features-any.png">
                </div>
                <div class="feature-title">
                    Any
                </div>
                <div class="feature-description">   
                    Any link is acceptable as long as it is valid and exists, providing flexibility in your sharing options.
                </div>
            </div>
        </div>
    </main>

    <?php include("footer.php"); ?>
</body>

</html>

