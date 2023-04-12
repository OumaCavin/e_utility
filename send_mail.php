<?php

require 'credentials.php';

  session_start();
   error_reporting(0);



// create a new PHPMailer instance
	$mail = new PHPMailer(true);
	
	if (isset($_POST["signup"])) {
	  $kp_id = mysqli_real_escape_string($conn, $_POST["signup_kp_id"]);
	  $kp_emailid = mysqli_real_escape_string($conn, $_POST["signup_kp_emailid"]);
	  $pass = mysqli_real_escape_string($conn, ($_POST["signup_password"]));

	  $check_email = mysqli_num_rows(mysqli_query($conn, "SELECT kp_emailid FROM staff_register WHERE kp_emailid='$kp_emailid'"));

	if ($check_email > 0) {
		?>
		  <script>
			alert("<?php echo "User registration failed. " . $kp_emailid . " already exists in our database!"?>");
		  </script>
		  <?php 
	  } else if(strlen($_POST["signup_kp_id"])==8){
		$sql = "INSERT INTO staff_register (kp_id, kp_emailid, pass) VALUES ('$kp_id', '$kp_emailid', '$pass')";
		$result = mysqli_query($conn, $sql);
		if ($result) {
		  $_POST["signup_kp_id"] = "";
		  $_POST["signup_kp_emailid"] = "";
		  $_POST["signup_password"] = "";
		  echo "<script>alert('User registration successful.');</script>";
		}else {
		  ?>
		  <script>
			alert("<?php echo "User registration failed. " . $kp_id . " already exists in our database!"?>");
		  </script>
		  <?php 
	  }
	}else {
		?>
		<script>
			alert("<?php echo "User registration failed. " . $kp_id . " is incorrect"?>");
		</script>
		<?php
	  }
	}

	if (isset($_POST["send"])) {
		
	  $id = mysqli_real_escape_string($conn, $_POST["kp_id"]);
	  $dob = mysqli_real_escape_string($conn, ($_POST["kp_dob"]));

	  $all = mysqli_query($conn, "SELECT * FROM staff_register WHERE kp_id='$id' AND pass='$dob'");
	  $count = mysqli_num_rows($all);
	  
	if($count > 0){
    $fetch = mysqli_fetch_assoc($all);
    $kpmail = $fetch["kp_emailid"];
    $_POST["kp_id"] = "";
    $_POST["kp_dob"] = "";
    echo "<script>alert('Verify your email to login!');</script>";
    
	$otp = rand(100000,999999);
    $_SESSION['otp'] = $otp;
	$mailMsg = $_SESSION['otp'];
    $_SESSION['mail'] = $kpmail;
	$mailto = $_SESSION['mail'];

    unset($_SESSION['mail']);
    unset($_SESSION['otp']);

   //$mailto = $_POST['mail_to'];
   $mailSub = 'OTP Verification Code';
   //$mailMsg = $_POST['mail_msg'];

   require 'PHPMailer-master/PHPMailerAutoload.php';
   $mail = new PHPMailer();
   $mail ->IsSmtp();//in panel comment this
   $mail ->SMTPDebug = 0;
   $mail ->SMTPAuth = true;
   $mail ->SMTPSecure = 'tls';
   $mail ->Host = "smtp.gmail.com";
   $mail ->Port = 587; // or 587
   $mail ->IsHTML(true);
   $mail ->Username = "no.reply.kplc@gmail.com";
   $mail ->Password = "cfscvlulhgfdtqjo";
   $mail ->SetFrom("no.reply.kplc@gmail.com");
   $mail ->Subject = $mailSub;
   $mail ->Body = $mailMsg;
   $mail ->AddAddress($mailto);
	}
   if(!$mail->Send())
   {
       echo "Mail Not Sent";
   }
   else
   {
       echo "Mail Sent";
       header('location:verifyaccount.php');
   }

	}



   

