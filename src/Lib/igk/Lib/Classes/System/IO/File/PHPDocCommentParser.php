<?php
// @author: C.A.D. BONDJE DOUE
// @file: PHPDocCommentParser.php
// @date: 20230104 17:05:49
namespace IGK\System\IO\File;


///<summary></summary>
/**
* 
* @package IGK\System\IO\File
*/
class PHPDocCommentParser{
    const NAME_TOKEN = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-';
    var $summary; 
    var $param;
    var $return;
    var $description;
    var $api;
    var $throws;


    /**
     * get response object
     * @var mixed
     */
    var $responses;
    /**
     * block phpunit test 
     * @var ?
     * @usage @covers classMethod 
     */
    var $covers;

    /**
     * use with 
     * @var ?
     */
    var $uses;
    /**
     * request info
     * @var mixed
     */
    var $request;

    /**
     * to handle security
     * @var mixed
     */
    var $security;

    private function __construct(){
    }
    /**
     * parse php doc comment
     * @param string $cm 
     * @return PHPDocCommentParser 
     */
    public static function ParsePhpDocComment(string $cm){
        $c = trim(igk_str_rm_start($cm, "/**"));
        $c = rtrim(igk_str_rm_last($c, "*/"));
        $g = new self;
        $g->summary = '';
        $summary = false;
        $content = "";
        $name = "";
        array_map(function($c)use($g, & $summary, & $content, & $name){
            $k =  ltrim(trim($c), " *");            
            $offset = 1;
            if (!$summary){
                if (strlen($k)>0){
                    if ($k[0] ==='@'){
                        $summary=true;
                        $name = self::ReadName($k, $offset);
                    }                    
                }
                if (!$summary){
                    $g->summary.= $k;
                    return;
                }       
                $content .= self::_TreatContent(substr($k, $offset));
            } else {
                if (strlen($k)>0){
                    if ($k[0] ==='@'){
                        $g->$name($content);
                        $content = "";
                        $offset = 1;
                        $name = self::ReadName($k, $offset);
                        $s = trim(substr($k, $offset));                          
                        $content .= self::_TreatContent($s); 
                        if ($name=='api'){
                            $g->api = true;
                        }
                    }else{
                        $content .= $k;
                    }
                }
            }
            
        }, explode("\n", $c));
        if (!empty($content)){
            $g->$name($content);
        }
        return $g;
    }
    private static function _TreatContent(string $content){
        if (igk_str_endwith($content, "\\")){
            $content.="\n";
        }   
        return $content;
    }
    public function __call($name, $arguments)
    {
        $g = null;
        $name = str_replace('-', '_', $name);
        if (strpos($name,"swagger_")===0){
            $name = igk_str_rm_start($name, 'swagger_');
        }
        if (!property_exists($this, $name)){
            igk_die("document comment parse error : property not exists : ".$name);
        }
        if (count($arguments)>0){
            $g = trim($arguments[0]);     
            if (isset($this->$name)){
                if (!is_array($this->$name)){
                    $this->$name = [$this->$name];
                }
                $this->$name[] = $g;
            }else{
                $this->$name = $g;
            } 
            return $this;
        }
        else {
            return $this->$name;
        }
    }
    public function __get($name){
        return null;
    }
    /**
     * get if methods is deprecated
     * @return bool 
     */
    public function isDeprecated(){
        return property_exists($this, 'deprecated');
    }
    private static function ReadName($t, & $offset){
        $ln = strlen($t);
        $s  = "";
        while($offset<$ln){
            $ch = $t[$offset];
            if (strpos(self::NAME_TOKEN, $ch) === false){
                break;
            }
            $offset++;
            $s.= $ch;
        }
        return $s;
    }
    public function __toString()
    {
        $s = "/**\n";
        foreach($this as $k=>$v){
            if ($k=='responses'){
                $s.= "* @$k(";
                if ($v){
                    if (!is_array($v)){
                        $v = [$v];
                    }
                    $s .= implode ("\n* ", $v);
                    if (count($v)==1){
                        $s.= ")\n";
                    }else {
                        $s .="\n* )\n";
                    }
                    // $s .= "*)\n";
                }else{
                    $s.= ")\n";
                }
                continue;
            }
            if (!$v){
                continue;
            }
            if (!is_array($v)){
                $v = [$v];
            }
            if ($k == "summary"){
                $s .= "* ".trim(implode(" ", $v)). "\n";
                continue;
            }
            while(count($v)>0){
                $q = array_shift($v);
                $s.= "* @$k ";
                $s.= $q;            
                $s .= "\n";
            }
        }
        $s.= "*/";
        return $s;
    }
}