<?php

// @author: C.A.D. BONDJE DOUE
// @filename: MailDocumentTest.php
// @date: 20220506 09:04:05
// @desc: 

namespace IGK\Tests;

use IGK\System\Configuration\Controllers\ConfigureController;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Mail\NotifyConnexionMailDocument;
use IGK\System\Mail\IServiceMailData;

class MailDocumentTest extends BaseTestCase
{
    public function test_notyconnexion_mail()
    {
        $mail = new NotifyConnexionMailDocument(ConfigureController::ctrl(), new MockConfigureData());
        $s = $mail->render();
        $this->assertEquals(
            implode(
                "",
                [
                    '<div><div><div style="padding:0px;margin:0px;position:relative; padding:2em 0em; "> Connexion to Balafon Configuration interface </div> <div> ',
                    '<ul> <li>Time : 1983-08-04 07:00:00</li> ',
                    '<li>domain : test.local.host</li> <li>ip: 127.0.0.1</li> <li>agent : testing </li> </ul> </div> ',
                    '<div> Cet email est destiné vous sensibiliser à la sécurité. ' .
                        '</div></div></div>'
                ]
            ),
            $s,
            "mail document failed"
        );
    }
}

class MockConfigureData implements IServiceMailData
{
    public function getMailData(): array
    {
        return [ 
            "server_ip" => "127.0.0.1",
            "date" => "1983-08-04 07:00:00",
            "domain" => "test.local.host",
            "user_agent" => "testing" 
        ];
    }
}
