<?php
// @author: C.A.D. BONDJE DOUE
// @file: BalafonViewCompiler.php
// @date: 20220909 15:25:33
namespace IGK\System\Runtime\Compiler;

use IGK\Controllers\BaseController;
use IGK\Helper\StringUtility;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Exceptions\NotImplementException;
use IGK\System\IO\StringBuilder;
use IGK\System\Runtime\Compiler\Html\ConditionBlockNode;
use IGK\System\ViewEnvironmentArgs;
use IGK\System\ViewVarExpression;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Runtime\Compiler
 */
class BalafonViewCompiler
{ 
    /**
     * options passed to compilator
     * @var ?BalafonViewCompilerOptions
     */
    var $options = null;  
    /**
     * script to compile
     * @var mixed
     */
    var $source;
    /**
     * get last compilation result 
     * @var mixed
     */
    private $m_result;

    protected $m_compilerHandler;

    private $m_output;

    public function getLastResult()
    {
        return $this->m_result;
    }
    public function __construct()
    {
        $this->m_compilerHandler = $this->_createCompilerHandler();
    }
    protected function _createCompilerHandler()
    {
        return new BalafonViewCompilerHandler($this);
    }
    /**
     * compile current source
     * @param mixed $options 
     * @return false|BalafonViewCompilerResult 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     */
    public function compile($options)
    {
        if (empty($this->source)) {
            return false;
        }
        $this->options = $options;
        $this->m_result = static::CompileSource(
            $this->source,
            $this->options
        );
        // if ($this->m_result) {
        //     $this->_update_data($this->m_result);
        // }
        return $this->m_result;
    }
    public function compileBlock($blocks, $options)
    {
        
        $this->m_result = static::CompileSource(
            $this->source,
            $this->options
        ); 
        return $this->m_result;
    }
    /**
     * update source option data
     * @param BalafonViewCompilerResult $data 
     * @return void 
     */
    private static function _UpdateResult(BalafonViewCompilerResult $data)
    {
        return;

        if (($data->readOptions->sourceType == 'php') && strpos($data->source, "<?php") === 0) {
            $data->source = ltrim(substr($data->source, 5));
        }
        if ($data->readOptions->inPHPScript && (strrpos($data->source, "?>") !== false)) {
            $data->source = substr($data->source, 0, -2);
        }

        // treat data source from layout
        $sb = new StringBuilder;
        $sb->appendLine("<?php");
        if ($data->readOptions->namespace) {
            $sb->appendLine("");
            $sb->appendLine($data->readOptions->namespace);
            $sb->appendLine("");
        }
        if ($data->readOptions->usings) {
            ksort($data->readOptions->usings);
            $sb->appendLine("");
            foreach ($data->readOptions->usings as $m => $alias) {
                $sb->append("use " . $m);
                if ($m != $alias) {
                    $sb->append(" as " . $alias);
                }
                $sb->appendLine(";");
            }
            $sb->appendLine("");
        }


        if ($data->readOptions->classInsterfaceOrTrait) {

            foreach (["interface", "trait", "class"] as $m) {
                if (is_null($def = igk_getv($data->readOptions->classInsterfaceOrTrait, $m))) {
                    continue;
                }
                ksort($def);
                foreach ($def as $tm) {
                    $sb->appendLine($tm);
                }
            }
        }


        if ($data->readOptions->functions) {
            $g = $data->readOptions->functions;
            ksort($g);
            foreach ($g as $m => $f) {
                $sb->appendLine($f);
            }
        }

        $declaration = "" . $sb;
        // $src = "\$t->div()->Content = \"812 {\$___IGK_PHP_EXPRESS_VAR___('x')}\";";
        $src = $data->source;
        igk_debug(true);
        if (!empty($src)){
            $headers = trim(substr($declaration, 5));
            $code_instructions = [];
            if (!empty($headers)){
                $code_instructions[] = (object)["value"=>$headers];
            }
            $code_instructions[] = (object)["value"=>$src];
            // $code_instructions[] = (object)["value"=>'$x = 180;'];
            // $code_instructions[] = (object)["value"=>'$t->div()->Content = "Hello : {$___IGK_PHP_EXPRESS_VAR___(\'x\')}";'];
            $g = self::_CompileSourceCode(
                $code_instructions,
                null,
                $data->readOptions->detectVariables
            );
            $data->source = $g;
        }
        // igk_wln_e("the source: ", $g);


        // igk_wln_e("update source data .... ---", $declaration, "sb:" , "".$sb, 
        // "src:", $src,
        // "source:", $data->source);

        $sb->appendLine($data->source);
        $data->source = "" . $sb;
        if ($data->readOptions->exitDetected) {
            igk_debug_wln_e("exit detected - possibility - render directly or do response - :" . $data->readOptions->exitDetected);
        }
        // igk_debug_wln("before: ======================", $data->source, "detectVariables", $data->readOptions->detectVariables);
        // $_buffer = self::EvaluateCompiledSource($data->source, $this->controller, $this->args, $data->readOptions->detectVariables);
        // $_output = $this->args->t->render();

        // igk_wln_e(
        //     "inner eval ",
        //     $data->source,
        //     "buffer:",
        //     $_buffer,
        //     "declaration:",
        //     $declaration,
        //     "output:",
        //     $_output
        // );
    }
    /**
     * 
     * @param string $source 
     * @param array|object|BalafonViewCompilerOptions $options 
     * @return false|BalafonViewCompilerResult 
     */
    public static function CompileSource(string $source, $options = null)
    {
        $result = false;
        $g = new static;
        $g->options = $options;
        $_out_str = "";
        $g->m_output = &$_out_str;
        $tstart = igk_sys_request_time();
        $tokens = \token_get_all($source);
        $options = new BalafonViewCompilerReadOptions;
        $fc = function ($e) use (&$_out_str, &$result, $options, $g) {
            $g->tokenCompile($e, $_out_str, $result, $options);
        };
        while (!$result && (count($tokens) > 0)) {
            $q = array_shift($tokens);
            $fc($q);
        }
        
        $duration = igk_sys_request_time() - $tstart;
        if (!$result) {
            
            $tresult = new BalafonViewCompilerResult;
            $tresult->duration = $duration;
            $tresult->source = $_out_str;
            $tresult->readOptions = $options;  
            self::_UpdateResult($tresult); 
        }
        return $result;
    }

    /**
     * 
     * @param mixed $e 
     * @param mixed $_out_str 
     * @param mixed $result 
     * @param BalafonViewCompilerReadOptions $options 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function tokenCompile($e, &$_out_str, &$result, $options)
    {
        $skip = $options->skip_line;
        $scope = &$options->scopeFlag; // false;
        $using = &$options->usingFlag; // false; 
        if (!isset($options->usings)) {
            $options->usings = [];
        }
        if ($options->detectCloseTag && $options->inPHPScript) {
            $options->inPHPScript = false;
        }

        // priority to flag reading
        if ($options->flagReading) {
            if ($this->_read_flag($e, $_out_str, $result, $options)) {
                return;
            }
        }
        // priority to buffers read on variable 
        if (!empty($options->buffers) && ($options->varContext == "read")) {
            if ($this->_read_var_context($e, $options, $_out_str)) {
                return;
            }
        }
        // block reading
        if ($options->read_block_info) {
            if ($this->_read_block_context($e, $options, $_out_str)) {
                return;
            }
        }

        if (is_array($e)) {
            $id = $e[0];
            $value = $e[1];
            igk_debug_wln(token_name($id) . ":" . $value . "," . $skip);
            switch ($id) {
                case T_COMMENT:
                    $options->skip_line = 1;
                    if (preg_match("/#\s*{\{%(?P<expression>.+)\}\}/", $value, $data)) {
                        $this->evaluationComment(trim($data['expression']));
                    }
                    return;
                case T_VARIABLE:
                    // detect dynamic variable definition in read context
                    $l = 0;
                    $name = substr($value, 1);
                    if (!isset($options->detectVariables[$name])) {
                        $options->detectVariables[$name] = null;
                    }
                    if (($options->varContext == "read")) {
                        if ($options->lastTokenReadId == T_CURLY_OPEN){
                            $options->buffers .= '$___IGK_PHP_EXPRESS_VAR___(\'' . $name . '\')';    
                        }else{
                            $options->buffers .= 'igk_express_var("' . $name . '")';
                        }
                    } else {
                        $_out_str .= $value;
                    }                    
                    break;
                case T_NAMESPACE:
                    if ($scope)
                        igk_die("Namespace is scope not allowed");
                    $using = "namespace";
                    $options->skip_line = 1;
                case T_WHITESPACE:
                    if ($skip) {
                        return;
                    }
                    if (!empty($options->buffers)) {
                        $options->buffers .= $value;
                    } else {
                        $_out_str .= $value;
                    }
                    break;
                case T_OPEN_TAG:
                    $options->skip_line = 1;
                    if (empty($_out_str)) {
                        $options->sourceType = "php";
                    } else {
                        $options->sourceType = "mixed";
                    }
                    $_out_str .= $value;
                    $options->inPHPScript = true;
                    break;
                case T_CLOSE_TAG:
                    $_out_str .= $value;
                    $options->detectCloseTag = 1;
                    break;
                case T_USE:
                    if (!$scope) {
                        $using = "use";
                        $options->skip_line = 1;
                    }
                    break;
                case T_NAME_QUALIFIED:
                case T_NAME_FULLY_QUALIFIED:
                    if ($using) {
                        if (isset($options->usings[$value])) {
                            igk_die("already contain require - " . $value);
                        }
                        $options->usings[$value] = $value;
                        $options->usingClass = $value;
                    }
                    break;
                case T_AS:
                    if ($using == 'use') {
                        $options->asOperatorFlag = 1;
                    } else {
                        $_out_str .= $value;
                    }
                    break;
                case T_IF:
                case T_ELSE:
                case T_ELSEIF:
                case T_WHILE:
                case T_DO:
                case T_FOREACH:
                case T_SWITCH:
                    if ($using) {
                        igk_wln_e("detect in use: block ");
                    }
                    $read_block_info = new BalafonViewCompilerConditionBlockInfo($value, $options->scopeDepth);                     
                    $block_list[] = $read_block_info;
                    // $buffers = &$read_block_info->value;
                    if (($id == T_ELSE) || ($id == T_ELSEIF)) {
                        array_pop($block_list);
                        $c = count($block_list);
                        if ($c == 0) {
                            igk_die("not allowed index");
                        }
                        if (!$block_list[$c - 1]->childs)
                            $block_list[$c - 1]->childs = [];
                        if ($block_list[$c - 1]->type != "if") {
                            igk_die("not a valid childs");
                        }
                        $block_list[$c - 1]->childs[] = $read_block_info;
                    }
                    $options->read_block_info = $read_block_info;
                    break;
                default:
                    if (!$using) {
                        if (T_FUNCTION == $id) {
                            // expect read function 
                            $options->flagReading = 'function';
                            $options->buffers .= $value;
                            break;
                        }

                        if (T_CLASS == $id) {
                            // expect read class + special modifier detection ...  
                            $options->flagReading = 'class';
                            $options->buffers .= $value;
                            break;
                        }
                        if (T_INTERFACE == $id) {
                            // expect read function 
                            $options->flagReading = 'interface';
                            $options->buffers .= $value;
                            break;
                        }
                        if (T_TRAIT == $id) {
                            // expect read function 
                            $options->flagReading = 'trait';
                            $options->buffers .= $value;
                            break;
                        }
                        if (($id == T_ABSTRACT) ||
                            ($id == T_FINAL)
                        ) {
                            // passing to buffer
                            $options->buffers .= $value;
                            // igk_wln("::::" . $options->flagReading, "====" . $options->buffers);
                            break;
                        }
                        $_out_str .= $value;
                        if ((T_EXIT == $id) || ((T_STRING == $id) && (($value == "igk_exit") || ($value == 'igk_do_response')))) {
                            $options->exitDetected = $value;
                        }
                    } else {
                        // using flag read
                        if ((T_STRING == $id)) {
                            if ($using == "namespace") {
                                $options->namespace = $value;
                            } else if ($options->asOperatorFlag) {
                                $options->usings[$options->usingClass] = $value;
                                $options->asOperatorFlag = null;
                            }
                        }
                    }
                    break;
            }
            $options->lastTokenReadId = $id;
        } else {
            // echo "char e :".$e.",=\n";
            igk_debug_wln("NO_TOKEN:" . $e . "," . $skip);
            if (!$skip && !$using) {
                $_out_str .= $e;
                switch ($e) {
                    case "{":
                        $options->scopeDepth++;
                        break;
                    case "}":
                        $options->scopeDepth--;
                        break;
                    case "=":
                        $options->varContext = 'read';
                        break;
                    case ";":
                        $options->varContext = null;
                        break;
                }
            } else {
                if ($using && ($e == ';')) {
                    $using = false; //reset using
                    $skip = 0;
                    $options->skip_line = 1;
                }
            }
        }
        if ($skip && !$using) {
            $options->skip_line = 0;
        }
    }

    /**
     * 
     * @param mixed $e 
     * @param BalafonViewCompilerReadOptions $options 
     * @param string $_out_str 
     * @return true 
     */
    protected function _read_var_context($e, $options, string &$_out_str)
    {
        $value = $e;
        $id = null;
        if (is_array($e)) {
            $id = $e[0];
            $value = $e[1];
        }
        $buffers = &$options->buffers;
        $buffers .= $value;
        igk_debug_wln("var_reading: " . $id . " =" . $value);
        switch ($id) {
            case T_OBJECT_OPERATOR:
                // stop closing read 
                $_out_str .= $buffers;
                $buffers = "";
                break;
            default:
                switch ($value) {
                    case '}':
                        // stop closing read 
                        $_out_str .= $buffers;
                        $buffers = "";
                        break;
                    case ';':
                        $_out_str .= $buffers;
                        $buffers = "";
                        $options->varContext = null;
                        break;
                }
                break;
        }
        return true;
    }

    /**
     * 
     * @param mixed $e 
     * @param BalafonViewCompilerReadOptions $options 
     * @param string $_out_str 
     * @return void 
     */
    protected function _read_block_context($e, BalafonViewCompilerReadOptions $options, string &$_out_str): bool
    {
        /**
         * @var BalafonViewCompilerConditionBlockInfo $read_block_info
         */
        $value = $e;
        $id = null;
        if (is_array($e)) {
            $id = $e[0];
            $value = $e[1];
        }
        $buffers = &$options->buffers;
        $read_block_info = &$options->read_block_info;
        $buffers .= $value;
        $depth = &$options->scopeDepth;
        // readblock
        if ($read_block_info) {
            $read_block_info->value .= $value;
            if (is_null($read_block_info->instruction))
                $read_block_info->instruction = new BalafonViewConditionBlockInstructionInfo;

            // read line instruction
            if (!$read_block_info->conditionRead) {
                if ($value!="{")
                if (!empty($read_block_info->instruction->value) || !(empty(trim($value))))
                    $read_block_info->instruction->value .= $value;
            }

            if ($read_block_info->conditionRead) {
                $read_block_info->condition .= $value;
            } else {
                $read_block_info->instructCode .= $value;
            }
            switch ($value) {
                case "{":
                    // start read block
                    $read_block_info->block = true;
                    $depth++;
                    break;
                case "}":
                    $depth--;
                    if ($read_block_info->depth == $depth) {
                        // block instruct end
                        $this->_update_block_info($read_block_info);

                        $instructions = new BalafonViewCompileInstruction;
                        $instructions->extract = true;
                        $instructions->data = $read_block_info->instructions;
                        $instructions->controller = $this->options->view_args->ctrl;
                        $instructions->variables = $read_block_info->variables;
                        $response = $instructions->compile();  

                        $node = new ConditionBlockNode() ;
                        $node->type = $read_block_info->type;
                        $node->output = $response;
                        $node->condition = $read_block_info->condition;
                        $_g = $node->render();
                        $_out_str .= self::_AppendPHPCode($_g);  
                        $buffers = null;

                        // igk_wln_e("response:", $response, $_out_str, "response block:", $node->render());
                        // //detect node modification 
                        // $m = clone ($this->options->view_args);
                        // if ($c = BalafonViewCompilerUtility::CheckBlockModify(
                        //     $read_block_info->instructCode,
                        //     $m,
                        //     true
                        // )) {
                        //     $g = new ConditionBlockNode;
                        //     $g->type = $read_block_info->type;
                        //     $g->condition = $read_block_info->condition;
                        //     // $g->add($m->t); 
                        //     $this->options->view_args->t->add($g);
                        // }
                        // igk_wln_e("block modify? ", $c, $this->options->view_args->t);
                        // igk_trace();
                        // igk_wln_e("finish read block 1 ", $read_block_info);
                        $read_block_info = null;
                    }
                    break;
                case ";":
                    $read_block_info->instructions[] = $read_block_info->instruction;
                    $read_block_info->instruction = null;

                    if (!$read_block_info->block) {
                        // single instruction    
                        $this->_update_block_info($read_block_info);
                        if ($response = self::_CompileSourceCode($read_block_info->instructions, $this->options->view_args->ctrl , $read_block_info->variables)){
                            $node = new ConditionBlockNode() ;
                            $node->type = $read_block_info->type;
                            $node->output = $response;
                            $node->condition = $read_block_info->condition;
                            $_g = $node->render();
                            $_out_str .= self::_AppendPHPCode($_g);  
                        }
                        $read_block_info = null;
                        igk_wln_e("finish read block 2 ", $read_block_info);
                  
                    }
                    break;
                case "(":
                    if ($read_block_info->conditionRead) {
                        $read_block_info->conditionDepth++;
                    }
                    break;
                case ")":
                    if ($read_block_info->conditionRead) {
                        $read_block_info->conditionDepth--;
                        if ($read_block_info->conditionDepth == -1) {
                            $read_block_info->conditionRead = false;
                        }
                    }
                    break;
                case '=':
                    if ($read_block_info->instruction->data){
                        // igk_wln("the instruct code : ", $read_block_info->instructCode, $value, "read?",
                        // $read_block_info->conditionRead, "bock?", $read_block_info->block);
                        $this->_remove_block_instruct_data($read_block_info);
                      
                    } 
                    break;
                case '->':
                    if ($read_block_info->instruction->data && !$read_block_info->openCurlyFlag  ){   
                        $this->_remove_block_instruct_data($read_block_info);   
                    }
                    break;
                default:
                    switch ($id) {
                        case T_VARIABLE:
                            $name = substr($value, 1);
                            $new = false;
                            if (!isset($read_block_info->variables)) {
                                $read_block_info->variables = [];
                            }
                            if (!key_exists($name, $read_block_info->variables)) {
                                $read_block_info->variables[$name] = null;
                                $new = true;
                            }
                            // transform 
                            $read_block_info->detectVariable = $name;
                            if ($read_block_info->conditionRead) {
                                if ($read_block_info->depth == 0) {
                                    // global parameter or defined parameter
                                    if (!$new && key_exists($name, $options->detectVariables + $read_block_info->variables)) {
                                        //  igk_wln_e("variables in array ");
                                        // replace position - with expression var
                                        $pos = strrpos($read_block_info->value, $value);
                                        $read_block_info->value = substr($read_block_info->value, 0, $pos) . $this->_get_express_var($name);

                                        // replace condition block 
                                        $pos = strrpos($read_block_info->condition, $value);
                                        $read_block_info->condition = substr($read_block_info->condition, 0, $pos) . $this->_get_express_var($name);

                                        igk_wln_e(
                                            "variables ,",
                                            $options->detectVariables,
                                            "condition: " . $read_block_info->condition,
                                            "value:" . $read_block_info->value
                                        );
                                    }
                                }
                            } else {

                                $pos = strrpos($read_block_info->instruction->value, $value);
                                $read_block_info->instruction->data = [$name, $this->_get_express_var($name), $pos];
                                $read_block_info->instruction->value = substr($read_block_info->instruction->value, 0, $pos) . 
                                $read_block_info->instruction->data[1];


                                $pos = strrpos($read_block_info->value, $value);
                                $read_block_info->value = substr($read_block_info->value, 0, $pos) . $read_block_info->instruction->data[1];

                                // $pos = strrpos($read_block_info->instructCode, $value);
                                // $read_block_info->instructCode = substr($read_block_info->instructCode, 0, $pos) . $read_block_info->instruction->data[1];
                                // igk_wln_e("in not condition read .... - ".$name, token_name($read_block_info->lastTokenId));
                            }
                            if ($read_block_info->openCurlyFlag) {
                            }
                            // igk_wln_e("******************************: ".$name, $read_block_info->value, $read_block_info);
                            break;
                        case T_CURLY_OPEN:
                            $read_block_info->openCurlyFlag = true;
                            $read_block_info->instruction->data =null;
                            break;
                    }
            }

            if ($id && $read_block_info) {
                $read_block_info->lastTokenId = $id;
            }
            return true;
        }
        igk_wln_e("end read .... ");
        return false;
    }
    private static function _AppendPHPCode(string $_g){
        if (strpos($_g, "<?php")===0)
            $_g = ltrim(substr($_g, 5));
        if (strrpos($_g, "?>")!==(strlen($_g)-2)){ 
            $_g = "<?php\n"; 
        }
        return $_g;
    }
    private function _get_express_var(string $name): string
    {
        return sprintf("igk_express_var(\"%s\")", $name);
    }
    private function _remove_block_instruct_data(BalafonViewCompilerConditionBlockInfo $read_block_info){
        $m = $read_block_info->instruction->data[1];
        $read_block_info->instruction->value = StringUtility::ReplaceAtOffset(
            $read_block_info->instruction->value,
            '$'.$read_block_info->instruction->data[0],
            $read_block_info->instruction->data[2],
            strlen($m)
        );  
        //remove last instruct data
        // igk_wln_e("last value : ", $read_block_info->instruction->value, "instruct:::" , $read_block_info->instruct);
        if (false !== ($pos = strrpos($read_block_info->value, $m))){            
            $read_block_info->value = StringUtility::ReplaceAtOffset(
                $read_block_info->value,
                '$'.$read_block_info->instruction->data[0],
                $pos,
                strlen($m)
            );
        }
        //     $read_block_info->value = substr($read_block_info->value, 0, $pos);
        if (false !== ($pos = strrpos($read_block_info->instructCode, $m)))
        {
            // igk_trace();
            // igk_debug_wln_e("code : ", $read_block_info->instructCode, "value:" , $read_block_info->value);
            // $read_block_info->instructCode =            
            // substr($read_block_info->instructCode, 0, $pos).
            // '$'.$read_block_info->instruction->data[0].
            // substr($read_block_info->instructCode, $pos+strlen($m)); 
            $read_block_info->instructCode = StringUtility::ReplaceAtOffset(
                $read_block_info->instructCode,
                '$'.$read_block_info->instruction->data[0],
                $pos,
                strlen($m)
            );
        }
        $read_block_info->instruction->data = null;
    }
    /**
     * update instruction code
     * @param BalafonViewCompilerConditionBlockInfo $t 
     * @return void 
     */
    private function _update_block_info(BalafonViewCompilerConditionBlockInfo $t)
    {
        if ($t->block) {
           $t->instructCode = trim(substr($t->value, strpos($t->value, "{") + 1, -1));
        } else {
            $t->instructCode = trim($t->instructCode);
        }
        $t->condition = trim($t->condition);
    }
    /**
     * 
     * @param mixed $e 
     * @param mixed $_out_str 
     * @param mixed $result 
     * @param BalafonViewCompilerReadOptions $options 
     * @return true|void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _read_flag($e, &$_out_str, &$result, $options)
    {
        $value = $e;
        $id = null;
        if (is_array($e)) {
            $id = $e[0];
            $value = $e[1];
        }
        $buffers = &$options->buffers;

        switch ($options->flagReading) {
            case "trait":
            case "interface":
            case "class":
                $buffers .= $value;
                switch ($id) {
                    case T_STRING:
                        if (is_null($options->dataInfo)) {
                            $options->dataInfo = (object)[
                                "type" => $options->flagReading,
                                "name" => $value,
                                "depth" => $options->scopeDepth, // scope depth to restore 
                            ];
                        }
                        return true;
                    default:
                        return self::_end_info($value, $options, $buffers);
                }
            case "function":
                $buffers .= $value;
                switch ($id) {
                    case T_STRING:
                        // igk_wln("detecting..... function name --- ", $id, "g:", $value, $buffers);
                        if (is_null($options->dataInfo)) {
                            $options->dataInfo = (object)[
                                "type" => "function",
                                "name" => $value,
                                "depth" => $options->scopeDepth, // scope depth to restore 
                            ];
                        }
                        break;
                    default:
                        if (($value == '(') && is_null($options->dataInfo)) {
                            // $detect anonymous function 
                            $_out_str .= $buffers;
                            $options->buffers = "";
                            $options->flagReading = false;
                            return true;
                        }
                        return self::_end_info($value, $options, $buffers);
                }
                return true;
        }
    }

    /**
     * 
     * @param string $value 
     * @param BalafonViewCompilerReadOptions $options 
     * @param string $buffers 
     * @return bool 
     */
    private static function _end_info($value, $options, &$buffers)
    { 
        switch ($value) {
            case "{":
                $options->scopeDepth++;
                break;
            case "}":
                $options->scopeDepth--;
                if ($options->dataInfo->depth == $options->scopeDepth) {
                    // end reading function 
                    // igk_wln_e("end read function ");
                    if ($options->dataInfo->type == "function") {
                        $options->functions[$options->dataInfo->name] = $buffers;
                    } else {
                        if (!isset($options->classInsterfaceOrTrait[$options->dataInfo->type])) {
                            $options->classInsterfaceOrTrait[$options->dataInfo->type] = [];
                        }
                        $options->classInsterfaceOrTrait[$options->dataInfo->type][$options->dataInfo->name] = $buffers;
                    }
                    $buffers = "";
                    $options->flagReading = false;
                    // igk_wln("*******************************:".$options->dataInfo->name.":". count($options->functions))  ;
                    $options->dataInfo = null;
                    return true;
                }
                break;
        }
        return true;
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
        $this->m_output .= $this->m_compilerHandler->evaluate($data);
    }
    /**
     * 
     * @param string $file 
     * @param null|object $options 
     * @return bool|string|BalafonViewCompilerResult compilation result
     */
    public static function CompileFile(string $file,  ?object $options = null)
    {
        $src = file_get_contents($file);
        if (!empty($src) && ($o = self::CompileSource($src, $options))) {
            return $o;
        }
        return false;
    }


    /**
     * 
     * @param string $source 
     * @param BaseController $controller 
     * @param mixed $target 
     * @param mixed $data 
     * @return mixed bool or argument 
     */
    public static function EvaluateCompiledSource(string $source, BaseController $controller, ?ViewEnvironmentArgs $args = null, &$variables = null)
    {
        if (is_null($args)) {
            $args = new ViewEnvironmentArgs;
            $args->ctrl = $controller;
            $args->t = igk_create_node("div");
        }
        return BalafonViewCompilerUtility::EvalSourceArgs($source, $args, $variables);
    }
    /**
     * include compiled source file 
     * @param string $source 
     * @param BaseController $controller 
     * @param mixed $target 
     * @param mixed $data 
     * @return void 
     */
    public static function IncludeCompiledSource(string $file, BaseController $controller, ?ViewEnvironmentArgs $args = null, $data = null)
    {
        if (is_null($args)) {
            $args = new ViewEnvironmentArgs;
        }
        $fc = \Closure::fromCallable(function () {
            ob_start();
            extract(func_get_arg(1));
            include(func_get_arg(0));
            $c = ob_get_contents();
            ob_end_clean();
            return $c;
        })->bindTo($controller);
        return $fc($file, (array)$args);
    }

    private static function _CompileSourceCode($instructions, $controller, & $variables){
        $v_c = new BalafonViewCompileInstruction;
        $v_c->extract = true;
        $v_c->data = $instructions;
        $v_c->controller = $controller;
        $v_c->variables = $variables;
        return $v_c->compile();
    }
}
