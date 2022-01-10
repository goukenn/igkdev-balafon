<?php
namespace IGK\Helper;

use IGK\System\Html\HtmlUtils;

///<summary>String utility </summary>
abstract class StringUtility{
    /**
     * convert to uri presentation
     */
    public static function Uri($u){
        return str_replace("\\", "/", $u);
    }
    public static function UriCombine(...$args){
        return self::Uri(implode("/", $args));
    }
    /**
     * convert to path presentation
     */
    public static function Dir($dir, $separator = DIRECTORY_SEPARATOR){
        $g = self::Uri($dir);
        if ($separator="/")
            return $g;
        $g = str_replace("/", $dir, $g);
        return $g;
    }

    ///<summary></summary>
    ///<param name="text"></param>
    ///<param name="pattern"></param>
    /**
    * 
    * @param mixed $text
    * @param mixed $pattern
    */
    public static function Contains($text, $pattern){
        if(!empty($pattern))
            return (strstr($text, $pattern) != null);
        return true;
    }
    ///<summary></summary>
    ///<param name="chaine"></param>
    ///<param name="pattern"></param>
    /**
    * 
    * @param mixed $chaine
    * @param mixed $pattern
    */
    public static function EndWith($chaine, $pattern){
        $chaine=trim($chaine);
        $c=strlen($chaine);
        $p=strlen($pattern);
        $i=strripos($chaine, $pattern);
        if($i === false){
            return false;
        }
        if(($i != -1) && (($i + $p) === $c))
            return true;
        return false;
    }
    ///<summary></summary>
    ///<param name="s"></param>
    /**
    * regex detection of formatted string
    * @param string $s formatted string. 
    */
    public static function Format($s){
        $c=preg_match_all("/\{(?P<value>[0-9]+)\}/i", $s, $match);
        if($c > 0){
            $args=array_slice(func_get_args(), 1);
            for($i=0; $i < $c; $i++){
                $index=$match["value"][$i];
                if(is_numeric($index)){
                    if(isset($args[$index])){
                        $s=str_replace($match[0][$i], HtmlUtils::GetValue($args[$index]), $s);
                    }
                }
            }
        }
        return $s;
    }
    ///<summary></summary>
    ///<param name="chaine"></param>
    ///<param name="research"></param>
    ///<param name="offset"></param>
    //@chaine : string where to operate
    /**
    * 
    * @param mixed $chaine
    * @param mixed $research
    * @param mixed $offset the default value is 0
    */
    public static function IndexOf($chaine, $research, $offset=0){
        if(empty($chaine) || empty($research))
            return -1;
        $i=strpos($chaine, $research, $offset);
        if($i === false)
            return -1;
        return $i;
    }
    ///<summary></summary>
    ///<param name="tab"></param>
    ///<param name="separator" default=","></param>
    ///<param name="key" default="true"></param>
    /**
    * 
    * @param mixed $tab
    * @param mixed $separator the default value is ","
    * @param mixed $key the default value is true
    */
    public static function Join($tab, $separator=",", $key=true){
        $s=IGK_STR_EMPTY;
        $t=0;
        if($tab){
            foreach($tab as $k=>$v){
                if($t == 1)
                    $s .= $separator;
                if($key)
                    $s .= $k;
                else
                    $s .= "".$v;
                $t=1;
            }
        }
        return $s;
    }
    ///<summary></summary>
    ///<param name="chaine"></param>
    ///<param name="pattern"></param>
    /**
    * 
    * @param mixed $chaine
    * @param mixed $pattern
    */
    public static function StartWith($chaine, $pattern){
        return (self::IndexOf($chaine, $pattern) == 0);
    }
    ///<summary></summary>
    ///<param name="chaine"></param>
    ///<param name="start"></param>
    ///<param name="length" default="null"></param>
    //@personal sub
    /**
    * 
    * @param mixed $chaine
    * @param mixed $start
    * @param mixed $length the default value is null
    */
    public static function Sub($chaine, $start, $length=null){
        if($length){
            return substr($chaine, $start, $length);
        }
        else
            return substr($chaine, $start);
    }
}