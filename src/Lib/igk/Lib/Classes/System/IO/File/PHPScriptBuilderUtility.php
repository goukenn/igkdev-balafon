<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PHPScriptBuilderUtility.php
// @date: 20220803 13:48:55
// @desc: 
// @file: PHPScriptBuilderUtility.php
// @author: C.A.D. BONDJE DOUE
namespace IGK\System\IO\File;
use IGK\System\IO\StringBuilder;
use IGK\System\Text\RegexMatcherContainer;
use IGK\System\Text\RegexMatcherUtility;
use Illuminate\Validation\Rules\In;
use ReflectionClass;
use ReflectionMethod;

/**
 * Phpscript builder utility.
 * @package IGK\System\IO\File
 */
abstract class PHPScriptBuilderUtility
{
    /**
     * auto generate doc.
     * @param mixed ...$sources
     * @return null|string
     */
    public static function MergeSource(...$sources): ?string
    {
        if (!$sources) return null;
        $tsrc = "";
        $root_depth = 0;
        $v_tcount = 0;
        foreach ($sources as $value) {
            if (!$value)
                continue;
            $src = file_get_contents($value);
            $skip_first = false;
            $tokens = token_get_all($src);
            if (strpos($src, "<?php") === 0) {
                $skip_first = true;
            }
            if (($root_depth == 0) && ($skip_first)) {
                $root_depth = 1;
            }
            $sb = new StringBuilder();
            $declare = 0;
            while (count($tokens)) {
                $e = array_shift($tokens);
                $v = $e;
                if (is_array($v)) {
                    $v = $e[1];
                }
                if ($skip_first) {
                    $v_tcount++;
                    if ($e[0] == T_OPEN_TAG) {
                        $skip_first = 0;
                        continue;
                    }
                }
                if ($e[0] == T_NAMESPACE) {
                    $declare = 1;
                    continue;
                } else {
                    if ($declare) {
                        if ($v == ';') {
                            $declare = 0;
                        }
                        continue;
                    }
                }
                if ($e[0] == T_CLOSE_TAG) {
                    $v_tcount--;
                }
                $sb->append($v);
            }
            $g = rtrim($sb . "");
            if (igk_str_endwith($g, '?>')) {
                $g = substr($g, 0, -2);
            }
            $tsrc .= $sb . "";
        }
        $s = (strpos($tsrc, "<?php") === 0) ? '' : "<?php\n";
        return $s . $tsrc;
    }
    /**
     * auto generate doc.
     * @param null|string $desc
     * @return string
     */
    public static function GetArrayReturn($data, ?string $fc = null, ?string $desc = null)
    {
        $o  = "<?php\n";
        if ($desc)
            $o .= "// @desc: " . $desc . "\n";
        if ($fc)
            $o .= "// @file: " . basename($fc) . "\n";
        $o .= "// @file: " . date("Y-m-d") . "\n";
        $o .= "// @author: " . IGK_AUTHOR . "\n";
        $o .= "return [" . $data . "];";
        return $o;
    }
    /**
     * remove php comment token 
     * @param string $source source
     * @return string 
     */
    public static function RemoveComment(string $source)
    {
        $comments = \token_get_all($source);
        $src = implode("", array_map(function ($m) {
            if (is_array($m)) {
                if (token_name($m[0]) == "T_COMMENT") {
                    return null;
                }
                return $m[1];
            }
            return $m;
        }, $comments));
        return $src;
    }
    /**
     * auto generate doc.
     * @param mixed $data
     * @return string
     */
    public static function ExtractClassDefinition($data, ?string $name = null, $options = null)
    {
        $sb = new PhpScriptBuilder;
        $def = new StringBuilder;
        if ($options) {
            $sb->no_header_comment = igk_getv($options, 'noHeader');
        }
        $r = [];
        if (is_object($data)) {
            $r = array_merge($r, array_filter(array_keys((array)$data), function ($d) {
                return strpos("\0", $d) === false;
            }));
        } else {
            $r = array_keys($data);
        }
        sort($r);
        foreach ($r as $k) {
            $type = 'mixed';
            $v = igk_getv($data, $k);
            if (is_numeric($v)) {
                $type = 'number';
            } else if (is_string($v)) {
                $type = 'string';
            } else if (is_array($v)) {
                $type = 'array';
            }
            $def->appendLine(sprintf("/**\n* @var %s\n*/", $type));
            $def->appendLine(sprintf('var $%s;', $k));
        }
        $type = $name ? 'class' : 'function';
        $sb
            ->name($name)
            ->type($type)->defs($def);
        return $sb->render();
    }
    /**
     * extract parameter list from func definition 
     * @param string $src 
     * @return array 
     */
    public static function ExtractArgsFromFuncParamDefinition(string $src, &$default = null, $src_obj = null)
    {
        $regex = new RegexMatcherContainer;
        $pos = 0;
        $def = [];
        $regex->autoStore = false;
        RegexMatcherUtility::AppendPhpHereDoc($regex, $heredoc);
        $string = $regex->appendStringDetection('string',true);
        $regex->autoStore = true;
        $comments[] = $regex->appendMultilineComment()->last();
        $comments[] = $regex->appendSingleLineComment()->last();
        $regex->match("\\$(?<n>[a-zA-Z_][a-zA-Z_0-9]*)", 'varName');
        $regex->match(",", 'punctuation');
        $_def = $regex->begin("=", '(?=,)', 'const')->last();
        $array = $regex->begin("\[", "\]", 'array')->last();
        $array->patterns = [
            $heredoc,
            $comments,
            $string,
            $array 
        ];
        $_def->patterns = [
            $comments,
            $array,
        ];
        $name = null;
        while ($g = $regex->detect($src, $pos)) {
            if ($e = $regex->end($g, $src, $pos)) {
                $id = $e->tokenID;
                if ($id == 'varName') {
                    $def[] = $name = igk_conf_get($e->beginCaptures, 'n/0');
                }
                if ($id == 'const') {
                    $default[$name] = self::TreatDefaultValue(substr($e->value,1), $src_obj);
                }
            }
        }
        return $def;
    }
    /**
     * treat default value 
     * @param string $default 
     * @return float|null|mixed|string 
     */
    public static function TreatDefaultValue(string $default, $src=null)
    {
        $g = trim($default);
        if (is_numeric($g)){
            return floatval($g);
        }
        if (is_string($g)){
            if (preg_match('/^(\'|")/', $g)){
                $default = igk_str_remove_quote($g);
                return $default;
            } else if ($g=='null'){
                return null;
            }
            if (in_array($g, ['true','false'])){
                return $g=='true';
            }
            if (defined($g)){
                return constant($g);
            }
            if (preg_match('/^(static|self)::(?P<n>.+)/', $g, $tab)){
                if (is_object($src)){
                    $ref = new ReflectionClass(get_class($src));
                    return $ref->getConstant($tab['n']);
                }
            }
            if ($r = json_decode($default)){
                return $r;
            }
        }        
        return $default;
    }
}