<?php

// Prevent direct access
if (basename($_SERVER['SCRIPT_FILENAME']) == 'header.php') {
    http_response_code(403); // Forbidden
    header("Location: index.php");
    exit();
}
?>

<link rel="stylesheet" href="css/header.css">
<script src="js/header.js" defer></script>

    <header>
        <nav class="navbar">

            <a href="#" class="nav-title">URLShawty</a>
            <?php if (isset($_SESSION['feedback'])): ?>
                <p id="feedback-below-header" class="fade-out"><?= $_SESSION['feedback'] ?></p>
                <?php unset($_SESSION['feedback']); // Clear the message after displaying it ?>
            <?php endif; ?>

            <ul class="nav-menu">
                <?php include_once("functions/check_login_status.php") ?>
                
                <?php if(!login_status()): ?>
                    <li>
                        <a href="shorten-random.php" class="nav-link">Generate Shortlinks</a>
                    </li>
                    <li>
                        <a href="index.php" class="nav-link">Get Started</a>
                    </li>
                    <li>
                        <a href="login.php" class="nav-link">Log in</a>
                    </li>
                    <li>
                        <a href="register.php" class="nav-link">Register</a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="shorten-random.php" class="nav-link">Generate Shortlinks</a>
                    </li>
                    <li>
                        <a href="my-urls.php" class="nav-link">My URLs</a>
                    </li>
                    <li>
                        <a href="index.php" class="nav-link">Get Started</a>
                    </li>
                    <li>
                        <a href="functions/logout.php" class="nav-link">Logout</a>
                    </li>
                <?php endif;?>
            </ul>

            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>

        </nav>
    </header>