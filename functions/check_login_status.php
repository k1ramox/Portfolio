<?php
    function login_status() {
        if (isset($_SESSION['id'])) {
            return true; //logined
            }
        else {
            return false; //not logined
        }
    }
?>