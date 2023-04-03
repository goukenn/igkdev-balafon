<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReadUtility.php
// @date: 20221019 22:59:28
namespace IGK\System\Runtime\Compiler;

use IGK\System\IO\StringBuilder;
use IGK\System\Text\LinePrefixMapper;
use IGKException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Runtime\Compiler
 */
class ReadTokenUtility
{


    private static function _GetPonderation($mod, $p)
    {
        $h = 0;
        foreach ($mod as $k) {
            $h += pow(2, array_search($k, $p));
        }
        return $h;
    }
    /**
     * merge variables 
     * @param mixed $vars 
     * @param bool $mergeVariable 
     * @return string 
     */
    public static function GenerateVariables($vars, bool $mergeVariable = false, ?string $type=null)
    {

        $sb = new StringBuilder();
        $prop = ["const", "global", "var", "public", "private", "protected"];
        usort($vars, function ($a, $b) use ($prop) {
            $ma = self::_GetPonderation($a->modifiers, $prop);
            $mb = self::_GetPonderation($b->modifiers, $prop);
            if ($ma == $mb)
                return strcasecmp($a->name, $b->name);
            return $ma <=> $mb;
        });
        $p = null;
        $gp = [];
        foreach ($vars as $k) {
            if ($k->dependOn) {
                continue;
            }
            if (($type == 'function')){
                if (count($vars)==1){
                    continue;
                } 
            }
            $name = $k->name;
            $d = $k->default;

            $prefix = in_array("const", $k->modifiers) ? "" : "\$";
            if ($mergeVariable) {
                $mod = implode(" ", $k->modifiers);
                if ($mod !== $p) {
                    if ($p) {
                        $sb->appendLine(implode(' ', array_filter([$p,  implode(",", $gp[$p])])) . ";");
                    }
                    $p = $mod;
                    $gp = [];
                }
                $src = '';
                $src .= sprintf("%s", implode(" ", [
                    // implode(" ", $k->modifiers),
                    $prefix . $name
                ]));
                if (!empty($d) || is_numeric($d)) {
                    $src .= (" = " . $d);
                }
                $gp[$p][] =  $src;
            } else {
                $sb->append(sprintf("%s", implode(" ", array_filter([
                    trim(implode(" ", $k->modifiers)),
                    $prefix . $name
                ]))));
                if (!empty($d) || is_numeric($d)) {
                    $sb->append(" = " . $d);
                }
                $sb->appendLine(";");
            }
        }
        if ($mergeVariable && !is_null($p)) {
            $sb->appendLine(implode(' ', array_filter([$p,  implode(", ", $gp[$p])])) . ";");
        }
        return $sb . "";
    }
    /**
     * 
     * @param mixed $structs 
     * @param null|StringBuilder $cheader 
     * @return string 
     * @throws IGKException 
     */
    public static function GenerateStruct($structs, bool $header = false, ?IReadTokenMergeOption $options = null)
    {
        /**
         * @var ReadTokenStructInfo|ReadTokenStructFunctionInfo $tm
         */
        $sb = new StringBuilder();
        $p = new LinePrefixMapper;
        $p->prefix = "\t";

        foreach (["interface", "trait", "class", "function"] as $m) {
            if (is_null($def = igk_getv($structs, $m))) {
                continue;
            }
            usort($def, function ($a, $b) {
                return strcasecmp($a->name, $b->name);
            });
            foreach ($def as $tm) {

                $src = $tm->output($options);
                if (!$header) {
                    $sb->appendLine(sprintf("%s", $src . "\n"));
                    continue;
                }


                // because class can't be decalared for multiple evaluation need to protect
                switch ($tm->type) {
                    case "class":
                        $sb->appendLine(sprintf("if (!class_exists(%s::class)){\n%s\n}", $tm->name, $src) . "\n");
                        break;
                    case "interface":
                        $sb->appendLine(sprintf("if (!interface_exists(%s::class)){\n%s\n}", $tm->name, $src) . "\n");
                        break;
                    case "trait":
                        $sb->appendLine(sprintf("if (!trait_exists(%s::class)){\n%s\n}", $tm->name,  $src) . "\n");
                        break;
                    case "function":
                        $sb->appendLine(sprintf("if (!function_exists('%s')){\n%s\n}", $tm->name,  $src) . "\n");
                        break;
                }
            }
        }
        return $sb . "";
    }

    /**
     * generate use and attach to string builder
     * @param mixed $uses 
     * @param StringBuilder $sb 
     * @return void 
     */
    public static function GenerateUses($uses, StringBuilder $sb)
    {
        ksort($uses,  SORT_FLAG_CASE | SORT_STRING);
        $sb->appendLine("");
        foreach ($uses as $k => $v) {
            $s = "";
            if ($v != $k) {
                $s = " as " . $v;
            }
            $sb->appendLine("use " . $k . $s . ";");
        }
    }
}
