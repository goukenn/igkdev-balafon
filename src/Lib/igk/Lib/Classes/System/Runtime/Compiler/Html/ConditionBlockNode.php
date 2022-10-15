<?php
// @author: C.A.D. BONDJE DOUE
// @file: ConditionBlockNode.php
// @date: 20221011 13:13:48
namespace IGK\System\Runtime\Compiler\Html;

use IGK\System\Html\Dom\HtmlNode;
use IGK\System\IO\StringBuilder;

///<summary></summary>
/**
* render php compiled condition node
* @package IGK\System\Runtime\Compiler\Html
*/
class ConditionBlockNode extends HtmlNode{
    protected $tagname = "igk:compiler-condition-block";
    var $type;
    var $condition;
    var $output;
    public function getCanRenderTag(){
        return false;
    }
    public function render($options = null)
    {
        $out = "";
        if ($tc = $this->getRenderedChilds($options)){
            $out = implode("", array_map(function($a){
                if ($a===$this){
                    igk_die("not allowed in rendering");
                }
                return $a->render();
            }, $tc));
        }
        if (method_exists($this, $fc = "render".ucfirst($this->type))){
            return call_user_func_array([$this, $fc], [$out]);
        }
        return $this->render_CodeBlock($this->type, $out);
    }
    public function render_CodeBlock($type, $out){
        $t_pos = false;
        $_out = !empty($this->output) ? trim($this->output) : "";
        if (empty($_out)){
            $t_pos = strpos($_out, "<?php");           
        }

        $sb = new StringBuilder();
        $sb->append("<?php ");        
        $sb->append(sprintf('%s%s:', $this->type, $this->condition)); 
        $endtag = "end".$this->type.";";
        $check = true;
        if (!empty($out)){
            $sb->append("?>".$out);
            $check = false;
        }   
        if (!empty($_out)){ 
            if ($t_pos===0){
                $_out = substr($_out, 5);
            }
            if (!$check){
                $sb->appendLine("?>");
            }else {
                $sb->appendLine(($check ? "\n": "?>").$_out);
            }          
            $last = strrpos($_out, "<?php");
            if ($last === (strlen($_out)-5)){
                $sb->append($endtag.' ?>');
            }else{
                $sb->append("<?php\n".$endtag.' ?>');
            } 
        } else {
            if (!$check)
                $sb->append('<?php '.$endtag.' 3?>');
            else 
                $sb->appendLine('?>\n<?php '.$endtag.'8 ?>'); 
        }
        return $sb."";
    }   
    public function renderIf($out){
        return $this->render_CodeBlock($this->type, $out);        
    }
}