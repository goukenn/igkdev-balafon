<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlTagExpressionName.php
// @date: 20221018 11:21:15
namespace IGK\System\Html;


///<summary></summary>
/**
* 
* @package IGK\System\Html
*/
class HtmlTagExpressionName{
    var $name;
    public function __construct($name)
    {
        $this->name = $name;
    }
    public function __toString()
    {
        return $this->name;
    }
}