<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlCallbackValueAttribute.php
// @date: 20220504 07:59:09
// @desc: 
namespace IGK\System\Html\Dom;
/**
 * use to retrieve value from callback
 * @package IGK\System\Html\Dom
 */
class HtmlCallbackValueAttribute extends HtmlItemAttribute{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $callback;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $options;

    /**
    * .ctr
    * @param callable $callback
    * @param mixed ...$options
    */
    public function __construct(callable $callback, ...$options)
    { 
        $this->callback = $callback;
        $this->options = $options;
    }

    /**
    * auto generate doc.
    * @param null|mixed $option
    */
    public function getValue($option = null) {  
        $fc = $this->callback;
        return $fc($option, $this);
    }
}