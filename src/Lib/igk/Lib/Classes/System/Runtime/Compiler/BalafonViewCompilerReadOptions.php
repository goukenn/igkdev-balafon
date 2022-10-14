<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonViewCompilerReadOptions.php
// @date: 20220909 15:47:43
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class BalafonViewCompilerReadOptions{
    /**
     * skip the line empty line
     * @var mixed
     */
    var $skip_line;
    /**
     * list on using
     * @var array
     */
    var $usings = [];

    /**
     * view namespace
     * @var mixed
     */
    var $namespace;

    /**
     * layout setting options
     * @var mixed
     */
    var $layout;

    var $scopeFlag = false;
    var $usingFlag = false;
    var $asOperatorFlag = false;

    /**
     * exit detected
     * @var false
     */
    var $exitDetected = false;

    /**
     * detect close tag
     * @var false
     */
    var $detectCloseTag = false;

    /**
     * in php script 
     * @var bool end in php script
     */
    var $inPHPScript = false;

    /**
     * source type : mixed|php. php start with php
     * @var mixed
     */
    var $sourceType;

    var $flagReading;

    /**
     * @var int $scopeDepth
     */
    var $scopeDepth = 0;

    var $buffers = "";

    /**
     * store global definition
     * @var array
     */
    var $functions =[];

    /**
     * store global declared structures, $type, [name=>blockcode]
     * @var array
     */
    var $classInsterfaceOrTrait = [];

    /**
     * store tempory information 
     * @var ?object|string
     */
    var $dataInfo;

    /**
     * variable context detection . after = operator ('read') i next operator = single then override
     * @var mixed
     */
    var $varContext;

    /**
     * detected variables list
     * @var array
     */
    var $detectVariables = [];

    /**
     * reading block information
     * @var ?BalafonViewCompilerConditionBlockInfo
     */
    var $read_block_info;


    /**
     * store last token read id
     * @var mixed
     */
    var $lastTokenReadId;
}