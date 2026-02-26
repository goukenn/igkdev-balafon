<?php
// @file: IGKObjStorage.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\System\IToArray;
/**
 * use to copy and retrieve data or null
 * @package 
 */
class IGKObjStorage implements IToArray{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_init;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_storage = [];

    /**
    * .ctr
    * @param null|array $tab
    */
    public function __construct(?array $tab=null){
        if($tab && is_array($tab)){
            $this->m_init = true;
            foreach($tab as $k=>$v){
                $this->__set($k, $v);
            }
            $this->m_init = false;
        }
    }

    /**
    * .destructor
    * @param mixed $v
    */
    public function __get($v){
        if(isset($this->m_storage[$v])){
            return $this->m_storage[$v];
        }
        return null;
    }

    /**
    * destructor
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){
        if (!$this->m_init){
            if($v === null){
                unset($this->m_storage[$n]);
                return;
            }
        }
        $this->m_storage[$n]=$v;
    }

    /**
    * get string presentation.
    */
    public function __toString(){
        return __CLASS__;
    }
    /**
     * 
     * @return null|array 
     */

    public function to_array():?array{
        $tab = array_slice($this->m_storage,0); 
        return $tab;
    }
    /**
     * return json data
     * @return string|false 
     */

    public function to_json(){
        return json_encode($this->to_array());
    }

    /**
    * check if isset innaccessible property
    * @param mixed $name
    */
    public function __isset($name)
    {
        return key_exists($name, $this->m_storage);
    }

    /**
    * auto generate doc.
    * @param mixed $name
    */
    public function isset($name){
        return $this->__isset($name);
    }
}