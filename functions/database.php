<?php
    
    //Using PDO
    try {
        $handler = new PDO('mysql:host=localhost;dbname=urlshortenerdb', 'root', '');
    } catch(PDOException) {
        echo "Database problem.";
        die();
    }
    

    /*
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "urlshortenerdb";
    try {
        $conn = mysqli_connect( $db_server,
                                $db_user,
                                $db_pass,
                                $db_name );
    }
    catch(mysqli_sql_exception) {
        echo "Could not connect";
    }
    */

?>