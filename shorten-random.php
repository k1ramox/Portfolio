<?php
    session_start();
    
    //check if the user is logined
    include("functions/check_login_status.php");
    if (!login_status()) {
        header("Location: login.php");
        $_SESSION['login_required_prompt'] = true;
        exit();
    }

    /*
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include("functions/database.php");
        include("functions/shortenUrl.php");

        $url = trim($_POST['url']);

        if (empty($url)) {
            echo "Url cannot be empty";
            exit();
        }

        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url; // Default to HTTPS
        }


        //$sub_url = $_POST['sub_url'];

        /*
        $sql = "INSERT INTO url_infos (user_id, url, sub_url) VALUES (?, ?, ?)";
        $query = $handler->prepare($sql);

        $query->execute(array($_SESSION['id'], $url, $sub_url));
        

    }
        */
    /* list of links for the user
    else {
        include("functions/database.php");

        $sql = "SELECT * FROM url_infos WHERE user_id = ?";

        $query = $handler->prepare($sql);

        $query->execute(array($_SESSION['id']));
        
        $list = $query->fetchAll(PDO::FETCH_ASSOC);

        
        
        foreach($list as $row) {
            echo $row['url'];
        } 

    } */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/shorten-random.css">
    <script src="js/shorten-random.js" defer></script>

    <title>URLShawty - Generate Short Links</title>
</head>

<body>
    <?php include("header.php"); ?>

    <main id="main-shorten-random">;
        <div id=shorten-random-container>

            <form id="form-generate-random" action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <div id="form-generate-random-display">
                    <input id="urlInput" type="text" name="url" placeholder="Paste your URL here...">
                    <input id="shortrandom-button" type="submit" value="SHORTEN NOW">
                </div>

                <h5 id="random-generate-status">Loading please wait...</h5>
                <input id="try-again-button" type="submit" value="TRY AGAIN">
                
                <div id=success-generate-random>
                    <input id="urlResult" disabled type="text" value="" placeholder="Result">
                    <input id="copy-button" type="button" value="COPY">
                    <input id="shorten-another-button" type="submit" value="SHORTEN ANOTHER">
                </div>
            </form>
        </div>
    </main>

    <?php include("footer.php"); ?>
</body>

</html>