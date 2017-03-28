<!DOCTYPE html>
<?php

	
    //Database Connection
    require("db.php");

	$checkLogin = true;
    //Check if Input was submitted
    if(!empty($_POST)){
        //Check if deviceNumber had input
        if(empty($_POST['deviceNumber'])){
            echo "<script type='text/javascript'>alert('Please enter a device');</script>";
			$checkLogin = false;
        }
		
        //Check if password had input
        if(empty($_POST['password'])){
			echo "<script type='text/javascript'>alert('Please enter a password');</script>";
			$checkLogin = false;
        }
		
		//Check if a valid email was entered
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            echo "<script type='text/javascript'>alert('Invalid E-Mail Address');</script>";
			$checkLogin = false;
        }
		
		//check if re-type password field is empty
        if(empty($_POST['passwordCheck'])) {
            echo "<script type='text/javascript'>alert('Confirm password field is empty');</script>";
			$checkLogin = false;
		}
		
		//check if password and passwordCheck match 
        if($_POST['passwordCheck'] != $_POST['password']) {
            echo "<script type='text/javascript'>alert('Password does not match re-type password');</script>";
			$checkLogin = false;
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
			$checkLogin = false;
        }
        $row = $stmt->fetch();
        if($row){
            echo "<script type='text/javascript'>alert('This email is already in use');</script>";
			$checkLogin = false;
			//die("This email is already in use");
        }
		
        //////////////////Check that the device entered is a valid device//////////////////////
		if ($checkLogin) 
		{
		  $query = "SELECT * FROM devices WHERE id = :deviceNumber";
		  $query_params = array(':deviceNumber' => $_POST['deviceNumber']);
		  try{
			  $stmt = $db->prepare($query);
			  $result = $stmt->execute($query_params);
		  }
		  catch(PDOException $ex){
			  die("Failed to run query: " . $ex->getMessage());
		  }
		  $row = $stmt->fetch();
		  if(!$row){
			  echo "<script type='text/javascript'>alert('invalid device entered');</script>";
			  header("Location: signup.php");
			  die("Redirecting to signup.php");
		  }
		  else {
			  $query = "SELECT deviceID FROM users WHERE deviceID = :deviceNumber";
			  $query_params = array(':deviceNumber' => $_POST['deviceNumber']);
			  try{
				  $stmt = $db->prepare($query);
				  $result = $stmt->execute($query_params);
			  }
			  catch(PDOException $ex){
				  die("Failed to run query: " . $ex->getMessage());
			  }
			  $row = $stmt->fetch();
			  if($row){
				  header("Location: signup.php");
				  die("Redirecting to signup.php");
			  }
			  //////////////////Salt and hash the given Password//////////////////////
			  $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
			  $password = hash('sha256', $_POST['password'] . $salt);
			  for($round = 0; $round < 65536; $round++){
				  $password = hash('sha256', $password . $salt);
			  }
			  //////////////////Insert new User into Database////////////////////////
			  
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
	  }
	  if(!$checkLogin) {
		  /*echo "<script type='text/javascript'>alert('check login = false');</script>";*/
		  $checkLogin = true;
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
      <form method="post" action = "signup.php" class="form-signin">
        <div clas="imageResize"><img src="./res/logo.png"</div>
        <h2 class="form-signin-heading">Sign up</h2>
        <input name = "deviceNumber" id="inputEmail" class="form-control" placeholder="Device Number" required autofocus>
        <input name = "email" type="email" id="inputEmail" class="form-control" placeholder="Email Address" required>
       <input name = "password" type="password" id="inputEmail" class="form-control" placeholder="Password" required>
       <input name = "passwordCheck" type='password' id="inputEmail" class="form-control" placeholder="Confirm Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
        <p style="text-align:center;">or</p>
        <button onclick = "window.location='./index.php'; return false;" class="btn btn-lg btn-primary btn-block">Sign in</button>
      </form>
<!-- /container -->
    <script src="./assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
