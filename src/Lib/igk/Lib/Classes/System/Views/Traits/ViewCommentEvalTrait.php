<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCommentEvalTrait.php
// @date: 20230123 11:08:37
namespace IGK\System\Views\Traits;

use IGK\Helper\StringUtility;

///<summary></summary>
/**
* 
* @package IGK\System\Views\Traits
*/
trait ViewCommentEvalTrait{
    protected function evalData(string $data){
        $name = $data;
            $args = [];
            if (strpos($data, "@")=== 0){
                $offset = 1;
                $name = '@'.StringUtility::ReadIdentifier($data, $offset);
                if ($offset<strlen($data)){
                    $g = ltrim(substr($data, $offset));
                    $ch = $g[0];
                    if ($ch=="="){
                        $g = substr($g, 1);
                        $args = StringUtility::ReadArgs($g);
                    } else {
                        $args = StringUtility::ReadArgs($g);
                    }
                } 
            }
            if ($fc = igk_getv($this->activates, $name)){
                if (is_string($fc)){
                    if (method_exists($this, $fc)){
                        return call_user_func_array([$this, $fc], $args);
                    }
                }
                if (is_callable($fc)){
                    return $fc($this);
                }
            } else {
                if (method_exists($this, $fc = ltrim($name, '@'))){
                    return call_user_func_array([$this, $fc], $args);
                }
            }
    }
}