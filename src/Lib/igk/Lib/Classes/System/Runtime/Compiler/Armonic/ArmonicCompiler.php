<?php
// @author: C.A.D. BONDJE DOUE
// @file: ArmonicCompiler.php
// @date: 20221023 10:51:56
namespace IGK\System\Runtime\Compiler\Armonic;
use IGK\Helper\Activator;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Runtime\Compiler\ICompiler;
use IGK\System\Runtime\Compiler\ICompilerTokenHandler;
use IGK\System\Runtime\Compiler\ReadTokenOptions;
use IGK\System\Runtime\Compiler\ReadTokenVariableFlagOption;
use IGK\System\Runtime\Compiler\CompilerFlagState;
use IGK\System\Runtime\Compiler\ReadTokenFunctionFlagOption;
use IGK\System\Runtime\Compiler\ReadTokenStructFunctionInfo;
use IGK\System\Runtime\Compiler\ReadTokenStructInfo;
use IGK\System\Runtime\Compiler\TokenCompilerBase;
use IGK\System\Runtime\Compiler\ReadTokenExpressionFlagOptions;
use IGK\System\Runtime\Compiler\Traits\CompilerTokenCompileTrait;
use IGK\System\Runtime\Compiler\Traits\CompilerTokenBracketTrait;
use IGK\System\Runtime\Compiler\Traits\CompilerTokenCommentHandlerTrait;
use IGK\System\Runtime\Compiler\Traits\CompilerTokenMergeSourceTrait;
use IGK\System\Runtime\Compiler\Traits\CompilerTokenReadStructHandlerTrait;
use IGK\System\Runtime\Compiler\Traits\CompilerTokenTrait;
use IGK\System\Runtime\Compiler\ViewCompiler\ViewCompilerConstants;
use IGKException;
use ReflectionException;

/**
 * 
 * @package IGK\System\Runtime\Compiler\Armonic
 */
/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler\Armonic
*/
class ArmonicCompiler extends TokenCompilerBase implements ICompiler, ICompilerTokenHandler
{
    use CompilerTokenTrait;
    use CompilerTokenBracketTrait;
    use CompilerTokenCommentHandlerTrait;
    use CompilerTokenReadStructHandlerTrait;
    use CompilerTokenCompileTrait;
    use CompilerTokenMergeSourceTrait;
    /**
    * Constant: operator symbol.
    * @var mixed
    */
    const OPERATOR_SYMBOL = ViewCompilerConstants::OPERATOR_SYMBOL;
    /**
    * Callback handler for flag handler.
    * @var mixed
    */
    var $flagHandler;
    /**
    * Property: tab stop.
    * @var mixed
    */
    var $tab_stop;
    /**
     * handle white space
     * @param ReadTokenOptions $options 
     * @param null|string $id 
     * @param string $value 
     * @return void 
     */
    protected function _handleWhiteSpace(ReadTokenOptions $options, ?string $id, string &$value)
    {
        if ($id == T_WHITESPACE) {
            $value = strpos($value, "\n") !== false ? "\n" : " ";
            if ($options->skipWhiteSpace) {
                $value = "";
            } else {
                $options->skipWhiteSpace = 1;
            }
        } else {
            $options->skipWhiteSpace = 0;
        }
    }
    /**
     * get tab stop
     * @param mixed $options 
     * @return string 
     */
    protected function _getTabStop($options)
    {
        // + | --------------------------------------------------------------------
        // + | GET TABSTOP 
        // + |
        return str_repeat($this->tab_stop, $options->bracketDepth);
    }
    /**
    * Handles Token.
    * @param ReadTokenOptions $options
    * @param null|string $id
    * @param string $value
    * @return bool
    */
    public function HandleToken(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $v_buffer = &$options->buffer;
        $v_flag = &$options->flag;
        if ($v_flag && $this->_handleFlag($options, $id, $value)) {
            return true;
        }
        switch ($id) {
            case T_OPEN_TAG:
                if ($options->startReadFlag) {
                    $v_buffer .= $value;
                } else {
                    $options->startReadFlag = 1;
                }
                break;
            case T_COMMENT:
                break;
            case T_DOC_COMMENT:
                break;
            case T_VARIABLE:
                $this->_readVariable($options, $id, $value);
                break;
            case T_STRING:
                $this->_readExpression($options, ":global");
                $options->buffer .= $value;
                break;
            case T_FINAL:
            case T_PUBLIC:
            case T_PROTECTED:
            case T_STATIC:
            case T_GLOBAL:
            case T_CONST:
            case T_ABSTRACT:
            case T_VAR:
            case T_PRIVATE:
                $options->modifiers[$value] = $value;
                if ($id == T_CONST) {
                    $this->_pushFlag($options);
                    $options->flag = CompilerFlagState::READ_CONST;
                    $options->flagOptions = null;
                }
                break;
            case T_CLASS:
            case T_INTERFACE:
            case T_TRAIT:
                $this->_readStruct($options, $id, $value);
                break;
            case T_FUNCTION:
                $this->_readFunction($options, $id, $value);
                break;
            case T_NAMESPACE:
                if ($options->depth == 0) {
                    $options->flag = CompilerFlagState::READ_NAMESPACE;
                }
                break;
            case T_USE:
                if ($options->flag) {
                    $this->_pushFlag($options);
                    $options->flag = null;
                    $options->flagOptions = null;
                }
                if ($options->depth == 0) {
                    $options->flag = CompilerFlagState::READ_GLOBAL_USE;
                } else {
                    $options->flag  = CompilerFlagState::READ_CLASS_USE;
                }
                break;
            default:
                if (self::IsOperator($value)){
                    $v_buffer = rtrim($v_buffer);
                    $sp = " ";
                    if (in_array($value, ['->','(','[']))
                        $sp = "";
                    $value = $sp. $value.$sp;
                    $options->skipWhiteSpace = true;
                }
                $v_buffer .= $value;
                break;
        }
        return true;
    }
    /**
     * check if value is operator
     * @param string $value 
     * @return bool 
     */
    protected static function IsOperator(string $value): bool{
        return in_array($value, explode(',', self::OPERATOR_SYMBOL));
    }
    /**
    * Handle read const.
    * @param ReadTokenOptions $options
    * @param mixed $id
    * @param mixed $value
    */
    protected function _handleReadConst(ReadTokenOptions $options, $id, $value)
    {
        if (is_null($options->flagOptions)) {
            if ($options->struct_info) {
                if ($options->struct_info->type == 'interface') {
                    igk_die("const not allowed in interface");
                }
                $var_info = (object)[
                    "name" => '',
                    'modifiers' => $options->modifiers,
                    'default' => null,
                    'buffer' => '',
                    'isConst' => true,
                    'mod' => '',
                    'dependOn' => false,
                    'initialize'=>true
                ];
                $options->flag = CompilerFlagState::READ_CONST;
                $options->flagOptions = ["var" => &$var_info, 'op' => 'name'];
                $options->struct_info->variables[] = $var_info;
            }
        }
        switch ($id) {
            case T_STRING:
                if ($options->flagOptions['op'] == 'name') {
                    $options->flagOptions["var"]->name = $value;
                    $options->flagOptions['op'] = 'def';
                } else {
                    $options->flagOptions["var"]->buffer .= $value;
                }
                break;
            default:
                switch ($value) {
                    case ';':
                        $options->flag = null;
                        $options->flagOptions["var"]->default =
                            trim($options->flagOptions["var"]->buffer);
                        unset($options->flagOptions["var"]->buffer);
                        $options->flagOptions = null;
                        $this->_resetCommentAndModifier($options);
                        $this->_popFlag($options);
                        $this->options->skipWhiteSpace = true;
                        break;
                    case '=':
                        break;
                    default:
                        $options->flagOptions["var"]->buffer .= $value;
                        break;
                }
                break;
        }
        return true;
    }
    #region FUNCTIONS
    // + | -------------------------------------------------------------------------------
    // + | READ FUNCTIONS
    // + |
    /**
    * Read function.
    * @param ReadTokenOptions $options
    * @param mixed $id
    * @param mixed $value
    */
    protected function _readFunction(ReadTokenOptions $options, $id, $value)
    {
        $this->_pushFlag($options);
        $options->flag = CompilerFlagState::READ_FUNCTION;
        $options->flagOptions = ReadTokenFunctionFlagOption::CreateFlag();
        $options->flagOptions->depth = $options->depth;
        $struct = new ReadTokenStructFunctionInfo($value);
        $struct->modifiers = $options->modifiers;
        $struct->parent = $options->struct_info;
        $struct->buffer = "";
        $options->flagOptions->buffer = &$struct->buffer;
        // + | use struct attache to parent for special function case 
        $options->struct_info = $struct;
        $this->_resetCommentAndModifier($options);
    }
    /**
    * Handle read function.
    * @param ReadTokenOptions $options
    * @param mixed $id
    * @param mixed $value
    * @return bool
    */
    protected function _handleReadFunction(ReadTokenOptions $options, $id, $value): bool
    {
        /**
        * auto generate doc.
        * @var ReadTokenStructFunctionInfo $struct
        */
        $struct = $options->struct_info;
        $flagOption = &$options->flagOptions;
        $buffer = &$struct->buffer;
        if (($flagOption->op == "arg") && ($value == ",")) {
            $buffer = rtrim($buffer);
        }
        if (self::IsModifier($id)) {
            $options->modifiers[$value] = $value;
            $options->skipWhiteSpace = 1;
            return true;
        }
        switch ($id) {
            case T_COMMENT:
                $buffer = rtrim($buffer);
                return true;
            case T_DOC_COMMENT:
                $buffer = rtrim($buffer);
                return true;
            case T_STRING:
                if (!$struct->readCode) {
                    switch ($flagOption->op) {
                        case 'name':
                            $struct->name = $value;
                            $flagOption->op = 'arg';
                            break;
                        case 'return':
                            $struct->return = $value;
                            $flagOption->op = 'block';
                            break;
                        case 'arg':
                        default:
                            $buffer .= $value;
                            if ($flagOption->op == 'arg') {
                                $d = trim($value);
                                if (!empty($d)) {
                                    if ($d == '=') {
                                        $flagOption->argType = 1;
                                    } else {
                                        if ($flagOption->argType) {
                                            $struct->args[$flagOption->argName]["default"] = $d;
                                            $flagOption->argType = null;
                                            $flagOption->type = null;
                                        } else {
                                            $flagOption->type = $d;
                                        }
                                    }
                                }
                            }
                            break;
                    }
                    return true;
                }
                break;
            case T_USE: 
                break;
            case T_RETURN:
                $buffer .= $value . " ";
                $options->skipWhiteSpace = 1;
                $this->_readExpression($options, ":return");
                return true;
            case T_VARIABLE:
                $name = substr($value, 1);
                if ($flagOption->op == "arg") {
                    $struct->args[$name] = [
                        "type" => igk_getv($flagOption, "type"),
                        "default" => null
                    ];
                    $flagOption->argName = $name;
                    $flagOption->type = null;
                    if (substr($buffer, -1) == ',') { 
                        $buffer .= ' ';
                    }
                    $buffer .= $value;
                    return true;
                } else {
                    $this->_readVariable($options, $id, $value);
                    return true;
                }
                break;
            default:
                switch ($value) {
                    case ':':
                        $flagOption->op = 'return';
                        break;
                    case '(':
                        $v_op = $flagOption->op;
                        if (($v_op == 'arg') || ($v_op == 'name')) {
                            $flagOption->condition = true;
                            $flagOption->op = 'arg';
                            return true;
                        } else if ($v_op == "block") {
                            $this->_readConditionBlock($options, $id, $value);
                            return true;
                        }
                        break;
                    case ')':
                        if ($flagOption->depth == $options->depth) {
                            if ($flagOption->condition) {
                                $struct->condition = trim($struct->buffer);
                                if (empty($struct->name)) {
                                    $flagOption->op = 'useOrBlock';
                                } else {
                                    $flagOption->op = 'block';
                                }
                            } else {
                                if ($struct->getIsAnonymous()) {
                                    $struct->uses = trim($struct->buffer);
                                    $flagOption->op = 'block';
                                } else {
                                    igk_die("syntax not valid");
                                }
                            }
                            $struct->buffer = "";
                            $flagOption->condition = false;
                            return true;
                        }
                        break;
                    case '{':
                        if (!$struct->readCode) {
                            $struct->readCode = true;
                            $this->_pushBuffer($options, $struct->buffer, 'func_code');
                            $this->_attachFuncToParent($options, $struct);
                            return true;
                        }
                        break;
                    case '}':
                        if ($flagOption->depth == $options->depth) {
                            return $this->_endReadFunction($options, $id, $value);
                        }
                        break;
                    case ';':
                        if (!$struct->readCode) {
                            $this->_attachFuncToParent($options, $struct);
                            if (
                                !empty($struct->name) &&
                                (in_array('abstract', $struct->modifiers) ||
                                    ($struct->parent && ($struct->parent->type == 'interface')
                                    ))
                            ) {
                                $struct->isAbstract = true;
                                $struct->readCode  = true;
                                $this->_popFlag($options);
                                return true;
                            } else {
                                igk_die("not a valid syntax in function declaration");
                            }
                        } else {
                            $buffer = rtrim($buffer, ";");
                        }
                        break;
                    default:
                        if (($flagOption->op == 'name') && ($value == "&")) {
                            $struct->refFunc = true;
                            return true;
                        }
                        break;
                }
        }
        $buffer .= $value;
        return true;
    }
    /**
    * auto generate doc.
    * @param ReadTokenOptions $options
    * @param mixed $id
    * @param mixed $value
    * @return bool
    */
    protected function _endReadFunction(ReadTokenOptions $options, $id, $value): bool
    {
        $this->_popBuffer($options, 'func_code');
        $this->_popFlag($options);
        $options->struct_info = $options->struct_info->parent;
        if ($options->flag) {
            $this->_handleFlag($options, $id, $value);
        }
        return true;
    }
    /**
    * Attach func to parent.
    * @param mixed $options
    * @param mixed $struct
    */
    protected function _attachFuncToParent($options, $struct)
    {
        if (!$struct->getIsAnonymous()) {
            if ($struct->parent) {
                $struct->parent->structs[$struct->type][] = $struct;
            } else {
                $options->structs[$struct->type][] = $struct;
            }
        }
    }
    #endregion
    /**
    * Push flag.
    * @param ReadTokenOptions $options
    */
    protected function _pushFlag(ReadTokenOptions $options)
    {
        array_push($options->flags, [
            "flag" => $options->flag,
            "options" => $options->flagOptions
        ]);
    }
    /**
    * Pop flag.
    * @param ReadTokenOptions $options
    */
    protected function _popFlag(ReadTokenOptions $options)
    {
        if ($q = array_pop($options->flags)) {
            $options->flag = $q["flag"];
            $options->flagOptions = $q["options"];
        } else {
            $options->flag = null;
            $options->flagOptions = null;
        }
    }
    /**
    * Push buffer.
    * @param ReadTokenOptions $options
    * @param mixed & $buffer
    * @param null|string $id
    */
    protected function _pushBuffer(ReadTokenOptions $options, &$buffer, ?string $id = "")
    {
        $options->buffers[] = ["buffer" => &$options->buffer, "id" => $id];
        $options->buffer = &$buffer;
    }
    /**
    * Pop buffer.
    * @param ReadTokenOptions $options
    * @param null|string $id
    */
    protected function _popBuffer(ReadTokenOptions $options, ?string $id = null)
    {
        if ($op = array_pop($options->buffers)) {
            $buff = &$op["buffer"];
            $options->buffer = &$buff;
        } else {
            $options->buffer = "";
        }
    }
    /**
    * Handle flag.
    * @param ReadTokenOptions $options
    * @param null|string $id
    * @param string $value
    * @return bool
    */
    protected function _handleFlag(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $flag = &$options->flag;
        if ($this->flagHandler) {
            $fc = $this->flagHandler;
            return $fc($options, $id, $value);
        }
        if ($flag == CompilerFlagState::READ_CONDITION_BLOCK) {
            return $this->_handleConditionBlock($options, $id, $value);
        }
        if ($flag == CompilerFlagState::READ_CONST) {
            return $this->_handleReadConst($options, $id, $value);
        }
        if ($options->flag == CompilerFlagState::READ_VARIABLE) {
            return $this->_handleReadVariable($options, $id, $value);
        }
        if ($options->flag == CompilerFlagState::READ_EXPRESSION) {
            return $this->_handleReadExpression($options, $id, $value);
        }
        if ($options->flag == CompilerFlagState::READ_FUNCTION) {
            return $this->_handleReadFunction($options, $id, $value);
        }
        if ($options->flag == CompilerFlagState::READ_STRUCT) {
            return $this->_handleReadStruct($options, $id, $value);
        }
        // + | read struct
        if ($flag == CompilerFlagState::READ_STRUCT) {
            return $this->handleReadClass($flag, $options, $id, $value);
        }
        // + | read class 
        if ($flag == CompilerFlagState::READ_CLASS) {
            return $this->handleReadClass($flag, $options, $id, $value);
        }
        // + | read class use 
        if ($flag == CompilerFlagState::READ_CLASS_USE) {
            return $this->handleGlobalUseFlag($flag, $options, $id, $value);
        }
        // + | use global uses
        if ($flag == CompilerFlagState::READ_GLOBAL_USE) {
            return $this->handleGlobalUseFlag($flag, $options, $id, $value);
        }
        // + | read namespace 
        if ($flag == CompilerFlagState::READ_NAMESPACE) {
            switch ($id) {
                case T_STRING:
                case T_NAME_QUALIFIED:
                    if (!empty($options->namespace) && !$options->isNamespaceBlock) {
                        igk_die("namespace declaration not valid");
                    }
                    $options->namespace = $value;
                    break;
                default:
                    switch ($value) {
                        case ';':
                            $flag = null;
                            break;
                        case '{':
                            $options->isNamespaceBlock = true;
                            break;
                    }
                    break;
            }
            return true;
        }
        if (method_exists(static::class, $flag)) {
            return call_user_func_array([$this, $flag], [$options, $id, $value]);
        }
        return false;
    }
    #region STRUCTS
    // + | -------------------------------------------------------------------------------
    // + | READ STRUCTS
    // + |
    /**
    * Read struct.
    * @param ReadTokenOptions $options
    * @param mixed $id
    * @param mixed $value
    */
    protected function _readStruct(ReadTokenOptions $options, $id, $value)
    {
        $this->_pushFlag($options);
        $options->flag = CompilerFlagState::READ_STRUCT;
        $options->flagOptions = (object)[
            "op" => "name",
            "depth" => $options->depth,
            "buffer" => ""
        ];
        $p = $options->struct_info;
        $struct = new ReadTokenStructInfo($value);
        $struct->parent = $p;
        $struct->modifiers = $options->modifiers;
        $options->flagOptions->buffer = &$struct->buffer;
        $options->struct_info = $struct;
        $this->_resetCommentAndModifier($options);
    }
    /**
    * Handle read struct.
    * @param ReadTokenOptions $options
    * @param mixed $id
    * @param mixed $value
    * @return bool
    */
    protected function _handleReadStruct(ReadTokenOptions $options, $id, $value): bool
    {
        $struct = $options->struct_info;
        $flagOptions = $options->flagOptions;
        switch ($id) {
            case T_STRING:
                if (!$struct->readCode) {
                    switch ($options->flagOptions->op) {
                        case 'extends':
                            $struct->extends = $value;
                            break;
                        case 'implement':
                            if ($struct->type != 'class') {
                                igk_die($struct->type . " can't implement");
                            }
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
                $options->flagOptions->op = 'extends';
                break;
            case T_IMPLEMENTS:
                $options->flagOptions->op = 'implement';
                break;
            default:
                switch ($value) {
                    case '{':
                        if (!$struct->readCode) {
                            $struct->readCode = true;
                            $this->_pushBuffer($options, $struct->buffer, 'struct_class');
                            return true;
                        }
                        break;
                    case '}':
                        if ($flagOptions->depth == $options->depth) {
                            return $this->_endReadStruct($options, $id, $value);
                        }
                        return true;
                }
                if ($struct->readCode) {
                    return false;
                }
                break;
        }
        return true;
    }
    /**
    * End read struct.
    * @param ReadTokenOptions $options
    * @param mixed $id
    * @param mixed $value
    * @return bool
    */
    protected function _endReadStruct(ReadTokenOptions $options, $id, $value): bool
    {
        $struct =  $options->struct_info;
        $this->_popBuffer($options);
        $this->_popFlag($options);
        $options->struct_info = $struct->parent;
        $options->structs[$struct->type][$struct->name] = $struct;
        return true;
    }
    #endregion
    #region VARIABLES
    // + | -------------------------------------------------------------------------------
    // + | -------------------------------------------------------------------------------
    // + | READ VARIABLE
    // + | -------------------------------------------------------------------------------
    /**
    * auto generate doc.
    * @param ReadTokenOptions $options
    * @param mixed $id
    * @param mixed $value
    * @return
    */
    private function _readVariable(ReadTokenOptions $options, $id, $value)
    {
        $name = substr($value, 1);
        if ($struct = $options->struct_info) {
            if ($struct->type == 'interface') {
                igk_die("-- variable not allowed in interface -- ", true);
            }
        }
        $this->_pushFlag($options);
        $options->flag = CompilerFlagState::READ_VARIABLE;
        $options->flagOptions = ReadTokenVariableFlagOption::CreateFlag([
            "name" => $name,
            "modifiers" => $options->modifiers,
            "value" => null,
            "dependOn" => false,
            "render" => $options->struct_info && ($options->depth > $options->struct_info->depth + 1)
        ]);
        $this->_resetCommentAndModifier($options);
    }
    /**
     * append variable
     * @param ReadTokenOptions $options 
     * @return void 
     */
    protected function _appendVariable(ReadTokenOptions $options)
    {
        $tab = &$options->variables;
        $flagOptions = $options->flagOptions;
        if ($options->struct_info) {
            $tab = &$options->struct_info->variables;
        }
        $v_new  = false;
        if (!isset($tab[$flagOptions->name])) {
            $tab[$flagOptions->name] = (object)[
                "name" => $flagOptions->name,
                "modifiers" => $flagOptions->modifiers,
                "dependOn" => $flagOptions->dependOn,
                "default" => $flagOptions->render ? null : rtrim($flagOptions->buffer, ",;"),
                'initialize'=>false
            ];
            $v_new = true;
        }
        $this->_popFlag($options);
        if ($options->flag) {
            // + | passing to parent
            if (!$v_new || !$options->struct_info || ($flagOptions->render)) {
                $this->_appendToFlagOptionBuffer(
                    $options,
                    trim('$' . $flagOptions->name . $flagOptions->buffer)
                );
            }
        } else {
            if ($flagOptions->render) {
                // + | passing to global buffer
                $options->buffer .= trim('$' . $flagOptions->name . $flagOptions->buffer);
            }
        }
    }
    /**
    * auto generate doc.
    * @param ReadTokenOptions $options
    * @param null|string $id
    * @param string $value
    * @return bool
    */
    private function _handleReadVariable(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $flagOptions = $options->flagOptions;
        switch ($value) {
            case ";": 
            case ",":
                if ($flagOptions->render) {
                    $flagOptions->buffer = rtrim($flagOptions->buffer, ';,') . $value;
                }
                $this->_appendVariable($options);
                break;
            case '[':
            case '(':
                $this->_readExpression($options, $value);
                break;
            case '->':
            case '=':
            case '=>':
            case '+=':
            case '.=':
            case '*=':
            case '/=':
            case '-=':
                if ($value == '=') {
                    if ($flagOptions->render) {
                        $flagOptions->buffer .= " = ";
                    }
                }
                $this->_readExpression($options, $value);
                break;
            case '}':
                if ($options->curl_open) {
                    $this->_appendVariable($options, $id, $value);
                    $options->buffer .= $value;
                    $options->curl_open = false;
                }
                break;
        }
        return true;
    }
    #endregion
    #region EXPRESSION
    /**
     * start read expression
     * @param ReadTokenOptions $options 
     * @param string $type 
     * @return void 
     */
    protected function _readExpression(ReadTokenOptions $options, string $type)
    {
        $this->_pushFlag($options);
        $options->flag = CompilerFlagState::READ_EXPRESSION;
        $options->flagOptions = Activator::CreateNewInstance(ReadTokenExpressionFlagOptions::class, [
            "_t_" => "expression_info",
            "depth" => $options->depth,
            "type" => $type,
            'quoteStart' => false,
            'buffer' => ''
        ]);
        $this->_pushBuffer($options, $options->flagOptions->buffer, $options->flag);
        $options->skipWhiteSpace = true;
    }
    /**
    * auto generate doc.
    * @param mixed $value
    * @return bool
    */
    protected function _endReadExpression(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $fop = $options->flagOptions;
        switch ($fop->type) {
            case ':global':
            case ':return':
            case ':var':
            case ':expression':
                if ($fop->depth == $options->depth) {
                    $this->_popBuffer($options);
                    if (!empty($fop->buffer)) {
                        $options->buffer .= rtrim($fop->buffer, ",;") . $value;
                    }
                    $this->_popFlag($options);
                    if ($options->flag) {
                        return $this->_handleFlag($options, $id, $value);
                    }
                } else {
                    $fop->buffer .= $value;
                }
                break;
            case "(":
            case "[":
                $this->_popBuffer($options);
                $this->_popFlag($options);
                if ($options->flag === CompilerFlagState::READ_VARIABLE) {
                    $options->flagOptions->dependOn = true;
                    $options->flagOptions->render = 1;
                }
                $this->_appendToFlagOptionBuffer($options, $fop->type . $fop->buffer);
                $this->_handleFlag($options, $id, $value);
                return true;
                break;
            case "->":
            case "=>":
            case "+=":
            case ".=":
            case "-=":
            case "/=":
            case "*=":
            case "%=":
                if ($fop->depth == $options->depth) {
                    $this->_popBuffer($options);
                    $this->_popFlag($options);
                    $sp = ' ';
                    if ($fop->type == '->') {
                        $sp = '';
                    }
                    if ($options->flagOptions) {
                        $this->_appendToFlagOptionBuffer($options, $sp . $fop->type . $sp . $fop->buffer);
                        if ($options->flag === CompilerFlagState::READ_VARIABLE) {
                            $options->flagOptions->dependOn = true;
                            $options->flagOptions->render = 1;
                        }
                        return $this->_handleFlag($options, $id, $value);
                    }
                    $options->buffer .= $value;
                }
                break;
            case '=':
                $v_data = $fop->buffer . ($value == ')' ? '' : $value);
                return $this->_appendEndEqualExpression($options, $id, $value, $v_data);
        }
        return true;
    }
    /**
    * Append end equal expression.
    * @param mixed $options
    * @param mixed $id
    * @param mixed $value
    * @param mixed $data
    * @return bool
    */
    protected function _appendEndEqualExpression($options, $id, $value, $data): bool
    {
        $this->_popBuffer($options);
        $this->_popFlag($options);
        if ($options->flag) {
            $this->_appendToFlagOptionBuffer($options, $data);
            return $this->_handleFlag($options, $id, $value);
        } else {
            $options->buffer .= rtrim($data, ",;") . $value;
        }
        return true;
    }
    /**
    * Append to flag option buffer.
    * @param mixed $options
    * @param mixed $data
    */
    protected function _appendToFlagOptionBuffer($options, $data)
    {
        if ($options->flag) {
            $options->flagOptions->buffer .= $data;
        } else {
            $options->buffer .= $data;
        }
    }
    /**
    * Handle comment.
    * @param ReadTokenOptions $options
    * @param null|string $id
    * @param string $value
    * @return bool
    */
    protected function _handleComment(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        return true;
    }
    /**
     * handle read expression
     * @param ReadTokenOptions $options 
     * @param null|string $id 
     * @param string $value 
     * @return bool 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    protected function _handleReadExpression(ReadTokenOptions $options, ?string $id, string $value): bool
    {
        $fop = $options->flagOptions;
        switch ($id) {
            case T_COMMENT:
                return false;
            case T_VARIABLE:
                $options->skipWhiteSpace = true;
                if (($fop->type == ':global') || !$fop->ignoreDependency) {
                    $this->_readVariable($options, $id, $value);
                    $options->flagOptions->dependOn = true;
                    $options->flagOptions->render = 1;
                    $name = $options->flagOptions->name;
                    $this->_appendVariable($options);
                    $fop->dependOn[$name] = $name;
                    return true;
                }
                $fop->buffer .= $value;
                return true;
            case T_FUNCTION:
                $fop->buffer .= $value;
                $options->skipWhiteSpace = true;
                if (!$fop->ignoreDependency) {
                    $fop->ignoreDependency = true;
                    $fop->functionDepth = $options->depth;
                }
                return true;
        }
        if ($fop->ignoreDependency && ($value == "}") && ($fop->functionDepth == $options->depth)) {
            $fop->ignoreDependency = false;
            $fop->functionDepth = null;
        }
        switch ($value) {
            case ';':
            case ',':
                if ($value == ',') {
                    if ($fop->depth != $options->depth) {
                        $fop->buffer = rtrim($fop->buffer) . $value . " ";
                        $options->skipWhiteSpace = true;
                        return true;
                    }
                }
                if ($fop->ignoreDependency && ($fop->depth != $options->depth)) {
                    $fop->buffer .= $value;
                    return true;
                }
                $options->skipWhiteSpace = false;
                return $this->_endReadExpression($options, $id, $value);
            case '"': 
                $fop->buffer .= $value;
                if ($fop->quoteStart) {
                    $fop->quoteStart = 0;
                } else {
                    $fop->quoteStart = 1;
                }
                break;
            default:
                if (in_array($value, explode(',', "&,=,+,-,%,/,*,+=,-=,*=,.=,/=,&&,||,."))) {
                    $value = " $value ";
                    $options->skipWhiteSpace = true;
                    $fop->buffer = rtrim($fop->buffer);
                }
                $fop->buffer = $fop->buffer . $value;
                break;
        }
        return true;
    }
    #endregion
    /**
    * Bind to flag.
    * @param ReadTokenOptions $options
    * @param string $s
    * @param mixed $id
    * @param mixed $value
    */
    protected function _bindToFlag(ReadTokenOptions $options, string $s, $id, $value)
    {
        if ($options->flag) {
            $this->_appendToFlagOptionBuffer($options, $s);
            return $this->_handleFlag($options, $id, $value);
        } else {
            $options->buffer .= $s;
        }
        return true;
    }
    /**
    * auto generate doc.
    * @param mixed $id
    * @return
    */
    private static function IsModifier($id)
    {
        switch ($id) {
            case T_FINAL:
            case T_PUBLIC:
            case T_PROTECTED:
            case T_STATIC:
            case T_GLOBAL:
            case T_CONST:
            case T_ABSTRACT:
            case T_VAR:
            case T_PRIVATE:
                return true;
        }
        return false;
    }
    #region CONDITION BLOCK
    /**
     * read condition block
     * @param ReadTokenOptions $options 
     * @param null|string $id 
     * @param string $value 
     * @return void 
     */
    protected function _readConditionBlock(ReadTokenOptions $options, ?string $id, string $value)
    {
        $this->_pushFlag($options);
        $options->flag = CompilerFlagState::READ_CONDITION_BLOCK;
        $options->flagOptions = (object)[
            "depth" => $options->depth,
            "buffer" => $value
        ];
        $this->_pushBuffer($options, $options->flagOptions->buffer, CompilerFlagState::READ_CONDITION_BLOCK);
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    protected function _handleConditionBlock(ReadTokenOptions $options, ?string $id, string $value)
    {
        $fop = $options->flagOptions;
        $v_buffer = &$fop->buffer;
        $v_buffer .= $value;
        switch ($value) {
            case ")":
                if ($fop->depth == $options->depth) {
                    $this->_endReadConditionBlock($options, $id, $value);
                    return true;
                }
                break;
        }
        return true;
    }
    /**
    * auto generate doc.
    * @param string $value
    * @return void
    */
    protected function _endReadConditionBlock(ReadTokenOptions $options, ?string $id, string $value)
    {
        $fop = $options->flagOptions;
        $this->_popFlag($options);
        $this->_popBuffer($options, CompilerFlagState::READ_CONDITION_BLOCK);
        $this->_appendToFlagOptionBuffer($options, $fop->buffer);
    }
    #endregion
}