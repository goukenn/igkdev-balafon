<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewTokenizeExpressionInfo.php
// @date: 20221021 09:46:12
namespace IGK\System\Runtime\Compiler\ViewCompiler;
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
class ViewTokenizeExpressionInfo{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $buffer;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $variables;
    /**
     * is contain operation
     * @var mixed
     */
    var $op = false;
    /**
     * expression depend on
     * @var array
     */
    var $dependOn = [];
}