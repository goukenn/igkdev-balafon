<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadTokenOptions.php
// @date: 20221019 16:12:51
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler
*/
class ReadTokenOptions implements IReadTokenOptions{
    
    var $source;
    /**
     * array of uses 
     * @var ?array
     */
    var $uses;

    /**
     * source namespace
     * @var string
     */
    var $namespace;

    /**
     * global declared variables
     */
    var $variables = [];

    /**
     * current read flag read
     * @var mixed
     */
    var $flag;

    /**
     * block depth
     * @var int
     */
    var $depth = 0;

    /**
     * is namespace block
     * @var bool
     */
    var $isNamespaceBlock = false;

    /**
     * is flag options
     * @var mixed
     */
    var $flagOptions;

    /**
     * flags buffers
     * @var array
     */
    var $flags = [];


    /**
     * structure definitions
     * @var array
     */
    var $structs = [];

    /**
     * store read modifiers
     * @var array
     */
    var $modifiers = [];

    /**
     * store doc comment 
     * @var array
     */
    var $docComments;

    /**
     * comment definition
     * @var ?string comment definition
     */
    var $comment;

    /**
     * global source buffer
     * @var mixed
     */
    var $buffer = "";

    /**
     * skip white space
     * @var int
     */
    var $skipWhiteSpace = 0;

    /**
     * current structure reading
     * @var ?ReadTokenStructInfo
     */
    var $struct_info;

    /**
     * stored buffer list
     * @var array
     */
    var $buffers = [];

    /**
     * return detected
     * @var bool
     */
    var $globalReturnDetectedFlag = false;

    /**
     * start reading
     * @var false
     */
    var $startReadFlag = false;

    /**
     * descripbe comment
     * @var array
     */
    var $describeComments = [];


    /**
     * merge variable declaration
     * @var bool
     */
    var $mergeVariable = false;

    /**
     * no comment
     * @var bool
     */
    var $noComment = false;

    /**
     * curl open flag
     * @var false
     */
    var $curl_open = false;

    /**
     * close curl on block
     * @var false
     */
    var $close_curl = false;

    /**
     * here doc flag
     * @var mixed
     */
    var $heredocFlag = false;

    /**
     * fore stop reading after handle
     * @var false
     */
    var $stop_read = false;

    var $exit_detecteds = [];

    /**
     * bracket depth
     * @var int
     */
    var $bracketDepth = 0;
}