<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewReadConditionFlagInfo.php
// @date: 20221027 08:04:10
namespace IGK\System\Runtime\Compiler\ViewCompiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
class ViewReadBlockFlagInfo{
    /**
     * current depth
     * @var mixed
     */
    var $depth;
    /**
     * required read condition at start
     * @var mixed
     */
    var $condition;
    /**
     * is multicode block
     * @var true
     */
    var $multicode;
    /**
     * buffer 
     * @var mixed
     */
    var $buffer;
    /**
     * out condition string
     * @var mixed
     */
    var $out_condition;
    /**
     * out type
     * @var string
     */
    var $type;
    /**
     * condition readed
     * @var bool
     */
    var $condition_read = false;

    /**
     * is litteral block expression
     */
    var $litteral = false;
}