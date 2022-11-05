<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewTokenBlock.php
// @date: 20221021 12:22:12
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use IGK\System\IO\StringBuilder;
use IGK\System\Runtime\Compiler\ReadTokenUtility;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
class ViewTokenBlock{
    const TOKEN_TYPES = "try|catch|finaly|if|elseif|else|switch|case|default|for|foreach|while|do";
    var $type; 

    var $parent;
  
    /**
     * block of instructions
     * @var array
     */
    var $blocks = [];

    /**
     * block condition 
     * @var mixed
     */
    var $conditions;

    /**
     * block buffer
     * @var mixed
     */
    var $buffer;

    /**
     * depth of the block
     * @var int
     */
    var $depth = 0;

    var $tabstop = "\t";

    var $structs = [];

    public function __construct(string $type)
    {
        if (!in_array($type, $this->getTokenTypeArray())){
            igk_die("not a valid tokenblock type ");
        }
        $this->type = $type;
    }
    public function getTokenTypeArray(){
        return explode("|", self::TOKEN_TYPES);
    }

    public function generateCode(){
        $sb = new StringBuilder;
        $sb->tabstop = str_repeat($this->tabstop, $this->depth);  
        $endtag = "end".$this->type.";";
        $code = $this->getCodeString();
        $c = $this->conditions ? "(".$this->conditions.")": "";
        $sb->appendLine(sprintf('%s %s:', $this->type, $c)); 
        $sb->appendLine(rtrim($code));
        $sb->append(sprintf('%s', $endtag));
        return ''.$sb;
    }
    public function getCodeBlock(){
        return [];
    }

    /**
     * return generated code string
     * @return string 
     */
    public function getCodeString(){
        $tab = $this->blocks;
        $p = null;
        $q = null;
        $sb = new StringBuilder; 

        $sb->tabstop = str_repeat($this->tabstop, $this->depth+1);
        // render structure
        if ($this->structs){
           ReadTokenUtility::GenerateStruct($this->structs, $sb);
        }

        while(count($tab)>0){
            $q = array_shift($tab);
            if ($q instanceof self){
                $sb->appendLine($q->generateCode());
            }
            else if (is_string($q)){
               $sb->appendLine($q);
            }
        }
        return ''.$sb;
    }

}