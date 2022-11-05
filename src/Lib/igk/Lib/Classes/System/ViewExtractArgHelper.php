<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewExtractArgHelper.php
// @date: 20221012 16:06:14
namespace IGK\System;

use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

///<summary></summary>
/**
* expression view helper
* @package IGK\System
*/
class ViewExtractArgHelper implements ArrayAccess{
    private $_output = "";   
    private $_name;
    use ArrayAccessSelfTrait;

    public function __construct(string $name)
    {
        $this->_name = $name;
    }
    public function __toString()
    {
        return sprintf("<?= $%s ?>", $this->_name.$this->_output);
    }
    public function __get($n){
        $this->_output.="->".$n;
        return $this;
    }
    public function __set($n, $args){
        igk_wln("not allowed");
    }
    public function __call($name, $arguments)
    {
        $this->_output.=sprintf("->".$name."(%s)", 
            implode(",", array_map([\IGK\Helper\ArrayUtils::class, "ArgumentsMap"], $arguments))
        );
        return $this;
    }
    public function _access_OffsetGet($name){
        $this->_output.=sprintf("['%s']", $name);
        return $this;
    }
}