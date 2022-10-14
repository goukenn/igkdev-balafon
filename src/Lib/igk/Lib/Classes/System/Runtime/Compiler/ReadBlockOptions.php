<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadBlockOptions.php
// @date: 20221013 12:36:46
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
 * 
 * @package IGK\System\Runtime\Compiler
 */
class ReadBlockOptions
{
    var $depth = 0; // " => 0, // depth 
    /**
     * current reading block
     * @var ?ReadBlockInstructionInfo
     */
    var $blockInfo = null; // " => null,
    var $blocks = []; // " => [], // store block list
    var $lastValue = ""; // " => &$lvalue,
    var $lastToken = ""; // " => &$ltoken,
    var $nextRequest = null; // " => null, //next value request,
    var $appendNext = null; // " =>null

    /**
     * 
     * is in buffer modee . buffer and 
     * @var bool
     */
    var $buffering;


    /**
     * represent the buffer list in expressionBuffer
     * @var array
     */
    var $bufferLists = [];

    /**
     * var code default buffer
     * @var string
     */
    var $buffer = "";

    /**
     * read modifier
     * @var array
     */
    var $modifiers = [];

    /**
     * detected namespace
     * @var bool|string
     */
    var $namespace;

    var $flag = null;

    var $flagOption;

    // if variable detected must create an express_eval node
    /**
     * expression buffer depth for condition and operation
     * @var int
     */
    var $expressionConditionDepth=0;

    /**
     * expression buffer 
     * @var string
     */
    var $expressionBuffer="";
    /**
     * 
     * @var ReadBlockExpressionInfo[]|Array<ReadBlockExpressionInfo> expression buffer
     */
    var $expressions = [];
}
