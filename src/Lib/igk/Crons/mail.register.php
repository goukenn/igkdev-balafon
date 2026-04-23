#!/usr/bin/env php
<?php
// @author: C.A.D. BONDJE DOUE
// @filename: mail.register.php
// @date: 20220801 13:36:12
// @desc: cron scripts 
use IGK\System\Cron\CronExecutionStatus;
use IGK\System\Net\Mail;

$options = (object)[
  'email'=>$to
];
$cnf = igk_configs();
$from = $cnf->get("mail_contact", "info@".$cnf->get("website_domain"));
$langs = []; 
igk_ilog('try send mail');
$_mail = new Mail();
if ($cci){
    $_mail->addToCC($cci);
}
if ($cc){
    $_mail->addToGCC($cc);
}
$_mail->setTitle($title);
$_mail->setHtmlMsg($msg);
$_mail->setFrom($from);
$_mail->addTo($to);
if ($_mail->sendMail()){
    return CronExecutionStatus::STOP;
}
$this->status = -1;
return CronExecutionStatus::RESTART; 