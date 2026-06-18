<?php
// @author: C.A.D. BONDJE DOUE
// @file: PHPDocCommentParser.php
// @date: 20230104 17:05:49
namespace IGK\System\IO\File;
use IGK\System\IO\File\Php\PhpDocBlockBase;
use IGK\System\IO\File\Php\Traits\PHPDocCommentParseTrait;
use IGKException;
use Override;

/**
* extends to handle custom property 
* @package IGK\System\IO\File
*/
class PHPDocCommentParser extends PhpDocBlockBase{
    use PHPDocCommentParseTrait;
    /**
    * Listener: property filter listener.
    * @var mixed
    */
    private $m_propertyFilterListener;
    /**
    * Listener: property handle listener.
    * @var mixed
    */
    private $m_propertyHandleListener;
    /**
    * Property: summary.
    * @var mixed
    */
    var $summary;
    /**
    * Property: param.
    * @var mixed
    */
    var $param;
    /**
    * Property: return.
    * @var mixed
    */
    var $return;
    /**
    * Property: description.
    * @var mixed
    */
    var $description;
    /**
    * Property: api.
    * @var mixed
    */
    var $api;
    /**
    * Property: throws.
    * @var mixed
    */
    var $throws;
    /**
     * authorization to bind to 
     * @var mixed
     */
    var $auth;
    /**
     * get response object
     * @var mixed
     */
    var $responses;
    /**
     * block phpunit test 
     * @var mixed
     * @usage @covers classMethod 
     */
    var $covers;
    /**
     * use with 
     * @var ?array
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
    /**
    * auto generate doc.
    * @var ?bool auth enable strict definition
    */
    var $strict_auth;
 
    protected static function CreateInstance()
    {
        $cl = new static;
        return $cl;
    }

    #[Override]
    public function getExtraProperties()
    {
        throw new \Exception('Not implemented 55 ');
    }
    /**
    * Returns Property Filter Listener.
    */
    public function getPropertyFilterListener(){
        return $this->m_propertyFilterListener;
    }
    /**
    * auto generate doc.
    * @param mixed $listener
    * @return void
    */
    public function setPropertyFilterListener($listener){
        $this->m_propertyFilterListener = $listener;
    }
    /**
    * auto generate doc.
    * @param mixed $handler
    * @return void
    */
    public function setPropertyHandlerListener($handler){
        $this->m_propertyHandleListener = $handler;
    }
    /**
    * .ctr
    * @return mixed
    */
    private function __construct(){
    }
    /**
     * override static content
     * @param string $content 
     * @return string 
     */
    protected static function _TreatContent(string $content): string{
        if (igk_str_endwith($content, "\\")){
            $content.="\n";
        }   
        return $content;
    }
    /**
     * magic invoke for property missing call
     * @param mixed $name 
     * @param mixed $arguments 
     * @return mixed 
     * @throws IGKException 
     */
    public function __call($name, $arguments)
    {
        $g = null;
        $name = str_replace('-', '_', $name);
        $filter = $this->m_propertyFilterListener;
        $handler = $this->m_propertyHandleListener;
        if ($filter){
            $name = $filter($name, $this);
        }
        $skip = false;
        if (!$handler || !($skip = $handler($name, $arguments, $this))){  
            if (!property_exists($this, $name)){
                throw new \IGKException("document comment parse error : property not exists [".$name."]");
            }
        }
        if($skip){
            return $this;
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
    /**
    * .destructor
    * @param mixed $name
    */
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
    /**
    * auto generate doc.
    * @param mixed $t
    * @param mixed & $offset
    * @return mixed
    */
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
    /**
    * get string presentation.
    */
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