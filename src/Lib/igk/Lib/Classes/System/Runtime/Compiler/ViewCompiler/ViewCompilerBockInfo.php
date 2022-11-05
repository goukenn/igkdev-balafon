<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCompilerBockInfo.php
// @date: 20221025 12:23:47
namespace IGK\System\Runtime\Compiler\ViewCompiler;


///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
class ViewCompilerBockInfo{
    const CONDITION_BLOCK = "if|while|foreach|elseif|for|switch|catch";
    const INNER_BLOCK = "else|elseif|case|default";
    /**
     * 
     * @var string
     */
    var $type;

    /**
     * 
     * @var ?ViewCompilerBockInfo
     */
    var $parent;

    /**
     * condition string
     * @var $condition
     */
    var $condition;

    /**
     * array of block in this
     * @var array
     */
    var $blocks = [];

    /**
     * inner code
     * @var string
     */
    var $buffer = "";

    private $m_isClose;

    public function __construct(string $type)
    {
        $this->type = $type;   
        $this->m_isClose = false;
    }
    /**
     * require condition block
     * @return bool 
     */
    public function requireCondition():bool{
        return in_array($this->type, explode("|", self::CONDITION_BLOCK));
    }

    public function startBlock(){
        $c = $this->condition;
        switch($this->type){
            case 'case':
                return $this->type.$c.":";
                return;
        }

        if (!empty($c)){
            $c =" (".$c.")";
        }
        return $this->type.$c.":";
    }
    public function endBlock(){
        switch($this->type){
            case 'case':
                return "break;";            
        }
        return sprintf("end%s;", $this->type);
    }

    public function isInnerBlock() : bool{
        return in_array($this->type, explode("|", self::INNER_BLOCK));
    }
    /**
     * is child allowed
     * @param string $type 
     * @return bool 
     */
    public function childOf(string $type):bool{
        $tab =[
            "if"=>["else","elseif"],
            "switch"=>["case","default"],
            "case"=>["break"],
            "default"=>["break"]
        ];
        $g = igk_getv($tab, $type);
        
        return array_search($this->type, $g) !== false;
    }
    public function isChildContainer(){
        return in_array($this->type, ["if", "switch", "case", "default"]);
    }
    public function close(){
        $this->m_isClose = true;
    }
    public function closed(){
        return $this->m_isClose;
    }
}