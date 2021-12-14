<?php
// @file: IGKNSValue.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;

use IIGKHtmlGetValue;

///<summary>namespace value attribute</summary>
final class HtmlNSValueAttribute implements IIGKHtmlGetValue{
    private $m_n, $m_ns;
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="ns"></param>
    public function __construct($n, $ns){
        $this->m_ns=$ns;
        $this->m_n=$n;
    }
    ///<summary>display value</summary>
    public function __toString(){
        return __CLASS__.":ns:".$this->m_ns;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function getValue($options=null){
        if(igk_html_is_ns_child($this->m_n)){
            return $this->m_ns;
        }
        return null;
    }
}
