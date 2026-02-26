<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadTokenVariableFlagOption.php
// @date: 20221023 14:53:29
namespace IGK\System\Runtime\Compiler;
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ReadTokenVariableFlagOption extends ReadTokenFlagOptions{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $name;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $default;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $dependOn = false;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $modifiers= [];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $render = false;
}