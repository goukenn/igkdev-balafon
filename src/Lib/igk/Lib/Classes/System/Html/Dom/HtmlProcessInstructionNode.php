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

/**
* Html process instruction node.
* @package IGK\System\Html\Dom
*/
class HtmlProcessInstructionNode extends HtmlNode{
    /**
    * Property: no close.
    * @var mixed
    */
    private  $m_noClose;
    /**
    * .ctr
    * @param mixed $content
    * @param mixed $noClose
    */
    public function __construct($content, $noClose=false){
        parent::__construct("igk-process");
        $this->content = $content;
        $this->m_noClose = $noClose; 
    }
    /**
    * Get rendering children.
    * @param null|mixed $option
    */
    protected function _getRenderingChildren($option=null){
        return null;
    }
    /**
    * Returns string representation.
    */
    public function __toString(){
        return __CLASS__."#".$this->render();
    }
    /**
    * Add child.
    * @param mixed $item
    * @param null|mixed $index
    */
    protected function _addChild($item, $index=null){
        return false;
    }
    /**
    * Adds.
    * @param mixed $item
    * @param null|mixed $attributes
    * @param null|mixed $index
    */
    public function add($item, $attributes=null, $index=null){
        return null;
    }
    /**
    * Returns Can Render Tag.
    */
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
    /**
    * Renders.
    * @param null|mixed $options
    */
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
        $out = "<?";
        $out .= $src;
        if(!$this->m_noClose){
            $out .= "?>";
        }
        return $out;
    }
}