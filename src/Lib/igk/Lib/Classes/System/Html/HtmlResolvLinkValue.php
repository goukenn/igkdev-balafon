<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlResolvLinkValue.php
// @date: 20220803 13:48:55
// @desc: 

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
        return $lnk;
    }     
}