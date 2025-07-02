<?php
// @file: HtmlExpressionAttribute.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;



class HtmlExpressionAttribute implements IHtmlGetValue{
    private $m_v;
    public function __construct($v){
        $this->m_v=$v;
    }
    public function getValue($o=null){
        return $this->m_v;
    }
}
