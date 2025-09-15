<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>PHPMailer - SMTP test</title>
</head>
<body>
<?php

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
#date_default_timezone_set('Etc/UTC');

require '../class.phpmailer.php';

$hostname = "localhost";
$username = "root";
$password = "OpenSpace";
$database = "elecrama";

$connection=mysqli_connect($hostname,$username,$password);
if (mysqli_connect_errno()){
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
mysqli_select_db($connection, $database);


$query = "SELECT id, company_name, gender, contact_person, email FROM user_details WHERE email_send=0 LIMIT 2";
$res = mysqli_query($connection, $query);
while($row = mysqli_fetch_array($res)){
	$password = rand(1111111111, 9999999999);
	
	$query = "UPDATE user_details SET password=".$password.", email_send=1 WHERE id=".$row['id']."";
	mysqli_query($connection, $query);
	
	//Create a new PHPMailer instance
	$mail = new PHPMailer();
	//Tell PHPMailer to use SMTP
	$mail->IsSMTP();
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug  = 2;
	//Ask for HTML-friendly debug output
	$mail->Debugoutput = 'html';
	//Set the hostname of the mail server
	$mail->Host       = "ssl://smtp.rediffmailpro.com";
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port       = 465;
	//Whether to use SMTP authentication
	$mail->SMTPAuth   = true;
	//Username to use for SMTP authentication
	$mail->Username   = "elecrama@ieema.org";
	//Password to use for SMTP authentication
	$mail->Password   = "ele@12345";
	//Set who the message is to be sent from
	$mail->SetFrom('elecrama@ieema.org', 'ELECRAMA 2018');
	//Set an alternative reply-to address
	#$mail->AddReplyTo('elecrama@ieema.org','ELECRAMA 2018');
	//Set who the message is to be sent to
	$mail->AddAddress($row['email'], $row['contact_person']);
	$mail->AddBCC("elecrama18@gmail.com", 'ELECRAMA 2018');
	//Set the subject line
	$mail->Subject = 'Login ID & Password';
	//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
	$emailUrl = "https://elecrama.preconfirm.in/PHPMailer/examples/Preregisterform_email.php?gender=".urlencode($row['gender'])."&contact_person=".urlencode($row['contact_person'])."&email=".urlencode($row['email'])."&password=".urlencode($password)."&company_name=".urlencode($row['company_name'])."";
	
	$contentHtml = file_get_contents($emailUrl);
	
	$mail->MsgHTML($contentHtml, dirname(__FILE__));
	//Replace the plain text body with one created manually
	#$mail->AltBody = 'This is a plain-text message body';

	//Send the message, check for errors
	if(!$mail->Send()) {
	  echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
	  echo "Message sent!";
	}
}


?>
</body>
</html>
