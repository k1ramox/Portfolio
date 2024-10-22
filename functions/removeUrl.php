<?php
session_start();

header('Content-Type: application/json');

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $code = basename($data['code']);

    //connect to database
    include("database.php");

    //Get all user's url
    try {
        $sql = "SELECT * FROM url_infos WHERE user_id = ?";
        $query = $handler->prepare($sql);
    
        $query->execute(array($_SESSION['id']));
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(PDOException) {
        echo json_encode(['success' => false, 'message' => 'Something went wrong!']);
        exit();
    }

    //Check if ang id kay nga nakuha kay angkop sa current user
    $exist = false;
    foreach($result as $row) {
        if ($row['code'] == $code) {
            $exist = true;
            break;
        }
    }

    if (!$exist) {
        echo json_encode(['success' => false, 'message' => 'server validation got u!']);
        exit();
    }

    //DELETE row
    try {
        $sql = "DELETE FROM url_infos WHERE code = ? AND user_id = ?";
        $query = $handler->prepare($sql);
    
        $query->execute(array($code, $_SESSION['id']));
        
        echo json_encode(['success' => true, 'message' => $code . ' row deleted!']);
    }
    catch(PDOException) {
        echo json_encode(['success' => false, 'message' => 'Something went wrong!']);
        exit();
    }

}

?>