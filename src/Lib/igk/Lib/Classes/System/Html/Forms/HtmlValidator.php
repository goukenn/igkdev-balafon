<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlValidator.php
// @date: 20230117 15:16:11
namespace IGK\System\Html\Forms;

use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use ReflectionException;

///<summary></summary>
/**
* use to manage data to validate
* @package IGK\System\Html\Forms
*/
class HtmlValidator implements IFormValidator{
    var $skip_all;
    var $allowed_tags;

    public function validate($value, $default = null, $fieldinfo = null, &$error = []) { 
        $value = $this->treatValue($value);
        return $value;
    }
    /**
     * skip tag content validator
     * @param mixed $value 
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function treatvalue($value){
        $ln = strlen($value);
        $cpos = $pos = 0;
        $output = "";
        while($pos<$ln){
            if (($pos = strpos($value, '<', $pos))!==false){
                $output.= substr($value, $cpos, $pos - $cpos);
                $pos++; 
                $end = false;               
                $name = self::ReadTagName($value, $pos, $ln, $end);
                $content = self::GetAttributeContent($value, $pos, $ln);   
                if ($this->skip_all || !isset($this->allowed_tags[$name])){
                    $content = "";
                }else{
                    $output.=$content.">";
                }  
                $cpos = $pos;
            }else{
                break;
            }
        }
        $output.= substr($value, $cpos);
        return $output;
    }
    public static function GetAttributeContent($value, &$pos, $ln){
        $content = "";
        $empty = false;
        while($pos<$ln){
            $ch = $value[$pos];
            if ($empty && ($ch!=">")){
                igk_die("not a valid html definition");
            }
            switch ($ch) {
                case '"':
                case "'":
                    $content.= igk_str_read_brank($ch,$pos, $ch, $ch);
                    break;                        
                case '>':
                    // end reading tag
                    $pos++;
                    return $content;                     
                    $ch="";
                    break;
                case '/':
                    $empty = true;
                    break;
            }
            $content.=$ch;
            $pos++;
        }
        return null;
    }
    public static function ReadTagName(string $value, & $pos, $ln, & $end){
        $n = "";
        $end = false;
        while($pos<$ln){
            $ch = $value[$pos];
            if (!$end && ($ch=="/")){
                $end = true;                 
                $pos++;
                continue;
            }
            if (strpos(IGK_IDENTIFIER_TAG_CHARS, $ch) === false) {
                break;
            }
            $n.=$ch;
            $pos++;
        }
        return $n;
    }


}