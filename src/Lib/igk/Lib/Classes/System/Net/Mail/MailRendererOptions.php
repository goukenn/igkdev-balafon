<?php
// @author: C.A.D. BONDJE DOUE
// @file: MailRendererOptions.php
// @date: 20230425 09:26:57
namespace IGK\System\Net\Mail;
use IGK\System\Html\HtmlRendererOptions;
use IGK\System\Html\HtmlRenderingContext;

/**
* auto generate doc.
* @package IGK\System\Net\Mail
*/
class MailRendererOptions extends HtmlRendererOptions{
    /**
    * Property: render theme.
    * @var mixed
    */
    var $renderTheme;
    /**
    * Property: context.
    * @var mixed
    */
    var $Context = HtmlRenderingContext::Mail;
    /**
    * .ctr
    */
    public function __construct()
    {        
    }
}