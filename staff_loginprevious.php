<?php
require_once 'credentials.php';


require_once 'PHPMailer/vendor/autoload.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/OAuth.php';


session_start();

error_reporting(0);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailOAuth;
use PHPMailer\PHPMailer\OAuth;

	
	if (isset($_POST["signup"])) {
	  $kp_id = $_POST["signup_kp_id"];
	  $kp_emailid = $_POST["signup_kp_emailid"];
	  $pass = $_POST["signup_password"];
	  
	    $kp_id = mysqli_real_escape_string($conn, $kp_id);
		$kp_emailid = mysqli_real_escape_string($conn, $kp_emailid);
		$pass = mysqli_real_escape_string($conn, $pass);

	  $check_email = mysqli_num_rows(mysqli_query($conn, "SELECT kp_emailid FROM staff_register WHERE kp_emailid='$kp_emailid'"));
	  

	if ($check_email > 0) {
		?>
		  <script>
			alert("<?php echo "User registration failed. " . $kp_emailid . " already exists in our database!"?>");
		  </script>
		  <?php 
	  } else if(strlen($_POST["signup_kp_id"])>=8){
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
    $_SESSION['mail'] = $kpmail;
	
	//Create a new instance of the PHPMailerOAuth class and set the OAuth credentials
//	$mail = new PHPMailerOAuth();
	//Create a new PHPMailer instance
$mail = new  PHPMailer\PHPMailer\PHPMailer(true);
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 2;
//Set the hostname of the mail server
$mail->Host = 'smtp.gmail.com';
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 587;
//Set the encryption system to use - ssl (deprecated) or tls
$mail->SMTPSecure = 'tls';
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Set AuthType to use XOAUTH2
$mail->AuthType = 'XOAUTH2';
//Fill in authentication details here
//Either the gmail account owner, or the user that gave consent
$email = 'no.reply.kplc@gmail.com';

$clientId = '802180525412-8grl9ddeopdod6gufdfp5lb6klfoshrs.apps.googleusercontent.com';

$clientSecret = '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';
//Obtained by configuring and running get_oauth_token.php
//after setting up an app in Google Developer Console.
$mail->oauthRedirectUri = 'http://localhost/e_utility/vendor/phpmailer/phpmailer/src/oauth.php';
/*

https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline&client_id=802180525412-8grl9ddeopdod6gufdfp5lb6klfoshrs.apps.googleusercontent.com&redirect_uri=http://localhost/oauth.php&scope=https://mail.google.com/
*/
//$refreshToken = '1/nPnTTF3worGWi7EU8wt7URIK4YacAK_Rxuxw5PqxGyU';
//Create a new OAuth2 provider instance
$provider = new Google([
    'clientId' => $clientId,
    'clientSecret' => $clientSecret
]);
//Pass the OAuth provider instance to PHPMailer
$mail->setOAuth(
    new OAuth([
        'provider' => $provider,
        'clientId' => $clientId,
        'clientSecret' => $clientSecret,
        'refreshToken' => $refreshToken,
        'userName' => $email
    ])
);

/*// create a new PHPMailer instance
	$mail = new PHPMailer();
	
// Set the SMTPDebug property to 2
     $mail->SMTPDebug = 2;
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER; 
    $mail->IsSMTP();
	$mail->SMTPAuth = true;
	//$mail->Mailer = "smtp";
	//$mail ->SMTPDebug = 0;
    $mail->Host="smtp.gmail.com";
   

    $mail->Username=EMAILID;
    $mail->Password=PASSWORD;
	$mail->SMTPSecure='tls';
	//$mail->SMTPSecure='ssl';
	//$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port= 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
	$mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => true,
			'verify_peer_name' => true,
			'allow_self_signed' => false,
			'cafile' => '/path/to/roots.crt'
		)
    
 */   //Recipients
	$mail->setFrom(EMAILID, 'EnergiBot');
    $mail->addAddress($kpmail,$id);
	//$mail->addAddress($kpmail);
   
	// Content
	$mail->isHTML(true);   // Set email format to HTML
    $mail->Subject="Your OTP verification code to login to EnergiBot";
    $content="<p>Dear Staff, </p><h3>Your OTP verification code is: $otp <br></h3>
    <br/>
    <p>Only after verification you will be able to access our chatbot - EnergiBot to ask your queries.</p>
    <br/><br/>
    <p style='color: red'>This is a system generated mail. So, please do not reply to this mail!</p>
    <p>With regards,</p>
    <p><b>EnergiBot</b></p>
    ";
	$mail->MsgHTML($content);
	/*
	$mail->Body="<p>Dear Staff, </p><h3>Your OTP verification code is: $otp <br></h3>
    <br/>
    <p>Only after verification you will be able to access our chatbot - EnergiBot to ask your queries.</p>
    <br/><br/>
    <p style='color: red'>This is a system generated mail. So, please do not reply to this mail!</p>
    <p>With regards,</p>
    <p><b>EnergiBot</b></p>
    ";*/
    //$mail->send();
    if(!$mail->send()){
		echo "Error while sending Email.";
		var_dump($mail);
      ?>
      <script>
        alert("<?php echo "Login details are incorrect. Please try again!"?>");
      </script>
      <?php
      }else{
      ?>
      <script>
        alert("<?php echo "OTP sent to " . $kpmail ?>");
        window.location.replace('sta_otp.php');
      </script>
      <?php
    }            
  }else{
	  echo "Email sent successfully";
    ?>
      <script>
        alert("<?php echo "Either you are not registered or login details are incorrect!"?>");
      </script>
    <?php
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="Staff_login.css" />
  <title>Staff Log in & Register Form</title>
</head>

<body>
  <div class="top-bar">
    <div class="logo" style="margin-left:11px;">
      <img src="https://i.postimg.cc/Jz8X1TrB/logo.jpg" height=50 width=50>
    </div>
    <div class="top-left"><a href="KP_Homepage.html" style="text-decoration: none; color:white">Kenya Power</a></div>
    <div class="top-right">
    </div>
  </div>
  <div class="container">
    <div class="forms-container">
      <div class="signin-signup">
        <form action="" class="sign-in-form" method="post">
          <center> <img src="https://i.postimg.cc/pTB08FnL/Chatbotlogo.png" height=200 width=200></center>
          <h2 class="title">Log In</h2>
          <div class="input-field">
            <i class="fas fa-user"></i>
            <input type="text" placeholder="Staff ID" name="kp_id" pattern="^kpl\d{5}$" minlength="8" title="Please enter correct Staff ID in small letters" value="<?php echo $_POST['kp_id']; ?>" required />
          </div>
          <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" onfocus="(this.type='date')" placeholder="Date of Birth" name="kp_dob" min="1958-01-01" max="2012-12-31" title="Please enter a valid Date Of Birth only" value="<?php echo $_POST['kp_dob']; ?>" required />
          </div>
          <input type="submit" name="send" value="Send OTP" class="btn solid" />
        </form>
        <form action="" class="sign-up-form" method="post">
          <center> <img src="https://i.postimg.cc/pTB08FnL/Chatbotlogo.png" height=200 width=200></center>
          <h2 class="title">Register</h2>
          <div class="input-field">
            <i class="fas fa-user"></i>
            <input type="text" placeholder="Staff ID" name="signup_kp_id" pattern="^kpl[0-9]{5}$" minlength="8" title="Please enter correct Staff ID in small letters"  value="<?php echo $_POST["signup_kp_id"]; ?>" required />
          </div>
          <div class="input-field">
            <i class="fas fa-envelope"></i>
            <input type="email" placeholder="Kenya Power Email" name="signup_kp_emailid" pattern="^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$" title="Please enter a valid Kenya Power email id only" size="50" value="<?php echo $_POST["signup_kp_emailid"]; ?>" required />
          </div>
          <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" onfocus="(this.type='date')" placeholder="Date of Birth" name="signup_password" min="1958-01-01" max="2012-12-31" title="Please enter a valid Date Of Birth only" value="<?php echo $_POST["signup_password"]; ?>" required />
          </div>
          <input type="submit" name="signup" class="btn" value="Register" />
        </form>
      </div>
    </div>

    <div class="panels-container">
      <div class="panel left-panel">
        <div class="content">
          <h3>New at EnergiBot?</h3>
          <p>Just register with your Kenya Power ID and then login to get all your queries related to Kenya Power cleared with EnergiBot!!
          </p>
          <button class="btn transparent" id="sign-up-btn">
            Register
          </button>
        </div>
      </div>
      <div class="panel right-panel">
        <div class="content">
          <h3>Already Registered with EnergiBot?</h3>
          <p>
            Just Login with the Kenya Power ID (& other credentials) and get all your queries related to Kenya Power cleared with EnergiBot!!
          </p>
          <button class="btn transparent" id="sign-in-btn">
            Log In
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="js_staff_file.js"></script>
</body>

</html>