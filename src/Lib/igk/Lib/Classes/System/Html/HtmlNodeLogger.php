<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlNodeLogger.php
// @date: 20230526 01:53:27
namespace IGK\System\Html;

/**
* logger
* @package IGK\System\Html
*/
class HtmlNodeLogger
{
    /**
    * Property: t.
    * @var mixed
    */
    var $t;
    /**
    * .ctr
    * @param mixed $t
    */
    public function __construct($t)
    {
        $this->t = $t;
    }
    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $arguments
    */
    public function __call($name, $arguments)
    {
        $dv = $this->t->div();
        $dv['class'] = $name;
        $dv->Content = implode('', $arguments);
    }
}