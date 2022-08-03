<?php
// @author: C.A.D. BONDJE DOUE
// @filename: email.php
// @date: 20220803 13:48:58
// @desc: 


use IGK\System\Console\Logger;
use IGK\System\Net\Mail;

$mail = new Mail();
$mail->addTo($to);
$mail->From = "cbondje@igkdev.be";
$mail->HtmlMsg= $message;
$mail->Title= $subject;

if ($mail->sendMail()){
    //Logger::success("send success: ".$to);
    return true;
}
// Logger::info("email not send to:".$to);
return false;