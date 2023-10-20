<?php
// @author: C.A.D. BONDJE DOUE
// @filename: StringUtility.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Helper;

use IGK\Controllers\BaseController;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\HtmlUtils;
use IGK\System\IO\StringBuilder;
use IGK\System\Regex\Replacement;
use IGKException;
use ReflectionException;

///<summary>String utility helper. store string help function  </summary>
/**
 * 
 * @package IGK\Helper
 */
abstract class StringUtility
{
    const IDENTIFIER_TOKEN = "_1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    /**
     * helper to read brank
     * @param string $str 
     * @param int $pos 
     * @return mixed 
     */
    public static function ReadBrank(string $ln, int & $pos ){
        $ch = $ln[$pos];
        switch($ch){
            case "'":
            case '"':
                $ch = igk_str_read_brank($ln, $pos, $ch, $ch);
                break;
            case '{':
                $ch = igk_str_read_brank($ln, $pos, '}','{');
                break;
            case '(':
                    $ch = igk_str_read_brank($ln, $pos, ')','(');
                break;
            case '[':
                $ch = igk_str_read_brank($ln, $pos, ']','[');
                break;
        }
        return $ch;
    }
    public static function NotNullOrEmptyFilterCallback()
    {
        return function ($a) {
            if (is_null($a)) {
                return false;
            }
            if (is_string($a) && (strlen(trim($a)) == 0)) {
                return false;
            }
            return $a;
        };
    }
    /**
     * get constant name
     * @param string $s 
     * @param string $splitter 
     * @return string 
     */
    public static function GetConstantName(string $s, string $splitter = "/[A-Z0-9]+/")
    {
        return strtoupper(self::GetSnakeKebab($s, $splitter));
    }
    /**
     * skake kebab data
     * @param string $haystack 
     * @param string $splitter 
     * @return string 
     */
    public static function GetSnakeKebab(string $haystack, string $splitter = "")
    {
        $s_out = '';
        $haystack = preg_replace('/[^_a-z]/i','', $haystack);
        $ln = strlen($haystack);
        $letter = '_ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pos = 0;
        $m = 0;
        $sep = '';
        $word = '';
        while ($pos < $ln) {
            $ch = $haystack[$pos];
            $in_split = false !== strpos($letter, $ch);
            if (!$m) {
                if ($in_split) {                  
                    if (!empty($word)) {
                        $s_out .= $sep.ucfirst($word);
                        $word = '';
                        $sep = '_';
                    } 
                    $m = 1;
                }
            }else{
                if(!$in_split){
                    $m = 0;
                }
                $ch = strtolower($ch);
            }
            if ($ch=='_'){
                $ch = '';
            }
            $word .= $ch;
            $pos++;
        }
        if($w = ucfirst(trim($word)))
            $s_out .= $sep.$w;
        return $s_out; 
    }
    /**
     * remove quote from string 
     * @param string $data 
     * @param string $start_quote 
     * @param null|string $end_quote 
     * @return string new string
     */
    public static function RemoveQuote(string $data, string $start_quote = '"', ?string $end_quote = null)
    {
        $end_quote = $end_quote ?? $start_quote;
        if (strpos($data, $start_quote) === 0) {
            $data = substr($data, 1);
            if (strpos($data, $end_quote, -1) !== false) {
                $data = substr($data, 0, -1);
            }
        }
        return $data;
    }
    /**
     * get name_space 
     * @param string $namespace 
     * @return string 
     */
    public static function NS(string $namespace): string
    {

        $ns = str_replace("/", "\\", $namespace);
        $ns = trim(str_replace(" ", "", $ns));
        return $ns;
    }
    public static function AuthorizationPath(string $name, ?string $controller): string
    {
        return implode("@", array_filter([$controller, $name]));
    }
    /**
     * helper to retrieve key name
     * @param BaseController $controller 
     * @return string 
     */
    public static function GetControllerKeyName(BaseController $controller): string
    {
        return igk_uri(get_class($controller));
    }
    public static function GetApplicationMailTitle(BaseController $controller, ?string $title = null)
    {
        return $title ??
            $controller->getConfig('domain') ??
            igk_configs()->system_mail_title ??
            igk_configs()->domain;
    }
    /**
     * display name
     * @param string $firstName 
     * @param string $lastName 
     * @return string|null 
     */
    public static function DisplayName(?string $firstName = null, ?string $lastName = null): ?string
    {
        $r = null;
        if ($d = array_filter([$firstName, strtoupper($lastName ?? '')])) {
            $r = implode(' ', $d);
        }
        return $r;
    }
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
                    $index = intval($index);
                    $a = igk_getv($args, $index);                    
                    $s = str_replace($match[0][$i], HtmlUtils::GetValue($a) ?? '', $s);                    
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
        $k = "";
        while ($ln > $pos) {
            $ch = $data[$pos];
            switch ($ch) {
                case "'":
                case '"':
                    $ps = self::StringValue(igk_str_read_brank($data, $pos, $ch, $ch, null, false, 1), $ch);
                    if (!empty($k)){
                        $args[$k] = $ps;
                    }else{
                        $args[] = $ps;
                    }
                    $v = "";
                    $k = '';
                    break;
                case $separator:
                    if (!empty($v)){
                        $args[$k] = $v;
                    }
                    $v = "";
                    $k = "";
                    break;
                case '=':
                    $k = trim($v);
                    $v = '';
                    break;
                default:
                    $v .= $ch;
                    break;
            }
            $pos++;
        }
        if (!empty($v)){
            $v = trim($v);
            if (!empty($k))
                $args[$k] = $v;
            else 
                $args[] = $v;
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

        return  substr($haystack, 0, $offset) .
            $insert . substr(
                $haystack,
                $offset + $length
            );
    }

    public static function DisplayAddress(
        ?string $street = null,
        ?string $number = null,
        ?string $box = null,
        ?string $city = null,
        ?int $postalCode = null,
        $country = null
    ) {
        $sb = new StringBuilder;
        if ($street) {
            $sb->append($street);
            if ($number)
                $sb->append(" - " . $number);
            if ($box)
                $sb->append("/" . $number);
            $sb->appendLine();
        }
        if ($city) {
            if ($postalCode)
                $sb->append(sprintf("%s - ", $postalCode));
            $sb->appendLine(sprintf("%s", $city));
        }
        if ($country) {
            $sb->appendLine(__('country.' . $country));
            // $sb->appendLine(\IGK\Models\Countries::GetCache('clISO', $country));
        }
        return $sb . "";
    }
    /**
     * helper function resources if not null
     * @param mixed $value 
     * @param mixed $format 
     * @return mixed 
     */
    public static function FormatIfNotNull($value, $format)
    {
        if (!is_null($value)) {
            if (is_string($format)) {
                return __($format, $value);
            }
            return $format($value);
        }
        return null;
    }
    /**
     * sanitize text and return a identifer 
     * @param string $identifer 
     * @return string
     */
    public static function SanitizeIdentifier(string $identifer): string
    {
        $rp = new Replacement();
        $rp->add('/\s+/', '')
            ->add('/[^' . self::IDENTIFIER_TOKEN . ']/', '_')
            ->add('/^[0-9]/', '_\\0');
        $identifer = $rp->replace(trim($identifer));
        return $identifer;
    }

    /**
     * array to environment - filter value
     * @param mixed $tab 
     * @return string 
     */
    public static function ArrayToEnvironment($tab):string{   
       return implode("\n", array_filter(array_map(function($v,$k){
            if (!$v){
                return null;
            }
            return $k.'='.$v;
        }, $tab, array_keys($tab))));
    }
}
