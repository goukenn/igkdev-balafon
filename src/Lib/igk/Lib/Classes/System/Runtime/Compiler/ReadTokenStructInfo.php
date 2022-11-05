<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadTokenStructInfo.php
// @date: 20221019 20:50:30
namespace IGK\System\Runtime\Compiler;

use IGK\System\IO\StringBuilder;
use IGKException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Runtime\Compiler
 */
class ReadTokenStructInfo
{
    /**
     * type of the struct
     * @var string
     */
    var $type;

    /**
     * name of the struct
     * @var ?string
     */
    var $name = "";
    /**
     * comment attached to the struct
     * @var ?string
     */
    var $comment;
    /**
     * phpDoc attached to block
     * @var ?string
     */
    var $phpDoc;

    /**
     * buffer string. store read data
     * @var string
     */
    var $buffer;

    /**
     * modifier attached
     * @var ?array
     */
    var $modifiers;

    /**
     * depth read
     * @var int
     */
    var $depth = 0;

    /**
     * 
     * @var ReadTokenStructInfo
     */
    var $parent;

    /**
     * read code flag
     * @var bool
     */
    var $readCode = false;

    var $extends;

    var $implements;

    /**
     * child structs 
     * @var array
     */
    var $structs = [];

    /**
     * child uses
     * @var array
     */
    var $uses = [];

    /**
     * store variable
     * @var array
     */
    var $variables = [];

    /**
     * store code ouput
     * @var mixed
     */
    protected $m_output;

    var $mergeVariable =false;

    public function __construct(string $type)
    {
        if (!in_array($type, ["trait", "interface", "class", "function"])) {
            igk_die("not a valid value");
        }
        $this->type  = $type;
    }
    protected function getHeader(){
        $mod = $this->modifiers ? implode(' ', $this->modifiers):'';
        return sprintf("%s", implode(" ", array_filter([$mod, $this->type, 
         $this->name]))); 
    }
    /**
     * 
     * @param ReadTokenOptions $options 
     * @return void 
     * @throws IGKException 
     */
    public function buildBuffer(?IReadTokenMergeOption $options=null)
    { 
        $v_buffer = $this->buffer ?? "";
        $this->buffer = "";
        $sb = new StringBuilder($this->buffer );
        $depth = str_repeat("\t", $this->depth + 1);
        $noComment = $options && $options->noComment; 
        if (!$noComment){
            $comment = $this->comment ?? "///<summary></summary>";
            $phpDoc = $this->phpDoc ?? "/**\n* \n*/";
            $sb->appendLine($comment);
            $sb->appendLine($phpDoc);
        }
        $sb->append($this->getHeader());
        if ($this->extends) {
            $sb->append(sprintf(" extends %s", $this->extends));
        }
        if ($this->implements) {
            sort($this->implements);
            $sb->append(sprintf(" implements %s", implode(", ", $this->implements)));
        }
        $sb->appendLine("{");
        if ($this->type == "class" && $this->uses) {
            // use in class must be traits
            // $sb->appendLine();
            ReadTokenUtility::GenerateUses($this->uses, $sb);
            $sb->appendLine();
        }
        if ($this->variables){
            $sb->appendLine();
            $sb->appendLine(rtrim(ReadTokenUtility::GenerateVariables($this->variables, $this->mergeVariable))); 
            $sb->appendLine();
        }

        if ($this->structs){
            $sb->appendLine();
            $sb->appendLine(rtrim(ReadTokenUtility::GenerateStruct($this->structs, null, $options)));
        }
        if ($buffer = trim($v_buffer))
            $sb->appendLine($buffer);
        // generate
        $sb->append("}");  
    }
    /**
     * get the generated output
     * @return mixed 
     */
    public function output(?IReadTokenMergeOption $options=null){ 
        
        $bck = & $this->buffer ;
        $this->m_output = "";
        $this->buffer = & $this->m_output;
        $this->buffer = trim($bck);
        $this->buildBuffer($options);
        $this->buffer = & $bck;
        return $this->m_output;
    }

    public function initFlagOption(ReadTokenOptions $options){
        return ["op"=>"name"];
    }
    public function updateParentBuffer():bool{
        return false;
    }
    public function generatePhpDoc($options){
        $sb = new StringBuilder();
        $sb->appendLine("/**");
        $sb->appendLine("* ");
        if ($options->namespace)
            $sb->appendLine("* @package ".$options->namespace);         
        $sb->append("*/");
        return $sb.'';
    }
}
