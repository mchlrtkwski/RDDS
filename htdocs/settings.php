<!DOCTYPE html>
<?php
//Database Connection
require("db.php");
////////////////////Check if session is set/////////////////////////////////////
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php");
}

$savedChangeBanner = false;
/////////////////////Check to see if phoneNumber needs editing//////////////////
if(!empty($_POST['phoneNumber'])){
  $query_params = array(':phone' => $_POST['phoneNumber'],':user_id' => $_SESSION['user']['id'],);
  $query = "UPDATE users SET phone = :phone WHERE id = :user_id";
  try{
    $stmt = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch(PDOException $ex){
    die("Failed to run query: " . $ex->getMessage());
  }
  $savedChangeBanner = true;
}
////////////////////Check to see if carrier needs editing///////////////////////
if(!empty($_POST['carrier'])){
  $query_params = array(':carrier' => $_POST['carrier'], ':user_id' => $_SESSION['user']['id'],);
  $query = "UPDATE users SET carrier = :carrier WHERE id = :user_id";
  try{
    $stmt = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch(PDOException $ex){
    die("Failed to run query: " . $ex->getMessage());
  }
  $savedChangeBanner = true;
}
////////////////////Check to see if alertMethod needs editing///////////////////
if(!empty($_POST['alertMethod'])){
  $query_params = array(':alertMethod' => $_POST['alertMethod'], ':user_id' => $_SESSION['user']['id']);
  $query = "UPDATE users SET alertMethod = :alertMethod WHERE id = :user_id";
  try{
    $stmt = $db->prepare($query);
    $result = $stmt->execute($query_params);
  }
  catch(PDOException $ex){
    die("Failed to run query: " . $ex->getMessage());
  }
  $savedChangeBanner = true;
}
////////////////////Grab current information from Database//////////////////////
$userArray = $_SESSION['user'];
$userId = $userArray['id'];
$query = "SELECT email, phone, alertMethod, carrier FROM users WHERE id = $userId";
try{
  $stmt = $db->prepare($query);
  $stmt->execute();
}
catch(PDOException $ex){
  die("Failed to run query: " . $ex->getMessage());
}
$rows = $stmt->fetchAll();
////////////////////Set Default Variables///////////////////////////////////////
$phoneNumber = "";
$alertMethod = "";
$carrier = "";
foreach($rows as $row){
  $phoneNumber = $row['phone'];
  $alertMethod = $row['alertMethod'];
  $carrier = $row['carrier'];
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
  <link href="./css/signin.css" rel="stylesheet">
  <link href="./css/main2.css" rel="stylesheet">
  <link href="./css/footer.css" rel="stylesheet">
  <link href="./assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
  <script src="./assets/js/ie-emulation-modes-warning.js"></script>
</head>

<body>
  <div class="container">
    <form action="./settings.php" method = "POST" class="form-signin">
      <div class="imageResize"><img src="./res/logo.png"></div>
      <h2 class="form-signin-heading">Settings</h2>
      <!--///////////////Alert User if Changes were made/////////////////////-->
      <?php if ($savedChangeBanner){echo "<p style=\"text-align:center; color: red;\">CHANGES HAVE BEEN SAVED</p>";}?>
        <label for="inputEmail" class="sr-only">Email address</label>
        <hr>
        <p>Telephone Number & Carrier</p>
        <div class="telNumber">
          <!--///////////////Echo current phoneNumber////////////////////////-->
          <input name = "phoneNumber" id="inputEmail" class="form-control" placeholder="Phone Number" value = <?php echo $phoneNumber; ?> required autofocus>
          <select name = "carrier">
            <!--///////////////Set current default setting///////////////////-->
            <option value="" <?php if ($carrier == ""){echo "selected";}?>>None</option>
            <option value="@txt.att.net" <?php if ($carrier == "@txt.att.net"){echo "selected";}?>>AT&T</option>
            <option value="@mymetropcs.com" <?php if ($carrier == "@mymetropcs.com"){echo "selected";}?>>Metro PCS</option>
            <option value="@tmomail.net" <?php if ($carrier == "@tmomail.net"){echo "selected";}?>>T-Mobile</option>
            <option value="@vtext.com" <?php if ($carrier == "@vtext.com"){echo "selected";}?>>Verizon</option>
          </select>
        </div>
        <hr>
        <p>Select Alert Preference</p>
        <div class="choiceSelect">
          <!--///////////////////Set current default setting/////////////////-->
          <input type="radio" name="alertMethod" value="Email" <?php if ($alertMethod == "Email"){echo "checked";}?>> Email
          <input type="radio" name="alertMethod" value="Text" <?php if ($alertMethod == "Text"){echo "checked";}?>> Text
          <input type="radio" name="alertMethod" value="Both" <?php if ($alertMethod == "Both"){echo "checked";}?>> Both
        </div>
        <hr>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Apply Changes</button>
        <hr>
        <footer>
          <br>
          <div class = "footerButtons">
            <button onclick = "window.location='./home.php'; return false;"><img src = './res/log.png'></button>
            <button class = "active" onclick = "window.location='./signup.php'; return false;"><img class="invert" src = './res/settings.png'></button>
          </div>
          <br>
        </footer>
        <a style="display:block; text-align:center;" href="logout.php">Logout</a>
        <script src="./assets/js/ie10-viewport-bug-workaround.js"></script>
      </div>
    </body>
    </html>
