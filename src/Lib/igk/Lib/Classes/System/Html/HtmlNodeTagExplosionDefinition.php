<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlNodeTagExplosionDefinition.php
// @date: 20230328 13:47:42
namespace IGK\System\Html;

use IGK\System\ArrayMapKeyValue;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\IO\Configuration\ConfigurationReader;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html
 */
class HtmlNodeTagExplosionDefinition
{
    /**
     * 
     * @var HtmlNodeBuilder
     */
    var $builder;

    // explode definition 
    var $split = ">";

    private static $sm_static;


    public function __construct(HtmlNodeBuilder $builder)
    {
        $this->builder = $builder;
    }
    /**
     * get core builder 
     * @return static
     */
    public static function Core(){
        return self::$sm_static ?? self::$sm_static = new static(new HtmlNodeBuilder(igk_create_notagnode()));
    }
    /**
     * 
     * @param mixed $node 
     * @param array $data 
     * @return mixed 
     * @throws IGKException 
     * @throws EnvironmentArrayException 
     */
    public function setup($node, $data, $tnode=null){
        $bck = $this->builder->t;
        if ($tnode){
            $this->builder->t = $tnode;
        }
        $r = $this->builder->setup($node, $data);
        $this->builder->t = $bck;
        return $r;
    }
    /**
    * explode tag
    * @param string $tagname 
    * @param mixed $pnode 
    * @param mixed $context 
    * @return array 
    * @throws IGKException 
    * @throws ArgumentTypeNotValidException 
    * @throws ReflectionException 
    */
    public function explode(string $tagname, &$pnode, $context = null)
    {

        $id = null;
        $classes = null;
        $args = null;
        $name = null;
        $attr = null;
        $defs = [];
        $ln = strlen($tagname);
        $pos = 0;
        $v = "";
        if (preg_match('/[\(\[\{\>]/', $tagname)) {
            while ($pos < $ln) {
                $ch = $tagname[$pos];
                switch ($ch) {
                    case '"':
                    case "'":
                        $v .= igk_str_read_brank($tagname, $pos, $ch, $ch);
                        break;
                    case "[":
                        $v .= igk_str_read_brank($tagname, $pos, ']', '[');
                        $ch = '';
                        break;
                    case "(":
                        $v .= igk_str_read_brank($tagname, $pos, ')', '(');
                        $ch = '';
                        break;
                    case "{":
                        $v .= igk_str_read_brank($tagname, $pos, '}', '{');
                        $ch = '';
                        break;
                    case $this->split:
                        if (!empty($cv = trim($v))) {
                            $defs[] = $cv;
                            $v = "";
                            $ch = '';
                        }
                        break;
                    case ' ':
                        $ch = '';
                }
                $v .= $ch;
                $pos++;
            }
            if (!empty($ch = trim($v))) {
                array_push($defs, $ch);
            }
        } else {
            $defs[] = $tagname;
        }
        $n = &$pnode;
        while (count($defs) > 1) {
            $q = array_shift($defs);
            list($tagname, $id, $classes, $args, $name, $attr) = self::ExplodeTag($q);
            if (is_null($args)) {
                $args = [];
            }
            $n = $n->$tagname(...$args);
            if ($classes) {
                $n->setClass($classes);
            }
            if ($attr) {
                $n->setAttributes($attr);
            }
            if ($id) {
                $n->setAttribute('id', $id);
            }
            if ($name) {
                $n->setAttribute('name', $name);
            }
        }
        $tagname = array_shift($defs);
        list($tagname, $id, $classes, $args, $name, $attr) = self::ExplodeTag($tagname);
        return [trim($tagname), $id, $classes, $args, $name, $attr];
    }

    public static function ExplodeTag(string $tagname, $context = null): array
    {
        $id = null;
        $classes = null;
        $args = null;
        $name = null;
        $attr = null;
        if (strpos($tagname, '(') !== false) {
            !preg_match("/\((?P<name>[^\)]+)/i", $tagname, $tab) && igk_die("argument not valid. " . $tagname);
            // get args to setups
            $start = $pos = strpos($tagname, '(');
            $g = igk_str_read_brank($tagname, $pos, ')', '(');
            $a = substr($g, 1, -1);
            $args = igk_engine_get_attr_arg($a, $context);
            $tagname = igk_str_rm($tagname, $start,  $pos - $start + 1);
            //  igk_debug_wln("current context ", $tagname, $args, HtmlLoadingContext::GetCurrentContext());
        }
        if (strpos($tagname, '[') !== false) {
            !preg_match("/\[(?P<name>[^\[\]]+)/i", $tagname, $tab) && igk_die("argument not valid. " . $tagname);
            // get args to setups
            $start = $pos = strpos($tagname, '[');
            $g = igk_str_read_brank($tagname, $pos, ']', '[');
            $a = substr($g, 1, -1);
            // $attr = igk_engine_get_attr_arg($a, $context);
            $tagname = igk_str_rm($tagname, $start,  $pos - $start + 1);
            $r = new ConfigurationReader();
            $r->separator = ':';
            $r->delimiter = ',';
            $attr = ArrayMapKeyValue::Map(function($k,$v){
                if (is_null($v)){
                    if (strpos($k, "@")===0){
                        return [$k = ltrim($k, '@'), $k];
                    }
                    return null;
                }
                return [$k, $v];
            }, (array)$r->read($a));

            //  igk_debug_wln("current context ", $tagname, $args, HtmlLoadingContext::GetCurrentContext());
        }
        // + | identify id 
        if (strpos($tagname, '#') !== false) {
            $c = preg_match_all("/#(?P<name>[^\%\.#\\s\(\)]+)/i", $tagname, $tab);
            for ($i = 0; $i < $c; $i++) {
                // get id last id and remove it from tag
                $id = $tab['name'][$i];
                $tagname = str_replace($tab[0][$i], '', $tagname);
            }
        }
         // + | identify class 
        if (($v_pos = strpos($tagname, '.')) !== false) {
            $tclasses = [];
            if ($c = preg_match_all("/\.(?P<name>[^\%\.\\s#\(\)]+)/i", $tagname, $tab)) {
                for ($i = 0; $i < $c; $i++) {
                    // get id last id and remove it from tag
                    $tclasses[$tab['name'][$i]] = 1;
                    $tagname = str_replace($tab[0][$i], '', $tagname);
                }
            } else {
                if (igk_environment()->isDev()) {
                    igk_die("not a valid class specification.");
                }
                $tagname = substr($tagname, $v_pos, 1);
            }
            $classes = implode(' ', array_keys($tclasses));
        }
         // + | identify name 
        if (strpos($tagname, '%') !== false) {
            $c = preg_match_all("/\%(?P<name>[^\.#\\s\(\)]+)/i", $tagname, $tab);
            for ($i = 0; $i < $c; $i++) {
                // get id last id and remove it from tag
                $name = $tab['name'][$i];
                $tagname = str_replace($tab[0][$i], '', $tagname);
            }
        }

        return [trim($tagname), $id, $classes, $args, $name, $attr];
    }
}
