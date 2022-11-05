<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadTokenStructFunctionInfo.php
// @date: 20221020 08:06:25
namespace IGK\System\Runtime\Compiler;

use IGK\System\IO\StringBuilder;
use IGKException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Runtime\Compiler
 */
class ReadTokenStructFunctionInfo extends ReadTokenStructInfo
{


    var $condition;

    /**
     * params arguments
     * @var array
     */
    var $args = [];

    var $return;

    /**
     * ref function 
     * @var bool
     */
    var $refFunc = false;

    /**
     * is abstract
     * @var false
     */
    var $isAbstract = false;
    /**
     * get if method is anonymous
     * @return bool 
     */
    public function getIsAnonymous(): bool
    {
        return empty($this->name);
    }
    protected function getHeader()
    {
        $mod = $this->modifiers ? implode(' ', $this->modifiers) : '';
        return sprintf("%s(", implode(" ", array_filter([
            $mod, $this->type,
            $this->refFunc ? ' & ' :
                null, $this->name
        ])));
    }
    /**
     * build buffer
     * @param ReadTokenOptions $options 
     * @return void 
     * @throws IGKException 
     */
    public function buildBuffer(?IReadTokenMergeOption $options=null)
    {  
        $v_buffer = $this->buffer;
        $this->buffer = "";
        $is_ano = $this->getIsAnonymous();
        $sb = new StringBuilder($this->buffer);
        // $depth = str_repeat("\t", $this->depth);
        $merge = $options ? $options->mergeVariable : 0;
        $noComment = $options && $options->noComment; 
        // $sb->tabstop = $depth;
        if (!$is_ano && !$noComment) {
            $comment = $this->comment ?? "///<summary></summary>";
            // auto generate
            if (is_null($this->phpDoc)) {
                $this->phpDoc = $this->generatePhpDoc($options);
            }

            $phpDoc = $this->phpDoc ?? "/**\n* \n*/";
            $sb->appendLine($comment);
            $sb->appendLine($phpDoc);
        }
        $mod = $this->modifiers ? implode(' ', $this->modifiers) : '';
        $sb->append(sprintf("%s(", implode(" ", array_filter([
            $mod, $this->type,
            $this->refFunc ? ' & ' :
                null, $this->name
        ]))));
        if ($this->condition) {
            $sb->append(sprintf("%s", $this->condition));
        }
        $sb->append(')');
        if ($this->uses && $this->getIsAnonymous()) {
            $sb->append(sprintf(" use (%s)", $this->uses));
        }
        if ($this->return) {
            $sb->append(sprintf(": %s", $this->return));
        }
        if ($this->isAbstract) {
            $sb->appendLine(";");
        } else {
            $sb->appendLine("{");

            if ($this->variables){
                if (!empty($meth = rtrim(ReadTokenUtility::GenerateVariables($this->variables, $merge)))){
                    $sb->appendLine($meth); 
                    $sb->appendLine();
                }
            }
            if ($this->structs) {
                $sb->appendLine();
                $sb->appendLine(ReadTokenUtility::GenerateStruct($this->structs, null));
            }
            if (!empty($buff = trim($v_buffer)))
                $sb->appendLine($buff); 
            // generate
            $sb->append("}");
        }
        $this->m_output = $sb . '';
    }
    public function initFlagOption(ReadTokenOptions $options)
    {
        return ["op" => "name", "condition" => false, "argType" => null, "type" => null];
    }
    public function updateParentBuffer(): bool
    {
        return $this->getIsAnonymous();
    }

    public function generatePhpDoc($options)
    {
        $sb = new StringBuilder();
        $sb->appendLine("/**");
        $sb->appendLine("* ");
        if (is_null($this->parent) &&  $options && $options->namespace)
            $sb->appendLine("* @package " . $options->namespace);
        foreach ($this->args as $k => $v) {
            $sb->appendLine(sprintf('* @param %s $%s', igk_getv($v, 'type', 'mixed'), $k));
        }
        $sb->appendLine(sprintf("* @return %s", $this->return ?? "mixed"));
        $sb->append("*/");
        return $sb . '';
    }
}
