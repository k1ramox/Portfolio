<?php

session_start();
include("database.php");

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Gather infos
    try {
        $sql = "SELECT * FROM url_infos WHERE code = ?";
        $query = $handler->prepare($sql);
        $query->execute(array($code));
    
        $row = $query->fetch(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e) {
        echo "Something went wrong, " . $e->getMessage();
    }

    if ($row) {
        // Increment the visit count
        if (!isset($_SESSION['visited'][$code])) { //check if wala pa navisit sa user
            try {
                $sql = "UPDATE url_infos SET visit_count = visit_count + 1 WHERE code = ?";
                $query = $handler->prepare($sql);
                $query->execute(array($code));
                
                //markahan nga navisit na sa user 
                $_SESSION['visited'][$code] = true;
            }
            catch (PDOException $e) {
                echo "Something went wrong, " . $e->getMessage();
            }
        }
        // Redirect to the original URL
        header("Location: " . $row['url']); // 301 permanent redirect
        exit(); // Important to exit after the redirect
    } else {
        echo "URL not found.";
    }
} else {
    echo "No code provided.";
}

?>