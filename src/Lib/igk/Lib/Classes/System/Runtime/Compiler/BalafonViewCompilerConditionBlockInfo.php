<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonViewCompilerConditionBlockInfo.php
// @date: 20221012 08:11:49
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
 * 
 * @package IGK\System\Runtime\Compiler
 */
class BalafonViewCompilerConditionBlockInfo
{

    var $type; // " => $value,
    var $value; // " => $value,
    var $depth; // " => $options->scopeDepth,
    /**
     * bool start reading block
     * @var false
     */
    var $block;  
    var $instructCode ="";  
    var $conditionRead; // " => in_array($value, explode("|", "if|elseif|while|switch")),
    var $condition; // " => "",
    var $conditionDepth; // " => -1,
    var $childs; // " => null
    var $lastTokenId;
    /**
     * in curly open flag
     * @var false
     */
    var $openCurlyFlag = false;
    /**
     * last detected variable
     * @var mixed
     */
    var $detectVariable;

    /**
     * store list of instruction info
     * @var array
     */
    var $instructions = [];

    /**
     * store instuction
     * @var ?BalafonViewConditionBlockInstructionInfo
     */
    var $instruction;

    /**
     * array of detected variables in block
     * @var mixed
     */
    var $variables;

    public function __construct(string $type, int $depth)
    {
        $this->type = $type;
        $this->value = $type;
        $this->depth = $depth;
        $this->block = false; 
        $this->conditionRead = in_array($type, explode("|", "if|elseif|while|switch"));
        $this->condition = "";
        $this->conditionDepth = -1;
        $this->childs = null;
    }
}
