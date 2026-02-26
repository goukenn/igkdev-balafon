<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlTagExpressionName.php
// @date: 20221018 11:21:15
namespace IGK\System\Html;
/**
* 
* @package IGK\System\Html
*/
class HtmlTagExpressionName{

    /**
    * Name of name.
    * @var mixed
    */
    var $name;

    /**
    * .ctr
    * @param mixed $name
    */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
    * get string presentation.
    */
    public function __toString()
    {
        return $this->name;
    }
}