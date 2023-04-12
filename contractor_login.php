<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;
 
 
require_once 'vendor/autoload.php';
require_once 'class-db.php';
require_once 'credentials.php';

session_start();

error_reporting(0);

if (isset($_POST["signup"])) {
	  $kp_id = $_POST["signup_kp_id"];
	  $kp_emailid = $_POST["signup_kp_emailid"];
	  $pass = $_POST["signup_password"];
	  
	    $kp_id = mysqli_real_escape_string($conn, $kp_id);
		$kp_emailid = mysqli_real_escape_string($conn, $kp_emailid);
		$pass = mysqli_real_escape_string($conn, $pass);

		$check_email = mysqli_num_rows(mysqli_query($conn, "SELECT kp_emailid FROM contractor_register WHERE kp_emailid='$kp_emailid'"));

	if ($check_email > 0) {
		?>
		  <script>
			alert("<?php echo "User registration failed. " . $kp_emailid . " already exists in our database!"?>");
		  </script>
		  <?php 
	  } else if(strlen($_POST["signup_kp_id"])>=8){
    $sql = "INSERT INTO contractor_register (kp_id, kp_emailid, pass) VALUES ('$kp_id', '$kp_emailid', '$pass')";
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

  $all = mysqli_query($conn, "SELECT * FROM contractor_register WHERE kp_id='$id' AND pass='$dob'");
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
	
//Create a new PHPMailer instance
 
$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPDebug = 3;
$mail->Host = 'smtp.gmail.com';
$mail->Port = 465;
 
//Set the encryption mechanism to use:
// - SMTPS (implicit TLS on port 465) or
// - STARTTLS (explicit TLS on port 587)
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
 
$mail->SMTPAuth = true;
$mail->AuthType = 'XOAUTH2';
 
$email = 'no.reply.kplc@gmail.com'; // the email used to register google app
$clientId = '802180525412-8grl9ddeopdod6gufdfp5lb6klfoshrs.apps.googleusercontent.com';
$clientSecret = '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';
 
$db = new DB();
$refreshToken = $db->get_refersh_token();

 
//Create a new OAuth2 provider instance
$provider = new Google(
    [
        'clientId' => $clientId,
        'clientSecret' => $clientSecret,
    ]
);
 
//Pass the OAuth provider instance to PHPMailer
$mail->setOAuth(
    new OAuth(
        [
            'provider' => $provider,
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'refreshToken' => $refreshToken,
            'userName' => $email,
        ]
    )
);
//Recipients
$mail->setFrom(EMAILID, 'EnergiBot');
$mail->addAddress($kpmail,$id); 
 
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
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);


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
        window.location.replace('con_otp.php');
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
  <link rel="stylesheet" href="Contractor_login.css" />
  <title>Contractor Log in & Register Form</title>
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
            <input type="text" placeholder="Contractor ID" name="kp_id" pattern="^kpl\d{5}$" minlength="8" title="Please enter correct Contractor ID in small letters" value="<?php echo $_POST['kp_id']; ?>" required />
          </div>
          <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" onfocus="(this.type='date')" placeholder="Date of Birth" name="kp_dob" min="1900-01-01" max="2004-12-31" title="Please enter a valid Date Of Birth only" value="<?php echo $_POST['kp_dob']; ?>" required />
          </div>
          <input type="submit" name="send" value="Send OTP" class="btn solid" />
        </form>
        <form action="" class="sign-up-form" method="post" id="registration-form">
          <center> <img src="https://i.postimg.cc/pTB08FnL/Chatbotlogo.png" height=200 width=200></center>
          <h2 class="title">Register</h2>
          <div class="input-field">
            <i class="fas fa-user"></i>
            <input type="text" placeholder="Contractor ID" name="signup_kp_id" pattern="^kpl\d{5}$" minlength="8" title="Please enter correct Contractor ID in small letters" value="<?php echo $_POST["signup_kp_id"]; ?>" required />
          </div>
          <div class="input-field">
            <i class="fas fa-envelope"></i>
            <input type="email" placeholder="Kenya Power Email" name="signup_kp_emailid" pattern="^[A-Za-z]{2}[A-Za-z]*(?:[A-Za-z]{2,})?@(?:kplc\.co\.ke)$" title="Please enter a valid Kenya Power email id only" size="40" value="<?php echo $_POST["signup_kp_emailid"]; ?>" required />
          </div>
          <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" onfocus="(this.type='date')" placeholder="Date of Birth" name="signup_password" min="1900-01-01" max="2004-12-31" title="Please enter a valid Date Of Birth only" value="<?php echo $_POST["signup_password"]; ?>" required />
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

  <script src="js_contractor_file.js"></script>
</body>

</html>