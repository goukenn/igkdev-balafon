<?php

namespace IGK\System\IO\File;

use IGKException;

class PHPScriptBuilder
{
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
        $o = "";
        $h = "";
        $h = implode("\n", array_filter([
            "// @author: " . ($this->author ?? IGK_AUTHOR),
            $this->file ? "// @file: " . $this->file : null,
            $this->desc ? "// @desc: " . implode("\n//", explode("\n", $this->desc)) : null,
            "// @date: " . date("Ymd H:i:s")
        ])) . "\n";
        if ($ns = $this->namespace) {
            $h .= "namespace " . $ns . ";\n\n";
        }
        if ($_uses = $this->uses){
            if (is_string($_uses))
            {
                $_uses = [$_uses];
            }
            $h .= implode("\n", array_map(function($n){
                return "use ".$n.";";
            }, $_uses));
        }

        $defs = "";
        if ($e = $this->defs) {
            $defs .= implode("\n", array_map(function ($s) {
                return "\t" . $s;
            }, explode("\n", $e))) . "\n";
        }

        switch ($this->type) {
            case "function":
                $o .= preg_replace("/^\\t/m", "", $defs);
                break;
            case "class":
                if ($d = $this->doc) {
                    // documents
                    $o .= "///<summary>" . $d . "</summary>\n";
                    $o .= "/**\n * " . $d . "\n */\n";
                }
                if (!empty($modifier = $this->class_modifier)) {
                    $modifier .= " ";
                }
                $_uses = [];
                if ($e = $this->use){
                    if (!is_array($e)){
                        $e = [$e];
                    }  
                    $e = array_unique($e);
                    array_map($this->_getHeaderMap($h, $_uses), $e);
                }

                $o .= $modifier . $this->type . " " . $this->name;
                if ($e = $this->extends) {
                    $cu = igk_html_uri($e);
                    if (!empty($ns) || (count(explode("/", $cu)) > 1)){
                        if (!in_array($e, $_uses)){
                            $h .= "use " . $e . ";\n";
                            $_uses[] = $e;
                        }
                    }
                    $v_as = igk_getv($_uses, $e);                    
                    $o .= " extends " .( $v_as ? $v_as :  basename(igk_html_uri($e)));
                }
                if ($e = $this->implements) {
                    if (!is_array($e)) {
                        $e = [$e];
                    }
                    $e = array_unique($e);
                    array_map($this->_getHeaderMap($h, $_uses), $e);
                    $o .= " implements " . implode(",", $e);
                }
               

                $o .= "{\n";
                $o .= rtrim($defs);
                $o .= "\n}";
            default:
                break;
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
                $h .= "use " . $e . $ms.";\n";
                $_uses[] = $e;
                if (!empty($as)){
                    $_uses[$e]=$as;
                }
            }
        };
    }
}
