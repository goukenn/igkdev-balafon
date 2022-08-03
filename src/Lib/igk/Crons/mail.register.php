#!/usr/bin/env php
<?php

// @author: C.A.D. BONDJE DOUE
// @filename: mail.register.php
// @date: 20220801 13:36:12
// @desc: cron scripts 

use IGK\Models\Mailinglists;
use IGK\Resources\R;
use IGK\System\Console\Logger;
use IGK\System\Net\Mail;


$ctrl = igk_get_defaultwebpagectrl();
 
if ($ctrl){
   
}
$uri = "https://local.com:7300/registerService/activate-mail/?q=".base64_encode(http_build_query([
    "email"=>"bondje.doue@gmail.com", 
]));

// $options = (object)[
//     "title"=>"registration",
//     "msg"=>"welcome to , local.com<br /><p>please <a href=\"".$uri."\">click here to actived </a> your new letter</p>",
//     "msg-fr"=>"",
//     "msg-nl"=>"",
//     "email"=>"bondje.doue@igkdev.com",
//     "activate_uri"
// ];

// igk_wln_e($options);

$r = [];
if (is_null($options->email)){
    $r= Mailinglists::select_all(
        ["clState"=>0]
    );
}else{
    $r=Mailinglists::select_all(
        ["clEmail"=>$options->email]
    );
}
$from = igk_configs()->get("mail_contact", "info@".igk_configs()->get("website_domain"));
$langs = []; 

foreach($r as $i){
    Logger::print('send : '.$i->clEmail);
    $_mail = new Mail();
    $locale = $i->clml_locale ?? "en";

    $msg = igk_getv($options, "msg-".$locale, igk_getv($options, "msg"));
    $_mail->setFrom($from);
    $_mail->addTo($i->clEmail);
    $_mail->setTitle($options->title);   
    $_mail->setHtmlMsg($msg);
    if (!$_mail->sendMail()){
        igk_ilog("failed to send mail to ".$_mail);
        igk_ilog($_mail->getErrorMsg());
        return false;
    }
}

return true;