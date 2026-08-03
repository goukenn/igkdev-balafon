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
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;
use IGKException;
use ReflectionException;
/**
* auto generate doc.
* @package IGK\Helper
*/
/**
 * auto generate doc.
 * @package IGK\Helper
 */
abstract class StringUtility
{
    /**
     * Constant: identifier token.
     * @var mixed
     */
    const IDENTIFIER_TOKEN = "_1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    /**
     * Constant: default trim char.
     * @var mixed
     */
    const DEFAULT_TRIM_CHAR = " \n\r\t\v\0";
    /**
     * auto generate doc.
     * @param array *71280add
     * @return bool
     */
    public static function StrArrayContains(string $name, array $list): bool
    {
        $l = $list;
        while (count($l)) {
            $q = array_shift($l);
            if (strpos($name, $q) !== false) {
                return true;
            }
        }
        return false;
    }
    /**
     * get function name
     * @param string $s 
     * @return string 
     */
    public static function FuncName(string $s): string
    {
        $s = preg_replace("/[^a-z_]/i", "_", $s);
        $s = preg_replace("/_+/i", "_", $s);
        return $s;
    }
    /**
    * auto append prefix to column
    * @param string $column
    * @param null|string $prefix
    * @return string
    */
    public static function AutoPrefix(string $column, ?string $prefix = null): string
    {
        if (empty($prefix)) {
            return $column;
        }
        if (!igk_str_startwith($column, $prefix)) {
            $column = $prefix . $column;
        }
        return $column;
    }
    /**
     * auto generate doc.
     * @param array $tab
     * @return string
     */
    public static function DumpArray(array $tab): string
    {
        $sb = new StringBuilder;
        $ch = '';
        foreach ($tab as $k => $v) {
            if (is_numeric($k)) {
                if (is_numeric($v)) {
                    $sb->appendLine($ch . $v . '');
                } else
                    $sb->appendLine($ch . '"' . $v . '"');
            } else
                $sb->appendLine($ch . '"' . $k . '"=>"' . $v . '"');
            $ch = ',';
        }
        return $sb;
    }
    /**
    * read line utility class
    * @param string $content
    * @param int & $pos
    * @return string
    */
    public static function ReadLine(string $content, int &$pos)
    {
        $lin = strpos($content, "\n", $pos);
        $s = 0;
        if (false === $lin) {
            $s = substr($content, $pos);
            $pos = strlen($content);
        } else {
            $s = substr($content, $pos, $lin - $pos);
            $pos = $lin;
        }
        return $s;
    }
    /**
     * reduction condition block code 
     * @param string $condition 
     * @return string 
     */
    public static function ReduceConditionBlock(string $condition)
    {
        $g = $condition;
        $v_regex = "/[\(\)\s]/";
        $g = preg_replace("/\s+/", " ", $g);
        while (strpos($g, '(') === 0) {
            $cpos = 0;
            $tg = trim(igk_str_rm_last(
                igk_str_rm_start(
                    igk_str_read_brank($g, $cpos, ')', '('),
                    '('
                ),
                ')'
            ));
            $fg = preg_replace($v_regex, '', $g);
            $ffg = preg_replace($v_regex, '', $tg);
            if ($fg !== $ffg) {
                break;
            }
            $g = $tg;
        }
        return $g;
    }
    /**
    * helper to read brank
    * @param string $ln
    * @param int & $pos
    * @return mixed
    */
    public static function ReadBrank(string $ln, int &$pos)
    {
        $ch = $ln[$pos];
        switch ($ch) {
            case "'":
            case '"':
                $ch = igk_str_read_brank($ln, $pos, $ch, $ch);
                break;
            case '{':
                $ch = igk_str_read_brank($ln, $pos, '}', '{');
                break;
            case '(':
                $ch = igk_str_read_brank($ln, $pos, ')', '(');
                break;
            case '[':
                $ch = igk_str_read_brank($ln, $pos, ']', '[');
                break;
        }
        return $ch;
    }
    /**
     * Not null or empty filter callback.
     */
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
    public static function GetConstantName(string $s)
    {
        return strtoupper(self::GetSnakeKebab($s, false));
    }
    /**
    * skake kebab data
    * @param string $haystack
    * @param ?bool $hiphen
    * @return string
    */
    public static function GetSnakeKebab(string $haystack, ?bool $hiphen = false)
    {
        $s_out = '';
        $haystack = preg_replace('/[^_a-z]/i', '', $haystack);
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
                        $s_out .= $sep . ucfirst($word);
                        $word = '';
                        $sep = '_';
                    }
                    $m = 1;
                }
            } else {
                if (!$in_split) {
                    $m = 0;
                }
                $ch = strtolower($ch);
            }
            if ($ch == '_') {
                $ch = '';
            }
            $word .= $ch;
            $pos++;
        }
        if ($w = ucfirst(trim($word)))
            $s_out .= $sep . $w;
        if ($hiphen) {
            return str_replace('_', '-', $s_out);
        }
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
     * auto generate doc.
     * @param string $text
     * @return string
     */
    public static function RemoveAccents(string $text)
    {
        $accents = [
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ä' => 'a',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ç' => 'c',
            'ñ' => 'n',
            '@' => 'a',
            'ô' => 'o',
            'ö' => 'o',
            'ÿ' => 'y',
        ];
        return strtr($text, $accents);
    }
    /**
     * slugify text
     * @param string $text 
     * @return string 
     */
    public static function Slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = self::RemoveAccents($text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        $text = preg_replace("/^d+-/", '', $text);
        return $text;
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
    /**
    * auto generate doc.
    * @param string $name
    * @param null|string $controller
    * @return string
    */
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
    /**
     * Returns Application Mail Title.
     * @param BaseController $controller
     * @param null|string $title
     */
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
    /**
     * Name display.
     * @param null|string $firstname
     * @param null|string $lastname
     */
    public static function NameDisplay(?string $firstname = null, ?string $lastname = null)
    {
        return implode(" ", array_filter([ucfirst($firstname ?? ""), strtoupper($lastname ?? "")]));
    }
    /**
     * Date display.
     * @param mixed $date
     * @param mixed $in
     * @param null|string $out
     */
    public static function DateDisplay($date, $in = "Y-m-d", ?string $out = null)
    {
        if ($out === null) {
            $out = igk_configs()->get("date_display_format", "M d, Y");
        }
        return igk_format_date($date, $in, $out);
    }
    /**
     * Location display.
     * @param null|string $location
     */
    public static function LocationDisplay(?string $location = null)
    {
        return $location;
    }
    /**
     * Rm sub string.
     * @param string $str
     * @param mixed $offset
     * @param int $length
     */
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
    /**
     * Identifier.
     * @param string $n
     */
    public static function Identifier(string $n)
    {
        $rx =  "/^" . IGK_IDENTIFIER_RX . "$/i";
        if (preg_match($rx, $n)) {
            return $n;
        }
        // + | replace all non valid symbol to _
        $n = preg_replace("#[^0-9a-z]#i", "_", $n);
        $n = ucwords(ucfirst($n), "_");
        if (!preg_match($rx, $n))
            return null;
        return $n;
    }
    /**
     * Sanitize line.
     * @param string $str
     */
    public static function SanitizeLine(string $str)
    {
        $t = preg_split("/(\r\n)|(\n)|(\t)/i", $str);
        return implode("", array_filter($t, function ($i) {
            return empty(trim($i)) ? null : $i;
        }));
    }
    /**
    * convert to uri presentation
    * @param ?string $u
    */
    public static function Uri(?string $u = "")
    {
        if ($u === null)
            return $u;
        return str_replace("\\", "/", $u);
    }
    /**
    * Uri combine.
    * @param mixed ...$args
    */
    public static function UriCombine(...$args)
    {
        return self::Uri(implode("/", $args));
    }
    /**
    * convert to path presentation
    * @param mixed $dir
    * @param mixed $separator
    */
    public static function Dir($dir, $separator = DIRECTORY_SEPARATOR)
    {
        $g = self::Uri($dir);
        if ($separator = "/")
            return $g;
        $g = str_replace("/", $dir, $g);
        return $g;
    }
    /**
    * auto generate doc.
    * @param mixed $text
    * @param mixed $pattern
    */
    public static function Contains($text, $pattern)
    {
        if (!empty($pattern))
            return (strstr($text, $pattern) != null);
        return true;
    }
    /**
    * auto generate doc.
    * @param mixed $chaine
    * @param mixed $pattern
    */
    public static function EndWith($chaine, $pattern)
    {
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
    /**
    * auto generate doc.
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
    /**
    * auto generate doc.
    * @param mixed $tab
    * @param mixed $separator
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
    /**
    * auto generate doc.
    * @param mixed $chaine
    * @param mixed $pattern
    */
    public static function StartWith($chaine, $pattern)
    {
        return (self::IndexOf($chaine, $pattern) == 0);
    }
    /**
    * auto generate doc.
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
    * @param int & $offset
    * @param int $offset
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
     * Single quote replace.
     * @param string $src
     */
    public static function SingleQuoteReplace(string $src)
    {
        $ctn = new RegexMatcherContainer;
        $s = $ctn->begin("(')", "\\1", 'string-to-quote')->last();
        $s->match("\\\\.", 'espaced');
        $s = $ctn->begin("(\")", "\\1", 'stringLitteral')->last();
        $s->match("\\\\.", 'espaced');
        $src = $ctn->replace($src, function ($e, $o, &$toffset) {
            if ($e->tokenID == "string-to-quote") {
                return sprintf('"%s"', igk_str_remove_quote($e->value));
            }
            return $e->value;
        });
        return $src;
    }
    /**
    * auto generate doc.
    * @param string $data
    * @param string $separator
    * @return array
    */
    public static function ReadArgs(string $data, $separator = ",")
    {
        if (preg_match("/['\"]/", $separator)) {
            igk_die("separator not valid");
        }
        $data = self::SingleQuoteReplace($data);
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
                    if (!empty($k)) {
                        $args[$k] = $ps;
                    } else {
                        $args[] = $ps;
                    }
                    $v = "";
                    $k = '';
                    break;
                case $separator:
                    if (!empty($v)) {
                        if (empty($k)) {
                            $args[] = $v;
                        } else {
                            $args[$k] = $v;
                        }
                    }
                    $v = "";
                    $k = "";
                    break;
                case '=':
                    $k = trim($v);
                    $v = '';
                    break;
                default:
                    if ($ch == "{") {
                        $b = igk_str_read_brank($data, $pos, '}', $ch);
                        if ($tab = json_decode($b)) {
                            if (!empty($k)) {
                                $args[$k] = $tab;
                            } else {
                                $args[] = $tab;
                            }
                            $k = $v = '';
                        } else {
                            $v .= $b;
                        }
                        $ch = '';
                    }
                    if ($ch == "[") {
                        // + support read array 
                        $b = igk_str_read_brank($data, $pos, ']', $ch);
                        if ($tab = json_decode($b)) {
                            if (!empty($k)) {
                                $args[$k] = $tab;
                            } else {
                                $args[] = $tab;
                            }
                            $k = $v = '';
                        } else {
                            $error = json_last_error_msg();
                            $l = self::ReadArrayConstants($b);
                            $c = empty($k) ?
                                [$l] : [$k => $l];
                            $args = array_merge($args, $c);
                        }
                        $ch = '';
                    }
                    $v .= $ch;
                    break;
            }
            $pos++;
        }
        if (!empty($v)) {
            $v = trim($v);
            if (!empty($k))
                $args[$k] = $v;
            else
                $args[] = $v;
        }
        return $args;
    }
    /**
     * Reads Array Constants.
     * @param mixed $v
     */
    public static function ReadArrayConstants($v)
    {
        $c = new RegexMatcherContainer;
        $g = $c->begin('\[', '\]')->last();
        $string = $c->appendStringDetection('string')->last();
        $constant = $c->match("(?i)([a-z_][a-z0-9_]*)", 'constants')->last();
        $sep = $c->match(",", 'sep')->last();
        $glue = $c->match("\.", 'glue')->last();
        $g->patterns = [
            $string,
            $constant,
            $glue,
            $sep,
            $g
        ];
        $pos = 0;
        $temp = '';
        $r = [];
        $glue = false;
        while ($g = $c->detect($v, $pos)) {
            if ($e = $c->end($g, $v, $pos)) {
                if ($e->tokenID == 'sep') {
                    if ($temp) {
                        $r[] = $temp;
                        $temp = '';
                    }
                    continue;
                }
                if ($e->tokenID == 'glue') {
                    $glue = true;
                    continue;
                }
                if ($e->tokenID) {
                    $tv = $e->value;
                    if ($e->tokenID == 'string') {
                        $tv = igk_str_remove_quote($tv);
                    }
                    if ($temp) {
                        if ($glue) {
                            $temp .= $tv;
                            $glue =  false;
                        }
                    } else {
                        $temp = $tv;
                        $glue = false;
                    }
                }
            }
        }
        if ($temp) {
            $r[] = $temp;
        }
        return $r;
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
    /**
     * Displays Address.
     * @param null|string $street
     * @param null|string $number
     * @param null|string $box
     * @param null|string $city
     * @param null|int $postalCode
     * @param null|mixed $country
     */
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
    public static function ArrayToEnvironment($tab): string
    {
        return implode("\n", array_filter(array_map(function ($v, $k) {
            if (!$v) {
                return null;
            }
            return $k . '=' . $v;
        }, $tab, array_keys($tab))));
    }
    /**
     * auto generate doc.
     * @param string $value
     * @return string
     */
    public static function ConstantToCamelCaseClassName(string $value): string
    {
        return implode("", array_map("ucfirst", array_map("strtolower", explode("_", $value))));
    }
    /**
     * convert path to class name 
     * @param string $value 
     * @return string 
     */
    public static function PathToClassName(string $value): string
    {
        $value = str_replace('-', '_', $value);
        $action_name = implode('', array_map('ucfirst',  array_filter(explode('_', $value))));
        return implode("/", array_map('ucfirst', explode('/', $action_name)));
    }
    /**
     * split with non escaped char
     * @param string $haystack 
     * @param string $char split char
     * @return string[] 
     */
    public static function SplitWithNonEscapedChar(string $haystack, string $char)
    {
        $offset = 0;
        $tab = [];
        $i = 0;
        while (($i = strpos($haystack, $char, $i)) !== false) {
            if ($i > 0) {
                if ($haystack[$i - 1] == '\\') {
                    $i++;
                    continue;
                }
            }
            $tab[] = substr($haystack, $offset, $i - $offset);
            $offset = $i + 1;
            $i++;
        }
        if ($c = substr($haystack, $offset)) {
            $tab[] = $c;
        }
        return $tab;
    }
    /**
     * auto generate doc.
     * @param string $haystack
     * @param array $range
     * @return string[]|array<int, string>
     */
    public static function SplitLitteral(string $haystack, array $range)
    {
        $v_offset = 0;
        $v_t = [];
        while (count($range) > 0) {
            $q = array_shift($range);
            if (is_array($q)) {
                $to = $q[1];
                $q = $q[0];
            } else {
                $to = $q + 1;
            }
            if ($q < $v_offset) {
                return $v_t;
            }
            $s = substr($haystack, $v_offset, $q - $v_offset);
            $v_offset = $to;
            $v_t[] = $s;
        }
        if (!empty(trim($s = substr($haystack, $v_offset)))) {
            $v_t[] = $s;
        }
        return array_filter($v_t);
    }
    /**
    * auto generate doc.
    * @param mixed $haystack
    * @param array $range
    * @param bool $infinite
    * @return string[]
    */
    public static function SplitRange($haystack, array $range, bool $infinite = true): array
    {
        $r = [];
        while (count($range)) {
            $q = array_shift($range);
            $c = explode($q, $haystack, 2);
            if (count($c) == 2) {
                $r[] = $c[0];
                $haystack = $c[1];
                if ($infinite)
                    $range[] = $q;
            } else {
                $r[] = $haystack;
                $haystack = null;
                break;
            }
        }
        if ($haystack) {
            $r[] = $haystack;
        }
        return $r;
    }
    /**
    * auto generate doc.
    * @param string $p
    * @param int $spaceLineDepth
    * @return int
    */
    public static function GetTabStopDepth(string $p, $spaceLineDepth = 4): int
    {
        $ln = strlen($p);
        if ($p == "\t") {
            return $ln;
        }
        if (($spaceLineDepth > 0) && (($ln % $spaceLineDepth) == 0)) {
            $ln = $ln / $spaceLineDepth;
        }
        return intval($ln);
    }

    public static function EscapeChar(string $l, string $char = RegexMatcherUtility::DEFAULT_ESCAPED_LIST){
        $t = array_unique(str_split($char, 1));
        foreach($t as $k){
            $l = str_replace($k, '\\'.$k, $l);
        }
        return $l;
    }
}
