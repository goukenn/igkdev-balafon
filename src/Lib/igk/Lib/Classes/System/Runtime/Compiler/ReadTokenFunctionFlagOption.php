<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadTokenFunctionFlagOption.php
// @date: 20221024 02:04:09
namespace IGK\System\Runtime\Compiler;
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ReadTokenFunctionFlagOption extends ReadTokenFlagOptions{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $op="name";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $depth = 0;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $condition = "";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $type;
    /**
     * argument name
     * @var mixed
     */
    var $argName;
    /**
     * argument type
     * @var mixed
     */
    var $argType;
}