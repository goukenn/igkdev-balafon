<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewTokenizeCompiler.php
// @date: 20221021 08:43:40
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use IGK\System\Runtime\Compiler\CompilerFlagState;
use IGK\System\Runtime\Compiler\IReadTokenOptions;
use IGK\System\Runtime\Compiler\ReadTokenOptions;
use IGK\System\Runtime\Compiler\ReadTokenStructInfo;
use IGK\System\Runtime\Compiler\TokenCompilerBase;
use IGK\System\Runtime\Compiler\Traits\CompilerTokenBracketTrait;
use IGK\System\Runtime\Compiler\Traits\CompilerTokenCommentHandlerTrait;
use IGK\System\Runtime\Compiler\Traits\CompilerTokenEntryTrait;
use IGK\System\Runtime\Compiler\Traits\CompilerTokenReadStructHandlerTrait;
use IGK\System\Runtime\Compiler\Traits\CompilerTokenStateBufferTrait;

///<summary></summary>
/**
 * 
 * @package IGK\System\Runtime\Compiler\ViewCompiler
 */
class ViewTokenizeCompiler  extends TokenCompilerBase
{
    use CompilerTokenEntryTrait;
    use CompilerTokenStateBufferTrait;
    use CompilerTokenBracketTrait;
    use CompilerTokenReadStructHandlerTrait;
    use CompilerTokenCommentHandlerTrait;
    /**
     * 
     * @var ViewTokenizeOptions
     */
    private $m_tokenOptions;

    var $converter;

    private $m_flags = [];

    private $m_clodeblock = [];

    public function __construct()
    {
        $this->m_tokenOptions = new ViewTokenizeOptions();
    }
    protected function _handleWhiteSpace($options, $id, $value){
        
    }
    public function HandleToken(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $_opt = $this->m_tokenOptions;
        $buffer = &$_opt->buffer;
        $flag = &$_opt->flag;
        $flagOptions = &$_opt->flagOptions;       

        if ($id == T_WHITESPACE) {
            if ($_opt->skipWhiteSpace) {
                $value = '';
            } else {
                $value = strpos($value, "\n") !== false ? "\n" : ' ';
                $_opt->skipWhiteSpace = 1;
            }
        } else if (!empty($value) && $_opt->skipWhiteSpace) {
            $_opt->skipWhiteSpace = 0;
        }

        $this->_checkBracket($_opt, $value);

        if ($flag) {
            $_opt->options = $options;
            if ($this->_handleFlag($_opt, $id, $value)) {
                return true;
            }
        }

        switch ($id) {
            case T_OPEN_TAG:
                //ignore open tag
                if ($_opt->start) {
                    $buffer .= $value;
                }
                $_opt->start = 1;
                break;
            case T_EXIT:
                break;
            case T_RETURN:
                break;
            case T_VARIABLE:
                $this->_readTokenVariable($_opt, $value);
                break;
            case T_FUNCTION:
                // expect anonymous function on expression
                if ($flag == CompilerFlagState::READ_EXPRESSION) {
                    // change flag options
                    $flagOptions = [
                        "flag" => $flagOptions,
                        "parent" => $flag,
                        "depth" => $_opt->depth
                    ];
                    $flag = CompilerFlagState::READ_SKIP_BLOCK;
                    $buffer .= $value;
                } else {
                    igk_wln_e(__FILE__ . ":" . __LINE__, "function declared not in expression");
                }
                break;
            case T_ABSTRACT:
            case T_GLOBAL:
            case T_CONST:
            case T_FINAL:
            case T_PRIVATE:
            case T_PROTECTED:
            case T_STATIC:
                $_opt->modifiers[] = $value;
                break;
            case T_CLASS:
            case T_INTERFACE:
            case T_TRAIT:
                $v_struct_info = new ReadTokenStructInfo($value);
                $v_struct_info->comment = $_opt->comment;
                $v_struct_info->modifiers = $_opt->modifiers;
                $v_struct_info->parent = $_opt->struct_info;
                $v_struct_info->depth = $_opt->depth;
                $this->_resetCommentAndModifier($_opt);
                $this->_pushFlag($_opt);
                $flag = $id == T_CLASS ? CompilerFlagState::READ_CLASS : CompilerFlagState::READ_STRUCT;
                // add struct to dependency
                if ($_opt->block){
                    $_opt->block->structs[$value][] = $v_struct_info;    
                }else{
                    $_opt->structs[$value][] = $v_struct_info;
                }
                $_opt->flagOptions = ["op" => "name"];
                $_opt->struct_info = $v_struct_info;
                break;
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
                $this->_pushFlag($_opt);
                $newBlock = new ViewTokenBlock($value);
                $newBlock->parent = $_opt->block;
                $newBlock->depth = $_opt->depth;
                $newBlock->buffer = "";
                $newBlock->tabstop  = $this->converter->tabstop;
                if ($newBlock->parent){
                    $newBlock->parent->blocks[] = $newBlock;
                }
                $flag = CompilerFlagState::READ_BLOCK;
                $flagOptions = [
                    "op" => "start",
                    "multicode" => false,
                ];
                $_opt->block = $newBlock;
                $this->pushBuffer($_opt, $newBlock->buffer,'block');
                $_opt->skipWhiteSpace = 1;
                break;
            default:
                switch ($value) {
                    case '=':
                        if ($flag == CompilerFlagState::READ_VARIABLE) {
                            $n = $flagOptions["name"];
                            $buffer .= sprintf(
                                ViewTokenizeArgConstants::SETTER_VAR . '[\'%s\'] = $%s ',
                                $n,
                                $n
                            );
                            $this->_popFlag($_opt);
                            // $flag = igk_getv($flagOptions, "flag");
                            // $flagOptions = null;
                            $buffer .= $value;
                            $_opt->skipWhiteSpace = 0;

                            $this->_pushFlag($_opt);
                            // go to read expression
                            $flag = CompilerFlagState::READ_EXPRESSION;
                            $flagOptions = new ViewTokenizeExpressionInfo;
                            $flagOptions->variables = &$_opt->variables;
                            $this->pushBuffer($_opt, $flagOptions->buffer, 'expression');
                        }
                        break;
                    case ';':
                        if ($flag == CompilerFlagState::READ_EXPRESSION) {
                            $this->popBuffer($_opt, 'expression');
                            $expression = $flagOptions->buffer;
                            if (count($flagOptions->dependOn) > 0) {
                                if ($flagOptions->op) {
                                    $_opt->buffer .= " " . ViewTokenizeArgConstants::EXPRESSION . '[igk_express_eval(' .
                                        escapeshellarg(ltrim($expression)) . ')];';
                                } else {
                                    $n = array_keys($flagOptions->dependOn)[0];
                                    $_opt->buffer .= " " . ViewTokenizeArgConstants::GETTER_VAR . '[' .
                                        escapeshellarg(ltrim($n)) . '];';
                                }
                            } else {
                                $expression .= $value;
                                $_opt->buffer .= $expression;
                            }
                            $this->_popFlag($_opt);
                            if ($flag) {
                                // $_opt->buffer =  rtrim($_opt->buffer, ';');
                                if ($this->_handleFlag($_opt, $id, $value)) {
                                    return true;
                                }
                            }
                            // $flagOptions = null;
                            // $flag = null;
                        } else {
                            $buffer .= $value;
                        }
                        break;
                    default:
                        $buffer .= $value;
                        break;
                }
                break;
        }
        return true;
    }

    private function _handleFlag(ViewTokenizeOptions $option, $id, $value)
    {
        $flag = &$option->flag;
        $flagOptions = &$option->flagOptions;
        if ($flag == CompilerFlagState::READ_EXPRESSION) {
            if (!empty(trim($value)) && ($value != ';') && ($id != T_VARIABLE)) {
                $flagOptions->op = true;
            }
        };

        if ($flag == CompilerFlagState::READ_SKIP_BLOCK) {
            $option->buffer .= $value;
            if (($value == '}') && ($flagOptions["depth"] == $option->depth)) {
                $p = $flagOptions["parent"];
                $flagOptions = $flagOptions["flag"];
                $flag = $p;
            }
            return true;
        };

        if ($flag == CompilerFlagState::READ_BLOCK) {
            if ($this->_readTokenBlock($option, $id, $value)) {
                return true;
            }
        } 

         // + | read struct
         if ($flag == CompilerFlagState::READ_STRUCT) {
            return $this->handleReadClass($flag, $option, $id, $value);
        }
        // + | read class 
        if ($flag == CompilerFlagState::READ_CLASS) {
            return $this->handleReadClass($flag, $option, $id, $value);
        }
        // + | read class use 
        if ($flag == CompilerFlagState::READ_CLASS_USE) {
            return $this->handleGlobalUseFlag($flag, $option, $id, $value);
        }
    }
    public function compileSource(string $source): ?string
    {
        $this->parseToken($source);

        return $this->mergeSourceCode();
    }

    public function mergeSourceCode(): ?string
    {
        return $this->m_tokenOptions->output();
    }

    private function _readTokenBlock(ViewTokenizeOptions $options, $id, $value)
    {
        $v_block = $options->block;
        $v_flag = &$options->flag;
        $v_buffer = &$options->buffer;
        $v_flagOptions = &$options->flagOptions;

        switch ($value) {
            case '(':
                if ($v_flagOptions["op"] == 'start')
                    $v_flagOptions["op"] = "condition";
                else {
                    $v_buffer .= $value;
                }
                break;
            case ')':
                if (($v_flagOptions["op"] == "condition") && ($v_block->depth == $options->depth)) {
                    $v_block->conditions = trim($v_buffer);
                    $v_buffer = "";
                    $v_flagOptions["op"] = "code";
                }
                else 
                    $v_buffer .= $value;
                break;
            default:
                if ($v_flagOptions["op"] == 'code') {
                    // if ($id == T_VARIABLE) {
                    //     $this->_readTokenVariable($options, $value);
                    //     return true;
                    // }
                    switch ($value) {
                        case ';':
                            // in single
                            $code = trim($v_buffer);
                            if (substr($code, -1) != ';') {
                                $code .= ';';
                            }
                            if (!$v_flagOptions["multicode"]) {
                                $this->_popFlag($options);                                
                                $v_block->blocks[] = $code;
                                $v_buffer = "";
                                if (is_null($v_block->parent))
                                    $options->buffer .= $v_block->generateCode($options);                                
                                 
                                $options->block = $v_block->parent;
                                 
                            } else {
                                // + | for multiline
                                $v_block->blocks[] .= $code;
                                $v_buffer = "";
                            }
                            return true;
                        case '{':
                            if ($v_flagOptions["op"] == 'code') {
                                $v_flagOptions["multicode"] = 1;                             
                                return true;
                            }
                        case '}':
                            if ($v_block->depth == $options->depth) {
                                // end multi code
                                $this->_readTokenEndBlock($options, $id, $value);
                                return true;
                            }
                            break;
                    }
                    return false;
                } 
                if ($v_flagOptions["op"] != 'start')
                    $v_buffer .= $value;
                break;
        }
        return true;
    }
    private function _readTokenEndBlock(ViewTokenizeOptions $options, $id, $value)
    {
        $v_block = $options->block;
        $this->_popFlag($options);
        $this->popBuffer($options, 'block');
        $this->m_clodeblock[] = $v_block;
        if (is_null($v_block->parent)){
            if (empty($tbuff = trim($options->buffer))){
                $options->buffer = $tbuff;
            }
            $options->buffer .= ltrim($v_block->generateCode());
        }
        $options->block = $v_block->parent;
    }

    private function _readTokenVariable(ViewTokenizeOptions $options, $value)
    {
        $flag = &$options->flag;
        $flagOptions = &$options->flagOptions;
        $name = substr($value, 1);
        $buffer = &$options->buffer;
        if (!isset($options->variables[$name]))
            $options->variables[$name] = null;
        if ($flag == CompilerFlagState::READ_EXPRESSION) {
            $flagOptions->dependOn[$name] = null;
            $buffer .= $value;
        } else {
            $this->_pushFlag($options);
            $flagOptions = [
                "name" => $name,
            ];
            $flag = CompilerFlagState::READ_VARIABLE;
        }
        $options->skipWhiteSpace = 1;
    }

    private function _popFlag($options)
    {
        if ($q = array_pop($this->m_flags)) {
            $options->flag = $q["flag"];
            $options->flagOptions = $q["options"];
        } else {
            $options->flag = null;
            $options->flagOptions = null;
        }
    }
    private function _pushFlag($options)
    {
        array_push($this->m_flags, [
            "flag" => $options->flag,
            "options" => $options->flagOptions
        ]);
    }


    protected function handleReadClass(&$flag, IReadTokenOptions $options, $id, $value): bool
    {
        $struct = $options->struct_info;
        $flagOptions = & $options->flagOptions;
        if (!$struct)
        {
            return false;
        }
        switch ($id) {
            case T_STRING:
                if (!$struct->readCode) {
                    switch ($flagOptions["op"]) {
                        case 'extends':
                            $struct->extends = $value;
                            break;
                        case 'implement':
                            if (!$struct->implements) {
                                $struct->implements = [];
                            }
                            $struct->implements[$value] = $value;
                            break;
                        default:
                            $struct->name = $value;
                            break;
                    }
                }
                break;
            case T_EXTENDS:
                $flagOptions["op"] = 'extends';
                break;
            case T_IMPLEMENTS:
                $flagOptions["op"] = 'implement';
                break;
            default:
                if (!$struct->readCode){                
                    switch ($value) {
                        case '{':
                            $struct->readCode = true;
                            // start reading code 
                            // $options->flagOption = null;
                            // $flag = null;
                            $this->pushBuffer($options, $struct->buffer, 'class');
                            $struct->popBuffer = true;
                            break;
                    } 
                } else {
                    if (($value=="}") && ($struct->depth == $options->depth)){
                        $this->_popFlag($options);  
                        $struct = null;
                        return true;
                    }
                    return false;
                }
        }
        return true;
    }
}
