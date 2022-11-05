<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerTokenReadStructHandler.php
// @date: 20221021 19:03:12
namespace IGK\System\Runtime\Compiler\Traits;

use IGK\System\Runtime\Compiler\CompilerFlagState;
use IGK\System\Runtime\Compiler\IReadTokenOptions;

///<summary></summary>
/**
 * 
 * @package IGK\System\Runtime\Compiler\Traits
 */
trait CompilerTokenReadStructHandlerTrait
{
    use CompilerTokenBracketTrait;

    protected function handleReadClass(&$flag, IReadTokenOptions $options, $id, $value): bool
    {
        $struct = $options->struct_info;

        switch ($id) {
            case T_STRING:
                if (!$struct->readCode) {
                    switch ($options->flagOptions["op"]) {
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
                $options->flagOptions["op"] = 'extends';
                break;
            case T_IMPLEMENTS:
                $options->flagOptions["op"] = 'implement';
                break;
            default:
                switch ($value) {
                    case '{':
                        $struct->readCode = true;
                        // start reading code 
                        $options->flagOption = null;
                        $flag = null;
                        $this->pushBuffer($options, $struct->buffer, 'class');
                        $struct->popBuffer = true;
                        break;
                }
                $this->_checkBracket($options, $value);
        }
        return true;
    }

    /**
     * handle global use flag
     * @param mixed $flag 
     * @param mixed $options 
     * @param mixed $id 
     * @param mixed $value 
     * @return bool 
     * @throws IGKException 
     */
    protected function handleGlobalUseFlag(&$flag, IReadTokenOptions $options, $id, $value): bool
    {
        $uses = &$options->uses;
        if ($flag == CompilerFlagState::READ_CLASS_USE) {
            $uses = &$options->struct_info->uses;
        }
        if (is_null($options->flagOptions)) {
            $options->flagOptions = [];
        }
        switch ($id) {
            case T_NAME_QUALIFIED:
            case T_NAME_FULLY_QUALIFIED:
                $uses[$value] = $value;
                $options->flagOptions = ["name" => $value, "alias" => false];
                break;
            case T_AS:
                $options->flagOptions["alias"] = true;
                break;
            case T_CONST:
                $options->flagOptions = ["type" => "const", "alias" => false, "name" => ""];
                break;
            case T_FUNCTION:
                $options->flagOptions = ["type" => "function", "alias" => false, "name" => ""];
                break;
            case T_NS_SEPARATOR:
                $v_name = $options->flagOptions["name"];
                unset($uses[$v_name]);
                $options->flagOptions["name"] .= $value;
                // igk_wln(__FILE__ . ":" . __LINE__, $options->flagOptions, $uses);
                break;
            case T_STRING:
                if ($flag == CompilerFlagState::READ_CLASS_USE) {
                    if (!$options->flagOptions || !$options->flagOptions["alias"]) {
                        $uses[$value] = $value;
                        $options->flagOptions = ["name" => $value, "alias" => false];
                    } else {
                        if ($options->flagOptions["alias"]) {
                            $v_name = $options->flagOptions["name"];
                            $uses[$v_name] = $value;
                            $options->flagOptions = null;
                        }
                    }
                } else {
                    if (!$options->flagOptions) {
                        $uses[$value] = $value;
                        $options->flagOptions = ["name" => $value, "alias" => false];
                    } else {
                        $v_name = $options->flagOptions["name"];
                        switch (igk_getv($options->flagOptions, "type")) {
                            case 'function':
                                if ($options->flagOptions["alias"]) {
                                    $v_name = $options->flagOptions["name"];
                                    $uses[$v_name] = $value;
                                } else {
                                    $name = "function " . $value;
                                    $uses[$name] = $name;
                                    $options->flagOptions["name"] = $name;
                                }
                                break;
                            case 'const':
                                if ($options->flagOptions["alias"]) {
                                    $v_name = $options->flagOptions["name"];
                                    $uses[$v_name] = $value;
                                } else {
                                    $name = "const " . $value;
                                    $uses[$name] = $name;
                                    $options->flagOptions["name"] = $name;
                                }
                                break;
                            default:
                                if ($options->flagOptions["alias"]) { 
                                    $uses[$v_name] = $value;
                                    $options->flagOptions[$v_name] = $value;
                                } else {
                                    $v_name .= $value;
                                    $uses[$v_name] = $v_name;
                                    $options->flagOptions["name"] = $v_name;
                                }
                                break;
                        }
                    }
                }
                break;
            default:
                switch ($value) {
                    case ';':
                        // igk_wln_e("done ", $uses);
                        $this->_popFlag($options);
                        break;
                }
        }
        return true;
    }
}
