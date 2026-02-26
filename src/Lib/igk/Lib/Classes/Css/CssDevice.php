<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssDevice.php
// @date: 20220422 12:32:13
// @desc: css device helper -- 
// 
// + | --------------------------------------------------------------------
// + | group media type in on device so we can 
// + | make them device operate at once
// + | implement it in a .pcss file in case you whant to use it.
// + |
// + | > create and instance an mege it
// + | > sample : $mobile = new CssDevice($sm_screen, $xsm_screen); 
namespace IGK\Css;
use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessPropertyTrait;
use IGKMedia;
/**
 * css device helper 
 */
class CssDevice implements ICssSupport, ArrayAccess{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_devices;
    use ArrayAccessPropertyTrait;
    /**
     * 
     * @param mixed $devices medias
     * @return void 
     */

    public function __construct(IGKMedia & ...$devices)
    {
        $this->m_devices = $devices;
    }
    /**
     * support device css rule 
     * @param string $rule 
     * @return mixed 
     */

    public function supports(string $rule) {  
        $rule = $this->m_devices[0]->supports($rule);
        if ($rule){
            for($i= 1; $i < count($this->m_devices) ; $i++){
                $def = $this->m_devices[$i];
                $def->bindSupport($rule); 
            }
        }
        return $rule;
    }

    /**
    * auto generate doc.
    * @param mixed $n
    * @param mixed $v
    */
    public function _access_OffsetSet($n, $v)
    {
        foreach($this->m_devices as $def){
            $def[$n] = $v;
        }
    }

    /**
    * auto generate doc.
    * @param mixed $n
    */
    public function _access_OffsetUnset($n)
    {
        foreach($this->m_devices as $def){
            unset($def[$n]);
        }
    }
    /**
     * check on all device that value exists
     * @param mixed $n 
     * @return void 
     */

    public function _access_offsetExists($n)
    {
        $r = true;
        foreach($this->m_devices as $def){
            if (!($r = ($r && isset($def[$n]))))
                break;
        }
        return $r;
    }

    /**
    * auto generate doc.
    * @param mixed $n
    */
    public function _access_OffsetGet($n)
    {
        if ($this->_access_offsetExists($n)){
            return $this->m_devices[$n];
        }
        return null;
    }
}