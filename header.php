<header>
    <link rel="stylesheet" href="css/header.css">

    <div id="text-header-container">
        <h1>
            <a href="index.php">URL Shortener</a>
        </h1>
    </div>

    <nav>
        <ul>
            <?php if (isset($_SESSION['username'])): ?>
                <li><a href="My URLs.php">Profile</a></li>
                <li><a href="functions/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">login</a></li>
                <li><a href="register.php">register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>