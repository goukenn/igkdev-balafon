<?php
namespace IGK\Resources;

use IGK\System\Html\HtmlAttributeValue;
use IGK\System\Html\IHtmlGetValue;
use IGKResourceUriResolver;
use IGKValidator;

/**
 * string resource uri data
 * @package IGK\Resources
 */
class ResourceData extends HtmlAttributeValue implements IHtmlGetValue{
    var $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
    public function __toString()
    {
        return $this->value;
    }
    public function getValue($option = null){
        igk_wln_e("get value ... ");
        if (IGKValidator::IsUri($this->value)){
            return $this->value;
        }
        $m = null;
        if (file_exists($this->value)){
            $m =  IGKResourceUriResolver::getInstance()->resolve($this->value);
        }
        igk_wln_e("resolving .... ".$this->value);
        return $m; 
    }
}