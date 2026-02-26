<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadTokenExpressionFlagOptions.php
// @date: 20221025 09:29:16
namespace IGK\System\Runtime\Compiler;
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ReadTokenExpressionFlagOptions extends ReadTokenFlagOptions{

    /**
    * Property: t.
    * @var mixed
    */
    var $_t_;

    /**
    * Property: depend on.
    * @var mixed
    */
    var $dependOn = [];

    /**
    * Property: depth.
    * @var mixed
    */
    var $depth = 0;

    /**
    * Type of type.
    * @var mixed
    */
    var $type;

    /**
    * Property: quote start.
    * @var mixed
    */
    var $quoteStart = false;
    /**
     * a splitted expression 
     * ->()->data = data
     * @var mixed
     */
    var $split = false;
    /**
     * ignore dependency variable
     * @var false
     */
    var $ignoreDependency = false;
    /**
     * strore expression declaration depth;
     * @var mixed
     */
    var $functionDepth;

    /**
    * Property: rtrim.
    * @var mixed
    */
    var $rtrim = false;
    /**
     * mark that instruction argument replace in case of dependency
     * @var false
     */
    var $args_replaced = false;
}