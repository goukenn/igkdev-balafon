<?php

// @author: C.A.D. BONDJE DOUE
// @filename: CssParser.php
// @date: 20220310 11:16:25
// @desc: parse css 
namespace IGK\System\Html\Css;

use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

class CssParser implements ArrayAccess{
    private $source;
    private $definition;
    use ArrayAccessSelfTrait;

    private function __construct()
    {        
    }
    /**
     * get json definition
     * @return string|false 
     */
    public function to_json($mode=JSON_PRETTY_PRINT){
        return json_encode($this->definition,$mode);
    }

    public function to_array(){
        return $this->definition;
    }
    private static function _join_css_tab($d, $k){
        return $k.":".$d;
    }
    public function to_css(){
        return implode("\n", array_map(function($d, $c){
            if (is_array($d)){
                $v = $c."{\n".implode(";", array_map( [self::class, "_join_css_tab"], $d, array_keys($d))).";\n}";                
            }else{
                $v =  $c.": ".$d.";";
            }
            return $v;
        }, $this->definition, array_keys($this->definition)));
    }
    private static function __ReadDefinition(string $content){
        $def = [];
        $len = strlen($content);
        $pos = 0;
        $selector = '';
        $name = '';
        $mode = 0; // 0 = global , 1: child selecte 
        $value = '';
        $rv = "";

        while($pos< $len){
            $ch = $content[$pos];
            switch ($ch) {
                case '{':
                    if ($mode==0){
                        if (empty($rv = trim($rv))){
                            die("not sected");
                        }
                        $mode = 1;
                        $name = $rv;
                        $selector.=$name;
                        $rv = '';     
                    }else {
                        die("start not allowed");
                    }
                    break;
                case ':':
                    if (empty($rv = trim($rv))){
                        die("name is empty.: ".$rv);
                    }
                    $name = $rv;
                    if ($mode==0){
                        $selector.= $name.":";
                    }
                    $rv = '';             
                    break;
                case ';':
                    if (empty($rv = trim($rv)) && (strlen($rv)==0)){
                        die("name is empty.;".$rv);
                    }
                    $value = $rv;
                    if($mode == 0){
                        $def[$name] = $value;
                    }else {
                        $def[$selector][$name] = $value;
                        $name = '';
                        $value = '';
                    }
                    $rv = '';   
                    break;
                case "'":
                    $pos++;
                    $rv.= substr(igk_str_read_brank($content, $pos, $ch, $ch),0, -1);

                    break;
                case '}':
                    if (!empty($rv = trim($rv))){                       
                    // finish selector 
                        if ($mode == 1){
                            $def[$selector][$name] = $rv;
                            $mode = 0;                       
                        }else{
                            $def[$name] = $rv;
                        }
                        $selector = '';
                        $name = '';
                        $rv = '';
                    }
                    break;
                default:
                    $rv.= $ch;
                    break;
            }
            $pos++;
        }
        if (!empty($rv = trim($rv))){            
            $def[$name] = $rv;
            $rv = null;
        }
      
        return $def; 
    }
    /**
     * load css style string
     * @param string $content 
     * @return CssParser 
     */
    public static function Parse(string $content): self{
        $g = new self();
        $g->source = $content;
        $g->definition = self::__ReadDefinition($content); 
        return $g;
    } 

    function _access_OffsetSet( $n,  $v){
        $this->definition[$n] = $v;
    }
    function _access_OffsetGet( $n){
        return igk_getv($this->definition, $n);
    }
    function _access_OffsetUnset( $n){
       unset($this->definition[$n]);
    }
    function _access_offsetExists($n){
        return  isset($this->definition[$n]);
    }
    /**
     * retrieve margin definition
     * @return array 
     */
    public function margin(){
        return $this->_get_size_def("margin");      
    }
    /**
     * retrive padding definition
     * @return array 
     */
    public function padding(){
        return $this->_get_size_def("padding");      
    }
    private function _get_size_def($name){
        $t = $r = $b = $l = 'auto';
        if ($m = $this[$name]){
            $c = array_filter(explode(" ", $m));
            switch (count($c)) {
                case 1:
                    $t = $r = $b = $l = $c[0];
                    break;
                case 2:
                    $t = $b = $c[0];
                    $r = $l = $c[1];
                case 4:
                    break;
                    $t = $c[0];
                    $r = $c[1];
                    $b = $c[2];
                    $l = $c[3];
                default:
                    die("not valid");
                    break;
            }
        }
        if ($g = $this[$name."-left"]){
            $l = $g;
        }
        if ($g = $this[$name."-top"]){
            $t = $g;
        }
        if ($g = $this[$name."-right"]){
            $r = $g;
        }
        if ($g = $this[$name."-bottom"]){
            $b = $g;
        } 
        return [$t, $r, $b, $l];
    }
    public function position(){
        $t = $r = $b = $l = 'auto';        
        if ($g = $this["left"]){
            $l = $g;
        }
        if ($g = $this["top"]){
            $t = $g;
        }
        if ($g = $this["right"]){
            $r = $g;
        }
        if ($g = $this["bottom"]){
            $b = $g;
        } 
        return [$t, $r, $b, $l];
    }

    public function border(){
        
        $res = [];
        if ($all = $this["border"]){

        }
        if ($all = $this["border-color"]){
            $res["left"]["color"] = 
            $res["right"]["color"] = 
            $res["top"]["color"] = 
            $res["bottom"]["color"] = 
            $all;
        }
        if ($all = $this["border-width"]){
            $res["left"]["width"] = 
            $res["right"]["width"] = 
            $res["top"]["width"] = 
            $res["bottom"]["width"] = 
            $all;
        }
        
        foreach(["left","top","right", "bottom"] as $k){
            $gp = [];
            if ($w = $this["border-".$k."-width"]){
                ${$k[0]."w"} = $w;
                $gp["width"] = $w;
            }
            if ($c = $this["border-".$k."-color"]){
                ${$k[0]."c"} = $c;
                $gp["color"] = $c;
            }
            if ($gp){
                $res[$k] = (object)$gp;
            }
        }
        unset($k, $gp); 

        return (object) $res;
    }
}
