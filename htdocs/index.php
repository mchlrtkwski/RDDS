<!DOCTYPE html>
<?php
    //Database Connection
    require("db.php");

    $shouldPrintFail = false;

    if(!empty($_SESSION['user'])){
        header("Location: home.php");
        die("Redirecting to index.php");
    }

    //Check if a POST Request was made
    if(!empty($_POST))
    {
        //////////Pull Database information from submitted email////////////////
        $query = "SELECT id, password, salt, email FROM users WHERE email = :email";
        $query_params = array(':email' => $_POST['email']);
        try{
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){
            die("Failed to run query: " . $ex->getMessage());
        }
        ////////////////////////////////////////////////////////////////////////
        $login_ok = false;
        $row = $stmt->fetch();
        ////Compare submitted password to Stored Password by performing hash////
        if($row){
            $check_password = hash('sha256', $_POST['password'] . $row['salt']);
            for($round = 0; $round < 65536; $round++){
                $check_password = hash('sha256', $check_password . $row['salt']);
            }
            if($check_password === $row['password']){
                $login_ok = true;
            }
        }
        ////////Remove private information, then set the session variable///////
        if($login_ok){
            unset($row['salt']);
            unset($row['password']);
            $_SESSION['user'] = $row;
            header("Location: home.php");
            die("Redirecting to: private.php");
        }else{
          $shouldPrintFail = true;
        }
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
    <div class="container">
      <form method = "POST" action = "." class="form-signin">
        <div clas="imageResize"><img src="./res/logo.png"</div>
        <h2 class="form-signin-heading">Please sign in</h2>

        <!--//////////////////Print out failed login//////////////////////// -->
        <?php if ($shouldPrintFail){
			/*echo "<p style=\"text-align:center; color: red;\">Login Failure</p>";*/

			 echo "<script type='text/javascript'>alert('Invalid user or password');</script>";

			 }
		?>

        <input name = "email" type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
        <input name = "password" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <p style="text-align:center;">or</p>
      </form>
      <button onclick = "window.location='./signup.php'; return false;" class="btn btn-lg btn-primary btn-block">Sign up</button>
    </div> <!-- /container -->
    <script src="./assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
