<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadBlockExpressionInfo.php
// @date: 20221014 06:05:38
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* read block expression
* @package IGK\System\Runtime\Compiler
*/
class ReadBlockExpressionInfo{
    var $buffer = "";
    var $detectVars = [];
    var $depth = 0;
    /**
     * 
     * @var ?static
     */
    var $parent;

    public function __construct()
    {
        
    }
}