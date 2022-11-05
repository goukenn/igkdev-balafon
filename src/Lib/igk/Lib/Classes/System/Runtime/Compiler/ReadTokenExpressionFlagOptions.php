<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadTokenExpressionFlagOptions.php
// @date: 20221025 09:29:16
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ReadTokenExpressionFlagOptions extends ReadTokenFlagOptions{
    var $_t_;
    var $dependOn = [];
    var $depth = 0;
    var $type;
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

    var $rtrim = false;
    /**
     * mark that instruction argument replace in case of dependency
     * @var false
     */
    var $args_replaced = false;
}