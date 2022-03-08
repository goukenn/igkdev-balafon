<?php


namespace IGK\Resources;

use IGK\System\Html\IHtmlGetValue;
use IGKObject;

///<summary>Represente class: IGKLangExpression</summary>
/**
* Represente IGKLangExpression class
*/
final class IGKLangExpression extends IGKObject implements IHtmlGetValue {
    private $m_keys;
    ///<summary></summary>
    ///<param name="keys"></param>
    /**
    * 
    * @param mixed $keys
    */
    public function __construct($keys){
        if(!is_array($keys) || (igk_count($keys) == 0))
            igk_die("keys is not an array");
        $this->m_keys=$keys;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    /**
    * 
    * @param mixed $options the default value is null
    */
    public function getValue($options=null){
        $nl=R::GetCurrentLang();
        return igk_getv($this->m_keys, $nl, igk_getv(array_values($this->m_keys), 0));
    }
}