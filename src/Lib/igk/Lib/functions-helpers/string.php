<?php
// @author: C.A.D. BONDJE DOUE
// @filename: string.php
// @date: 20230302 12:55:00
// @desc: string method helpers
use IGK\Helper\StringUtility as stringUtility;
use IGK\System\Console\Logger;
use IGK\System\Text\RegexDetectBuffer;
use IGK\System\Text\RegexDetectHandler;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;

if (!function_exists('igk_str_add_suffix')){

/**
* auto generate doc.
* @param string $content
* @param string $suffix
* @param bool $check
* @return string
*/
function igk_str_add_suffix(string $content, string $suffix, bool $check=true):string{
        if ($check && igk_str_endwith($content, $suffix) ){
            return $content;
        }
        return $content.$suffix;
    }
}


if (!function_exists('igk_str_replace_assoc_array')) {
    /**
     * replace assoc pattern
     * @param array $assoc_pattern array of pattern=>replacement
     * @param string $subject 
     * @return string 
     */
    function igk_str_replace_assoc_array(array $assoc_pattern, string $subject): string
    {
        foreach ($assoc_pattern as $k => $v) {
            $subject = str_replace($k, $v, $subject);
        }
        return $subject;
    }
}
if (!function_exists('igk_str_preg_replace_assoc_array')) {
    /**
     * 
     * @param array $assoc_pattern array of regex=>replacement
     * @param string $subject 
     * @return string 
     */
    function igk_str_preg_replace_assoc_array(array $assoc_pattern, string $subject): string
    {
        foreach ($assoc_pattern as $k => $v) {
            $subject = preg_replace($k, $v, $subject);
        }
        return $subject;
    }
}


/**
 * shortcut to string ::Format method helper
 * @param string $data format key
 * @param ?array $params format param
 * @return string formatted string
 * @throws IGKException 
 */
function igk_str_format(string $data, ...$params):string
{
    return stringUtility::Format(...func_get_args());
}

if (!function_exists('igk_str_assert_prepend')){

/**
* auto generate doc.
* @param null|string $data
* @param string $prepend
*/
function igk_str_assert_prepend(?string $data, string $prepend){
        if ($data){
            $data = $prepend.$data;
        }
        return $data;
    }
}


if (!function_exists('igk_str_escape')) {
    /**
     * use to escape char
     * @param string $str 
     * @param string $list char list as string
     * @return string 
     */
    function igk_str_escape(string $str, string $char_list = "'")
    {
        $tab = str_split($char_list, 1);
        while (count($tab) > 0) {
            $q = array_shift($tab);
            $offset = 0;
            while (false !== ($pos = strpos($str, $q, $offset))) {
                if ($pos == 0) {
                    $str = '\\' . $str;
                    $offset = 1;
                } else {
                    if ($str[$pos - 1] == "\\") {
                        $offset = $pos + 1;
                    } else {
                        $str = substr($str, 0, $pos) . "\\" . substr($str, $pos);
                        $offset = $pos + 1;
                    }
                }
            }
        }
        return $str;
    }
}
if (!function_exists('igk_str_rm')) {

/**
* auto generate doc.
* @param string $str
* @param int $start_index
* @param null|int $length
*/
function igk_str_rm(string $str, int $start_index, ?int $length = null)
    {
        if (!is_null($length)) {
            return substr($str, 0, $start_index) . substr($str, $start_index + $length);
        }
        return substr($str, 0, $start_index);
    }
}
if (!function_exists('igk_str_insert')) {
    // function igk_str_insert(string $str, string $content, int $start_index)
    // {
    //     return substr($str, 0, $start_index) . $content . substr($str, $start_index);
    // }
    /**
     * 
     * @param mixed $glue string to insert 
     * @param mixed $text where to insert
     * @param mixed $start start index
     * @param mixed $offset offset from where substring start 
     */
    function igk_str_insert(string $glue, string $text, int $start, ?int $offset = null)
    {
        $offset = $offset === null ? $start : $offset;
        return substr($text, 0, $start) . $glue . substr($text, $offset);
    }
}

if (!function_exists('igk_str_lwfirst')) {
    /**
     * while first word segment is uppercase make it lower case 
     * @param mixed $g 
     * @return void 
     */
    function igk_str_lwfirst($g, ?string $tokens = null)
    {
        $s = '';
        $pos = 0;
        $ln = strlen($g);
        $tokens = $tokens ?? implode('', array_map('chr', range(ord('A'), ord('Z'))));
        // igk_wln_e("tokens", $tokens);
        while ($pos < $ln) {
            $ch = $g[$pos];
            if (strpos($tokens, $ch) === false) {
                $s .= substr($g, $pos);
                break;
            }
            $s .= strtolower($ch);
            $pos++;
        }
        return $s;
    }
}


if (!function_exists('igk_str_surround')) {
    /**
     * surround litteral with pattern
     * @param string $haystack 
     * @param string $pattern 
     * @return string 
     */
    function igk_str_surround(string $haystack, string $pattern = '"'): string
    {
        return $pattern . sprintf("%s", $haystack) . $pattern;
    }
}
if (!function_exists('igk_str_strip_surround')) {
    /**
     * strip surround litteral with pattern
     * @param string $haystack 
     * @param string $pattern 
     * @return string 
     */
    function igk_str_strip_surround(string $haystack): string
    {
        $ch = null;
        if (strpos($haystack, '"') === 0) {
            $ch = '"';
        } else if (strpos($haystack, "'") === 0) {
            $ch = "'";
        }
        if ($ch) {
            $haystack = trim($haystack, " " . $ch);
        }
        return $haystack;
    }
}

if (!function_exists('igk_str_encode_to_utf8')) {
    /**
     * encode to utf8  - php8 > 
     * @param null|string $s 
     * @param null|string $enc encoding
     * @return array|string|false 
     */
    function igk_str_encode_to_utf8(?string $s, ?string $enc = null)
    {
        return mb_convert_encoding($s, 'UTF-8', $enc ?? mb_list_encodings());
        // return mb_convert_encoding($s, 'ISO-8859-1', 'UTF-8');//$enc ?? mb_list_encodings());
    }
}

if (!function_exists('igk_str_repeat_callback')){
    /**
     * helper repleat a string expression call type
     * @param callable $callable 
     * @param int $times 
     * @return string 
     */
    function igk_str_repeat_callback(callable $callable, int $times=1){
        $s = '';
        $idx = 0;
        while($idx<$times){
            $idx++;
            $s.=$callable($idx);
        }
        return $s;
    }
}



if (!function_exists('igk_str_rm_php_csharp_summary')){

/**
* auto generate doc.
* @param string $src
*/
function igk_str_rm_php_csharp_summary(string $src){
 
$regex = new RegexMatcherContainer;

$regex->autoStore = false;
$sub = $regex->begin('<([a-zA-Z][a-zA-Z0-9\-:]*)', '\/>|<\/\\1\\s*>', 'sub')->last();
$inner = $regex->begin('>','(?=<)', 'inner-sub')->last();

$regex->autoStore = true;
$regex->appendStringDetection('string', true);
$regex->appendCommentDocBlock();
RegexMatcherUtility::AppendPhpHereDoc($regex);

 
$stop = $regex->createPattern(['match'=>'^\\s*[^\/\\s]+', 'tokenID'=>'ugly-line']);

$regex->match('<\?php(\\s*|$)', 'php-start');
$regex->match('\?>', 'php-end');

$inner->patterns = [
    $sub,
    $stop
];
// $g = $regex->begin('\/\/\/\\s*<([a-zA-Z][a-zA-Z0-9\-:]*)', '(?=^\\s*[^\/])|\/>|<\/\\1\\s*>(\\s*|$)', 'summary-start')->last();
$g = $regex->begin('(?:^\\s*|(?<=[^\/]))\/\/\/\\s*<([a-zA-Z][a-zA-Z0-9\-:]*)', '\/>|<\/\\1\\s*>(\\s*|$)', 'summary-start')->last();
$g->patterns = [
    $inner,
    $stop
];
 
$buffer = new RegexDetectBuffer;
$buffer->source = $src;
$inf =(object)['start'=>false];
(new RegexDetectHandler($regex))->detect(
    $src,
    function (
        $e
    ) use ($buffer, $inf) {
        igk_is_debug() && Logger::info('tokenID:'.$e->tokenID);  
        if ($inf->start && ($e->tokenID == 'summary-start')) { 
            if ($e->getisEnd()){ 
            $buffer->replace($e);
            }
        }
    }, function($g)use($inf){ 
        if ($g->match->tokenID=='php-start'){
            $inf->start = true;
        }
        if ($g->match->tokenID=='php-end'){
            $inf->start = false;
        }
    }
);

return $buffer->output();
}
}
