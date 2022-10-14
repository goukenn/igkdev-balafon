<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadBlockInstructionInfo.php
// @date: 20221013 11:35:14
namespace IGK\System\Runtime\Compiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime
*/
class ReadBlockInstructionInfo{
    var $type;
    var $name;
    var $depth;
    var $nameSupport;
    /**
     * source code of the block
     * @var string
     */
    var $code = "";

    /**
     * modifier attached to the block
     * @var mixed
     */
    var $modifiers = null;
 

    var $endConditionFlag=false;

    /**
     * depth start condition
     * @var ?int
     */
    var $endConditionDepth=null;

    var $blockFlag;

    var $condition;

    /**
     * 
     * @var static
     */
    var $parent;

    /**
     * code to compile
     * @var mixed
     */
    var $to_compile_code_buffer;

    /**
     * result of compiled. consider compiled result to produce php content.
     * @var mixed
     */
    var $compile_result = "";

    /**
     * array of value block
     * @var ?array
     */
    var $codeBlocks = [];

    public function isReadingCondition():bool{
        return $this->endConditionFlag && !is_null($this->endConditionDepth);
    }

    public static function GetNameSupport(string $type):bool{
        return in_array($type, explode("|", "function|trait|class|interface"));
    }
    public static function GetConditionSupport(string $type):bool{
        return in_array($type, explode("|", "if|elseif|catch|for|foreach|while|switch"));
    }
    public function getChildSupport(){
        return true;
    }

    public function __construct(string $type)
    {
        $this->type = $type;
        $this->readName = static::GetNameSupport($type); 
        $this->endConditionFlag = static::GetConditionSupport($type);
    }
    public function canAppendCode(){
        return !$this->readName && !$this->endConditionFlag;
    }
    public function addCompiledSource(string $source){
        $this->source_buffer .= $source."\n";
    }
    public function compile(){

    }
}