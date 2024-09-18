<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCompiler.php
// @date: 20221024 16:51:12
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use Exception;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\NotImplementException;
use IGK\System\IO\StringBuilder;
use IGK\System\Runtime\Compiler\Armonic\ArmonicCompiler;
use IGK\System\Runtime\Compiler\CompilerFlagState;
use IGK\System\Runtime\Compiler\ReadTokenExpressionFlagOptions;
use IGK\System\Runtime\Compiler\ReadTokenOptions;
use IGK\System\Runtime\Compiler\ViewCompiler\Traits\AttachBlockTrait;
use IGK\System\Runtime\Compiler\ViewCompiler\Traits\ViewCompilerReadDoLoopTrait;
use IGK\System\Runtime\Compiler\ViewCompiler\Traits\ViewReadConditionTrait;
use IGK\System\Runtime\Compiler\ViewCompiler\Traits\ViewCompilerReadSwitchCaseTrait;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewCompilerUtility;
use IGK\System\Views\ViewCommentArgs;
use IGKException;
use IGKHtmlDoc;
use ReflectionException;

require_once __DIR__ . "/helper-functions.php";


///<summary></summary>
/**
 * BALAFON VIEW COMPILER
 * @package IGK\System\Runtime\Compiler\ViewCompiler
 */
class ViewCompiler extends ArmonicCompiler implements IViewCompiler
{
    use ViewCompilerReadSwitchCaseTrait;
    use ViewCompilerReadDoLoopTrait;
    use ViewCompilerReadTryCatch;
    use AttachBlockTrait;
    use ViewReadConditionTrait;

    private $m_containers = [];

    /**
     * enable cache usage
     * @var bool
     */
    var $forCache;

    /**
     * options to poss to view
     */
    var $options;

    var $params;
    /**
     * expression regex to handle comment
     * @var string
     */
    var $expression_regex = ViewCommentArgs::COMMENT_EXPRESSION_REGEX;

    /**
     * laeve commend regex
     * @var string
     */
    var $expression_use_comment_regex = "/\/\/\s*\+/";

    /**
     * represent the token compiler 
     * @var mixed
     */
    private $m_tokenCompiler;
    /**
     * 
     * @var bool
     */
    private $m_output = true;

    /**
     * 
     * @var bool
     */
    var $documentBuild = false;

    /**
     * variables
     * @var mixed
     */
    var $variables = [];

    var $flagHandler = null;

    /**
     * instruction blocks
     * @var ?ViewInstructionBlock
     */
    var $instruction_blocks;

    /**
     * instruction buffer
     * @var string
     */
    var $instruction_buffer = "";
    /**
     * tabstop
     * @var string
     */
    var $tab_stop = "    ";

    private $top_expression;

    private $instruct_flag = false;

    private $m_init_variables = [];

    private $m_compile_handler;

    /**
     * getter expression list.
     * @var array
     */
    private $m_expressions = [];

    /**
     * stop strings 
     * @var mixed
     */
    var $stop_strings = [
        "igk_exit" => '$__do_exit_expression__',
        "igk_do_response" => '$__do_response_expression__',
        "eval" => '$__do_eval__'
    ];

    /**
     * current block
     * @var ?ViewCompilerBockInfo
     */
    private $m_block;
    private $m_end_condiontal = [];
    const READ_SETTER_VARIABLE = CompilerFlagState::READ_VARIABLE . "_setter";
    const READ_BLOCK = CompilerFlagState::READ_BLOCK;
    const READ_EXPECT_BLOCK_CONTAINER = "expect_block_childs";
    const READ_BLOCK_INSTRUCTION = "handle_instruction";
    const READ_CONDITIONAL_EXPRESSION = "handle_conditional_expression";
   
    const BLOCK_TRIM_CHAR = ViewCompilerConstants::BLOCK_TRIM_CHAR;

    public function __construct()
    {
        // parent::__construct();
        $this->instruction_blocks = new ViewInstructionBlock;
    }

    /**
     * evaluate comment with compiler handler
     * @param mixed $data 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function evaluationComment($data)
    {
        if (is_null($this->m_compile_handler)) {
            $this->m_compile_handler = $this->_createCompileHandler() ?? igk_die("failed to create compile handler");
        }
        $this->m_output .= $this->m_compile_handler->evaluate($data);
    }
    /**
     * create process command handler
     * @return ViewCompileProcessCommandHandler 
     */
    protected function _createCompileHandler()
    {
        return new ViewCompileProcessCommandHandler($this);
    }


    public function HandleToken(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        if (!$options->struct_info) {

            $v_flag = &$options->flag; 

            if ($v_flag) {
                // igk_debug_wln(
                //     __FILE__.":".__LINE__, 
                //     "handle_flag:::::::::::::::::::::::::" . $v_flag);
                if ($this->_handleFlag($options, $id, $value)) {
                    return true;
                }
                if ($options->stop_read) {
                    return false;
                }
            } else {
                $this->_pushFlag($options);
                $v_flag = self::READ_BLOCK_INSTRUCTION;
                $options->flagOptions = (object)[
                    "buffer" => &$this->instruction_buffer,
                    "root_buffer" => &$options->buffer
                ];
                $this->_pushBuffer($options, $options->flagOptions->buffer, self::READ_BLOCK_INSTRUCTION);
                $this->instruct_flag = true;
            }

            switch ($id) {
                case T_OPEN_TAG:
                    break;
                case T_COMMENT:
                    $options->skipWhiteSpace = true;
                    // detect layout command
                    if (preg_match($this->expression_regex, $value, $data)) {
                        if ($b = $this->evaluationComment(trim($data['expression']))) {
                            if ($this->m_block) {
                                $this->m_block->blocks[] = $b;
                            } else {
                                $this->instruction_blocks[] = $b;
                            }
                        }
                    } else {
                        $this->_handleComment($options, $id, $value);
                    }

                    break;
                case T_START_HEREDOC:
                    $this->instruction_buffer .= $value;
                    return true;
                case T_END_HEREDOC:
                    $this->instruction_buffer .= $value;
                    return true;
                case T_VARIABLE:
                    return $this->_readSetterVariable($options, $id, $value);
                case T_STRING:
                case T_ECHO:
                    if (key_exists($value, $this->stop_strings)) {
                        $value = $this->stop_strings[$value];
                    }
                    // start reading expression
                    $this->_readExpression($options, ":expression");
                    $options->flagOptions->buffer = $value . (($id == T_ECHO) ? " " : "");
                    $options->flagOptions->rtrim = true;
                    return true;
                case T_RETURN;
                    if (!$this->m_block) {
                        $options->exit_detecteds["return"] = 1;
                        $this->_readExpression($options, ":return");
                        $this->_appendToFlagOptionBuffer($options, $value . ' ');
                        $options->skipWhiteSpace = true;
                        $options->flagOptions->rtrim = true;
                        return true;
                    }
                    $this->_readExpression($options, ":return");
                    $this->_appendToFlagOptionBuffer($options, $value . ' ');
                    $options->skipWhiteSpace = true;
                    $options->flagOptions->rtrim = true;
                    return true;
                case T_EXIT:
                    $options->exit_detecteds["exit"] = 1;
                    return false;
                case T_IF:
                case T_ELSE:
                case T_ELSEIF:
                case T_FOR:
                case T_FOREACH:
                case T_WHILE:
                    $this->_readBlock($options, $id, $value);
                    return true;
                case T_DO:
                    $this->_readDoWhileBlock($options, $id, $value);
                    return true;
                case T_CATCH:
                case T_TRY:
                case T_FINALLY:
                    $this->_readReadTryCatchBlock($options, $id, $value);
                    return true;
                case T_SWITCH:
                case T_CASE:
                case T_DEFAULT:
                    $this->_readSwitchCaseBlock($options, $id, $value);
                    return true;
                case T_BREAK:
                    if ($this->m_block) {
                        if ($v_last = igk_array_peek_last($this->m_containers)) {
                            if ($v_last->closed()) {
                                $v_last = array_pop($this->m_containers);
                            }
                        }
                    } else {
                        igk_die("break not in block");
                    }
                    break;
                default:
                    switch($value)
                    {
                        case '(':
                            $this->_readConditionalExpression($options, $id, $value);                            
                            return true;
                    }
                break;
            }
        }
        return parent::HandleToken($options, $id, $value);
    }

    #block instruction that must end with )
    protected function _readConditionalExpression(ReadTokenOptions $options, ?string $id, string $value){
        $this->_pushFlag($options);
        $v_foption = (object)[
            "buffer"=>"(",
            "depth"=>$options->depth-1,
            "start"=>true,
            "end_instruction"=>")"
        ];
        $options->flag = self::READ_CONDITIONAL_EXPRESSION;
        $options->flagOptions = $v_foption;
        $this->_pushBuffer($options, $v_foption->buffer);
        $this->m_end_condiontal[] = new ViewEndConditional($v_foption, function(ReadTokenOptions $options, ?string $id, string $value){
            $fop = $this->info;
            if (($value == ')') && ($fop->depth == $options->depth)){
              return true;
            }
            return false;
        });
    }
    protected function _handleConditionalExpression(ReadTokenOptions $options, ?string $id, string $value): bool{
        $fop = $options->flagOptions;
        if ($id == T_VARIABLE){
            $this->_readSetterVariable($options, $id, $value);
            $options->flagOptions->conditional = true;
            return true;
            //$this->_endSetterVariable($options, $id, $value);
             
        }
        $fop->buffer .= $value;
        if (($value == ')') && ($fop->depth == $options->depth)){
            $this->_endConditionalExpression($options, $id, $value);
            // igk_wln("end conditionnal: ".$fop->buffer);
        }
        return true;
    }
    protected function _endConditionalExpression(ReadTokenOptions $options, ?string $id, string $value): bool{
        $fop = $options->flagOptions;
        array_pop($this->m_end_condiontal);
        $this->_popFlag($options);
        $this->_popBuffer($options);
        $this->_appendToFlagOptionBuffer($options, $fop->buffer);
        $options->skipWhiteSpace = true;
        return true;
    }
    /**
     * 
     * @param mixed $options 
     * @return void 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function endHandleToken($options)
    {
        // clear container
        if ($options->flag == self::READ_EXPECT_BLOCK_CONTAINER) {
            $this->_popBuffer($options);
            $this->_popFlag($options);
        }
        $this->m_containers = [];
        if ($this->instruct_flag) {
            $this->_popBuffer($options);
            $this->_popFlag($options);
        }
        if (!empty($options->buffers)) {

            igk_trace();
            print_r($options->buffers);
            igk_wln_e(
                "",
                __FILE__ . ":" . __LINE__,
                "buffers is not empty"
            );
            return;
        }
        if ($this->instruction_blocks) {
            // 
            // igk_debug_wln(
            //     __FILE__ . ":" . __LINE__,
            //     "::::::::::::::::::::::::::::::::::::::::::::compile__block"
            // );

            $sb = new StringBuilder;
            if ($this->forCache) {
                $header = parent::mergeSourceCode(true);

                if (count($options->exit_detecteds)) {
                    // + | --------------------------------------------------
                    // + | remove last instruction in case of return detected
                    array_pop($this->instruction_blocks);
                }
                igk_environment()->caching_result = true;
                ViewCompilerUtility::CompileBlocks($this->instruction_blocks, $sb, $this->options, $header, $this->variables);

                foreach($this->m_expressions as $exp=>$buffer){
                    $sb->replace($exp, $buffer);
                }
                $this->m_expressions = [];

                igk_environment()->caching_result = false;
            } else {
                ViewCompilerUtility::RenderBlock($this->instruction_blocks, $sb);
            }
            $options->buffer = $sb . '';
        }
        // parent::endHandleToken($options);
    }
    protected function _handleFlag(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $flag = &$options->flag;
        if ($flag == self::READ_CONDITIONAL_EXPRESSION) {
            return $this->_handleConditionalExpression($options, $id, $value);
        }
        if ($flag == self::READ_EXPECT_BLOCK_CONTAINER) {
            return $this->_handleExpectContainer($options, $id, $value);
        }
        if ($flag == self::READ_SETTER_VARIABLE) {
            return $this->_handleSetterVariable($options, $id, $value);
        }
        if ($flag == self::READ_BLOCK) {
            return $this->_handleReadBlock($options, $id, $value);
        }
        if ($flag == self::READ_BLOCK_INSTRUCTION) {
            return $this->_handleInstruction($options, $id, $value);
        }


        return parent::_handleFlag($options, $id, $value);
    }
    protected function _handleInstruction(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        if ($value == ";") {
            $g = &$this->instruction_buffer;
            $g = rtrim($g, ';');
            if (!empty($g)) {
                $g = $g . $value;
                if ($this->m_block) {
                    $options->flagOptions->root_buffer .= $g . "\n";
                }
                $this->_appendInstructBuffer();
            }
            $this->instruction_buffer = "";
            return true;
        } 
        return false;
    }

    private function _appendInstructBuffer()
    {
        $s = &$this->instruction_buffer;
        if (!empty($s)) {
            if ($this->m_block) {
                $this->m_block->blocks = $s;
            } else
                $this->instruction_blocks[] = $s;
            $s = "";
        }
    }
    #region Expression

    /**
     * 
     * @param ReadTokenOptions $options 
     * @param mixed $id 
     * @param mixed $value 
     * @return bool 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _endReadExpression(ReadTokenOptions $options, ?string $id, string $value): bool
    {

        /**
         * @var ReadTokenExpressionFlagOptions $fop  
         */
        $fop = $options->flagOptions;
        if (!$options->struct_info) {
            if ($this->top_expression === $fop) {
                $this->top_expression = null;
                // if ($value == ')'){
                //     $fop->buffer .= $value;
                //     return $this->_appendEndEqualExpression($options, $id, $value, $fop->buffer); 
                // }
            }
            if (count($fop->dependOn) > 0) {
                if ($fop->split) {
                    $v_cbuffer = $fop->buffer;
                    $v_eexpress = ViewTokenizeArgConstants::ExpressEvalGetter($fop->buffer, $fop->dependOn);
                    $this->m_expressions[
                        $v_eexpress
                    ] = $v_cbuffer;
                    $fop->buffer = $v_eexpress;

                } else {
                    if (!$fop->args_replaced) {
                        $this->_replaceGetterArgs($fop->dependOn, $fop->buffer);
                        $fop->args_replaced = true;
                    }
                }
            }
            // + | out of block
            $options->stop_read = (!$this->m_block) && $fop->type == ':return';
        }
        return parent::_endReadExpression($options, $id, $value) && !$options->stop_read;
    }
    protected function _handleComment(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        if (preg_match($this->expression_use_comment_regex, $value)) {
            if ($this->m_block) {
                $this->m_block->blocks[] = $value;
            } else {
                if (!$options->struct_info) {
                    $fop = $options->flagOptions;
                    if ($fop) {
                        if (substr($fop->buffer,-1)=="\n"){
                            $fop->buffer = substr($fop->buffer, 0, -1);
                        }
                        $fop->buffer .= trim($value)."\n";
                        $options->skipWhiteSpace = false;
                    } else {
                        $this->instruction_blocks[] = $value;
                    }
                }
            }

        }
        return true;
    }
    protected function _handleReadExpression(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        if (!$options->struct_info) { 
            $fop = $options->flagOptions;

            if ($this->m_end_condiontal){
                $c = igk_array_last($this->m_end_condiontal);
                if ($c->handle($options, $id, $value)){
                  
                    $this->_endReadExpression($options, $id, $value);
                    return true;
                }
            }

            

            if ($id == T_WHITESPACE) {
                if (!empty($value) && ($value == "\n") && $fop->ignoreDependency) { // possibility to containt white space
                    $value .= $this->_getTabStop($options);
                }
            } else if ($value == '}') {
                $tb = $this->tab_stop;
                $sb = rtrim($fop->buffer);
                if (igk_str_endwith($sb, "<?php") || ($tb && (($i = strrpos($fop->buffer, $tb)) !== false) && (($i + strlen($tb)) == strlen($fop->buffer))))
                    $fop->buffer = $sb . "\n" . $this->_getTabStop($options);
            }

            if (($options->heredocFlag) || ($id == T_END_HEREDOC)) {
                $fop->buffer .= $value;
                return true;
            }
            switch ($value) {
                case '=':
                    if (is_null($this->top_expression) && !$options->flagOptions->ignoreDependency) {
                        $this->top_expression = $fop;
                        $fop->buffer = rtrim($fop->buffer) . " = ";
                        if (count($fop->dependOn) > 0) {
                            $this->_replaceGetterArgs($fop->dependOn, $fop->buffer);
                            $fop->args_replaced = true;
                        }
                        $this->_readExpression($options, $value);
                        $options->flagOptions->split = 1;
                        return true;
                    }
                    break;
                case '}':
                    // + | expression auto depth 
                    // igk_wln_e(__FILE__.":".__LINE__,  $options->skipWhiteSpace);
                    if ($options->skipWhiteSpace)
                        $fop->buffer = rtrim($fop->buffer) . "\n" . str_repeat($this->tab_stop, $options->bracketDepth);
                    break;
                case ';':
                    if ($fop->rtrim) {
                        $fop->buffer = rtrim($fop->buffer);
                    }
                    break;               
            }
        }
        return parent::_handleReadExpression($options, $id, $value);
    }

    private function _replaceGetterArgs($depend, &$buffer)
    {
        foreach (array_keys($depend) as $k) {
            $buffer = str_replace(
                '$' . $k,
                ViewTokenizeArgConstants::GETTER_VAR . "['$k']",
                $buffer
            );
        }
    }
   
    #endregion

    #region GLOBAL_VARIABLE
    protected function _readSetterVariable(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $this->_pushFlag($options);
        $name = substr($value, 1);
        $options->flag = self::READ_SETTER_VARIABLE;
        $options->flagOptions = (object)[
            "name" => $name,
            "depth"=> $options->depth
        ];
        $options->skipWhiteSpace = true;
        return true;
    }
    protected function _handleSetterVariable(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $v_buffer = &$options->buffer;
        $name = $options->flagOptions->name;
        switch ($value) {
            case '(':
            case '[':
            case ')': // end setter var 
                $v = ViewTokenizeArgConstants::SETTER_VAR . '[\'' . $name . '\']';
                $v_buffer .= $v;
                $this->_endSetterVariable($options, $id, $value);
                if ($value != ')'){
                    $this->_readExpression($options, $value);
                } else {
                    if ($options->flag){
                        $this->_handleFlag($options, $id, $value);
                    }
                }
                return true;
                // return $this->_handleReadExpression($options, $id, $value);  
            case '=':
                # affectation detection
                if (!isset($this->m_init_variables[$name])) {
                    $v = ViewTokenizeArgConstants::SETTER_VAR . '[\'' . $name . '\'] = $' . $name;
                    $this->m_init_variables[$name] = 1;
                } else {
                    $v = '$' . $name;
                }
                $v_buffer .= $v;

                $this->_endSetterVariable($options, $id, $value);
                // + | expect expression
                $this->_readExpression($options, '=');
                return $this->_handleReadExpression($options, $id, $value);
            default:
                // + affectation 
                $sp = ' ';
                if (in_array($value, explode(',', self::OPERATOR_SYMBOL))) {
                    switch ($value) {
                        case '->':
                        case '(':
                        case '[':
                            $sp = '';
                            break;
                    }
                    $op = $sp . $value . $sp; 
                    // + | Important cause the variable detection to build view                         
                    $v = ViewTokenizeArgConstants::SETTER_VAR . '[\'' . $name . '\']' . $op;
                    
                    $v_buffer .= $v;
                    $this->_endSetterVariable($options, $id, $value);
                   
                    // + | expect expression
                    $this->_readExpression($options, ":var");
                    return true;
                }
                break;
        }
        return true;
    }
    protected function _endSetterVariable(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $name = $options->flagOptions->name;
        if (!isset($this->variables[$name])) {
            $this->variables[$name] = null;
        }
        $this->_popFlag($options);
        $options->skipWhiteSpace = true;
        return true;
    }
    #endregion

    #region BLOCK

    protected static function IsBlockCase($id): bool
    {
        switch ($id) {
            case T_IF:
            case T_ELSE:
            case T_ELSEIF:
            case T_FOR:
            case T_FOREACH:
            case T_WHILE:
            case T_DO:
            case T_TRY:
            case T_CATCH:
            case T_FINALLY:
            case T_SWITCH:
            case T_CASE:
            case T_DEFAULT:
                return true;
                break;
        }
        return false;
    }
    protected function _handleExpectContainer(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $c = trim($value);
        if (!empty($c) && self::IsBlockCase($id)) {
            $this->_popFlag($options);
            return false;
        }
        if (!empty($c)) {
            array_pop($this->m_containers);
            $this->_popFlag($options);
            if ($options->flag) {
                $this->_handleFlag($options, $id, $value);
            }
            return false;
        }
        return true;
    }
    protected function _readBlock(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $this->_pushFlag($options);
        $block = new ViewCompilerBockInfo($value);
        $this->_attacheBlock($block, $options, $id, $value);
        return true;
    }
    protected function _handleReadCaseBlock(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        throw new NotImplementException(__METHOD__);
        // $v_block = $this->m_block;
        // $v_flag = $options->flagOptions;
        // $v_buffer = &$v_flag->buffer;
        // if ($v_flag->condition) {
        //     switch ($value) {
        //         case ':':
        //             $v_flag->condition = false;
        //             $v_block->condition = trim($v_buffer);
        //             $v_buffer = "";
        //             break;
        //     }
        // } else {
        //     if (self::IsBlockCase($id)) {
        //         return false;
        //     }
        //     $v_buffer .= $value;
        // }
        // return true;
    }

    protected function _handleReadBlock(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $v_block = $this->m_block;
        $v_flag = $options->flagOptions;
        if (!$v_block) {
            igk_die("handle block error");
        }
        $v_buffer = &$v_flag->buffer;
        switch ($id) {
            case T_COMMENT:
            case T_DOC_COMMENT:            
                return false;
            default:
                if (self::IsBlockCase($id)) {
                    return false;
                }
                break;
        }
        if ($v_flag->condition) {
            // read condition
            $v_add = ($v_flag->depth + 1) < $options->depth;
            switch ($value) {
                case '(':
                    if ($v_add)
                        $v_buffer .= $value;
                    $options->skipWhiteSpace = true;
                    break;
                case ')':
                    if ($v_flag->depth < $options->depth)
                        $v_buffer .= $value;
                    else {
                        $v_flag->condition = false;
                        $v_block->condition = $v_flag->out_condition =  trim($v_buffer);
                        $v_buffer = "";
                    }
                    $options->skipWhiteSpace = true;
                    break;
                default:
                    $v_buffer .= $value;
                    break;
            }
        } else {
            if ($id == T_VARIABLE) {
                $this->_readSetterVariable($options, $id, $value);
                if ($options->heredocFlag) {
                    $this->_endSetterVariable($options, $id, $value);
                    $v_buffer .= $value;
                }
                return true;
            }
            switch ($id) {
                case T_ENDFOR:
                case T_ENDWHILE:
                case T_ENDIF:
                case T_ENDSWITCH:
                    $endtag = "end" . $v_flag->type;
                    // igk_debug_wln_e(__FILE__ . ":" . __LINE__, "end litteral", $endtag, $v_flag);
                    if (($endtag == $value) && ($v_flag->litteral)) {
                        // match expected litteral
                        $this->_endReadBlock($options, $id, $value);
                        return true;
                    }
                    break;
                case T_STRING:
                case T_ECHO:
                    // leave to top handler
                    return false;
            }
            // read code
            switch ($value) {
                case ':':
                    $v_flag->litteral = true;
                    $v_flag->multicode = true;
                    return true;
                case '{':
                    $v_flag->multicode = true;
                    if ($options->curl_open) {
                        $v_buffer .= $value;
                    }
                    break;
                case '}':
                    if ($options->close_curl) {
                        $v_buffer .= $value;
                    }
                    if (($v_flag->depth == $options->depth) & ($v_flag->multicode)) {
                        $this->_endReadBlock($options, $id, $value);
                        // igk_wln_e("multi code end", $v_buffer, $options->buffers);
                    }
                    break;
                case ';':
                    // append instruction to block
                    if (!empty($v_buffer)) {
                        $v_block->blocks[] = trim($v_buffer, self::BLOCK_TRIM_CHAR) . $value;
                    }
                    $v_block->buffer = "";
                    $v_buffer = "";
                    if (!$v_flag->multicode) {
                        $this->_endReadBlock($options, $id, $value);
                    }
                    break;
                default:
                    if ((!$v_flag->multicode) && (!empty($value))) {
                        return false;
                    }
                    $v_buffer .= $value;
                    break;
            }
        }
        return true;
    }
    protected function _endReadBlock(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $code = $this->m_block;
        $code->close();
        if (is_null($code->parent) && ($this->top_expression)) {
            igk_die("parent null but top_expression defined");
        }
        $this->m_block = $code->parent;
        $this->_popBuffer($options, $options->flagOptions->buffer);
        $this->_popFlag($options);
        if ($code->isChildContainer()) {
            $this->_pushFlag($options);
            $options->flag = self::READ_EXPECT_BLOCK_CONTAINER;
            $options->flagOptions = null;
        }
        return true;
    }
    #endregion

    /**
     * 
     * @param array $files 
     * @return null|string 
     */
    public function compile(array $files): ?string
    {
        $this->m_output = false;
        foreach ($files as $f) {
            if (is_file($f))
                $this->compileFile($f);
        }
        $this->m_output = true;
        return $this->mergeSourceCode();
    }
    /**
     * merge source script
     * @return null|string 
     */
    public function mergeSourceCode($header = false): ?string
    {
        if (!$this->m_output) return null;
        if ($this->documentBuild) {
            $doc = IGKHtmlDoc::CreateDocument("compiled-document");

            return $doc->render();
        }
        return parent::mergeSourceCode($header);
    }
}
