<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlDocCoreStyle.php
// @date: 20220823 14:11:34
// @desc: core style uri
namespace IGK\System\Html\Dom;
use IGK\Helper\ViewHelper;
use IGK\System\Html\Dom\HtmlCssLinkNode;

/**
 * Document core style manager
 * @package IGK\System\Html\Dom
 */
class HtmlDocCoreStyle extends HtmlCssLinkNode{
    /**
     * source controller 
     * @var mixed
     */
    var $controller;
    /**
    * .ctr
    * @param mixed $link
    * @param bool $sys system style 
    * @param bool $defer defer loading 
    */
    public function __construct($link, $sys, $defer)
    {     
        parent::__construct($link, $sys, $defer); 
    }
    /**
    * Accept render.
    * @param null|mixed $options
    * @return bool
    */
    protected function _acceptRender($options = null):bool
    { 
        $doc = null;
        $options && ($doc = igk_getv($options, "Document"));
        if ($doc && $doc->noCoreCss){
            return false;
        }  
        $ctrl = $doc->getParam('controller');
        if ($ctrl && (!$ctrl !== igk_get_defaultwebpagectrl())){
            $this->ln->link = igk_io_append_query($this->ln->link, ['c'=>base64_encode(get_class($ctrl))]); 
        } 
        
        return parent::_acceptRender($options);
    } 
}