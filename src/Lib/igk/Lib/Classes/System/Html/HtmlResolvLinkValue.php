<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlResolvLinkValue.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Html;
use IGK\System\Html\IHtmlGetValue;
use IGK\System\IO\SystemPath;
use IGKResourceUriResolver;
use IGKValidator;

/**
* Html resolv link value.
* @package IGK\System\Html
*/
class HtmlResolvLinkValue extends HtmlAttributeValue implements IHtmlGetValue {
 
    /**
     * Resolve and return the link value, handling URIs, paths, and resource resolution.
     *
     * @param mixed $options Optional resolution options
     * @return mixed The resolved link value
     */
    public function getValue($options = null) {
        if (($lnk = $this->value) && is_string($lnk)) {
            if ( IGKValidator::IsUri($lnk)){
                return $lnk;
            }
            if ($l = strstr($lnk, '?')){                
                $p = SystemPath::Parse($lnk); 
                if ($p->exists()){
                    return $p->resolve();
                }
            }
            if (igk_io_file_exists($lnk, true)){
                return IGKResourceUriResolver::getInstance()->resolve($lnk);
            }
        }
        if ($lnk instanceof IHtmlGetValue){
            return $lnk->getValue($options);
        }
        return $lnk;
    }     
}