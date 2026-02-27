<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadTokenVariableFlagOption.php
// @date: 20221023 14:53:29
namespace IGK\System\Runtime\Compiler;

/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler
*/
class ReadTokenVariableFlagOption extends ReadTokenFlagOptions{

    /**
    * Name of name.
    * @var mixed
    */
    var $name;

    /**
    * Property: default.
    * @var mixed
    */
    var $default;

    /**
    * Property: depend on.
    * @var mixed
    */
    var $dependOn = false;

    /**
    * Property: modifiers.
    * @var mixed
    */
    var $modifiers= [];

    /**
    * Property: render.
    * @var mixed
    */
    var $render = false;
}