<!DOCTYPE html>
<?php
	//TESTING SYNC
	//TESTING LOCAL TO TEST SERVER
    //Database Connection
    require("db.php");

    //Check if the session is set
    if(empty($_SESSION['user'])){
        header("Location: index.php");
        die("Redirecting to index.php");
    }

    $shouldBannerPrint = false;

    //////If the Clear button was submitted, clear the log in the Database//////
    if(!empty($_POST['clear'])){
      $query_params = array(':user_id' => $_SESSION['user']['id'],);
      $query = "UPDATE users SET log = \"\" WHERE id = :user_id";
      try{
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
      }
      catch(PDOException $ex){
        die("Failed to run query: " . $ex->getMessage());
      }
      $shouldBannerPrint = true;
    }
    /////////////////Grab the log from the Database/////////////////////////////
    $userArray = $_SESSION['user'];
    $userId = $userArray['id'];
    $query = "SELECT log FROM users WHERE id = $userId";
    try{
        $stmt = $db->prepare($query);
        $stmt->execute();
    }
    catch(PDOException $ex){
        die("Failed to run query: " . $ex->getMessage());
    }
    $rows = $stmt->fetchAll();
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
  <link href="./css/signin.css" rel="stylesheet">
  <link href="./css/main2.css" rel="stylesheet">
  <link href="./css/footer.css" rel="stylesheet">
  <link href="./assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
  <script src="./assets/js/ie-emulation-modes-warning.js"></script>
</head>

<body>
<div class="container">
    <form class="form-signin" method="POST" action="./home.php">
      <div clas="imageResize"><img src="./res/logo.png"></div>
      <h2 class="form-signin-heading">Log</h2>
      <hr>
      <!--///////Print confirmation if logs were recently cleared////////////-->
      <?php if($shouldBannerPrint){echo "<p style=\"text-align:center; color: red;\">LOGS CLEARED</p>";}?>

      <table style="border-collapse:collapse;">
        <tr class="tableHeads">
          <td>Incident Date</td>
          <td>Incident Time</td>
        </tr>
        <!--////////////Print the Log to the Screen///////////////////////// -->
        <?php foreach($rows as $row){echo $row['log'];}?>

      </table>
      <hr>
      <input type="hidden" name="clear" value="clear">
      <button class="btn btn-lg btn-primary btn-block" type="submit">Clear Log</button>
      <hr>
      <footer>
        <br>
        <div class = "footerButtons">
          <button class ="active" = "window.location='./signup.php'; return false;"><img src = './res/log.png' class="invert"></button>
          <button  onclick = "window.location='./settings.php'; return false;"><img src = './res/settings.png'></button>
        </div>
        <br></footer>
        <a style="display:block; text-align:center;" href="logout.php">Logout</a>
      </form>
    </div><!-- /container -->
    <script src="../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
  </html>
