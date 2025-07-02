<?php
// @file: IGKHtmlProcessInstruction.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;

use IGK\System\IO\File\PHPScriptBuilderUtility;
use IGKException;

class HtmlProcessInstructionNode extends HtmlNode{
    private  $m_noClose;
    public function __construct($content, $noClose=false){
        parent::__construct("igk-process");
        $this->content = $content;
        $this->m_noClose = $noClose; 
    }
    protected function _getRenderingChildren($option=null){
        return null;
    }
    public function __toString(){
        return __CLASS__."#".$this->render();
    }
    protected function _addChild($item, $index=null){
        return false;
    }
    public function add($item, $attributes=null, $index=null){
        return null;
    }
     
    public function getCanRenderTag(){
        return false;
    }
    /**
     * get if instruction is last rendering
     * @param mixed $option 
     * @return bool 
     * @throws IGKException 
     */
    public static function IsPhpCloseInstruct($option){
        $g = igk_getv($option, 'lastRendering');
        if($g && ($g instanceof self)){
            return $g->m_noClose;
        }
        return false;
    }
    public function render($options=null){
        $src=$this->getContent();
        if($compiler=igk_getv($options, "PHP.Compiler")){
            $src=$compiler->Compile($src);
        }
        else{
            if(igk_getv($options, "PHP.SkipComment")){             
                // + | remove comment 
                $src = substr(PHPScriptBuilderUtility::RemoveComment("<?".$src), 2);    
                // + | remove empty line            
                $src=implode("\n", array_filter(array_map("rtrim", explode("\n", $src)))); 
            }
        }
        $out="<?";
        $out .= $src;
        if(!$this->m_noClose){
            $out .= "?>\n";
        }
        return $out;
    }
}
