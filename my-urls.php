<?php
    session_start();

    // check if the user already logined.
    include("functions/check_login_status.php"); 
    if(!login_status()) {
        header("Location: login.php");
        $_SESSION['login_required_prompt'] = true;
        exit();
    }

    include("functions/database.php");
    

    $sql = "SELECT * FROM url_infos WHERE user_id = ?";
    $query = $handler->prepare($sql);
    $query->execute(array($_SESSION['id']));

    $url_infos = $query->fetchAll(PDO::FETCH_ASSOC);
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/my_urls.css">
    <script src="js/my-urls.js" defer></script>

    <title>URLShawty - My URLs</title>
</head>

<body>
    <?php include("header.php"); ?>

    <main id="main-myurls">
    <?php if ($url_infos): ?>
        <table class="url-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Original URL</th>
                </tr>
            </thead>
            <tbody>
                
                    <?php $index = 1; ?>
                    <?php foreach($url_infos as $row): ?> 
                        <tr>
                            <td style="font-weight: bold;"><?= $index++ ?></td>
                            <td class="original-url"><?= htmlspecialchars($row['url']) ?></td>
                            <input class="shortened-url" type="hidden" value="<?= htmlspecialchars($_SERVER['HTTP_HOST'] . '/' . $row['code']) ?>">
                            <input class="visit-count" type="hidden" value="<?= $row['visit_count'] ?>">
                            <td class="actions-url-td">
                                <img class="myurls-actions" src="images/myurls-actions.png">
                            </td>
                            <input class="url-id" type="hidden" value="<?= $row['id'] ?>">
                        </tr>
                    <?php endforeach; ?>
                
            </tbody>
        </table>
    <?php else: ?>
        <h2>No records.</h2>
    <?php endif; ?>
        <div id="modal-background">
            <div id="modal">
                    <p style="font-weight: bold; color:#f08080;" id="modal-feedback"></p>
                    <div id="top-section">
                        <div class="sideheader">
                            Link: 
                        </div>
                        <div class="value">
                            <input id="shortlink-container" disabled value="">
                        </div>
                    </div>
                    <div id="middle-section">
                        <div class="sideheader">
                            Visits: 
                        </div>
                        <div class="value">
                            <div id="visit-count-container">
                                <!-- visit count value goes here !-->
                            </div>
                        </div>
                    </div>
                    <div id="bottom-section">
                        <button id="copy-button">Copy</button>
                        <button id="remove-button">Remove</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include("footer.php"); ?>
</body>

</html>

