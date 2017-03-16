<!DOCTYPE html>
<?php
    //Database Connection
    require("db.php");

    //Check if Input was submitted
    if(!empty($_POST)){
        //Check if deviceNumber had input
        if(empty($_POST['deviceNumber'])){
            die("Please enter a device.");
        }
        //Check if password had input
        if(empty($_POST['password'])){
            die("Please enter a password.");
        }//Check if a valid email was entered
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            die("Invalid E-Mail Address");
        }

        //////////////Check to see if another user has that email///////////////
        $query = "SELECT 1 FROM users WHERE email = :email";
        $query_params = array(':email' => $_POST['email']);
        try{
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){
            die("Failed to run query: " . $ex->getMessage());
        }
        $row = $stmt->fetch();
        if($row){
            die("This email is already in use");
        }
        //////////////////Salt and hash the given Password//////////////////////
        $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
        $password = hash('sha256', $_POST['password'] . $salt);
        for($round = 0; $round < 65536; $round++){
            $password = hash('sha256', $password . $salt);
        }
        //////////////////Insert new User into Database/////////////////////////
        $query = "INSERT INTO users (deviceID, password, salt, email) VALUES (:deviceID,:password, :salt, :email)";
        $query_params = array(':deviceID' => $_POST['deviceNumber'], ':password' => $password, ':salt' => $salt, ':email' => $_POST['email']);
        try{
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex)
        {
            die("Failed to run query: " . $ex->getMessage());
        }
        ///////////////////Head back to login page//////////////////////////////
        header("Location: index.php");
        die("Redirecting to login.php");
    }

?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">
    <title>RDDS</title>
    <link href="./dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
    <link href="./css/signin.css" rel="stylesheet">
    <link href="./css/main.css" rel="stylesheet">
    <script src="./assets/js/ie-emulation-modes-warning.js"></script>
  </head>

  <body>
      <form method="post" action = "signup.php" class="form-signin">
        <div clas="imageResize"><img src="./res/logo.png"</div>
        <h2 class="form-signin-heading">Sign up</h2>
        <input name = "deviceNumber" id="inputEmail" class="form-control" placeholder="Device Number" required autofocus>
        <input name = "email" type="email" id="inputEmail" class="form-control" placeholder="Email Address" required>
        <input name = "password" id="inputEmail" class="form-control" placeholder="Password" required>
        <!--<input name = "passwordCheck" id="inputEmail" class="form-control" placeholder="Confirm Password" required>-->
        <br>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
        <p style="text-align:center;">or</p>
        <button onclick = "window.location='./index.php'; return false;" class="btn btn-lg btn-primary btn-block">Sign in</button>
      </form>
<!-- /container -->
    <script src="./assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
