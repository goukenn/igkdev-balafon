<?php
// @author: C.A.D. BONDJE DOUE
// @filename: email.php
// @date: 20220803 13:48:58
// @desc: 
use IGK\System\Console\Logger;
use IGK\System\Net\Mail;

$mail = new Mail();
$mail->addTo($to);
$mail->From = igk_configs()->mail_admin; 
$mail->HtmlMsg= $message;
$mail->Title= $subject;
if ($mail->sendMail()){
    return true;
}
return false;