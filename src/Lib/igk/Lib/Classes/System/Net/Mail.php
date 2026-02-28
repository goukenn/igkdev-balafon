<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Mail.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Net;
use Exception;
use Error;
use IGK\Helper\IO;
use IGK\System\EntryClassResolution;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\HtmlRenderer;
use IGKException;
use IGKObject;
use IGK\IMailAttachmentContainer;
/**
 * Represent a mail
 */
class Mail extends IGKObject implements IMailAttachmentContainer
{

    /**
    * Constant: content html text.
    * @var mixed
    */
    const CONTENT_HTML_TEXT = "text/html";

    /**
    * Constant: content img png.
    * @var mixed
    */
    const CONTENT_IMG_PNG = "image/png";

    /**
    * Constant: content plain text.
    * @var mixed
    */
    const CONTENT_PLAIN_TEXT = IGK_CT_PLAIN_TEXT;

    /**
    * Constant: part alternative.
    * @var mixed
    */
    const PART_ALTERNATIVE = "multipart/alternative";

    /**
    * Constant: part mixed.
    * @var mixed
    */
    const PART_MIXED = "multipart/mixed";

    /**
    * Constant: utf8 charset.
    * @var mixed
    */
    const UTF8_CHARSET = "UTF-8";

    /**
    * Constant: base64 chunk.
    * @var mixed
    */
    const BASE64_CHUNK = 76;

    /**
    * Property: error msg.
    * @var mixed
    */
    private $ErrorMsg;

    /**
    * Property: html charset.
    * @var mixed
    */
    private $html_charset = "iso-8859-1";

    /**
    * Property: files.
    * @var mixed
    */
    private $m_files;

    /**
    * Property: from.
    * @var mixed
    */
    private $m_from;

    /**
    * Property: htmlmsg.
    * @var mixed
    */
    private $m_htmlmsg;

    /**
    * Property: pwd.
    * @var mixed
    */
    private $m_pwd;

    /**
    * Property: replyto.
    * @var mixed
    */
    private $m_replyto;

    /**
    * Property: smtp port.
    * @var mixed
    */
    private $m_smtp_port;

    /**
    * Property: smtphost.
    * @var mixed
    */
    private $m_smtphost;

    /**
    * Property: socket timeout.
    * @var mixed
    */
    private $m_socketTimeout = 15;

    /**
    * Type of socket type.
    * @var mixed
    */
    private $m_socketType = "tls";

    /**
    * Property: textmsg.
    * @var mixed
    */
    private $m_textmsg;

    /**
    * Property: title.
    * @var mixed
    */
    private $m_title;

    /**
    * Property: to.
    * @var mixed
    */
    private $m_to;

    /**
    * Property: to bcc.
    * @var mixed
    */
    private $m_toBcc;

    /**
    * Property: tocc.
    * @var mixed
    */
    private $m_tocc;

    /**
    * Property: use auth.
    * @var mixed
    */
    private $m_useAuth;

    /**
    * Property: user.
    * @var mixed
    */
    private $m_user;

    /**
    * Property: text charset.
    * @var mixed
    */
    private $text_charset = "iso-8859-1";

    /**
    * Property: base64encoding.
    * @var mixed
    */
    var $Base64Encoding = true;

    /**
    * Returns Error Msg.
    */
    public function getErrorMsg()
    {
        return $this->ErrorMsg;
    }

    /**
    * auto generate doc.
    * @param array $definition
    * @return null|string
    */

    public static function MailFromArrayToString(array $definition): ?string
    {
        list($title, $mail) = igk_extract($definition, "title|mail");
        if ($title && $mail) {
            return "{$title} <{$mail}";
        }
        return $mail;
    }
    /**
     * send mail 
     * - $from must match configs::mail_userauth
     * - $from can be formed of "string = title<mail> | mail, string[] = "title","mail"
     * @param string $to destination mail 
     * @param null|string $subject subject 
     * @param null|string $message message to send
     * @param null|string $from source mail - 
     * @param null|string $reply response to 
     * @param mixed $attachement attachement definition 
     * @param string|'text/html'|'text/plain' $type mail type 
     * @param null|string $fromTitle 
     * @param callable|null $init 
     * @return int|bool 
     * @throws IGKException 
     * @throws Exception 
     * @throws Error 
     */

    public static function Mail(
        string $to,
        ?string $subject,
        ?string $message,
        ?string $from = null,
        ?string $reply = null,
        $attachement = null,
        string $type = "text/html",
        ?string $fromTitle = null,
        ?callable $init = null
    ) {
        $mail = new static();
        if ($init) {
            $init($mail);
        }
        if ($message instanceof HtmlItemBase) {
            $opt = HtmlRenderer::CreateRenderOptions();
            $opt->Context = "mail";
            $message = $message->render($opt);
        }
        $mail->HtmlMsg = $message;
        $mail->Title = $subject;
        $mail->From = $from ?? igk_configs()->get("mail_contact");
        $mail->HtmlCharset = self::UTF8_CHARSET;
        $mail->TextCharset = self::UTF8_CHARSET;
        $mail->setReplyTo($reply); 
        if ($type != 'text/html') {
            $mail->setTextMsg($message);
        }
        $mail->addTo($to);
        if (is_array($attachement)) {
            // $cl = \IGK\System\Net\MailAttachement::class;
            $cl = EntryClassResolution::MailAttachement;
            class_exists($cl, true) || igk_die('missing class');
            //include_once(IGK_LIB_CLASSES_DIR . '/System/Net/MailAttachement.php');
            foreach ($attachement as $cid => $v) { 
                $content_type = 'text/plain';
                $name = null;
                if (is_object($v) && igk_reflection_class_extends($v, $cl)) {
                    $mail->attach($v);
                } else {
                    if (is_string($v)) {
                        $content = $v;
                    } else if (is_array($v)) {
                        list($id, $content_type, $content, $name) = igk_extract($v, MailConstants::MAIL_ATTACHEMENT_ARRAY_OPTION_KEYS);
                        $cid = $id ?? (is_numeric($cid) ? MailConstants::MAIL_CID_PREFIX . $cid : $cid);
                    } else {
                        $content = $v->Content;
                        $content_type = $v->ContentType;
                        $cid = $v->CID ?? $cid;
                    }
                    $attach = $mail->attachContent($content, $content_type, $cid);
                    if ($name){
                        $attach->Name = $name;
                    }
                }
            }
        }
        return $mail->sendMail();
    }

    /**
    * auto generate doc.
    */
    public function __construct()
    {
        $this->ErrorMsg = "";
        $this->m_files = array();
        $this->m_to = array();
        $this->m_tocc = array();
        $this->m_toBcc = array();
        $app = igk_app();
        if ($app) {
            // + | --------------------------------------------------------------------
            // + | configure system
            // + |
            $this->m_useAuth = $app->Configs->mail_useauth;
            $this->m_smtphost = $app->Configs->mail_server;
            $this->m_user = $app->Configs->mail_user;
            $this->m_pwd = $app->Configs->mail_password;
            $this->m_smtp_port = $app->Configs->mail_port;
            $this->m_socketType = $app->Configs->mail_authtype;
        }
        $this->HtmlCharset = self::UTF8_CHARSET;
        $this->TextCharset = self::UTF8_CHARSET;
    }
    /**
     * send mail with TLS by using socket
     */
    private function __sendMailTLS($headers, $message)
    {
        if (!igk_network_available()) {
            return 0;
        }
        $errno = 0; //IGK_STR_EMPTY;
        $errstr = IGK_STR_EMPTY;
        $lf = "\r\n";
        $host = $this->m_smtphost;
        $user = $this->m_user;
        $pass = $this->m_pwd;
        $port = $this->m_smtp_port; //    
        $timeout = $this->m_socketTimeout;
        $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if (!$socket) {
            $emsg = "ERROR: " . $host . " " . $port . " - {$errstr} ({$errno})";
            igk_debug_wln($emsg);
            $this->ErrorMSG = $emsg;
            igk_debuggerview()->add("div")->Content = $emsg;
            return false;
        } else {
            $recipients = $this->m_to;
            if (igk_count($this->m_tocc) > 0)
                $recipients = array_merge($recipients, $this->m_tocc);
            if (igk_count($this->m_toBcc) > 0)
                $recipients = array_merge($recipients, $this->m_toBcc);
            $subject = $this->Title;
            if (!$this->server_parse($socket, '220')) {
                $this->_closeSocket($socket);
                return false;
            }
            fwrite($socket, 'EHLO ' . $host . $lf);
            if (!$this->server_parse($socket, '250')) {
                $this->_closeSocket($socket);
                return false;
            }
            if ($this->SocketType == "tls") {
                fwrite($socket, 'STARTTLS' . $lf);
                if (!$this->server_parse($socket, '220')) {
                    $this->_closeSocket($socket);
                    return false;
                }
                if (false == @stream_socket_enable_crypto(
                    $socket,
                    true,
                    STREAM_CRYPTO_METHOD_TLS_CLIENT
                )) {
                    $this->_closeSocket($socket);
                    igk_debug_wln("unable to start tls encryption");
                    return false;
                }
                fwrite($socket, 'HELO ' . $host . $lf);
                if (!$this->server_parse($socket, '250'))
                    return false;
            }
            igk_debug_wln("AUTH LOGIN");
            fwrite($socket, 'AUTH LOGIN' . $lf);
            if (!$this->server_parse($socket, '334')) {
                $this->_closeSocket($socket);
                return false;
            }
            igk_debug_wln("AUTH USER " . $user);
            fwrite($socket, base64_encode($user) . $lf);
            //fwrite($socket, $user.IGK_CLF);
            if (!$this->server_parse($socket, '334')) {
                $this->_closeSocket($socket);
                return false;
            }
            igk_debug_wln("AUTH pass " . $pass);
            fwrite($socket, base64_encode($pass) . $lf);
            //fwrite($socket, $pass.IGK_CLF);
            if (!$this->server_parse($socket, '235')) {
                $this->_closeSocket($socket);
                return false;
            }
            // + | --------------------------------------------------------------------
            // + | valid email from
            // + |
            $from = $this->FROM;
            if ($from) {
                if (!preg_match("/(\"(?<title>.*)\")?\<(?P<from>[^\^]+)\>/", $from, $t_tab)) {
                    $from = " <" . trim($from) . ">";
                } else {                   
                    $from = '<' . $t_tab['from'] . '>';
                }
            } else {
                // null reserved path
                $from = "<>";
            }
            igk_debug_wln("MAIL FROM: " . $from);
            fwrite($socket, 'MAIL FROM:' . $from . '' . $lf);
            if (!$this->server_parse($socket, '250')) {
                $this->_closeSocket($socket);
                return false;
            }
            foreach ($recipients as $email) {
                fwrite($socket, 'RCPT TO: <' . $email . '>' . $lf);
                if (!$this->server_parse($socket, '250')) {
                    $this->_closeSocket($socket);
                    return false;
                }
            }
            fwrite($socket, 'DATA' . $lf);
            if (!$this->server_parse($socket, '354')) {
                $this->_closeSocket($socket);
                return false;
            }
            $t  = "";
            fwrite($socket, $t . 'Subject: ' . $subject . $lf . 'To: <' . implode('>, <', $this->m_to) . '>' . $lf . $headers . "\r\n\r\n" . $message . $lf);
            fwrite($socket, "\r\n.\r\n");
            igk_debug_wln("END mail.");
            if (!$this->server_parse($socket, '250')) {
                $this->_closeSocket($socket);
                return false;
            }
            // igk_debug_wln("GOOD SENDDING --- CLOSE SOCKET");
            $this->_closeSocket($socket);
            return true;
        }
    }

    /**
    * auto generate doc.
    * @param mixed $socket
    */
    private function _closeSocket($socket)
    {
        fwrite($socket, 'QUIT' . IGK_CLF);
        fclose($socket);
    }

    /**
    * auto generate doc.
    * @param mixed $boundary
    */
    private function _getHeader($boundary)
    {
        $header = IGK_STR_EMPTY;
        if ($this->m_from)
            $header .= "From: " . $this->m_from . IGK_CLF;
        if ($this->m_replyto)
            $header .= "Reply-To: " . $this->m_replyto . IGK_CLF;
        $CC = self::GetMailList($this->m_tocc);
        if (!empty($CC)) {
            $header .= "Cc: <" . $CC . ">\r" . IGK_LF;
        }
        $CC = self::GetMailList($this->m_toBcc);
        if (!empty($CC)) {
            $header .= "Bcc: " . $CC . IGK_CLF;
        }
        $header .= "MIME-Version: 1.0\r" . IGK_LF;
        $header .= "Content-Type: multipart/related; boundary=$boundary\r" . IGK_LF;
        return $header;
    }

    /**
    * auto generate doc.
    * @param mixed $to
    */

    public function addTo($to)
    {
        if (is_string($to)) {
            $h = explode(',', $to);
            if (igk_count($h) > 1) {
                foreach ($h as $k) {
                    $this->m_to[] = $k;
                }
            } else
                $this->m_to[] = $to;
        } else {
            if (is_array($to)) {
                foreach ($to as $k => $v) {
                    switch (strtolower($k)) {
                        case "cc":
                            $this->addToCC($v);
                            break;
                        case "cci":
                            $this->addToGCC($v);
                            break;
                        default:
                            $this->addTo($v);
                            break;
                    }
                }
            } else
                $this->m_to[] = $to;
        }
    }

    /**
    * auto generate doc.
    * @param mixed $to
    */

    public function addToCC($to)
    {
        if (is_string($to)) {
            $h = explode(',', $to);
            if (igk_count($h) > 1) {
                foreach ($h as $k) {
                    $this->m_tocc[] = $k;
                }
            } else
                $this->m_tocc[] = $to;
        } else
            $this->m_tocc[] = $to;
    }
    /**
     * set carbon gcc 
     * @param string|string<array> $to mails list comma separated.
     */

    public function addToGCC($to)
    {
        if (is_string($to)) {
            $h = explode(',', $to);
            if (igk_count($h) > 1) {
                foreach ($h as $k) {
                    $this->m_toBcc[] = $k;
                }
            } else
                $this->m_toBcc[] = $to;
        } else
            $this->m_toBcc[] = $to;
    }

    /**
    * auto generate doc.
    * @param mixed $attachement
    */

    public function attach($attachement)
    {
        if ($attachement)
            $this->m_files[] = $attachement;
    }

    /**
    * auto generate doc.
    * @param mixed $cid the default value is null
    */

    public function attachContent($content, $contentType = IGK_CT_PLAIN_TEXT, $cid = null)
    {
        $attach = new MailAttachement();
        $attach->Content = $content;
        $attach->ContentType = $contentType;
        $attach->Type = "Content";
        $attach->Name = $attach->CID = $cid;
        $this->m_files[] = $attach;
        return $attach;
    }

    /**
    * auto generate doc.
    * @param mixed $cid the default value is null
    */

    public function attachFile($file, $contentType = IGK_CT_PLAIN_TEXT, $cid = null)
    {
        $attach = new MailAttachement();
        $attach->Link = $file;
        $attach->Content = igk_io_file_exists($file) ? IO::ReadAllText($file) : null;
        $attach->ContentType = $contentType;
        $attach->Type = "Uri";
        $attach->CID = $cid;
        $this->m_files[] = $attach;
        return $attach;
    }

    /**
    * auto generate doc.
    */
    public function ClearTo()
    {
        $this->m_to = array();
    }

    /**
    * auto generate doc.
    */
    public function getFrom()
    {
        return $this->m_from;
    }

    /**
    * auto generate doc.
    */
    public function getHtmlCharset()
    {
        return $this->html_charset;
    }

    /**
    * auto generate doc.
    */
    public function getHtmlMsg()
    {
        return $this->m_htmlmsg;
    }

    /**
    * auto generate doc.
    * @param mixed $tab
    */

    static function GetMailList($tab)
    {
        $o = IGK_STR_EMPTY;
        foreach ($tab as $k => $v) {
            if ($k > 0)
                $o .= ",";
            $o .= self::MailEntry($v);
        }
        return $o;
    }

    /**
    * auto generate doc.
    */
    public function getPort()
    {
        return $this->m_smtp_port;
    }

    /**
    * auto generate doc.
    */
    public function getPwd()
    {
        return $this->m_pwd;
    }

    /**
    * auto generate doc.
    */
    public function getReplyTo()
    {
        return $this->m_replyto;
    }

    /**
    * auto generate doc.
    */
    public function getSmtpHost()
    {
        return $this->m_smtphost;
    }

    /**
    * auto generate doc.
    */
    public function getSocketTimeout()
    {
        return $this->m_socketTimeout;
    }

    /**
    * auto generate doc.
    */
    public function getSocketType()
    {
        return $this->m_socketType;
    }

    /**
    * auto generate doc.
    */
    public function getTextCharset()
    {
        return $this->text_charset;
    }

    /**
    * auto generate doc.
    */
    public function getTextMsg()
    {
        return $this->m_textmsg;
    }
    /**
     * get title
     * @return ?string
     */

    public function getTitle()
    {
        return $this->m_title;
    }

    /**
    * auto generate doc.
    */
    public function getToString()
    {
        return self::GetMailList($this->m_to);
    }

    /**
    * auto generate doc.
    */
    public function getUseAuth()
    {
        return $this->m_useAuth;
    }

    /**
    * auto generate doc.
    */
    public function getUser()
    {
        return $this->m_user;
    }

    /**
    * auto generate doc.
    * @param mixed $c
    */

    static function MailEntry($c)
    {
        $out = IGK_STR_EMPTY;
        if (is_numeric($c) || (is_string($c) && !empty($c))) {
            $out .= $c;
        } else if (is_object($c) && (method_exists(get_class($c), IGK_FC_GETVALUE))) {
            $out .= $c->getValue();
        }
        return $out;
    }
    /**
     * send mail after configuration 
     * @return boolean
     */

    public function sendMail():bool
    {
        $boundary = igk_new_id();
        $to = $this->getToString();
        $title = $this->getTitle();
        $header = $this->_getHeader($boundary);
        $t = trim($to);
        if (empty($t))
            return false;
        $lf = IGK_CLF;
        $message = $lf;
        $message .= "This is a multi-part message in MIME Format." . $lf;
        $message .= "--$boundary" . $lf;
        $j1 = $this->TextMsg;
        $j2 = $this->HtmlMsg;
        $LINE = $lf . $lf;
        if (!((empty($j1) && empty($j2)))) {
            $message .= "Content-Type: multipart/alternative; boundary=sub_$boundary" . $lf;
            if (!empty($j1)) {
                $message .= $LINE . "--sub_$boundary" . $lf;
                $message .= "Content-Type: text/plain; charset=\"" . $this->text_charset . "\"" . $LINE;
                $message .= $j1;
            }
            if (!empty($j2)) {
                $message .= $LINE . "--sub_$boundary" . $lf;
                if ($this->Base64Encoding) {
                    $message .= "Content-Transfer-Encoding: base64" . $lf;
                    $message .= "Content-Type:text/html; charset=\"" . $this->html_charset . "\"" . $LINE;
                    $message .= implode("\n", str_split(base64_encode($j2), self::BASE64_CHUNK));
                } else {
                    $message .= "Content-Type:text/html; charset=\"" . $this->html_charset . "\"" . $LINE;
                    $message .= $j2;
                }
            }
            $message .= $LINE . "--sub_$boundary--" . $lf;
        }
        foreach ($this->m_files as $v) {
            $data = $v->getData();
            $message .= $LINE . "--$boundary" . $lf;
            $message .= "Content-Type: " . $v->ContentType;
            if ($v->Name) {
                $message .= "; name=\"" . $v->Name . "\"";
            }
            $message .= $lf;
            $message .= "Content-Transfer-Encoding: base64" . $lf;
            if (!$v->Visible) {
                $message .= "Content-Disposition: attachment" . $lf;
            }
            if ($v->CID) {
                $message .= "Content-ID: <" . $v->CID . ">" . $lf;
            }
            $message .= $lf . $lf . $data;
        }
        $message .= $lf . "--$boundary--" . $lf;
        $message .= "end of the multi-part";
        if ($this->UseAuth) {
            if (extension_loaded("openssl")) {
                $v = $this->__sendMailTLS($header, $message);
                if (!$v) {
                    igk_ilog("Mail Error:" . $this->ErrorMsg);
                }
                return $v;
            } else {
                igk_ilog("no openssl extension loaded", __METHOD__);
            }
            return false;
        } else {
            if (@mail($to, $title, $message, $header) === true) {
                return true;
            }
        }
        return false;
    }

    /**
    * auto generate doc.
    * @param mixed $expected_response
    */
    private function server_parse($socket, $expected_response)
    {
        if (igk_getv(socket_get_status($socket), "eof")) {
            return false;
        }
        $server_response = '';
        igk_debug_wln("Expected " . $expected_response);
        $i = 1;
        while (substr($server_response, 3, 1) != ' ') {
            igk_debug_wln("reponse ::::" . $server_response);
            if (!($server_response = fgets($socket, 256))) {
                $this->ErrorMsg = __FUNCTION__ . ' : Error while fetching server response codes.' . "-$socket-" . "{$expected_response} " . '[' . $server_response . ']';
                igk_debug_wln($this->ErrorMsg);
                igk_debuggerview()->add("div")->Content = $this->ErrorMsg;
                return false;
            }
        }
        igk_debug_wln('OK : "' . $server_response . '"');
        if (!(substr($server_response, 0, 3) == $expected_response)) {
            $this->ErrorMsg = __FUNCTION__ . ' Unable to send e-mail."' . $server_response . '"';
            igk_debug_wln($this->ErrorMsg);
            return false;
        }
        return true;
    }
    /**
     * set from .
     * "title" <mail@mail.com> | mail@mail.com
     * 
     * @param string $value
     */

    public function setFrom($value)
    {
        $this->m_from = $value;
    }
    /**
     * set from title
     * @param string $title 
     * @param string $mail 
     * @return void 
     */

    public function setFromTitle(string $title, string $mail){
        $this->setFrom(sprintf("\"%s\" <%s>", $title, $mail));
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setHtmlCharset($v)
    {
        $this->html_charset = $v;
    }

    /**
    * auto generate doc.
    * @param mixed $content
    */

    public function setHtmlMsg($content)
    {
        $this->m_htmlmsg = $content;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setPort($value)
    {
        $this->m_smtp_port = $value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setPwd($value)
    {
        $this->m_pwd = $value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setReplyTo($value)
    {
        $this->m_replyto = $value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setSmtpHost($value)
    {
        $this->m_smtphost = $value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setSocketTimeout($value)
    {
        $this->m_socketTimeout = $value;
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setSocketType($v)
    {
        switch (strtolower($v)) {
            case "tls":
            case "ssl":
                $this->m_socketType = strtolower($v);
                break;
        }
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setTextCharset($v)
    {
        $this->text_charset = $v;
    }

    /**
    * auto generate doc.
    * @param mixed $content
    */

    public function setTextMsg($content)
    {
        $this->m_textmsg = $content;
    }
    /**
     * set title
     * @param ?string $value
     */

    public function setTitle($value)
    {
        $this->m_title = $value;
    }

    /**
    * Sets Mail Auth Password.
    * @param null|string $password
    */
    public function setMailAuthPassword(?string $password)
    {
        $this->m_auth_password = $password;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setUseAuth($value)
    {
        $this->m_useAuth = $value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setUser($value)
    {
        $this->m_user = $value;
    }
}