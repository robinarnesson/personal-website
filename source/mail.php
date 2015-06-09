<?php

require_once '../comps/phpmailer/class.phpmailer.php';
require_once '../comps/phpmailer/class.smtp.php';

date_default_timezone_set('Etc/UTC');

class mail {
  public static function send($_subject, $_message, $_receivers) {
    $mail = new PHPMailer;

    $mail->isSMTP();

    $mail->SMTPDebug = 0; // 2=debug
    $mail->Debugoutput = 'html';

    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = constants::MAIL_HOST;
    $mail->Port = constants::MAIL_PORT;
    $mail->Username = constants::MAIL_USER;
    $mail->Password = constants::MAIL_PASS;

    $mail->setFrom(constants::MAIL_USER, constants::MAIL_FROM);
    $mail->Subject = $_subject;
    $mail->msgHTML($_message);
    $mail->AltBody = $_message;
    foreach ($_receivers as $addr)
      $mail->addAddress($addr);

    $mail->send();
  }
}
?>
