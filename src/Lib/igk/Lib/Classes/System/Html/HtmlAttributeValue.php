<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlAttributeValue.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Html;

abstract class HtmlAttributeValue implements IHtmlGetValue{
    protected $value;

    public function setValue($value){
        $this->value = $value;
        return $this;
    }
}