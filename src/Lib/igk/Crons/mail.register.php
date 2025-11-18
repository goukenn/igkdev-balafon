#!/usr/bin/env php
<?php

// @author: C.A.D. BONDJE DOUE
// @filename: mail.register.php
// @date: 20220801 13:36:12
// @desc: cron scripts 

use IGK\Models\Mailinglists; 
use IGK\System\Console\Logger;
use IGK\System\Cron\CronExecutionStatus;
use IGK\System\Net\Mail;


$ctrl = igk_get_defaultwebpagectrl();
 
 
$uri = "https://local.com:7300/registerService/activate-mail/?q=".base64_encode(http_build_query([
    "email"=>$to, 
]));

 $options = (object)[
  'email'=>$to
];

// $r = [];
// if (is_null($options->email)){
//     $r= Mailinglists::select_all(
//         [Mailinglists::FD_CLML_STATE=>0]
//     );
// }else{
//     $r=Mailinglists::select_all(
//         [Mailinglists::FD_CLML_EMAIL=>$options->email]
//     );
// }
$cnf = igk_configs();
$from = $cnf->get("mail_contact", "info@".$cnf->get("website_domain"));
$langs = []; 

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
// foreach($r as $i){
//     Logger::print('send : '.$i->clEmail);
//     $_mail = new Mail();
//     $locale = $i->clml_locale ?? "en";

//     $msg = igk_getv($options, "msg-".$locale, igk_getv($options, "msg"));
//     $_mail->setFrom($from);
//     $_mail->addTo($i->clEmail);
//     $_mail->setTitle($options->title);   
//     $_mail->setHtmlMsg($msg);
//     if (!$_mail->sendMail()){
//         igk_ilog("failed to send mail to ".$_mail);
//         igk_ilog($_mail->getErrorMsg());
//         return false;
//     }
// }

// return true;