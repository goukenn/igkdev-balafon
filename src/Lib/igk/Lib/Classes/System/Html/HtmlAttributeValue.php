<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlAttributeValue.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Html;
/**
 * attribute value
 * @package IGK\System\Html
 */
class HtmlAttributeValue implements IHtmlGetValue{

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $value;

    /**
    * auto generate doc.
    * @param null|mixed $options
    */
    public function getValue($options = null) { 
        return $this->value;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */
    public function setValue($value){
        $this->value = $value;
        return $this;
    }
}