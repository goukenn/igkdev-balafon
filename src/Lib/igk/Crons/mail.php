<?php
// @file: 
// @summary: send mail registration 
// @desc: script to send mail with cron setting
// @params : title, msg, fromTitle, 

use IGK\System\Cron\CronExecutionStatus;
use IGK\System\Net\Mail;

$cnf = igk_configs();
$from = $cnf->get("mail_contact", "info@".$cnf->get("website_domain"));

$_mail = new Mail();
$_mail->setTitle($title);
$_mail->setHtmlMsg($msg);
$_mail->setFrom($from);
$_mail->addTo($to); 
if ($_mail->sendMail()){
    return CronExecutionStatus::STOP;
}
$this->status = -1;
return CronExecutionStatus::RESTART;
