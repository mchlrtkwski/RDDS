<!DOCTYPE html>
<?php


	//Database Connection
	require("db.php");
	////////////////////Check if session is set/////////////////////////////////////
	if(empty($_SESSION['user'])){
	  header("Location: index.php");
	  die("Redirecting to index.php");
	}

	////////////////////Grab current information from Database//////////////////////
	$userArray = $_SESSION['user'];
	$userId = $userArray['id'];
	$query = "SELECT email, password, phone, alertMethod, carrier, salt FROM users WHERE id = $userId";
	try{
	  $stmt = $db->prepare($query);
	  $stmt->execute();
	}
	catch(PDOException $ex){
	  die("Failed to run query: " . $ex->getMessage());
	}
	$rows = $stmt->fetchAll();
	////////////////////Set Default Variables///////////////////////////////////////
	$notification = false;
	$checkSave = true;
	$checkOldPassword = true;
	$phoneNumber = "";
	$alertMethod = "";
	$changePassword = "";
	$carrier = "";
	$newPassword = "";
	$confirmPassword = "";
	$oldPassword = "";
	$passwordOption = "No";
	$salt = "";
	$previousPassword = "";
	foreach($rows as $row){
	  $phoneNumber = $row['phone'];
	  $alertMethod = $row['alertMethod'];
	  $carrier = $row['carrier'];
	  $salt = $row['salt'];
	  $previousPassword = $row['password'];
	}
	
	if(!empty($_POST)){
		if(!empty($_POST['passwordOption'])) {
			if ($_POST['passwordOption'] == "Yes") {
				//check if confirmPassword field is empty
				if(empty($_POST['confirmPassword'])) {
					echo "<script type='text/javascript'>alert('Confirm password field is empty');</script>";
					$checkSave = false;
				}
				
				//check if password field is empty
				if(empty($_POST['newPassword'])) {
					echo "<script type='text/javascript'>alert('New password field is empty');</script>";
					$checkSave = false;
				}
				
				//check if passwords match
				if($_POST['newPassword'] != $_POST['confirmPassword']) {
					echo "<script type='text/javascript'>alert('Password and confirm password do not match');</script>";
					$checkSave = false;
				}
				//check if old password field is empty
				if(empty($_POST['oldPassword'])) {
					echo "<script type='text/javascript'>alert('Old password field is empty');</script>";
					$checkOldPassword = false;
				}
				//check if old password matches
				else {
					$check_password = hash('sha256', $_POST['oldPassword'] . $salt);
					for($round = 0; $round < 65536; $round++){
						$check_password = hash('sha256', $check_password . $salt);
					}
					if($check_password === $previousPassword){
						$checkOldPassword = true;
					}
					else {
						echo "<script type='text/javascript'>alert('Wrong old password');</script>";
						$checkOldPassword = false;
						
					}
				}
			}
		}
		
		
		/////////////////////Check to see if phoneNumber needs editing//////////////////
		if(!empty($_POST['phoneNumber']) && $phoneNumber != $_POST['phoneNumber']){
		  $query_params = array(':phone' => $_POST['phoneNumber'],':user_id' => $_SESSION['user']['id'],);
		  $query = "UPDATE users SET phone = :phone WHERE id = :user_id";
		  try{
			$stmt = $db->prepare($query);
			$result = $stmt->execute($query_params);
		  }
		  catch(PDOException $ex){
			die("Failed to run query: " . $ex->getMessage());
		  }
		  $notification = true;
		  $phoneNumber = $_POST['phoneNumber'];
		}
		/////////////////////Check to see if password needs editing//////////////////
		if(!empty($_POST['passwordOption']) ){
			if ($_POST['passwordOption'] == "Yes" && $checkOldPassword && $checkSave) {
				$passwordAux = hash('sha256', $_POST['newPassword'] . $salt);
				for($round = 0; $round < 65536; $round++) {
					$passwordAux = hash('sha256', $passwordAux . $salt);
				}
				$query_params = array(':newPassword' => $passwordAux,':user_id' => $_SESSION['user']['id']);
				$query = "UPDATE users SET password = :newPassword WHERE id = :user_id";
				try{
					$stmt = $db->prepare($query);
					$result = $stmt->execute($query_params);
				}
				catch(PDOException $ex){
					die("Failed to run query: " . $ex->getMessage());
				}
				$notification = true;
			}	
		}	
		////////////////////Check to see if carrier needs editing///////////////////////
		if(!empty($_POST['carrier'] ) && $carrier != $_POST['carrier']){
		  $query_params = array(':carrier' => $_POST['carrier'], ':user_id' => $_SESSION['user']['id'],);
		  $query = "UPDATE users SET carrier = :carrier WHERE id = :user_id";
		  try{
			$stmt = $db->prepare($query);
			$result = $stmt->execute($query_params);
		  }
		  catch(PDOException $ex){
			die("Failed to run query: " . $ex->getMessage());
		  }
		  $notification = true;
		  $carrier = $_POST['carrier'];
		}
		////////////////////Check to see if alertMethod needs editing///////////////////
		if(!empty($_POST['alertMethod']) && $alertMethod != $_POST['alertMethod']){
		  $query_params = array(':alertMethod' => $_POST['alertMethod'], ':user_id' => $_SESSION['user']['id']);
		  $query = "UPDATE users SET alertMethod = :alertMethod WHERE id = :user_id";
		  try{
			$stmt = $db->prepare($query);
			$result = $stmt->execute($query_params);
		  }
		  catch(PDOException $ex){
			die("Failed to run query: " . $ex->getMessage());
		  }
		  $notification = true;
		  $alertMethod = $_POST['alertMethod'];
		}
	
		if(!$checkSave) $checkSave = true;
		if(!$checkOldPassword) $checkOldPassword = true;
		if ($notification) {
			echo "<script type='text/javascript'>alert('Changes Saved');</script>";
			$notification = false;
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
        <label for="inputEmail" class="sr-only">Email address</label>
        <hr>
        <p>Telephone Number & Carrier</p>
        <div class="telNumber">
          <!--///////////////Echo current phoneNumber////////////////////////-->
          <input name = "phoneNumber" id="phoneNumber" type="tel" pattern="^\d{10}$" class="form-control" placeholder="Phone Number" value = <?php echo $phoneNumber; ?> required>
          <select name = "carrier">
            <!--///////////////Set current default setting///////////////////-->
            <option value="@txt.att.net" <?php if ($carrier == "@txt.att.net"){echo "selected";}?>>AT&T </option>
            <option value="@mymetropcs.com" <?php if ($carrier == "@mymetropcs.com"){echo "selected";}?>>Metro PCS</option>
            <option value="@messaging.sprintpcs.com" <?php if ($carrier == "@messaging.sprintpcs.com"){echo "selected";}?>>Sprint</option>
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
          <input type="radio" name="alertMethod" value="None" <?php if ($alertMethod == "None"){echo "checked";}?>> None
        </div>

        <hr>
        <p>Change Password</p>
        <div class="choiceSelect">
          <!--///////////////////Set current default setting/////////////////-->
          <input type="radio" name="passwordOption" value="Yes"<?php if ($passwordOption == "Yes"){ echo "checked";}?>> Yes
          <input type="radio" name="passwordOption" value="No" checked="checked"<?php if ($passwordOption == "No"){echo "checked";}?>> No
        </div>

        <hr>
        
        <p>Old Password</p>
        <div class="telNumber">
          <!--///////////////Echo current phoneNumber////////////////////////-->
          <input name = "oldPassword" type ="password" id="oldPassword" class="form-control" placeholder="Old Password">
        </div>

        <hr>
        <p>Insert New Password</p>
        <div class="telNumber">
          <!--///////////////Echo current phoneNumber////////////////////////-->
          <input name = "newPassword" type ="password" id="newPassword" class="form-control" placeholder="New Password">
        </div>

        <hr>

        <p>Confirm New Password</p>
        <div class="telNumber">
          <!--///////////////Echo current phoneNumber////////////////////////-->
          <input name = "confirmPassword" type ="password" id="confirmPassword" class="form-control" placeholder="Confirm Password">

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
