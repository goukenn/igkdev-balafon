<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhpScriptBuilder.php
// @desc: PhpScript builder helper
// @date: 20210723 13:22:40
namespace IGK\System\IO\File;

use IGK\Helper\StringUtility;
use IGK\System\IO\Path;
use IGK\System\Traits\StoredPropertiesTrait;
use IGKException;

/**
 * php script builder
 * @package IGK\System\IO\File
 * @method self defs(string $content) set the containt definition
 * @method self uses(string|array $use) uses definition
 * @method self extends(string|array $class) if type is class mark extends
 * @method self author(string $author) set author text
 * @method self namespace(string $namespace) define the namespace
 * @method self type(string|'class'|'trait'|'interface'|'function' $type) define the type. class|trait|interface|function
 * @method self name(?string $name) define the of type in case class|trait|interface
 * @method self comment(?string $comment) define the top comment
 * @method self phpdoc(?string $phpdoc) set phpdoc
 * @method self file(string $phpdoc) set file 
 * @method self desc(?string $phpdoc) set description  
 * @method self class_modifier(?string $modifier) set the class type modifier 
 * @method self strict(?bool $strict_declare) set the class type modifier 
 */
class PHPScriptBuilder
{
    /**
     * Property: no header comment.
     * @var mixed
     */
    var $no_header_comment;
    /**
     * Property: author.
     * @var mixed
     */
    var $author;
    use StoredPropertiesTrait;
    /**
     * get fulle namespace 
     * @param string $type 
     * @param null|string $namespace 
     * @return string 
     */
    public static function GetFullType(string $type, ?string $namespace = null)
    {
        $ns = '';
        if ($namespace) {
            $ns = $namespace;
        }
        return StringUtility::NS(Path::Combine($ns, $type));
    }
    /**
     * Creates Empty Script Callback.
     */
    public static function CreateEmptyScriptCallback()
    {
        return function ($file) {
            $g = new self;
            $g->type("function");
            igk_io_w2file($file, $g->render());
        };
    }
    /**
     * .ctr
     */
    public function __construct()
    {
        $this->author = IGK_AUTHOR;
    }
    /**
     * .destructor
     * @param mixed $name
     */
    public function __get($name)
    {
        return $this->getProperty($name);
    }
    /**
     * Triggered when calling an inaccessible or undefined method on an object.
     * @param mixed $name
     * @param mixed $arguments
     */
    public function __call($name, $arguments)
    {
        if (isset($arguments[0]))
            $this->setProperty($name, $arguments[0]);
        return $this;
    }
    /**
     * write array
     * @param mixed $file 
     * @param mixed $tab 
     * @param string $desc 
     * @return void 
     * @throws IGKException 
     */
    public static function WriteArray($file, $tab, $desc = "")
    {
        $builder = new static;
        $s = self::DumpObject($tab);
        $builder->type("function")
            ->author(IGK_AUTHOR)
            ->desc($desc)
            ->file(basename($file))
            ->defs("return [$s];");
        igk_io_w2file($file, $builder->render());
    }
    /**
     * Dump object to string
     * @param mixed $data 
     * @return string 
     */
    public static function DumpObject($data)
    {
        $s = "";
        $tab = $data;
        array_walk($tab, function ($v, $k) use (&$s) {
            $q = [['v' => $v, 'k' => $k, 's' => &$s, 'sub' => false]];
            while (count($q) > 0) {
                $rtv = array_shift($q);
                $s = &$rtv['s'];
                $v = $rtv['v'];
                $k = $rtv['k'];
                $m = $rtv['sub'] ? ']'."\n" : '';
                $tv = '';
                if (is_array($v) || is_object($v)) {
                    $t = (array)$v;
                    if (empty($t)) {
                        $s .= "'{$k}'=>[]," . "\n";
                    } else {
                        $s .= "'{$k}'=>[";
                        while (count($t) > 0) {
                            $tkey = key($t);
                            $r = array_shift($t);
                            array_unshift($q, [
                                "v" => $r,
                                "k" => $tkey,
                                "s" => &$s,
                                "sub" => count($t) == 0,
                            ]);
                        }
                    }
                } else {
                    $tv = is_null($v) ? 'null' : sprintf("'%s'", $v);
                    if ('null' !== $tv) {
                        if (is_numeric($v)) {
                            $tv = $v;
                        }  
                    }
                    $s .= "'{$k}'=>{$tv},\n".$m;
                }
            }
        }, $tab);
        return $s;
    }
    /**
     * auto generate doc.
     * @param mixed $file
     * @param mixed $data
     * @param string $desc
     * @return void
     */
    public static function WriteData($file, $data, $desc = "")
    {
        $builder = new static;
        $s = "";
        $builder->type("function")
            ->author(IGK_AUTHOR)
            ->desc($desc)
            ->file(basename($file))
            ->defs("return {$data};");
        igk_io_w2file($file, $builder->render());
    }
    /**
     * Renders.
     */
    public function render()
    {
        $lf = "\n";
        $_setPhDoc = function ($d, $ns, $author) {
            $o = "";
            $o .= "/**\n";
            $o .= "* " . implode("\n*", explode("\n", trim($d))) . "\n";
            if ($ns) {
                $o .= "* @package {$ns}\n";
            }
            if ($author)
                $o .= "* @author {$author}\n";
            if ($phpdoc = $this->phpdoc) {
                $o .= "* " . implode("\n* ", explode("\n", $phpdoc));
            }
            $o .= "*/\n";
            return $o;
        };
        $o = "";
        $h = "";
        $v_author = ($this->author ?? IGK_AUTHOR);
        if (!$this->no_header_comment) {
            $h = implode("\n", array_filter([
                "// @author: " . $v_author,
                $this->file ? "// @file: " . $this->file : null,
                $this->desc ? "// @desc: " . implode($lf . "//", explode("\n", $this->desc)) : null,
                "// @date: " . date("Ymd H:i:s")
            ])) . $lf;
        }
        if ($this->strict) {
            $h .= 'declare(strict_types=1);' . $lf;
        }
        if ($ns = $this->namespace) {
            $h .= "namespace " . $ns . ";\n\n";
        }
        $t_uses = [];
        if ($_uses = $this->uses) {
            if (is_string($_uses)) {
                $_uses = [$_uses];
            }
        }
        $defs = "";
        if ($e = $this->defs) {
            $defs .= StringUtility::IndentContent($e) . "\n";
        }
        switch ($this->type) {
            case "function":
                $o .= preg_replace("/^\\t/m", "", $defs);
                break;
            case "class":
            case "interface":
            case "trait":
            case 'enum':
                if ($d = $this->doc) {
                    $o .= $_setPhDoc($d, $ns, $v_author);
                } else {
                    $o .= $_setPhDoc("", $ns, $v_author);
                }
                if (!empty($modifier = $this->class_modifier)) {
                    $modifier .= " ";
                }
                if ('enum' == $this->type) {
                    $o .= $this->type . " " . $this->name;
                } else {
                    $o .= $modifier . $this->type . " " . $this->name;
                    if ($e = $this->extends) {
                        $cu = igk_uri($e);
                        if (!empty($ns) || (count(explode("/", $cu)) > 1)) {
                            if (!isset($_uses[$e])) {
                                $_uses[$e] = $e;
                            }
                        }
                        $v_as = igk_getv($_uses, $e);
                        if (($this->type == 'class') && interface_exists($e)) {
                            $implements = $this->implements ?? [];
                            $implements[] = $e;
                            $this->implements($implements);
                        } else {
                            $o .= " extends " . ($v_as ? basename(igk_uri($v_as)) :  "\\" . $e);
                        }
                    }
                    if ($e = $this->implements) {
                        if (!is_array($e)) {
                            $e = [$e];
                        }
                        $e = array_unique($e);
                        array_map($this->_getHeaderMap($h, $_uses), $e);
                        $o .= " implements " . implode(",", array_map(function ($a) {
                            return basename(igk_uri($a));
                        }, $e));
                    }
                }
                $o .= "{\n";
                if (in_array($this->type, ['class', 'trait']) && ($traits = $this->traits)) {
                    $o .= implode("\n", array_map(function ($a) use (&$_uses) {
                        $_uses[$a] = $a;
                        return "\tuse " . basename(igk_uri($a)) . ";";
                    }, $traits)) . "\n";
                }
                $o .= rtrim($defs);
                $o .= "\n}";
            default:
                break;
        }
        if ($_uses) {
            $t_uses = [];
            $v_uses = array_map(function ($n, $k) use (&$t_uses) {
                $cl = $n;
                if (!is_int($k)) {
                    $cl = $k;
                }
                if (key_exists($cl, $t_uses)) {
                    return null;
                }
                $t_uses[$cl] = basename(igk_dir($cl));
                if (is_int($k) || ($k == $n)) {
                    return "use " . $n . ";";
                } else {
                    $t_uses[$cl] = $n;
                    return sprintf("use %s as %s;", $k, $n);
                }
            }, $_uses, array_keys($_uses));
            sort($v_uses);
            $h .= implode("\n", $v_uses) . PHP_EOL;
        }
        return "<?php\n" . $h . "\n" . $o;
    }
    /**
     * get script file header
     * @param mixed $options
     * @return string
     */
    public static function GenScriptFileHeader($options)
    {
        $l = igk_extract_var(
            $options,
            'author|file|version|date|desc'
        );
        $tb = [];
        foreach ($l as $k => $v) {
            if (!$v) continue;
            $tb[] = "// @" . $k . ": " . $v;
        }
        return implode("\n", $tb);
    }
    /**
     * auto generate doc.
     * @param mixed & $h
     * @param mixed & $_uses
     * @return mixed
     */
    private function _getHeaderMap(&$h, &$_uses)
    {
        return function ($e) use (&$h, &$_uses) {
            $as = "";
            $ms = "";
            if (is_array($e)) {
                $key = array_key_first($e);
                $as = $e[$key];
                $ms = " as " . $as;
                $e = $key;
            }
            if (!in_array($e, $_uses)) {
                $_uses[] = $e;
                if (!empty($as)) {
                    $_uses[$e] = $as;
                }
            }
        };
    }
}
