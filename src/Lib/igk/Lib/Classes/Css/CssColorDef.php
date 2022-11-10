<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssColorDef.php
// @date: 20220730 10:18:32
// @desc: color definition 

namespace IGK\Css;

use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

class CssColorDef implements ArrayAccess{
    use ArrayAccessSelfTrait;
    private $m_data;

    private $sm_instance;

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
    protected function _access_OffsetGet($k){
        return igk_getv($this->m_data, $k);
    }
}