<?php



use PHPMailer\PHPMailer\PHPMailer;
require_once('../connection.php');


$name = "Shopster";  // Name of your website or yours
$to = htmlspecialchars($_POST['email']);  // mail of reciever

$connection = DBConnection::get_instance()->get_connection();
$sql = "SELECT * FROM user_info WHERE email = '". $to . "'";

$result = mysqli_query($connection, $sql);

if ($result != false) {
    if ($result->num_rows < 1) {
        header("Location:../forgotPassword.php?msg=nofound");
        die();
    } 
} 
else{
    header("Location:../forgotPassword.php?msg=dbfail");
    die();
}



$subject = "From Shopster";
$body = "Shopster password recovery link: http://localhost/test/Shopster-main/src/resetPassword.php?email=$to";
$from = "csci4300grouphub@gmail.com";  // you mail
$password = "shopster1";  // your mail password

// Ignore from here

require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";
require_once "PHPMailer/Exception.php";
$mail = new PHPMailer();

// To Here

//SMTP Settings
$mail->isSMTP();
// $mail->SMTPDebug = 3;  Keep It commented this is used for debugging                          
$mail->Host = "smtp.gmail.com"; // smtp address of your email
$mail->SMTPAuth = true;
$mail->Username = $from;
$mail->Password = $password;
$mail->Port = 587;  // port
$mail->SMTPSecure = "tls";  // tls or ssl
$mail->smtpConnect([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
]);

//Email Settings
$mail->isHTML(true);
$mail->setFrom($from, $name);
$mail->addAddress($to); // enter email address whom you want to send
$mail->Subject = ("$subject");
$mail->Body = $body;
if ($mail->send()) {
    header("Location:../forgotPassword.php?msg=success");
} else {
    header("Location:../forgotPassword.php?msg=fail");
}
