<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhpScriptBuilder.php
// @desc: PhpScript builder helper
// @date: 20210723 13:22:40
namespace IGK\System\IO\File;

use IGK\Helper\StringUtility;
use IGKException;

/**
 * php script builder
 * @package IGK\System\IO\File
 * @method self defs(string $content) set the containt definition
 * @method self uses(string|array $use) uses definition
 * @method self extends(string|array $class) if type is class mark extends
 * @method self author(string $auther) set author text
 * @method self namespace(string $namespace) define the namespace
 * @method self type(string $type) define the type. class|trait|interface|function
 * @method self name(string $name) define the of type in case class|trait|interface
 * @method self comment(string $comment) define the top comment
 * @method self phpdoc(string $phpdoc) define element 
 * @method self file(string $phpdoc) define element 
 * @method self desc(string $phpdoc) define element 
 */
class PHPScriptBuilder
{
    var $no_header_comment;

    public function __construct()
    {
        $this->author = IGK_AUTHOR;
    }
    public function __get($name)
    {
        return null;
    }
    public function __call($name, $arguments)
    {
        $this->$name = $arguments[0];
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
        $s = "";
        array_walk($tab, function ($v, $k) use (&$s) {
            $s .= "'{$k}'=>'{$v}',\n";
        }, $tab);
        $builder->type("function")
            ->author(IGK_AUTHOR)
            ->desc($desc)
            ->file(basename($file))
            ->defs("return [$s];");
        igk_io_w2file($file, $builder->render());
    }
    /**
     * 
     * @param mixed $file 
     * @param mixed $data 
     * @param string $desc 
     * @return void 
     * @throws IGKException 
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
    public function render()
    { 
        $_setPhDoc = function($d, $ns){
            $o = "";
            $o .= "/**\n";
            $o .="* " . implode("\n*", explode("\n", trim($d)))."\n";
            if ($ns){
                $o .= "* @package {$ns}\n";
            }
            if ($phpdoc = $this->phpdoc){
                $o.= "* ".implode("\n* ", explode("\n", $phpdoc));
            }
            $o .= "*/\n";
            return $o;
        };
        $o = "";
        $h = "";
        if (!$this->no_header_comment){
            $h = implode("\n", array_filter([
                "// @author: " . ($this->author ?? IGK_AUTHOR),
                $this->file ? "// @file: " . $this->file : null,
                $this->desc ? "// @desc: " . implode("\n//", explode("\n", $this->desc)) : null,
                "// @date: " . date("Ymd H:i:s")
            ])) . "\n";
        }
        if ($ns = $this->namespace) {
            $h .= "namespace " . $ns . ";\n\n";
        }
        $t_uses = [];
        if ($_uses = $this->uses){      
            if (is_string($_uses))
            {
                $_uses = [$_uses];
            }
            // $_uses = $t_uses; 
        }

        $defs = "";
        if ($e = $this->defs) {
            $defs .= StringUtility::IndentContent($e)."\n";
            // implode("\n", array_map(function ($s) {
            //     return "\t" . $s;
            // }, explode("\n", $e))) . "\n";
        }

        switch ($this->type) {
            case "function":
                $o .= preg_replace("/^\\t/m", "", $defs);
                break;
            case "class":
            case "interface":
            case "trait":
                if ($d = $this->doc) {
                    // documents
                    $o .= "///<summary>" . implode("///", explode("\n", trim($d))). "</summary>\n";  
                    $o .= $_setPhDoc($d, $ns);
                    
                } else {
                    $o .= "///<summary></summary>\n";
                    $o .= $_setPhDoc("", $ns); 
                }
                if (!empty($modifier = $this->class_modifier)) {
                    $modifier .= " ";
                }
                // $_uses = [];
                // if ($e = $this->use){
                //     if (!is_array($e)){
                //         $e = [$e];
                //     }                   
                //     $e = array_unique($e);
                //     array_map($this->_getHeaderMap($h, $_uses), $e);
                // }

                $o .= $modifier . $this->type . " " . $this->name;
                if ($e = $this->extends) {
                    $cu = igk_uri($e);
                    if (!empty($ns) || (count(explode("/", $cu)) > 1)){
                        if (!isset($_uses[$e])){
                            // $h .= "use " . $e . ";\n";
                            $_uses[$e] = $e;
                        }
                    }
                    $v_as = igk_getv($_uses, $e);  
                    if (($this->type == 'class') && interface_exists($e)){
                        $implements = $this->implements ?? [];
                        $implements[] = $e;                       
                        $this->implements($implements);
                    } else{
                        $o .= " extends " .( $v_as ? basename(igk_uri($v_as)) :  "\\".$e);
                    }
                }
                if ($e = $this->implements) {
                    if (!is_array($e)) {
                        $e = [$e];
                    }
                    $e = array_unique($e);
                    array_map($this->_getHeaderMap($h, $_uses), $e);
                    $o .= " implements " . implode(",", array_map(function($a){ return basename(igk_uri($a)); }, $e));
                }
                $o .= "{\n";
                $o .= rtrim($defs);
                $o .= "\n}";
            default:
                break;
        }

        if ($_uses){
            // ksort($_uses);
            $v_uses = array_map(function($n, $k) use (& $t_uses){
                $cl = $n;
                if (!is_int($k)){
                    $cl = $k;
                }
                if (key_exists($cl, $t_uses)){
                    return null;
                }
                $t_uses[$cl] = basename(igk_dir($cl));
                if (is_int($k) || ($k==$n)){
                    return "use ".$n.";";
                }
                else{
                    $t_uses[$cl] = $n;
                    return sprintf("use %s as %s;", $k, $n);
                }
            }, $_uses, array_keys($_uses));
            sort($v_uses);
            $h .= implode("\n", $v_uses).PHP_EOL;
        }

        return "<?php\n" . $h . "\n" . $o;
    }
    private function _getHeaderMap(& $h,& $_uses)
    {
        return function($e)use(& $h, & $_uses){             
            $as = "";
            $ms = "";
            if (is_array($e)){
                $key = array_key_first($e);
                $as = $e[$key];
                $ms = " as ".$as;
                $e = $key;
            }  
            if (!in_array($e, $_uses)){
                // $h .= "use " . $e . $ms.";\n";
                $_uses[] = $e;
                if (!empty($as)){
                    $_uses[$e]=$as;
                }
            }
        };
    }
}
