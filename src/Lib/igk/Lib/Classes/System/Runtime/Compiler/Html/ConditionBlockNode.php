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
        $t_pos = false;
        $_out = !empty($this->output) ? trim($this->output) : "";
        $_header = true;
        if (empty($_out)){
            $t_pos = strpos($_out, "<?php");
            if ($t_pos === 0){
                $_header = false;
            }
        }

        $sb = new StringBuilder();
        $sb->append("<?php ");        
        $sb->append(sprintf('if %s: ',$this->condition)); 
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
            // if ($check){
            //     if (strpos($_out, "<?php")===0){
            //         $_out = substr($_out, 5);
            //         $sb->appendLine($_out);
            //     }else {
            //         $sb->appendLine(" ? >".$_out);
            //     }
            // }else {
            //     $sb->appendLine($_out);
            // }
            $last = strrpos($_out, "<?php");
            if ($last === (strlen($_out)-5)){
                $sb->append('endif; ?>');
            }else{
                $sb->appendLine('<?php endif; ?>');
            } 
        } else {
            if (!$check)
                $sb->append('<?php endif; ?>');
            else 
                $sb->appendLine('?>\n<?php endif; ?>'); 
        }
        return $sb."";
    }
}