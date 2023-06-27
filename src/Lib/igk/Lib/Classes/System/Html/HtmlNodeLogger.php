<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlNodeLogger.php
// @date: 20230526 01:53:27
namespace IGK\System\Html;


///<summary></summary>
/**
* logger
* @package IGK\System\Html
*/
class HtmlNodeLogger
{
    var $t;
    public function __construct($t)
    {
        $this->t = $t;
    }
    public function __call($name, $arguments)
    {
        $dv = $this->t->div();
        $dv['class'] = $name;
        $dv->Content = implode('', $arguments);
    }
}