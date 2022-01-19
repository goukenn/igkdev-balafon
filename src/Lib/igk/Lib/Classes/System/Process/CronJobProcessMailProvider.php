<?php

namespace IGK\System\Process;

use IGK\Controllers\BaseController;
use IGK\Models\Mails;
use IGK\System\Net\Mail;
use IGKObjStorage;

class CronJobProcessMailProvider extends CronJobProcessProviderBase
{
    public function treat($options)
    {
        return igk_get_robjs('to|subject|message', 0, $options);
    }
    public function exec($name, $options, ?BaseController $ctrl = null)
    {

        if ($mails = Mails::select_all(["mail_status" => 0])) {
            $base_from = igk_sys_configs()->mail_contact;
            foreach ($mails as $mail) {
                $mailinfo = new IGKObjStorage((array)json_decode($mail->mail_data));
                if (empty($mail->mail_from)) {
                    $mail->mail_from = $base_from;
                }
                $m = new Mail();
                $m->From = $mail->mail_from;
                $m->addTo($mailinfo->to);
                ($cc = $mailinfo->cc) && $m->addToCC($cc);
                ($cci = $mailinfo->cci) && $m->addToGCC($cci);

                $m->Title = $mailinfo->object;
                $m->HtmlMsg = $mailinfo->message;
                if ($m->sendMail()) {
                    $mail->mail_status = 1;
                } else {
                    $mail->main_try++;
                }
                $mail->update();
            }
        }
        return true;
    }
}
