<?php
// @author: C.A.D. BONDJE DOUE
// @file: MailRendererOptions.php
// @date: 20230425 09:26:57
namespace IGK\System\Net\Mail;

use IGK\System\Html\HtmlRendererOptions;

///<summary></summary>
/**
* 
* @package IGK\System\Net\Mail
*/
class MailRendererOptions extends HtmlRendererOptions{
    var $renderTheme;
    var $Context = 'mail';
    public function __construct()
    {        
    }
}