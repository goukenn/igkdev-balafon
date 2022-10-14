<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonViewCompilerUtility.php
// @date: 20221011 11:59:17
namespace IGK\System\Runtime\Compiler;

use IGK\Controllers\SysDbController;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Runtime\Compiler\Html\CompilerNodeModifyDetector;
use IGK\System\Runtime\Compiler\ReadBlockInstructionInfo;
use IGK\System\ViewEnvironmentArgs;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Runtime\Compiler
 */
abstract class BalafonViewCompilerUtility
{

    const VAR_READ = "VAR_READ";
    const CHANGE_BUFFER = "CHANGE_BUFFER";
    const RESTORE_BUFFER = "RESTORE_BUFFER";
    const VAR_OPERATOR = '.,+,-,--,++,|,||,*,/,%,&,&&,^,==,===,!=,!===,~';

    private static $DEBUG= 0;
    /**
     * check block block modification
     * @param string $source 
     * @param null|ViewEnvironmentArgs $args 
     * @param bool $checkBuffer 
     * @return bool 
     * @throws EnvironmentArrayException 
     */
    public static function CheckBlockModify(string $source, ?ViewEnvironmentArgs $args = null, bool $checkBuffer = false): bool
    {
        if (is_null($args)) {
            $args = new ViewEnvironmentArgs;
            $args->ctrl = SysDbController::ctrl();
            $args->t = igk_create_node('div');
            $args->t["class"] = "ckeck-block";
        }
        CompilerNodeModifyDetector::Init();
        $t = new CompilerNodeModifyDetector();
        if (strpos($source, "<?php") !== 0) {
            $source = "<?php\n" . $source;
        }
        $args->t = $t;
        $buffer = self::EvalSourceArgs($source, $args);
        $g = CompilerNodeModifyDetector::SysModify() || ($checkBuffer && !empty($buffer));
        CompilerNodeModifyDetector::UnInit();
        return $g;
    }
    /**
     * evaluate source args
     * @param string $source 
     * @param null|ViewEnvironmentArgs $args 
     * @param null|array $variables 
     * @return mixed 
     * @throws EnvironmentArrayException 
     */
    public static function EvalSourceArgs(string $source, ?ViewEnvironmentArgs $args = null, ?array &$variables = null)
    {
        if (is_null($args)) {
            $args = new ViewEnvironmentArgs;
        }
        require_once __DIR__ . '/helper-functions.php';
        $std_variable = new \stdClass();
        $std_variable->variables = $variables ?? [];
        igk_environment()->push(ViewEnvironmentArgs::class . "/compiler_args", $args);
        igk_environment()->push(ViewEnvironmentArgs::class . "/compiler_variables", $std_variable);
        $fc = \Closure::fromCallable(function () {
            ob_start();
            extract(func_get_arg(1));
            foreach (igk_environment()->peek(ViewEnvironmentArgs::class . "/compiler_variables")->variables as $k => $v) {
                if (isset($$k)) continue;
                $$k = &igk_environment()->peek(ViewEnvironmentArgs::class . "/compiler_variables")->variables[$k];
            }
            eval("?>" . func_get_arg(0));
            $c = ob_get_contents();
            ob_end_clean();
            return $c;
        })->bindTo($args->ctrl);
        $o = $fc($source, (array)$args);
        igk_environment()->pop(ViewEnvironmentArgs::class . "/compiler_args");
        igk_environment()->pop(ViewEnvironmentArgs::class . "/compiler_variables");
        return $o;
    }


    /**
     * php code source instruction list info list
     * @param string $code php code instruction
     * @param bool $root root code block
     * @param ?IBalafonViewCompiler $compiler
     * @return bool|array 
     */
    public static function GetInstructionsList(string $code, $root = true, IBalafonViewCompiler &$compiler = null)
    {
        $v_tab = [];
        if (strpos($code, "<?php") !== 0) {
            $code = "<?php\n" . $code;
        }
        $v_with = false;
        $fc_append_instruct = function (&$v_instruction, &$v_tab, $value, $v_with, ReadBlockOptions $options)  {
            if ($v_with) {
                $v_instruction .= $value;
                $blockinfo = $options->blockInfo;
                if ($blockinfo) {
                    if ($blockinfo->canAppendCode()) {
                        if (!empty($blockinfo->code) || !empty(trim($value))) {
                            $blockinfo->code .= $value;
                            if (!$options->buffering){
                                $blockinfo->to_compile_code_buffer .= $value; 
                            } else {
                                // igk_debug_wln("--------------------------------------add to buffer list ......");
                                $options->bufferLists["block_info_buffer_list"] = & $blockinfo->to_compile_code_buffer;
                            }
                        }
                    } else {
                        if (!$blockinfo->nameSupport && $blockinfo->endConditionFlag) {
                            if (!empty($blockinfo->condition) || !empty(trim($value)))
                                $blockinfo->condition .= $value;
                        }
                    }
                }

                switch ($value) {
                    case ";":
                        self::_InstructionAddCodeBlock($options, $v_tab, $v_instruction);
                        break;
                    case '(':
                        $options->expressionConditionDepth++;
                        if ($blf = $options->blockInfo) {
                            // end name reading if name support
                            if ($blf->nameSupport) {
                                $blf->nameSupport = false;
                            } 
                            // mark condition/argument read read start -                        
                            if ($blf->endConditionFlag && is_null($blf->endConditionDepth)) {
                                $blf->endConditionDepth = $options->expressionConditionDepth-1;                                                              
                            }
                            // check if still reading condition
                            if ($blf->isReadingCondition()){
                                // igk_debug_wln(__FILE__.":".__LINE__, "---------------------------read condition... ");
                                break;
                            }

                        }
                        if ($options->expressionConditionDepth == 1) {
                            $exblock = new ReadBlockExpressionInfo;
                            $options->expressionBuffer = &$exblock->buffer;
                            $options->expressionBuffer = $value;
                            $options->flag = self::CHANGE_BUFFER;
                            $options->expressions[] = $exblock; 
                            // $v_instruction .= "java";
                        }
                        break;
                    case ')':
                        $options->expressionConditionDepth--;
                        if ($blf = $options->blockInfo) {
                            if ($blf->endConditionFlag) {
                                if ($blf->endConditionDepth ==  $options->expressionConditionDepth) {
                                    $blf->endConditionFlag = false;
                                }
                                // igk_debug_wln(__FILE__.":".__LINE__, "---------------close but continue read condition : ?[".
                                // $blf->endConditionFlag
                                // ."] ");
                                break;
                            }
                        }
                       // igk_wln_e("finisht---------------close but continue read condition ");
                        if ($options->expressionConditionDepth === 0) {
                            self::_InstructionEndExpresion($options);                           
                        }
                        break;
                    case "{":
                        $options->depth++;
                        if ($blockinfo) {
                            $blockinfo->blockFlag = true;
                        }
                        break;
                    case "}":
                        $options->depth--;
                        if ($options->blockInfo) {
                            if ($options->blockInfo->type == "function") {
                                // must end with }; or })(
                                if (empty($options->blockInfo->name)) {
                                    $options->nextRequest = [";", ")"];
                                    $options->appendNext = ";";
                                }
                            }
                            if ($options->blockInfo->depth == $options->depth) {
                                // END BLOCK READ:
                                self::_InstructionEndBlockRead($options);
                            }
                        }
                        break;
                }
            }
        };
        $ctab = token_get_all($code);
        $lvalue = "";
        $ltoken = null;
        $options = new ReadBlockOptions();
        if ($compiler) {
            $options->compiler = $compiler;
        }
        $flag = &$options->flag;
        $v_buffer = &$options->buffer;



        while (count($ctab) > 0) {
            $value = array_shift($ctab);
            $id = null;
            if (is_array($value)) {
                $id = $value[0];
                $value = $value[1];
            }
            igk_debug_wln("token:" . ($id ? token_name($id) : "::") . ":" . $value);

            //checking for value buffer
            if ($flag == self::CHANGE_BUFFER) {
                $v_buffer = &$options->expressionBuffer;
                $options->buffering = true;
                $flag = null;
            }
            // restore buffer
            if ($flag == self::RESTORE_BUFFER) {
                $v_buffer = &$options->buffer; 
                $options->buffering = null; 
                $flag = null;
                // igk_debug_wln(__FILE__.":".__LINE__,  "------------------------------------restoring buffer", $v_buffer);
            }
            if ($flag == 'NS_READ') {
                switch ($id) {
                    case T_NAME_QUALIFIED:
                        $options->namespace = $value;
                        break;
                    default:
                        switch ($value) {
                            case ";":
                                $v_tab[] = (object)["value" => sprintf("namespace %s;", $options->namespace)];
                                $flag = null;
                                break;
                            case "{":
                                $options->blockInfo = new ReadBlockInstructionInfo("namespace");
                                $options->blockInfo->depth  = $options->depth;
                                $options->blocks[] = $options->blockInfo;
                                $flag = null;
                                break;
                        }
                }
                continue;
            }

            if ($flag == self::VAR_READ) {
                $append_value = false;
                $add_express = ($id == T_ENCAPSED_AND_WHITESPACE) || ('"' == $value);
                if ($add_express) {
                    $flag = null;
                    $x = sprintf('%s', $options->flagOption->express);
                    $fc_append_instruct($v_buffer, $v_tab, $x, $v_with, $options);
                    $options->flagOption = null;
                    $append_value = true;
                } else {
                    switch ($value) {
                        case "=":
                            // leave the value write context
                            $flag = null;
                            $v_x = sprintf('$%s ', $options->flagOption->name);
                            $fc_append_instruct($v_buffer, $v_tab, $v_x, $v_with, $options);
                            $options->flagOption = null;
                            $append_value = true;
                            // igk_wln_e("data: ", $v_instruction, $value);
                            break;
                            //    igk_wln_e("concating after....");
                            // break;
                        case ".":
                            igk_wln_e("concate found");
                            break;
                        case "(":
                        case "->":
                        case "?":
                        case ":":
                        case ";":
                        case "++":
                        case "--":
                        case "+":
                        case "%":
                        case ")":
                            $flag = null;
                            $x = "";
                            // igk_debug_wln("concat: ?????????\n", $options->flagOption);
                            if ($options->flagOption->contact) {
                                $x = sprintf('%s', $options->flagOption->express);
                            } else {
                                $x = sprintf('$%s', $options->flagOption->name);
                            }
                            $fc_append_instruct($v_buffer, $v_tab, $x, $v_with, $options);
                            $options->flagOption = null;
                            $append_value = true;
                            break;
                        default:
                            switch ($id) {
                                case T_WHITESPACE:
                                    $options->flagOption->space = 1;
                                    break;
                                default:
                                    $flag = null;
                                    $x = sprintf('$%s', $options->flagOption->name);
                                    $fc_append_instruct($v_buffer, $v_tab, $x, $v_with, $options);
                                    $options->flagOption = null;
                                    $append_value = true;
                                    break;
                            }
                            break;
                    }
                }
                if ($append_value) {
                    $fc_append_instruct($v_buffer, $v_tab, $value, $v_with, $options);
                }
                continue;
            }

            self::_InstructionListNextRequestHandle($options, $value, $v_buffer);

            switch ($id) {
                case T_NAMESPACE:
                    if (!empty($options->namespace))
                        return false;
                    $options->namespace = true;
                    $flag = 'NS_READ';
                    break;
                case T_CLASS:
                case T_INTERFACE;
                case T_TRAIT:
                    return false;
                case T_ABSTRACT:
                case T_PRIVATE:
                case T_PROTECTED:
                case T_FINAL:
                case T_PUBLIC:
                    // modifier
                    $options->modifiers[] = $value;
                    break;
                case T_SWITCH:
                case T_FOR:
                case T_TRY:
                case T_FOREACH:
                case T_WHILE:
                case T_IF:
                case T_ELSE:
                case T_ELSEIF:
                    self::_InstructCompileBlock($options, $compiler);
                    // start with try/instruction
                    $fc_append_instruct($v_buffer, $v_tab, $value, $v_with, $options);
                    $options->blockInfo = new ReadBlockInstructionInfo($value);
                    $options->blockInfo->depth  = $options->depth;
                    $options->blocks[] = $options->blockInfo;
                    if ($options->modifiers) {
                        $options->modifiers = [];
                    }
                    break;
                case T_CATCH:
                case T_FINALLY:
                case T_FUNCTION:
                case T_FUNC_C:
                    if ($root && empty(trim($v_buffer))) {
                        return false;
                    }
                    $fc_append_instruct($v_buffer, $v_tab, $value, $v_with, $options);
                    $options->blockInfo = new ReadBlockInstructionInfo($value);
                    $options->blockInfo->depth = $options->depth;
                    $options->blocks[] = $options->blockInfo;
                    if ($options->modifiers) {
                        $options->blockInfo->modifiers = $options->modifiers;
                        $options->modifiers = [];
                    }
                    break;
                case T_STRING:
                    $binfo = $options->blockInfo;
                    if ($binfo) {
                        if ($binfo->nameSupport) {
                            $binfo->name = $value;
                            $binfo->nameSupport = null;
                        }
                    }
                    $fc_append_instruct($v_buffer, $v_tab, $value, $v_with, $options);
                    break;
                case T_OPEN_TAG:
                    if ($v_with) {
                        $fc_append_instruct($v_buffer, $v_tab, $value, $v_with, $options);
                    }
                    $v_with = true;
                    break;
                case T_VARIABLE:
                    $name = substr($value, 1);
                    if ($compiler) {
                        if (!isset($compiler->variables[$name])) {
                            $compiler->variables[$name] = null;
                        }
                    }
                    $_bracket = (!$lvalue != '.') && (($lvalue == '"') || in_array($ltoken, [T_ENCAPSED_AND_WHITESPACE]));
                    if ($_bracket || ($ltoken == T_CURLY_OPEN)) {

                        $value = "\$___IGK_PHP_EXPRESS_VAR___('" . $name . "')";
                        if ($_bracket) {
                            $value = sprintf('{%s}', $value);
                        }
                    } else {
                        if (!empty($options->expressions)) {
                            $options->expressions[0]->detectVars[$name] = $options->flagOption;
                        } else {
                            $options->flag = self::VAR_READ;
                            $options->flagOption = (object)[
                                "name" => $name,
                                "express" => "igk_express_var('" . $name . "')",
                                "encapsed_express" => "\$___IGK_PHP_EXPRESS_VAR___('" . $name . "')",
                                "contact" => in_array($lvalue, explode(',', self::VAR_OPERATOR)),
                            ];
                            $value = "";
                        }
                    }
                    $fc_append_instruct($v_buffer, $v_tab, $value, $v_with, $options);
                    break;
                case T_BREAK:
                case T_CONTINUE:
                default:
                    $fc_append_instruct($v_buffer, $v_tab, $value, $v_with, $options);
                    break;
            }
            // last real value 
            if (!empty(trim($value)))
                $lvalue = $value;
            if ($id)
                $ltoken = $id;
        }

        if (!empty($g  = trim($v_buffer))) {
            if (preg_match("/[\};]$/", $g)) {
                if ($options->nextRequest) {
                    $v_buffer = rtrim($v_buffer) . ";";
                    $options->nextRequest = null;
                }
                // self::_InstructionListNextRequestHandle($options, $g);
                $v_tab[] = (object)["value" => trim($v_buffer)];
                $v_buffer = "";
            } else {
                error_log("possibility of missing data");
                igk_wln_e("last : $lvalue \ntoken: $ltoken : " . token_name($ltoken) . "\n" . $v_buffer);
            }
        }

        return $v_tab;
    }
    /**
     * add code block.
     * @param ReadBlockOptions $options 
     * @param array $v_tab 
     * @param string $v_instruction 
     * @return void 
     */
    private static function _InstructionAddCodeBlock(ReadBlockOptions $options, &$v_tab, &$v_instruction)
    {
        $g = trim($v_instruction);
        // igk_debug_wln(__FILE__.":".__LINE__, "------------------add instruction block", $g,
            //        $options->blockInfo->to_compile_code_buffer);
        if (!empty($g)) {
            if (!$options->blockInfo) {
                $v_block = (object)["value" => $g];
                $v_tab[] = $v_block;
                $v_instruction = "";
            }
        }
        if ($options->blockInfo) {
            $g = ltrim($options->blockInfo->to_compile_code_buffer, "{");
            $g = rtrim($g, "}");
            if (!empty($g = trim($g))) {
                $v_block = (object)["value" => $g];
                $options->blockInfo->codeBlocks[] = $v_block;
            }
            $options->blockInfo->to_compile_code_buffer = "";
        }
    }
    private static function _InstructionListNextRequestHandle(ReadBlockOptions $options, $value, string &$v_instruction)
    {
        if ($options->nextRequest && !empty($c = trim($value))) {
            if (!in_array($c, $options->nextRequest)) {
                if ($options->appendNext) {
                    $v_instruction = rtrim($v_instruction) . $options->appendNext;
                    $options->appendNext = null;
                } else {
                    igk_die(sprintf(
                        "expected '%s' but %s found.",
                        implode("' or '", $options->nextRequest),
                        $value
                    ));
                }
            }
            $options->nextRequest = null;
        }
    }
    private static function _InstructionEndBlockRead(ReadBlockOptions $options)
    {
        $blockinfo = array_pop($options->blocks);
        if ($blockinfo) {
            self::_InstructCompileBlock($options);
            // igk_debug_wln_e("the first block ", $blockinfo);
        }

        if (($c = count($options->blocks)) > 0) {
            $options->blockInfo = $options->blocks[$c - 1];
        } else {
            $options->blockInfo = null;
        }
    }
    /**
     * compile block
     * @param ReadBlockOptions $options 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _InstructCompileBlock(ReadBlockOptions $options)
    {
        $listener = $options->compiler;
        if ($options->blockInfo && $listener) {
            $result = $listener->compile($options->blockInfo->codeBlocks);
            igk_debug_wln_e(
                __FILE__.":".__LINE__, 
                "to compile: ", $options->blockInfo, 
                "result: ", 
                $result,
            );
            $options->blockInfo->compile_result .= $result;
            $options->blockInfo->to_compile_code_buffer = "";
        }
    }

    private static function _InstructionEndExpresion(ReadBlockOptions $options){
        $options->flag = self::RESTORE_BUFFER;
        $exblock = array_pop($options->expressions);
        if (!empty($exblock->detectVars)){
            // check if is method - request
            if (preg_match("/".IGK_IDENTIFIER_PATTERN."\s*\($/", $options->buffer))
            {
                foreach(array_keys($exblock->detectVars) as $n){
                    $exblock->buffer = str_replace('$'.$n, 'igk_express_arg("'.$n.'")', $exblock->buffer);
                }
                $exblock->buffer = ltrim($exblock->buffer,"(");
                $options->buffer .=  $exblock->buffer;
                self::_InstructAppendBuffering($options, $exblock->buffer);
                unset($options->expressionBuffer); // = null;
                $options->expressionBuffer = "";
                return;
            }
            // + | variable detected in expression in expression
            // igk_debug_wln(__FILE__.":".__LINE__,  "----------------------------variable detected", 
            //     "optionbuffer", $options->buffer );

            foreach(array_keys($exblock->detectVars) as $n){
                $exblock->buffer = str_replace('$'.$n, 'igk_express_var("'.$n.'")', $exblock->buffer);
            }


            $exblock->buffer = rtrim(ltrim($exblock->buffer , "("),")");
            $tmp_buffer = "igk_express_eval('" . $exblock->buffer . "'))";
            $options->buffer .= $tmp_buffer; 
            self::_InstructAppendBuffering($options, $tmp_buffer); 
        } else {
            // no var detected just leave at its
            $options->buffer .= $tmp_buffer = ltrim($exblock->buffer,"("); 
            self::_InstructAppendBuffering($options, $tmp_buffer);
        }
       
        unset($options->expressionBuffer); // = null;
        $options->expressionBuffer = "";
       
    }

    private static function _InstructAppendBuffering(ReadBlockOptions $options, string $buffering){
        // reference important to update the buffer list with 
        foreach($options->bufferLists as & $k){
            $k.= $buffering;
        }
        // igk_wln_e(__FILE__.":".__LINE__, " ---------------------------------- restore buffer list ", $k, $options->bufferLists );
        $options->bufferLists = [];
    } 
}
