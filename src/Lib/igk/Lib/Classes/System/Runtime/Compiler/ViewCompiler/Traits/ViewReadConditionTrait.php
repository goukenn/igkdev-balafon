<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewReadConditionTrait.php
// @date: 20221027 07:35:26
namespace IGK\System\Runtime\Compiler\ViewCompiler\Traits;

use IGK\System\Runtime\Compiler\ReadTokenOptions;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler\Traits
*/
trait ViewReadConditionTrait{
    protected function _readCondition(ReadTokenOptions $options, $id, $value){
        igk_debug_wln(__FILE__.":".__LINE__, "begin read condition...");
        $this->_pushFlag($options);
        $options->flag = '_handleReadCondition';
        $options->flagOptions = (object)[
            "type"=>":read_condition",
            "depth"=>$options->depth,
            "buffer"=>"",
            "dependOn"=>[]
        ];
    }
    /**
     * end read condition string
     * @param ReadTokenOptions $options 
     * @param mixed $id 
     * @param mixed $value 
     * @return void 
     */
    protected function _endReadCondition(ReadTokenOptions $options, $id, $value){
        $fop = $options->flagOptions;
        $this->_popFlag($options);
        if ($options->flag){
            $options->flagOptions->buffer .= sprintf("%s", $fop->condition);
            $this->_handleFlag($options, $id, $value);
            return;
        } else {
            $options->buffer .= sprintf("(%s)", $fop->condition);
        }
    }
    protected function _handleReadCondition(ReadTokenOptions $options, $id, $value){
        $fop = $options->flagOptions;
        $buffer = & $fop->buffer;
        if ($id == T_VARIABLE){
            $name = substr($value, 1);
            $fop->dependOn[$name] = null;
        }
        switch ($value) {
            case '(':
                if (($fop->depth + 1)<$options->depth){
                    $buffer .= $value;
                }
                break;
            case ')':                
                if ($fop->depth == $options->depth){
                    $fop->condition = $buffer;
                    $buffer = "";
                    $this->_endReadCondition($options, $id, $value);
                    return true;
                }
                $buffer.= $value;
                break;            
            default:
                $buffer .= $value;
                break;
        }
        return true;
    }
}