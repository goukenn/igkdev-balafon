<?php
// @author: C.A.D. BONDJE DOUE
// @file: PHPDocCommentParseTrait.php
// @date: 20230731 10:21:35
namespace IGK\System\IO\File\Php\Traits;


///<summary></summary>
/**
* 
* @package IGK\System\IO\File\Php\Traits
*/
trait PHPDocCommentParseTrait{
    
 /**
     * parse php doc comment
     * @param string $cm 
     * @return PHPDocCommentParser 
     */
    public static function ParsePhpDocComment(string $cm){
        $c = trim(igk_str_rm_start($cm, "/**"));
        $c = rtrim(igk_str_rm_last($c, "*/"));
        $g = new self;
        $g->summary = '';
        $summary = false;
        $content = "";
        $name = "";
        array_map(function($c)use($g, & $summary, & $content, & $name){
            $k =  ltrim(trim($c), " *");            
            $offset = 1;
            if (!$summary){
                if (strlen($k)>0){
                    if ($k[0] ==='@'){
                        $summary=true;
                        $name = $g->_readName($k, $offset);
                    }                    
                }
                if (!$summary){
                    $g->summary.= $k;
                    return;
                }       
                $content .= $g->_TreatContent(substr($k, $offset));
            } else {
                if (strlen($k)>0){
                    if ($k[0] ==='@'){
                        $g->$name($content);
                        $content = "";
                        $offset = 1;
                        $name = $g->_readName($k, $offset);
                        $s = trim(substr($k, $offset));                          
                        $content .= $g->_TreatContent($s); 
                        if ($name=='api'){
                            $g->api = true;
                        }
                    }else{
                        $content .= $k;
                    }
                }
            }
            
        }, explode("\n", $c));
        if (!empty($content)){
            $g->$name($content);
        }
        return $g;
    } 
}