<?php
require('../src/inc/functions.php');
include('../src/inc/pdo.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
$errors = array();
$success = false;
$useremail = trim(strip_tags($_POST['email']));
$sql = SQL_SELECT('users',false,'WHERE email =',$useremail);
if (empty($useremail)) {
   $errors['email'] = 'Veuillez renseigner ce champ';
} else {
   if (empty($sql)) {
   $errors['email'] = 'Cette adresse ne correspond à aucun compte.';
   }
   if ($sql['status_passwrd'] == TRUE) {
      $errors['email'] = 'Un email vous a déja eté envoyé';
   }
}
if(count($errors) == 0 ) {
   $success = true;
   $token = $sql['token'];
   require '../vendor/autoload.php';
   $mail = new PHPMailer();
   $mail->IsSMTP();
   $mail->SMTPDebug = 0;
   $mail->SMTPAuth = true;
   $mail->SMTPSecure = 'ssl';
   $mail->Host = "smtp.gmail.com";
   $mail->Port = 465;
   $mail->IsHTML(true);
   // ID GOOGLE ACCOUNT
   $mail->Username = "noreply.wirescan";
   $mail->Password = "Nfactory76000@";
   $mail->SetFrom("noreply.wirescan@gmail.com");
   ////////////////////
   $mail->Subject = "Reinitialisation de votre mot de passe";
   $mail->Body = "Bonjour " . ucfirst($sql['prenom']) . ", veuillez cliquer sur le lien suivant afin de modifier votre mot de passe :<br> http://localhost/projet/2-reseaux/site/passreset.php?token=".$token;
   $mail->AddAddress($useremail);
   if(!$mail->Send()) {
      $mailerror = $mail->ErrorInfo;
       echo "Mailer Error: " . $mail->ErrorInfo;
   } else {
      // EDIT SQL STATUS_PASSWRD APRES MODIF MDP
      $userid = $sql['id'];
      $updatevals = array(
          'status_passwrd' => '1'
      );
      SQL_UPDATE('users',$updatevals,$param = 'WHERE id = ',$userid);
   }
}
$data = array(
   'errors' => $errors,
   'success' => $success,
);
showJson($data);
