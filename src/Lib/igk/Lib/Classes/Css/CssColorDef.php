<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssColorDef.php
// @date: 20220730 10:18:32
// @desc: color definition 
namespace IGK\Css;
use ArrayAccess;
use Exception;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
/**
* Css color def.
* @package IGK\Css
*/
class CssColorDef implements ArrayAccess{
    use ArrayAccessSelfTrait;
    /**
    * Property: data.
    * @var mixed
    */
    private $m_data;
    /**
    * Property: instance.
    * @var mixed
    */
    private $sm_instance;
    /**
     * return definition 
     * @param string $name 
     * @return mixed 
     */
    public function get(string $name){
        return $this[$name];
    }
    /**
     * global instances
     * @return mixed 
     */
    public static function getInstance(){
        if (is_null(self::$sm_instance)){
            self::$sm_instance = new self;            
        }
        return self::$sm_instance;
    }
    /**
    * auto generate doc.
    * @param mixed $v
    * @return void
    */
    protected function _access_OffsetSet($k, $v){
        if ($g = CssColorMarkValue::Parse($v)){
            $this->m_data[$k] = $g;
        } else {
            if ($v===null){
                unset($this->m_data[$k]);
            }else{
                $g = new CssColorMarkValue;
                $g->key = $k;
                $g->color = $v;
                $this->m_data[$k] = $g;
            }
        }       
    }
    /**
    * auto generate doc.
    * @param mixed $k
    * @return mixed
    */
    protected function _access_OffsetGet($k){
        return igk_getv($this->m_data, $k);
    }
}