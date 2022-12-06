<?php
// @author: C.A.D. BONDJE DOUE
// @file: RenderDefinition.php
// @date: 20221202 12:04:24
namespace IGK\System\Html\Css\Traits;

use IGK\System\IO\StringBuilder;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Css\Traits
*/
trait RenderDefinitionTrait{
    public static function RenderDefinition($def){
        $sb = new StringBuilder;
        foreach($def as $k=>$v){
            if (is_array($v)){
                $sb->appendLine($k."{");
                foreach($v as $l=>$m){
                    $sb->appendLine(sprintf("%s:%s;", $l, $m));
                }
                $sb->appendLine("}");
            } else{
                igk_wln_e("bad > " .__CLASS__);
             } 
        }
        return $sb.'';
    }
}