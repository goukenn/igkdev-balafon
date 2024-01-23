<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhpDocBlockBase.php
// @date: 20230731 10:39:26
namespace IGK\System\IO\File\Php;


///<summary></summary>
/**
* 
* @package IGK\System\IO\File\Php
*/
abstract class PhpDocBlockBase{
    const NAME_TOKEN = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-\\';

    protected function _readName($t, & $offset){
        $ln = strlen($t);
        $s  = "";
        while($offset<$ln){
            $ch = $t[$offset];
            if (strpos(self::NAME_TOKEN, $ch) === false){
                break;
            }
            $offset++;
            $s.= $ch;
        }
        return $s;
    }
    /**
     * treat content
     * @param string $content 
     * @return string 
     */
    protected static function _TreatContent(string $content){
        if (igk_str_endwith($content, "\\")){
            $content.="\n";
        }   
        return $content;
    }
}