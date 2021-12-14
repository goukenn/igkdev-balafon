<?php
// @file: IGKHtmlProcessInstruction.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom; 

class HtmlProcessInstructionNode extends HtmlNode{
    private  $m_noClose;
    ///<summary>ctr</summary>
    ///<param name="content"></param>
    public function __construct($content, $noClose=false){
        parent::__construct("igk-process");
        $this->content=$content;
        $this->m_noClose=$noClose;
    }
    ///<summary></summary>
    ///<param name="option" default="null"></param>
    protected function __getRenderingChildren($option=null){
        return null;
    }
    ///<summary>display value</summary>
    public function __toString(){
        return __CLASS__."#".$this->render();
    }
    ///<summary></summary>
    ///<param name="item"></param>
    ///<param name="index" default="null"></param>
    protected function _AddChild($item, $index=null){
        return false;
    }
    ///<summary></summary>
    ///<param name="item"></param>
    ///<param name="attributes" default="null"></param>
    ///<param name="index" default="null"></param>
    public function add($item, $attributes=null, $index=null){
        return null;
    }
     
    ///<summary>Represente getCanRenderTag function</summary>
    public function getCanRenderTag(){
        return false;
    }
    ///<summary>Represente IsPhpCloseInstruct function</summary>
    ///<param name="option"></param>
    public static function IsPhpCloseInstruct($option){
        $g = igk_getv($option, 'lastRendering');
        if($g && ($g instanceof self)){
            return $g->m_noClose;
        }
        return false;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function render($options=null){
        $src=$this->getContent();
        if($compiler=igk_getv($options, "PHP.Compiler")){
            $src=$compiler->Compile($src);
        }
        else{
            if(igk_getv($options, "PHP.SkipComment")){
                $pos=0;
                $ln=strlen($src);
                $comment=false;
                $cs="";
                while($pos < $ln){
                    $ch=$src[$pos];
                    if($comment){
                        if($ch != "*"){
                            if($ch != '/'){
                                $comment=false;
                                $cs .= "/";
                            }
                        }
                    }
                    switch($ch){
                        case "'":
                        case "\"":
                        $cs .= igk_str_read_brank($src, $pos, $ch, $ch);
                        break;
                        case "/":
                        if(!$comment){
                            $comment=true;
                        }
                        else{
                            if($rpos=strpos($src, "\n", $pos)){
                                $pos=$rpos;
                            }
                            else{
                                $pos=$ln;
                            }
                            $comment=false;
                        }
                        break;
                        case "*":
                        if($comment){
                            if(($lpos=strpos($src, "*/", $pos)) > $pos){
                                $pos=$lpos + 2;
                                $comment=false;
                                break;
                            }
                            igk_wln_e("remove multilne comment :: ".$cs, $src, "pos : ".$lpos);
                        }
                        $cs .= $ch;
                        break;default: $cs .= $ch;
                        break;
                    }
                    $pos++;
                }
                $src=$cs;
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
