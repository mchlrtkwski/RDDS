<?php
    //Database Connection
    require("db.php");
    //Remove Session ID
    unset($_SESSION['user']);
    //Go back to Login page
    header("Location: index.php");
    die("Redirecting to: index.php");
?>
