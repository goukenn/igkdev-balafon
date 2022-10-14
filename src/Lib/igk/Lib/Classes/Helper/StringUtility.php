<?php
// @author: C.A.D. BONDJE DOUE
// @filename: StringUtility.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Helper;

use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\HtmlUtils;
use IGKException;
use ReflectionException;

///<summary>String utility helper </summary>
abstract class StringUtility
{
    const IDENTIFIER_TOKEN = "_1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    /**
     * check if uri start with compare
     * @param string $haystack source uri
     * @param string $compare uri to compare
     * @return bool
     * @throws IGKException 
     */
    public static function UriStart(string $haystack, string $compare): bool
    {

        $haystack = rtrim($haystack, "/");
        $compare = rtrim($compare, "/");
        if (strpos($haystack, $compare) === 0) {
            $u = rtrim(parse_url($haystack)["path"], "/");
            $v = rtrim(parse_url($compare)["path"], "/");


            return (bool)preg_match("#" . $v . "(/(.+)|\?|\#|$)#", $u);
        }
        return false;
    }
    public static function NameDisplay(?string $firstname = null, ?string $lastname = null)
    {
        return implode(" ", array_filter([ucfirst($firstname ?? ""), strtoupper($lastname ?? "")]));
    }
    public static function DateDisplay($date, $in = "Y-m-d", ?string $out = null)
    {
        if ($out === null) {
            $out = igk_configs()->get("date_display_format", "M d, Y");
        }
        //return igk_format_date($date, "Y-m-d", igk_configs()->get("date_display_format", "Y-m-d"));
        return igk_format_date($date, $in, $out);
    }
    public static function LocationDisplay(?string $location = null)
    {
        return $location;
    }
    public static function RmSubString(string $str, $offset, int $length)
    {

        return substr($str, 0, $offset) . substr($str, $offset + $length);
    }
    /**
     * get camel class name
     * @param string $name 
     * @return string 
     */
    public static function CamelClassName(?string $name = null)
    {
        if ($name == null)
            return $name;
        $name = preg_replace("#[^0-9a-z]#i", "_", $name);
        return str_replace("_", "", ucwords(ucfirst($name), "_"));
    }

    public static function Identifier(string $n)
    {
        $rx =  "/^" . IGK_IDENTIFIER_RX . "$/i";
        if (preg_match($rx, $n)) {
            return $n;
        }
        // + | replace all non valid symbol to _
        //$_under = strlen($n) - strlen(ltrim($n, '_'));
        $n = preg_replace("#[^0-9a-z]#i", "_", $n);
        $n = ucwords(ucfirst($n), "_");
        // $n = str_repeat("_", $_under).$n;
        // igk_wln("test : ".$n. " :: ");
        if (!preg_match($rx, $n))
            return null;
        return $n;
    }
    public static function SanitizeLine(string $str)
    {
        $t = preg_split("/(\r\n)|(\n)|(\t)/i", $str);
        return implode("", array_filter($t, function ($i) {
            return empty(trim($i)) ? null : $i;
        }));
    }
    /**
     * convert to uri presentation
     */
    public static function Uri(?string $u = "")
    {
        if ($u === null)
            return $u;
        return str_replace("\\", "/", $u);
    }
    public static function UriCombine(...$args)
    {
        return self::Uri(implode("/", $args));
    }
    /**
     * convert to path presentation
     */
    public static function Dir($dir, $separator = DIRECTORY_SEPARATOR)
    {
        $g = self::Uri($dir);
        if ($separator = "/")
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
    public static function Contains($text, $pattern)
    {
        if (!empty($pattern))
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
    public static function EndWith($chaine, $pattern)
    {
        $chaine = trim($chaine);
        $c = strlen($chaine);
        $p = strlen($pattern);
        $i = strripos($chaine, $pattern);
        if ($i === false) {
            return false;
        }
        if (($i != -1) && (($i + $p) === $c))
            return true;
        return false;
    }
    ///<summary></summary>
    ///<param name="s"></param>
    /**
     * regex detection of formatted string
     * @param string $s formatted string. 
     */
    public static function Format($s)
    {
        $c = preg_match_all("/\{(?P<value>[0-9]+)\}/i", $s, $match);
        if ($c > 0) {
            $args = array_slice(func_get_args(), 1);
            for ($i = 0; $i < $c; $i++) {
                $index = $match["value"][$i];
                if (is_numeric($index)) {
                    if (isset($args[$index])) {
                        $s = str_replace($match[0][$i], HtmlUtils::GetValue($args[$index]), $s);
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
    public static function IndexOf($chaine, $research, $offset = 0)
    {
        if (empty($chaine) || empty($research))
            return -1;
        $i = strpos($chaine, $research, $offset);
        if ($i === false)
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
    public static function Join($tab, $separator = ",", $key = true)
    {
        $s = IGK_STR_EMPTY;
        $t = 0;
        if ($tab) {
            foreach ($tab as $k => $v) {
                if ($t == 1)
                    $s .= $separator;
                if ($key)
                    $s .= $k;
                else
                    $s .= "" . $v;
                $t = 1;
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
    public static function StartWith($chaine, $pattern)
    {
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
    public static function Sub($chaine, $start, $length = null)
    {
        if ($length) {
            return substr($chaine, $start, $length);
        } else
            return substr($chaine, $start);
    }

    /**
     * read identifier token
     * @param string $hastack 
     * @param int $offset 
     * @param string $token 
     * @return string 
     */
    public static function ReadIdentifier(string $hastack, int &$offset = 1, string $token = self::IDENTIFIER_TOKEN): string
    {
        $s = "";
        $ln = strlen($hastack);
        while (($offset < $ln) && (strpos($token, $ch =  $hastack[$offset]) !== false)) {
            $offset++;
            $s .= $ch;
        }
        return $s;
    }
    /**
     * indent line 
     * @param string $data 
     * @param string $tab 
     * @return string 
     */
    public static function IndentContent(string $data, $tab = "\t")
    {
        $data = implode("\n", array_map(function ($s) use ($tab) {
            return $tab . $s;
        }, explode("\n", $data)));
        return $data;
    }
    /**
     * 
     * @param string $data 
     * @param string $separator 
     * @return array 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function ReadArgs(string $data, $separator = ",")
    {
        if (preg_match("/['\"]/", $separator)) {
            igk_die("separator not valid");
        }
        $ln = strlen($data);
        $args = [];
        $pos = 0;
        $v = "";
        while ($ln > $pos) {
            $ch = $data[$pos];
            switch ($ch) {
                case "'":
                case '"':
                    $args[] = self::StringValue(igk_str_read_brank($data, $pos, $ch, $ch, null, false, 1), $ch);
                    break;
                case $separator:
                    $args[] = $v;
                    $v = "";
                    break;
                default:
                    $v .= $ch;
                    break;
            }
            $pos++;
        }
        return $args;
    }
    /**
     * get inner string value
     * @param mixed $v 
     * @param string $ch 
     * @return string
     */
    public static function StringValue(string $v, $ch = "'"): string
    {
        if ((strpos($v, $ch) === 0) &&
            (strrpos($v, $ch, -1) !== 0)
        ) {
            $v = substr($v, 1, strlen($v) - 2);
        }
        return $v;
    }

    /**
     * insert string at offset
     * @param string $haystack string to modify 
     * @param string $insert the inserted string 
     * @param int $offset the offset
     * @return string 
     */
    public static function Insert(string $haystack, string $insert, int $offset)
    {
        return substr($haystack, 0, $offset) .
            $insert . substr($haystack, $offset);
    }
    /**
     * replace at offset 
     * @param string $haystack 
     * @param string $insert 
     * @param int $offset 
     * @param int $length 
     * @return string 
     */
    public static function ReplaceAtOffset(string $haystack, string $insert, int $offset, int $length)
    {
     
        return  substr($haystack, 0, $offset ) .
            $insert . substr(
                $haystack,
                $offset + $length
            );
    }
}
