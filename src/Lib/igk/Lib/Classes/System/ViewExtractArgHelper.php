<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExtractArgHelper.php
// @date: 20221012 16:06:14
namespace IGK\System;
use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
/**
* expression view helper
* @package IGK\System
*/
class ViewExtractArgHelper implements ArrayAccess{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $_output = "";

    /**
    * auto generate doc.
    * @var mixed
    */
    private $_name;
    use ArrayAccessSelfTrait;

    /**
    * .ctr
    * @param string $name
    */
    public function __construct(string $name)
    {
        $this->_name = $name;
    }

    /**
    * get string presentation.
    */
    public function __toString()
    {
        return sprintf("<?= $%s ?>", $this->_name.$this->_output);
    }

    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        $this->_output.="->".$n;
        return $this;
    }

    /**
    * destructor
    * @param mixed $n
    * @param mixed $args
    */
    public function __set($n, $args){
        igk_die("set view arg helper not allowed");
    }

    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $arguments
    */
    public function __call($name, $arguments)
    {
        $this->_output.=sprintf("->".$name."(%s)", 
            implode(",", array_map([\IGK\Helper\ArrayUtils::class, "ArgumentsMap"], $arguments))
        );
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $name
    */
    public function _access_OffsetGet($name){
        $this->_output.=sprintf("['%s']", $name);
        return $this;
    }
}