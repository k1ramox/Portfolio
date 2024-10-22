<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

header('Content-Type: application/json');

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);


if (isset($data['url'])) {
    $url = ($data['url']);

    include("validate_url.php");

    // Simple validation (You might want to add more robust checks)

    if (!preg_match('/^https?:\/\//', $url)) {
        $url = 'https://' . $url; // Default to HTTPS
    }

    if (urlExists($url)) {
        //generate random code 6 and unique
        $domain = $_SERVER['HTTP_HOST'];
        $code = substr(md5(uniqid()), 0, 6);

        // Respond with success

        //insert into database
        include("database.php");

        try {
            $sql = "INSERT INTO url_infos (user_id, url, code) VALUES (?, ?, ?)";
            $query = $handler->prepare($sql);
            $query->execute(array($_SESSION['id'], $url, $code));

            echo json_encode(['success' => true, 'shortenedUrl' => $domain . "/" . $code]);
        }
        catch(Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            exit();
        }
    
        
    } else {
        // Invalid URL
        echo json_encode(['success' => false, 'message' => 'Invalid URL']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No URL provided']);
}

























/*
function shortenUrl ($originalUrl) {
    include("functions/validate_url.php");
    include("functions/database.php");

    if (urlExists($originalUrl)) {
        //generate random code 6 and unique
        $code = substr(md5(uniqid()), 0, 6);

        $sql = "INSERT INTO url_infos (user_id, url, code) VALUES(?, ?, ?)";

        $query = $handler->prepare($sql);
        $query->execute(array($_SESSION['id'], $originalUrl, $code));

        return $code;
}
    else {
        return false;
    }
}
*/
?>