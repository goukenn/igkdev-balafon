<?php
// @author: C.A.D. BONDJE DOUE
// @file: PHPDocCommentParser.php
// @date: 20230104 17:05:49
namespace IGK\System\IO\File;
use IGK\System\IO\File\Php\PhpDocBlockBase;
use IGK\System\IO\File\Php\Traits\PHPDocCommentParseTrait;
use IGKException;
/**
* extends to handle custom property 
* @package IGK\System\IO\File
*/
class PHPDocCommentParser extends PhpDocBlockBase{
    use PHPDocCommentParseTrait;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_propertyFilterListener;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_propertyHandleListener;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $summary;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $param;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $return;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $description;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $api;

    /**
    * auto generate doc.
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
    /**
     * 
     * @var ?bool auth enable strict definition  
     */
    var $strict_auth;

    /**
    * auto generate doc.
    */
    public function getPropertyFilterListener(){
        return $this->m_propertyFilterListener;
    }
    /**
     * 
     * @param mixed $listener 
     * @return void 
     */

    public function setPropertyFilterListener($listener){
        $this->m_propertyFilterListener = $listener;
    }
    /**
     * 
     * @param mixed $handler 
     * @return void 
     */

    public function setPropertyHandlerListener($handler){
        $this->m_propertyHandleListener = $handler;
    }
    private function __construct(){
    }
    /**
     * override static content
     * @param string $content 
     * @return string 
     */

    protected static function _TreatContent(string $content){
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