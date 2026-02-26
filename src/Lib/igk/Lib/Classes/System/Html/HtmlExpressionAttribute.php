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

/**
* auto generate doc.
* @package IGK\System\Html
*/
class HtmlExpressionAttribute implements IHtmlGetValue{
    private $m_v;
    /**
     * Constructor.
     *
     * @param mixed $v The expression value to store
     */
    public function __construct($v){
        $this->m_v=$v;
    }
    /**
     * Return the stored expression value.
     *
     * @param mixed $o Optional options
     * @return mixed The stored value
     */
    public function getValue($o=null){
        return $this->m_v;
    }
}