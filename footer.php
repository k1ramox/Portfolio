<?php

// Prevent direct access
if (basename($_SERVER['SCRIPT_FILENAME']) == 'footer.php') {
    http_response_code(403); // Forbidden
    header("Location: index.php");
    exit();
}
?>

<footer style="display: flex; justify-content:center; position:fixed; right: 0; left: 0; bottom: 0; background-color: hsl(120, 15%, 50%);">
    <p>© <a style="text-decoration: none; color: blue;" href="https://www.facebook.com/k1ramox">Ken Ian Marañon</a> - All Rights Reserved</p>
</footer>