<?php

// @author: C.A.D. BONDJE DOUE
// @filename: NotifyConnexionMailDocument.php
// @date: 20220506 09:14:15
// @desc: 

namespace IGK\System\Html\Mail;

use IGK\System\Configuration\Controllers\ConfigureController;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Net\MailDocument;

class NotifyConnexionMailDocument extends MailDocument{
    /**
     * source tag
     * @var string
     */
    protected $tagname = "div";
    /**
     * service to provide mail data
     * @var mixed
     */
    protected $m_data_service;
    public function __construct(ConfigureController $controller, $provider=null)
    {
        $this->m_controller = $controller;
        $this->m_data_service = $provider;
        parent::__construct();
    }
    protected function initialize()
    {
        $message = igk_create_node("div");
        $message->article($this->m_controller, "mail.notify.template",
            $this->getmaildata());
        $this->add($message);
    }
    protected function getmaildata(){
        if ($this->m_data_service){
            return $this->m_data_service->getMailData();
        }

        return (object)array(
            "date" => igk_mysql_datetime_now(),
            "domain" => igk_app()->getConfigs()->website_domain,
            "server_ip"=>igk_server()->REMOTE_ADDR,
            "user_agent"=>igk_server()->HTTP_USER_AGENT
        );
    }
}
