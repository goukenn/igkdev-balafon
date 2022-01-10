<?php
namespace IGK\System\Html;

use IGK\System\Html\IHtmlGetValue;
use IGKResourceUriResolver;
use IGKValidator;

class HtmlResolvLinkValue extends HtmlAttributeValue implements IHtmlGetValue {
    public function getValue($options = null) { 
        if ($lnk  = $this->value){
            if (IGKValidator::IsUri($lnk)){
                return $lnk;
            }
            if (file_exists($lnk)){
                return IGKResourceUriResolver::getInstance()->resolve($lnk);
            }
        }
        return null;
    }     
}